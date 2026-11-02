<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; 
use App\Models\Attendance;
use App\Models\LeaveRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LeaveRequestController extends Controller
{
    public function index(Request $request)
    {
        $leaves = LeaveRequest::where('user_id', $request->user()->id)
            ->with('reviewer:id,name')
            ->latest()
            ->paginate(8);

        return view('user.izin', [
            'leaves' => $leaves,
            'rules'  => [
                'max_days'          => LeaveRequest::MAX_CALENDAR_DAYS,
                'sick_backdate'     => LeaveRequest::SICK_BACKDATE_DAYS,
                'max_attachment_kb' => LeaveRequest::MAX_ATTACHMENT_KB,
                'today'             => today()->toDateString(),
                'tomorrow'          => today()->addDay()->toDateString(),
                'sick_min'          => today()->subDays(LeaveRequest::SICK_BACKDATE_DAYS)->toDateString(),
            ],
        ]);
    }

    /** Kirim pengajuan baru (status awal: pending) */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'type'       => ['required', Rule::in(array_keys(LeaveRequest::TYPES))],
            'start_date' => ['required', 'date_format:Y-m-d'],
            'end_date'   => ['required', 'date_format:Y-m-d', 'after_or_equal:start_date'],
            'reason'     => ['required', 'string', 'min:10', 'max:1000'],
            'is_urgent'  => ['nullable', 'boolean'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:' . LeaveRequest::MAX_ATTACHMENT_KB],
        ], [
            'type.required'              => 'Pilih jenis pengajuan (Sakit atau Izin).',
            'start_date.required'        => 'Tanggal mulai wajib diisi.',
            'start_date.date_format'     => 'Format tanggal mulai tidak valid.',
            'end_date.required'          => 'Tanggal selesai wajib diisi.',
            'end_date.date_format'       => 'Format tanggal selesai tidak valid.',
            'end_date.after_or_equal'    => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
            'reason.required'            => 'Alasan wajib diisi.',
            'reason.min'                 => 'Alasan minimal :min karakter agar admin memahami keperluan Anda.',
            'reason.max'                 => 'Alasan maksimal :max karakter.',
            'attachment.mimes'           => 'Lampiran harus berupa file JPG, PNG, atau PDF.',
            'attachment.max'             => 'Ukuran lampiran maksimal 2 MB.',
            'attachment.uploaded'        => 'Lampiran gagal diunggah. Pastikan ukurannya tidak lebih dari 2 MB.',
        ]);

        $user   = $request->user();
        $type   = $data['type'];
        $urgent = $type === 'izin' && $request->boolean('is_urgent');
        $start  = Carbon::createFromFormat('Y-m-d', $data['start_date'])->startOfDay();
        $end    = Carbon::createFromFormat('Y-m-d', $data['end_date'])->startOfDay();
        $calendarDays = (int) $start->diffInDays($end, true) + 1;
        if ($calendarDays > LeaveRequest::MAX_CALENDAR_DAYS) {
            $this->invalid('end_date', 'Maksimal ' . LeaveRequest::MAX_CALENDAR_DAYS . ' hari per pengajuan. Ajukan terpisah untuk periode yang lebih panjang.');
        }

        if ($type === 'sakit') {
            $earliest = today()->subDays(LeaveRequest::SICK_BACKDATE_DAYS);
            if ($start->lt($earliest) || $start->gt(today())) {
                $this->invalid('start_date', 'Sakit hanya dapat diajukan mulai hari ini atau mundur maksimal ' . LeaveRequest::SICK_BACKDATE_DAYS . ' hari.');
            }
        } else {
            $earliest = $urgent ? today() : today()->addDay();
            if ($start->lt($earliest)) {
                $this->invalid(
                    'start_date',
                    $urgent
                        ? 'Izin mendesak hanya dapat dimulai hari ini atau setelahnya.'
                        : 'Izin keperluan lain diajukan minimal H-1 (mulai besok). Centang "Mendesak" jika bersifat darurat.'
                );
            }
        }

        $workDates = LeaveRequest::workingDates($start, $end);
        if (count($workDates) === 0) {
            $this->invalid('start_date', 'Rentang tanggal yang dipilih hanya berisi akhir pekan.');
        }
        if ($type === 'sakit' && count($workDates) > 1 && ! $request->hasFile('attachment')) {
            $this->invalid('attachment', 'Sakit lebih dari 1 hari kerja wajib melampirkan surat dokter.');
        }

        if ($user->periode_mulai && $start->lt($user->periode_mulai->copy()->startOfDay())) {
            $this->invalid('start_date', 'Tanggal mulai berada sebelum periode PKL Anda.');
        }
        if ($user->periode_selesai && $end->gt($user->periode_selesai->copy()->startOfDay())) {
            $this->invalid('end_date', 'Tanggal selesai berada setelah periode PKL Anda berakhir.');
        }

        $overlap = LeaveRequest::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved'])
            ->whereDate('start_date', '<=', $end)
            ->whereDate('end_date', '>=', $start)
            ->first();

        if ($overlap) {
            $this->invalid(
                'start_date',
                'Rentang ini bertabrakan dengan pengajuan ' . strtolower($overlap->typeLabel()) . ' Anda pada '
                . $overlap->start_date->format('d/m/Y') . ' – ' . $overlap->end_date->format('d/m/Y')
                . ' (status: ' . strtolower($overlap->statusLabel()) . ').'
            );
        }

        $existing = Attendance::where('user_id', $user->id)
            ->whereBetween('attendance_date', [$start->toDateString(), $end->toDateString()])
            ->orderBy('attendance_date')
            ->first();

        if ($existing) {
            $this->invalid(
                'start_date',
                'Anda sudah memiliki catatan absensi pada ' . $existing->attendance_date->format('d/m/Y')
                . ', sehingga pengajuan tidak dapat dibuat untuk tanggal tersebut.'
            );
        }

        $path = $request->file('attachment')?->store('leave_attachments/' . $user->id, 'local');

        LeaveRequest::create([
            'user_id'    => $user->id,
            'type'       => $type,
            'start_date' => $start->toDateString(),
            'end_date'   => $end->toDateString(),
            'reason'     => trim($data['reason']),
            'is_urgent'  => $urgent,
            'attachment' => $path,
            'status'     => 'pending',
        ]);

        return redirect()
            ->route('izin')
            ->with('success', 'Pengajuan ' . strtolower(LeaveRequest::TYPES[$type]) . ' berhasil dikirim dan menunggu persetujuan admin.');
    }
    public function cancel(Request $request, LeaveRequest $leave): RedirectResponse
    {
        abort_unless($leave->user_id === $request->user()->id, 403);

        $updated = LeaveRequest::whereKey($leave->id)
            ->where('status', 'pending')
            ->update(['status' => 'cancelled']);

        if (! $updated) {
            return back()->withErrors(['cancel' => 'Hanya pengajuan yang masih menunggu yang dapat dibatalkan.']);
        }

        return back()->with('success', 'Pengajuan berhasil dibatalkan.');
    }

    public function attachment(Request $request, LeaveRequest $leave): StreamedResponse
    {
        $user = $request->user();

        abort_unless($user->role === 'admin' || $leave->user_id === $user->id, 403);
        abort_unless($leave->attachment && Storage::disk('local')->exists($leave->attachment), 404);

        return Storage::disk('local')->response($leave->attachment);
    }

    private function invalid(string $field, string $message): never
    {
        throw ValidationException::withMessages([$field => $message]);
    }
}