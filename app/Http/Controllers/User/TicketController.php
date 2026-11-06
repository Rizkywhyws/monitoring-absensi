<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TicketController extends Controller
{
    private const IMAGE_MAX_DIMENSION = 1280;       
    private const IMAGE_QUALITY       = 75;         
    private const IMAGE_MAX_PIXELS    = 40000000;  

    public function index(Request $request)
    {
        $user = $request->user();
        $search = trim((string) $request->query('q', ''));
        $term = ltrim($search, '#');

        $scope = fn() => Ticket::query()
            ->when($user->role !== 'admin', fn($query) => $query->where('user_id', $user->id));

        $tickets = $scope()
            ->with('user:id,name', 'attachments')
            ->withCount('attachments')
            ->when($term !== '', function ($query) use ($term) {
                $query->where(function ($query) use ($term) {
                    $query->where('ticket_number', 'like', "%{$term}%")
                        ->orWhere('title', 'like', "%{$term}%")
                        ->orWhereHas('user', fn($creator) => $creator->where('name', 'like', "%{$term}%"));
                });
            })
            ->latest()
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $stats = [
            'total' => $scope()->count(),
            'today' => $scope()->whereDate('created_at', today())->count(),
            'with_attachment' => $scope()->has('attachments')->count(),
        ];

        return view('user.daftartTicket', compact('tickets', 'stats', 'search'));
    }

    public function create()
    {
        $nextNumber = sprintf('TKT-%05d', (Ticket::max('id') ?? 0) + 1);

        return view('user.createTicket', compact('nextNumber'));
    }

    public function store(StoreTicketRequest $request)
    {
        $data = $request->validated();

        $ticket = DB::transaction(function () use ($request, $data) {
            $ticket = Ticket::create([
                'ticket_number' => 'TMP-' . Str::random(20),
                'user_id' => $request->user()->id,
                'title' => $data['title'],
                'description' => $data['description'],
                'category' => 'Umum',
                'status' => 'to_do',
            ]);

            $ticket->update([
                'ticket_number' => sprintf('TKT-%05d', $ticket->id),
            ]);

            $this->storeAttachments($request, $ticket);

            return $ticket;
        });

        return redirect()
            ->route('tickets')
            ->with('success', "Tiket #{$ticket->ticket_number} berhasil dibuat.");
    }

    private function layoutData(Request $request): array
    {
        $name = $request->user()->name;

        return [
            'userName' => $name,
            'initials' => Str::upper(
                collect(explode(' ', trim($name)))
                    ->map(fn($word) => mb_substr($word, 0, 1))
                    ->take(2)
                    ->implode('')
            ),
        ];
    }

    public function show(Request $request, Ticket $ticket)
    {
        abort_unless(
            $request->user()->role === 'admin' || $ticket->user_id === $request->user()->id,
            403
        );

        $ticket->load(['user:id,name', 'attachments']);

        return view('user.detailTicket', compact('ticket') + $this->layoutData($request));
    }

    public function update(UpdateTicketRequest $request, Ticket $ticket)
    {
        $isAdmin = $request->user()->role === 'admin';

        abort_unless($isAdmin || $ticket->user_id === $request->user()->id, 403);

        if (! $isAdmin && in_array($ticket->status, Ticket::FINISHED, true)) {
            return back()->withErrors(['status' => 'Tiket yang sudah selesai tidak bisa diedit.']);
        }

        $data = $request->validated();
        $pathsToDelete = [];

        DB::transaction(function () use ($request, $ticket, $data, &$pathsToDelete) {
            $ticket->update([
                'title' => $data['title'],
                'description' => $data['description'],
            ]);

            $removeIds = $data['remove_attachments'] ?? [];
            if (! empty($removeIds)) {
                $toRemove = $ticket->attachments()->whereIn('id', $removeIds)->get();
                foreach ($toRemove as $attachment) {
                    $pathsToDelete[] = $attachment->file_path;
                    $attachment->delete();
                }
            }

            $this->storeAttachments($request, $ticket);
        });

        foreach ($pathsToDelete as $path) {
            Storage::disk('public')->delete($path);
        }

        return redirect()
            ->route('tickets')
            ->with('success', "Tiket #{$ticket->ticket_number} berhasil diperbarui.");
    }


    public function start(Request $request, Ticket $ticket): RedirectResponse
    {
        $affected = Ticket::query()
            ->whereKey($ticket->id)
            ->where('user_id', $request->user()->id)
            ->whereIn('status', ['to_do', 'hold', 'revision'])
            ->update([
                'status'     => 'on_progress',
                'started_at' => $ticket->started_at ?? now(),
            ]);

        return $affected
            ? back()->with('success', "Tiket #{$ticket->ticket_number} dimulai.")
            : back()->withErrors(['status' => 'Tiket tidak bisa dimulai dari status saat ini.']);
    }

    public function complete(Request $request, Ticket $ticket): RedirectResponse
    {
        $affected = Ticket::query()
            ->whereKey($ticket->id)
            ->where('user_id', $request->user()->id)
            ->where('status', 'on_progress')
            ->update([
                'status'       => 'done',
                'completed_at' => now(),
            ]);

        return $affected
            ? back()->with('success', "Tiket #{$ticket->ticket_number} selesai dan masuk Daily Activity.")
            : back()->withErrors(['status' => 'Mulai tiket terlebih dahulu sebelum menyelesaikannya.']);
    }

    private function storeAttachments(Request $request, Ticket $ticket): void
    {
        foreach ($request->file('attachments', []) as $file) {
            $stored = $this->storeFile($file);

            $ticket->attachments()->create([
                'file_name' => Str::limit($file->getClientOriginalName(), 255, ''),
                'file_type' => $stored['mime'],
                'file_size' => $stored['size'],
                'file_path' => $stored['path'],
            ]);
        }
    }

    /**
     * S
     * 
     *
     * @return array{path: string, mime: string, size: int}
     */
    private function storeFile(UploadedFile $file): array
    {
        $compressed = $this->compressImage($file);

        if ($compressed) {
            $path = 'ticket-attachments/' . Str::uuid() . '.' . $compressed['ext'];
            Storage::disk('public')->put($path, $compressed['binary']);

            return [
                'path' => $path,
                'mime' => $compressed['mime'],
                'size' => strlen($compressed['binary']),
            ];
        }

        return [
            'path' => $file->store('ticket-attachments', 'public'),
            'mime' => $file->getClientMimeType(),
            'size' => $file->getSize(),
        ];
    }

    /**
     *
     * @return array{binary: string, mime: string, ext: string}|null
     */
    private function compressImage(UploadedFile $file): ?array
    {
        if (! function_exists('imagecreatefromstring')) {
            return null;
        }

        $realPath = $file->getRealPath();
        $info = $realPath ? @getimagesize($realPath) : false;

        if ($info === false) {
            return null;
        }

        $mime = $info['mime'];
        $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

        if (! isset($extensions[$mime])) {
            return null;
        }

        if ($mime === 'image/webp' && ! function_exists('imagewebp')) {
            return null;
        }

        if ($info[0] * $info[1] > self::IMAGE_MAX_PIXELS) {
            return null;
        }

        $originalSize = (int) filesize($realPath);
        $image = @imagecreatefromstring((string) file_get_contents($realPath));

        if (! $image) {
            return null;
        }

        if ($mime === 'image/jpeg' && function_exists('exif_read_data')) {
            $orientation = (int) (@exif_read_data($realPath)['Orientation'] ?? 1);
            $angle = [3 => 180, 6 => -90, 8 => 90][$orientation] ?? 0;

            if ($angle !== 0) {
                $rotated = imagerotate($image, $angle, 0);
                if ($rotated) {
                    $image = $rotated;
                }
            }
        }

        $width = imagesx($image);
        $height = imagesy($image);
        $scale = min(1, self::IMAGE_MAX_DIMENSION / max($width, $height));

        if ($scale < 1) {
            $newWidth = max(1, (int) round($width * $scale));
            $newHeight = max(1, (int) round($height * $scale));

            $resized = imagecreatetruecolor($newWidth, $newHeight);

            if ($mime !== 'image/jpeg') {
                imagealphablending($resized, false);
                imagesavealpha($resized, true);
                imagefill($resized, 0, 0, imagecolorallocatealpha($resized, 0, 0, 0, 127));
            }

            imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            $image = $resized;
        }

        if ($mime !== 'image/jpeg') {
            imagealphablending($image, false);
            imagesavealpha($image, true);
        }

        ob_start();
        match ($mime) {
            'image/jpeg' => imagejpeg($image, null, self::IMAGE_QUALITY),
            'image/png'  => imagepng($image, null, 9),
            'image/webp' => imagewebp($image, null, self::IMAGE_QUALITY),
        };
        $binary = ob_get_clean();

        if ($binary === false || $binary === '' || strlen($binary) >= $originalSize) {
            return null;
        }

        return [
            'binary' => $binary,
            'mime'   => $mime,
            'ext'    => $extensions[$mime],
        ];
    }
}