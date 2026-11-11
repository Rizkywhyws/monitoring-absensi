@extends('layouts.app')

@section('title', 'Pengajuan Izin & Sakit')
@section('breadcrumb', 'Admin Workspace › Pengajuan Izin & Sakit')

@section('content')
    @php
        $tabs = [
            'pending' => 'Menunggu',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'cancelled' => 'Dibatalkan',
            'all' => 'Semua',
        ];
        $statusStyle = [
            'pending' => 'bg-tertiary-fixed text-on-tertiary-fixed',
            'approved' => 'bg-primary-fixed text-on-primary-fixed',
            'rejected' => 'bg-error-container text-on-error-container',
            'cancelled' => 'bg-surface-container-high text-on-surface-variant',
        ];
        $typeStyle = [
            'sakit' => 'bg-secondary-fixed text-on-secondary-fixed-variant',
            'izin' => 'bg-surface-container-high text-on-surface',
        ];
        $pgBase = 'px-space-sm py-space-xs rounded-lg border border-outline-variant font-label-lg text-label-lg';

        $range = function ($l) {
            return $l->start_date->equalTo($l->end_date)
                ? $l->start_date->locale('id')->translatedFormat('l, d M Y')
                : $l->start_date->locale('id')->translatedFormat('d M Y') .
                        ' – ' .
                        $l->end_date->locale('id')->translatedFormat('d M Y');
        };

        $rows = $leaves->getCollection()->mapWithKeys(
            fn($l) => [
                $l->id => [
                    'name' => $l->user?->name ?? '-',
                    'nim' => $l->user?->nim,
                    'division' => $l->user?->division,
                    'type' => $l->type,
                    'type_label' => $l->typeLabel(),
                    'urgent' => (bool) $l->is_urgent,
                    'range' => $range($l),
                    'work_days' => $l->workingDays(),
                    'reason' => $l->reason,
                    'status' => $l->status,
                    'status_label' => $l->statusLabel(),
                    'attachment' => $l->attachment ? route('izin.lampiran', $l) : null,
                    'submitted' => $l->created_at->locale('id')->translatedFormat('d M Y, H:i'),
                    'admin_note' => $l->admin_note,
                    'reviewer' => $l->reviewer?->name,
                    'reviewed_at' => $l->reviewed_at?->locale('id')->translatedFormat('d M Y, H:i'),
                    'urls' => [
                        'approve' => route('admin.izin.setujui', $l),
                        'reject' => route('admin.izin.tolak', $l),
                        'revoke' => route('admin.izin.cabut', $l),
                    ],
                ],
            ],
        );
    @endphp

    <div class="flex flex-col w-full gap-space-lg">

        <div>
            <h1 class="font-headline-xl text-headline-xl text-on-surface tracking-tight">Pengajuan Izin &amp; Sakit</h1>
            <p class="font-body-md text-body-md text-on-surface-variant mt-0.5">
                Tinjau pengajuan peserta PKL. Pengajuan yang disetujui otomatis tercatat di absensi pada hari kerja terkait.
            </p>
        </div>

        @if ($errors->any())
            <div
                class="px-space-md py-space-sm rounded-xl bg-error-container text-on-error-container font-body-md text-body-md">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="rounded-2xl bg-surface-container-lowest shadow-sm overflow-hidden">

            <div class="flex flex-wrap gap-space-lg px-space-lg border-b border-surface-container">
                @foreach ($tabs as $key => $label)
                    @php
                        $isActive = $status === $key;
                        $total = $key === 'all' ? $counts->sum() : $counts[$key] ?? 0;
                    @endphp
                    <a href="{{ route('admin.izin', array_filter(['status' => $key, 'keyword' => $keyword])) }}"
                        class="flex items-center gap-space-xs py-space-md font-label-lg text-label-lg font-bold border-b-4 transition-colors {{ $isActive ? 'text-primary border-primary' : 'text-on-surface-variant border-transparent hover:text-on-surface' }}">
                        {{ $label }}
                        <span
                            class="px-2 py-0.5 rounded-full font-label-sm text-label-sm {{ $isActive ? 'bg-primary-fixed text-on-primary-fixed' : 'bg-surface-container text-on-surface-variant' }}">
                            {{ $total }}
                        </span>
                    </a>
                @endforeach
            </div>

            <div class="p-space-lg bg-surface-bright">
                <form action="{{ route('admin.izin') }}" method="GET" class="flex flex-wrap items-center gap-space-sm">
                    <input type="hidden" name="status" value="{{ $status }}">
                    <input type="text" name="keyword" value="{{ $keyword }}" placeholder="Cari nama atau NIM..."
                        class="flex-1 min-w-[220px] bg-surface-container-low rounded-xl px-space-md py-3 font-body-md text-body-md text-on-surface outline-none focus:bg-surface-container transition-colors">
                    <button type="submit"
                        class="px-space-md py-3 rounded-xl bg-primary text-on-primary font-label-lg text-label-lg font-semibold hover:bg-primary-container transition-colors">
                        Cari
                    </button>
                    <a href="{{ route('admin.izin', ['status' => $status]) }}"
                        class="px-space-md py-3 rounded-xl bg-surface-container text-on-surface font-label-lg text-label-lg font-semibold hover:bg-surface-container-high transition-colors">
                        Reset
                    </a>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr
                            class="bg-surface-container-low text-left text-on-surface-variant font-label-md text-label-md uppercase tracking-wider">
                            @foreach (['Peserta', 'Jenis', 'Tanggal', 'Alasan', 'Lampiran', 'Diajukan', 'Status', 'Aksi'] as $th)
                                <th class="px-space-md py-space-sm whitespace-nowrap">{{ $th }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($leaves as $l)
                            <tr class="hover:bg-surface-bright transition-colors font-body-md text-body-md text-on-surface">
                                <td class="px-space-md py-space-sm border-b border-surface-container">
                                    <div class="font-semibold">{{ $l->user?->name ?? '-' }}</div>
                                    <div class="font-body-sm text-body-sm text-on-surface-variant">
                                        {{ $l->user?->nim }}@if ($l->user?->division)
                                            · {{ $l->user->division }}
                                        @endif
                                    </div>>
                                </td>
                                <td class="px-space-md py-space-sm border-b border-surface-container">
                                    <div class="flex flex-col items-start gap-1">
                                        <span
                                            class="px-2 py-0.5 rounded-full {{ $typeStyle[$l->type] ?? '' }} font-label-sm text-[11px]">{{ $l->typeLabel() }}</span>
                                        @if ($l->is_urgent)
                                            <span
                                                class="px-2 py-0.5 rounded-full bg-error-container text-on-error-container font-label-sm text-[11px]">Mendesak</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-space-md py-space-sm border-b border-surface-container whitespace-nowrap">
                                    {{ $range($l) }}
                                    <div class="font-body-sm text-body-sm text-on-surface-variant">{{ $l->workingDays() }}
                                        hari kerja</div>
                                </td>
                                <td class="px-space-md py-space-sm border-b border-surface-container max-w-[260px]">
                                    <span class="line-clamp-2 text-on-surface-variant">{{ $l->reason }}</span>
                                </td>
                                <td class="px-space-md py-space-sm border-b border-surface-container">
                                    @if ($l->attachment)
                                        <a href="{{ route('izin.lampiran', $l) }}" target="_blank" rel="noopener"
                                            class="inline-flex items-center gap-1 font-label-md text-label-md text-primary hover:underline">
                                            <span class="material-symbols-outlined text-[16px]">attach_file</span> Lihat
                                        </a>
                                    @else
                                        <span class="text-on-surface-variant">-</span>
                                    @endif
                                </td>
                                <td
                                    class="px-space-md py-space-sm border-b border-surface-container whitespace-nowrap text-on-surface-variant">
                                    {{ $l->created_at->locale('id')->translatedFormat('d M Y, H:i') }}
                                </td>
                                <td class="px-space-md py-space-sm border-b border-surface-container">
                                    <span
                                        class="inline-flex items-center gap-1 px-space-xs py-1 rounded-full {{ $statusStyle[$l->status] ?? '' }} font-label-md text-label-md font-bold whitespace-nowrap">
                                        ● {{ $l->statusLabel() }}
                                    </span>
                                </td>
                                <td class="px-space-md py-space-sm border-b border-surface-container">
                                    <button type="button" data-review="{{ $l->id }}"
                                        class="inline-flex items-center gap-1 h-9 px-space-sm rounded-lg {{ $l->status === 'pending' ? 'bg-primary text-on-primary hover:bg-primary-container' : 'bg-surface-container-low text-primary hover:bg-surface-container-high' }} font-label-md text-label-md font-bold transition-colors whitespace-nowrap">
                                        <span
                                            class="material-symbols-outlined text-[18px]">{{ $l->status === 'pending' ? 'rule' : 'visibility' }}</span>
                                        {{ $l->status === 'pending' ? 'Tinjau' : 'Detail' }}
                                    </button>
                                </td>
                            </tr>
                            @empty
                                <tr>
                                    <td colspan="8"
                                        class="px-space-md py-space-2xl text-center text-on-surface-variant font-body-md text-body-md">
                                        Tidak ada pengajuan pada filter ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($leaves->hasPages())
                    <div
                        class="px-space-lg py-space-md flex flex-col sm:flex-row items-center justify-between gap-space-sm font-body-md text-body-md text-on-surface-variant">
                        <div>Menampilkan {{ $leaves->firstItem() ?? 0 }} - {{ $leaves->lastItem() ?? 0 }} dari
                            {{ $leaves->total() }}</div>
                        <div class="flex gap-1.5">
                            @if ($leaves->onFirstPage())
                                <span class="{{ $pgBase }} opacity-50">‹</span>
                            @else
                                <a href="{{ $leaves->previousPageUrl() }}"
                                    class="{{ $pgBase }} hover:bg-surface-container">‹</a>
                            @endif

                            @foreach ($leaves->getUrlRange(max(1, $leaves->currentPage() - 2), min($leaves->lastPage(), $leaves->currentPage() + 2)) as $page => $url)
                                @if ($page == $leaves->currentPage())
                                    <span
                                        class="{{ $pgBase }} bg-primary text-on-primary border-primary">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}"
                                        class="{{ $pgBase }} hover:bg-surface-container">{{ $page }}</a>
                                @endif
                            @endforeach

                            @if ($leaves->hasMorePages())
                                <a href="{{ $leaves->nextPageUrl() }}"
                                    class="{{ $pgBase }} hover:bg-surface-container">›</a>
                            @else
                                <span class="{{ $pgBase }} opacity-50">›</span>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <div id="review-modal"
            style="display:none; position:fixed; inset:0; z-index:60; align-items:center; justify-content:center; padding:16px; background:rgba(0,0,0,.5); backdrop-filter:blur(4px);"
            role="dialog" aria-modal="true">
            <div
                class="w-full max-w-lg max-h-[90vh] overflow-y-auto rounded-2xl bg-surface-container-lowest shadow-2xl p-space-xl flex flex-col gap-space-md">

                <div class="flex items-start justify-between gap-space-sm">
                    <div class="flex flex-col min-w-0">
                        <span id="r-name" class="font-headline-md text-headline-md text-on-surface truncate">–</span>
                        <span id="r-meta" class="font-body-sm text-body-sm text-on-surface-variant">–</span>
                    </div>
                    <button type="button" id="review-close" aria-label="Tutup"
                        class="p-1 rounded-full text-on-surface-variant hover:bg-surface-container-high transition-colors">
                        <span class="material-symbols-outlined text-[22px]">close</span>
                    </button>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <span id="r-type" class="px-2 py-0.5 rounded-full font-label-sm text-[11px]">–</span>
                    <span id="r-urgent"
                        class="px-2 py-0.5 rounded-full bg-error-container text-on-error-container font-label-sm text-[11px]"
                        style="display:none;">Mendesak</span>
                    <span id="r-status" class="px-2 py-0.5 rounded-full font-label-sm text-[11px]">–</span>
                </div>

                <div class="flex flex-col gap-2 p-space-md rounded-xl bg-surface-container-low font-body-sm text-body-sm">
                    <div class="flex justify-between gap-3">
                        <span class="text-on-surface-variant">Tanggal</span>
                        <span id="r-range" class="font-label-md text-label-md text-on-surface text-right">–</span>
                    </div>
                    <div class="flex justify-between gap-3">
                        <span class="text-on-surface-variant">Hari kerja</span>
                        <span id="r-days" class="font-label-md text-label-md text-on-surface">–</span>
                    </div>
                    <div class="flex justify-between gap-3">
                        <span class="text-on-surface-variant">Diajukan</span>
                        <span id="r-submitted" class="font-label-md text-label-md text-on-surface">–</span>
                    </div>
                </div>

                <div class="flex flex-col gap-1">
                    <span class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider">Alasan</span>
                    <p id="r-reason" class="font-body-md text-body-md text-on-surface whitespace-pre-line break-words">–</p>
                </div>

                <a id="r-attachment" href="#" target="_blank" rel="noopener" style="display:none;"
                    class="self-start items-center gap-1.5 h-9 px-space-md rounded-lg bg-primary/10 text-primary font-label-md text-label-md hover:bg-primary/20 transition-colors">
                    <span class="material-symbols-outlined text-[18px]">attach_file</span> Buka lampiran
                </a>

                <div id="r-previous" class="flex items-start gap-space-xs px-space-sm py-2 rounded-lg bg-surface-container"
                    style="display:none;">
                    <span class="material-symbols-outlined text-[18px] mt-0.5 text-outline">comment</span>
                    <div class="flex flex-col">
                        <span id="r-previous-title" class="font-label-md text-label-md text-on-surface">Catatan admin</span>
                        <span id="r-previous-note" class="font-body-sm text-body-sm text-on-surface-variant">–</span>
                    </div>
                </div>

                <form id="review-form" method="POST" action="" class="flex flex-col gap-space-sm"
                    style="display:none;">
                    @csrf
                    @method('PUT')

                    <div class="flex flex-col gap-1.5">
                        <label for="admin_note" id="note-label"
                            class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider">Catatan untuk
                            peserta</label>
                        <textarea id="admin_note" name="admin_note" rows="3" maxlength="500"
                            class="w-full bg-surface-container-low rounded-xl px-space-md py-3 font-body-md text-body-md text-on-surface outline-none focus:bg-surface-container transition-colors resize-none"></textarea>
                        <p id="note-error" class="font-body-sm text-body-sm text-error" style="display:none;"></p>
                    </div>

                    <div id="actions-pending" class="flex gap-space-sm" style="display:none;">
                        <button type="submit" id="btn-reject" formaction=""
                            class="flex-1 h-11 rounded-lg bg-error-container text-on-error-container font-label-lg text-label-lg hover:opacity-90 transition-opacity flex items-center justify-center gap-1.5">
                            <span class="material-symbols-outlined text-[20px]">close</span> Tolak
                        </button>
                        <button type="submit" id="btn-approve" formaction=""
                            class="flex-1 h-11 rounded-lg bg-primary text-on-primary font-label-lg text-label-lg hover:bg-primary-container shadow transition-colors flex items-center justify-center gap-1.5">
                            <span class="material-symbols-outlined text-[20px]">check</span> Setujui
                        </button>
                    </div>

                    <div id="actions-approved" class="flex" style="display:none;">
                        <button type="submit" id="btn-revoke" formaction=""
                            class="flex-1 h-11 rounded-lg bg-surface-container-high text-error font-label-lg text-label-lg hover:bg-error-container transition-colors flex items-center justify-center gap-1.5">
                            <span class="material-symbols-outlined text-[20px]">undo</span> Cabut Persetujuan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endsection

