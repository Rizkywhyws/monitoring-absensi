<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeaveApprovalController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'status'  => ['nullable', 'in:pending,approved,rejected,cancelled,all'],
            'keyword' => ['nullable', 'string', 'max:100'],
        ]);

        $status  = $filters['status'] ?? 'pending';
        $keyword = $filters['keyword'] ?? null;

        $base = LeaveRequest::query()
            ->when($keyword, function ($q) use ($keyword) {
                $q->whereHas('user', function ($u) use ($keyword) {
                    $u->where(function ($w) use ($keyword) {
                        $w->where('name', 'like', "%{$keyword}%")
                            ->orWhere('nim', 'like', "%{$keyword}%");
                    });
                });
            });
        $counts = (clone $base)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $leaves = (clone $base)
            ->when($status !== 'all', fn($q) => $q->where('status', $status))
            ->with(['user', 'reviewer:id,name'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.AdminIzin', [
            'leaves'  => $leaves,
            'counts'  => $counts,
            'status'  => $status,
            'keyword' => $keyword,
        ]);
    }

    public function approve(Request $request, LeaveRequest $leave): RedirectResponse
    {
        $data = $request->validate([
            'admin_note' => ['nullable', 'string', 'max:500'],
        ]);

        $created = 0;
        $skipped = 0;

        $approved = DB::transaction(function () use ($leave, $data, $request, &$created, &$skipped) {
            $fresh = LeaveRequest::whereKey($leave->id)->lockForUpdate()->first();

            if (! $fresh || $fresh->status !== 'pending') {
                return false;
            }

            foreach (LeaveRequest::workingDates($fresh->start_date, $fresh->end_date) as $date) {
                try {
                    $row = Attendance::firstOrCreate(
                        ['user_id' => $fresh->user_id, 'attendance_date' => $date->toDateString()],
                        ['status' => $fresh->type, 'leave_request_id' => $fresh->id]
                    );

                    $row->wasRecentlyCreated ? $created++ : $skipped++;
                } catch (UniqueConstraintViolationException $e) {
                    $skipped++;
                }
            }

            $fresh->update([
                'status'      => 'approved',
                'admin_note'  => $data['admin_note'] ?? null,
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
            ]);

            return true;
        });

        if (! $approved) {
            return back()->withErrors(['review' => 'Pengajuan ini sudah diproses atau tidak lagi menunggu.']);
        }

        $message = "Pengajuan disetujui. {$created} hari dicatat sebagai " . strtolower($leave->typeLabel()) . '.';
        if ($skipped > 0) {
            $message .= " {$skipped} tanggal dilewati karena sudah memiliki catatan absensi.";
        }

        return back()->with('success', $message);
    }
    public function reject(Request $request, LeaveRequest $leave): RedirectResponse
    {
        $data = $request->validate([
            'admin_note' => ['required', 'string', 'min:5', 'max:500'],
        ], [
            'admin_note.required' => 'Catatan wajib diisi saat menolak pengajuan.',
            'admin_note.min'      => 'Catatan penolakan minimal :min karakter.',
        ]);

        $updated = LeaveRequest::whereKey($leave->id)
            ->where('status', 'pending')
            ->update([
                'status'      => 'rejected',
                'admin_note'  => $data['admin_note'],
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
            ]);

        if (! $updated) {
            return back()->withErrors(['review' => 'Pengajuan ini sudah diproses atau tidak lagi menunggu.']);
        }

        return back()->with('success', 'Pengajuan ditolak.');
    }

    public function revoke(Request $request, LeaveRequest $leave): RedirectResponse
    {
        $data = $request->validate([
            'admin_note' => ['nullable', 'string', 'max:500'],
        ]);

        $revoked = DB::transaction(function () use ($leave, $data, $request) {
            $fresh = LeaveRequest::whereKey($leave->id)->lockForUpdate()->first();

            if (! $fresh || $fresh->status !== 'approved') {
                return false;
            }

            Attendance::where('leave_request_id', $fresh->id)
                ->whereNull('check_in')
                ->delete();

            $fresh->update([
                'status'      => 'cancelled',
                'admin_note'  => $data['admin_note'] ?: 'Persetujuan dicabut oleh admin.',
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
            ]);

            return true;
        });

        if (! $revoked) {
            return back()->withErrors(['review' => 'Hanya pengajuan yang sudah disetujui yang dapat dicabut.']);
        }

        return back()->with('success', 'Persetujuan dicabut. Catatan izin/sakit pada absensi sudah dihapus.');
    }
}