@section('extra-scripts')
    <script>
        (function() {
            const ROWS = @json($rows);
            const $ = (id) => document.getElementById(id);

            const modal = $('review-modal');
            const form = $('review-form');
            const note = $('admin_note');
            const noteError = $('note-error');

            const TYPE_CLS = {
                sakit: 'bg-secondary-fixed text-on-secondary-fixed-variant',
                izin: 'bg-surface-container-high text-on-surface',
            };
            const STATUS_CLS = {
                pending: 'bg-tertiary-fixed text-on-tertiary-fixed',
                approved: 'bg-primary-fixed text-on-primary-fixed',
                rejected: 'bg-error-container text-on-error-container',
                cancelled: 'bg-surface-container-high text-on-surface-variant',
            };

            function show(id) {
                const r = ROWS[id];
                if (!r) return;

                $('r-name').textContent = r.name;
                $('r-meta').textContent = [r.nim, r.division].filter(Boolean).join(' · ') || '–';

                const t = $('r-type');
                t.textContent = r.type_label;
                t.className = `px-2 py-0.5 rounded-full font-label-sm text-[11px] ${TYPE_CLS[r.type] || ''}`;
                $('r-urgent').style.display = r.urgent ? 'inline-block' : 'none';
                const s = $('r-status');
                s.textContent = r.status_label;
                s.className = `px-2 py-0.5 rounded-full font-label-sm text-[11px] ${STATUS_CLS[r.status] || ''}`;

                $('r-range').textContent = r.range;
                $('r-days').textContent = `${r.work_days} hari`;
                $('r-submitted').textContent = r.submitted;
                $('r-reason').textContent = r.reason;

                const att = $('r-attachment');
                if (r.attachment) {
                    att.href = r.attachment;
                    att.style.display = 'inline-flex';
                } else {
                    att.style.display = 'none';
                }

                const prev = $('r-previous');
                if (r.admin_note || r.reviewer) {
                    $('r-previous-title').textContent = r.reviewer ?
                        `Ditinjau oleh ${r.reviewer}${r.reviewed_at ? ' · ' + r.reviewed_at : ''}` :
                        'Catatan';
                    $('r-previous-note').textContent = r.admin_note || '(tanpa catatan)';
                    prev.style.display = 'flex';
                } else {
                    prev.style.display = 'none';
                }

                note.value = '';
                noteError.style.display = 'none';
                const pending = r.status === 'pending';
                const approved = r.status === 'approved';

                form.style.display = pending || approved ? 'flex' : 'none';
                $('actions-pending').style.display = pending ? 'flex' : 'none';
                $('actions-approved').style.display = approved ? 'flex' : 'none';
                $('note-label').textContent = pending ?
                    'Catatan untuk peserta (wajib jika menolak)' :
                    'Catatan pencabutan (opsional)';

                $('btn-approve').setAttribute('formaction', r.urls.approve);
                $('btn-reject').setAttribute('formaction', r.urls.reject);
                $('btn-revoke').setAttribute('formaction', r.urls.revoke);

                modal.style.display = 'flex';
            }

            function close() {
                modal.style.display = 'none';
            }

            document.querySelectorAll('[data-review]').forEach((btn) => {
                btn.addEventListener('click', () => show(btn.dataset.review));
            });
            $('review-close').addEventListener('click', close);
            modal.addEventListener('click', (e) => {
                if (e.target === modal) close();
            });
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && modal.style.display === 'flex') close();
            });

            $('btn-reject').addEventListener('click', (e) => {
                if (note.value.trim().length < 5) {
                    e.preventDefault();
                    noteError.textContent = 'Catatan penolakan wajib diisi (minimal 5 karakter).';
                    noteError.style.display = 'block';
                    note.focus();
                }
            });

            form.addEventListener('submit', (e) => {
                if (e.defaultPrevented) return;
                setTimeout(() => {
                    form.querySelectorAll('button[type="submit"]').forEach((b) => {
                        b.disabled = true;
                    });
                }, 0);
            });
        })();
    </script>
@endsection
