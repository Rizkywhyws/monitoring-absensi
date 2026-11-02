@extends('layouts.App')

@section('title', 'Izin & Sakit - Sistem Manajemen PKL PLN Icon Plus')

@section('breadcrumb')
    Presensi &amp; Kehadiran <span class="material-symbols-outlined text-[14px] align-middle">chevron_right</span>
    <span class="text-primary font-label-md">Izin &amp; Sakit</span>
@endsection

@section('extra-styles')
    <style>
        button:disabled { opacity: .55; cursor: not-allowed; }
        .modal-card { animation: modal-in .25s cubic-bezier(.2, .9, .3, 1.1) both; }
        @keyframes modal-in {
            from { opacity: 0; transform: translateY(16px) scale(.96); }
            to { opacity: 1; transform: none; }
        }
        @media (prefers-reduced-motion: reduce) { .modal-card { animation-duration: .01ms !important; } }
    </style>
@endsection

@section('content')
    @php
        $typeStyle = [
            'sakit' => 'bg-secondary-fixed text-on-secondary-fixed-variant',
            'izin'  => 'bg-surface-container-high text-on-surface',
        ];
        $statusStyle = [
            'pending'   => 'bg-tertiary-fixed text-on-tertiary-fixed',
            'approved'  => 'bg-primary-fixed text-on-primary-fixed',
            'rejected'  => 'bg-error-container text-on-error-container',
            'cancelled' => 'bg-surface-container-high text-on-surface-variant',
        ];
        $inputBase = 'w-full bg-surface-container-low rounded-xl px-space-md py-3 font-body-md text-body-md text-on-surface outline-none focus:bg-surface-container transition-colors border border-transparent focus:border-primary/40';
    @endphp

    <div class="flex flex-col gap-space-xl">

        {{-- ================= HEADER ================= --}}
        <div>
            <h1 class="font-headline-md text-headline-md text-on-surface">Izin &amp; Sakit</h1>
            <p class="font-body-md text-body-md text-on-surface-variant">
                Ajukan izin atau sakit jika tidak dapat hadir. Pengajuan harus disetujui admin sebelum dicatat di absensi.
            </p>
        </div>

        @if ($errors->has('cancel') || $errors->has('review'))
            <div class="px-space-md py-space-sm rounded-xl bg-error-container text-on-error-container font-body-md text-body-md">
                {{ $errors->first('cancel') ?: $errors->first('review') }}
            </div>
        @endif

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-space-xl items-start">

            {{-- ================= FORM PENGAJUAN ================= --}}
            <div class="xl:col-span-5 rounded-xl bg-surface-container-lowest shadow-sm p-space-xl flex flex-col gap-space-lg">
                <div class="flex items-center gap-space-xs">
                    <span class="material-symbols-outlined text-primary text-[22px]">edit_calendar</span>
                    <span class="font-headline-sm text-headline-sm text-on-surface">Ajukan Izin / Sakit</span>
                </div>

                <form id="leave-form" method="POST" action="{{ route('izin.store') }}" enctype="multipart/form-data"
                    class="flex flex-col gap-space-md" novalidate>
                    @csrf

                    {{-- Jenis --}}
                    <div class="flex flex-col gap-1.5">
                        <span class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider">Jenis Pengajuan</span>
                        <div class="grid grid-cols-2 gap-space-sm">
                            @foreach (['sakit' => ['Sakit', 'sick'], 'izin' => ['Izin', 'event_busy']] as $value => [$label, $icon])
                                <label class="cursor-pointer">
                                    <input type="radio" name="type" value="{{ $value }}" class="peer sr-only"
                                        @checked(old('type', 'sakit') === $value)>
                                    <div class="flex items-center justify-center gap-1.5 h-12 rounded-xl bg-surface-container-low text-on-surface-variant font-label-lg text-label-lg border border-transparent transition-all peer-checked:bg-primary peer-checked:text-on-primary peer-checked:shadow-md">
                                        <span class="material-symbols-outlined text-[20px]">{{ $icon }}</span>
                                        <span>{{ $label }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('type') <p class="font-body-sm text-body-sm text-error">{{ $message }}</p> @enderror
                    </div>

                    {{-- Tanggal --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-sm">
                        <div class="flex flex-col gap-1.5">
                            <label for="start_date" class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider">Tanggal Mulai</label>
                            <input type="date" id="start_date" name="start_date" value="{{ old('start_date') }}" class="{{ $inputBase }}">
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label for="end_date" class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider">Tanggal Selesai</label>
                            <input type="date" id="end_date" name="end_date" value="{{ old('end_date') }}" class="{{ $inputBase }}">
                        </div>
                    </div>
                    @error('start_date') <p class="font-body-sm text-body-sm text-error -mt-2">{{ $message }}</p> @enderror
                    @error('end_date') <p class="font-body-sm text-body-sm text-error -mt-2">{{ $message }}</p> @enderror

                    <p id="date-hint" class="font-body-sm text-body-sm text-on-surface-variant -mt-2"></p>

                    {{-- Mendesak (hanya untuk izin) --}}
                    <label id="urgent-wrap" class="items-start gap-space-sm p-space-sm rounded-xl bg-surface-container-low cursor-pointer" style="display:none;">
                        <input type="checkbox" id="is_urgent" name="is_urgent" value="1" class="mt-1 accent-[#006857]"
                            @checked(old('is_urgent'))>
                        <span class="flex flex-col">
                            <span class="font-label-lg text-label-lg text-on-surface">Keperluan mendesak</span>
                            <span class="font-body-sm text-body-sm text-on-surface-variant">Centang jika darurat sehingga izin perlu dimulai hari ini (tidak sempat H-1).</span>
                        </span>
                    </label>

                    {{-- Ringkasan hari kerja --}}
                    <div id="days-summary" class="flex items-center gap-space-xs px-space-sm py-2 rounded-lg bg-primary/10 text-primary font-label-md text-label-md" style="display:none;">
                        <span class="material-symbols-outlined text-[18px]">date_range</span>
                        <span id="days-text"></span>
                    </div>

                    {{-- Alasan --}}
                    <div class="flex flex-col gap-1.5">
                        <div class="flex items-center justify-between">
                            <label for="reason" class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider">Alasan</label>
                            <span id="reason-count" class="font-body-sm text-body-sm text-on-surface-variant">0/1000</span>
                        </div>
                        <textarea id="reason" name="reason" rows="4" maxlength="1000"
                            placeholder="Jelaskan alasan Anda secara singkat dan jelas (minimal 10 karakter)"
                            class="{{ $inputBase }} resize-none">{{ old('reason') }}</textarea>
                        @error('reason') <p class="font-body-sm text-body-sm text-error">{{ $message }}</p> @enderror
                    </div>

                    {{-- Lampiran --}}
                    <div class="flex flex-col gap-1.5">
                        <div class="flex items-center justify-between">
                            <label for="attachment" class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider">Lampiran</label>
                            <span id="attachment-badge" class="px-2 py-0.5 rounded-full bg-surface-container-high text-on-surface-variant font-label-sm text-[10px]">Opsional</span>
                        </div>
                        <input type="file" id="attachment" name="attachment" accept=".jpg,.jpeg,.png,.pdf"
                            class="w-full font-body-sm text-body-sm text-on-surface-variant file:mr-3 file:px-space-md file:py-2 file:rounded-lg file:border-0 file:bg-primary/10 file:text-primary file:font-label-md file:cursor-pointer hover:file:bg-primary/20">
                        <p class="font-body-sm text-body-sm text-on-surface-variant">
                            JPG, PNG, atau PDF, maksimal 2 MB. Contoh: surat dokter. Hanya Anda dan admin yang dapat melihat lampiran.
                        </p>
                        <p id="attachment-error" class="font-body-sm text-body-sm text-error" style="display:none;"></p>
                        @error('attachment') <p class="font-body-sm text-body-sm text-error">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit" id="submit-btn"
                        class="w-full h-12 rounded-xl bg-primary text-on-primary font-label-lg text-label-lg shadow-md hover:bg-primary-container active:scale-[0.99] transition-all flex items-center justify-center gap-1.5">
                        <span class="material-symbols-outlined text-[22px]">send</span>
                        <span>Kirim Pengajuan</span>
                    </button>
                </form>

                <div class="flex flex-col gap-1.5 p-space-md rounded-xl bg-surface-container-low font-body-sm text-body-sm text-on-surface-variant">
                    <span class="font-label-lg text-label-lg text-on-surface flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[18px] text-primary">info</span> Ketentuan
                    </span>
                    <ul class="list-disc pl-space-md flex flex-col gap-1">
                        <li><strong>Sakit</strong>: diajukan hari ini atau mundur maksimal {{ $rules['sick_backdate'] }} hari. Sakit lebih dari 1 hari kerja wajib melampirkan surat dokter.</li>
                        <li><strong>Izin</strong>: diajukan minimal H-1, kecuali keperluan mendesak.</li>
                        <li>Maksimal {{ $rules['max_days'] }} hari per pengajuan. Hanya hari kerja (Senin–Jumat) yang dihitung.</li>
                        <li>Jika Anda tetap masuk dan check-in saat pengajuan masih menunggu, pengajuan dibatalkan otomatis.</li>
                    </ul>
                </div>
            </div>

            {{-- ================= RIWAYAT PENGAJUAN ================= --}}
            <div class="xl:col-span-7 rounded-xl bg-surface-container-lowest shadow-sm p-space-xl flex flex-col gap-space-md">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-space-xs">
                        <span class="material-symbols-outlined text-primary text-[22px]">history</span>
                        <span class="font-headline-sm text-headline-sm text-on-surface">Riwayat Pengajuan</span>
                    </div>
                    <span class="font-body-sm text-body-sm text-on-surface-variant">{{ $leaves->total() }} pengajuan</span>
                </div>

                <div class="flex flex-col gap-space-sm">
                    @forelse ($leaves as $l)
                        <div class="p-space-md rounded-xl bg-surface-container-low flex flex-col gap-space-xs">
                            <div class="flex items-center justify-between gap-space-sm flex-wrap">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="px-2 py-0.5 rounded-full {{ $typeStyle[$l->type] ?? '' }} font-label-sm text-[11px]">{{ $l->typeLabel() }}</span>
                                    @if ($l->is_urgent)
                                        <span class="px-2 py-0.5 rounded-full bg-error-container text-on-error-container font-label-sm text-[11px]">Mendesak</span>
                                    @endif
                                </div>
                                <span class="px-2 py-0.5 rounded-full {{ $statusStyle[$l->status] ?? '' }} font-label-sm text-[11px]">{{ $l->statusLabel() }}</span>
                            </div>

                            <div class="flex items-center gap-2 font-label-lg text-label-lg text-on-surface font-semibold">
                                <span class="material-symbols-outlined text-[18px] text-primary">date_range</span>
                                @if ($l->start_date->equalTo($l->end_date))
                                    {{ $l->start_date->locale('id')->translatedFormat('l, d M Y') }}
                                @else
                                    {{ $l->start_date->locale('id')->translatedFormat('d M Y') }} – {{ $l->end_date->locale('id')->translatedFormat('d M Y') }}
                                @endif
                                <span class="font-body-sm text-body-sm text-on-surface-variant font-normal">({{ $l->workingDays() }} hari kerja)</span>
                            </div>

                            <p class="font-body-md text-body-md text-on-surface-variant line-clamp-2">{{ $l->reason }}</p>

                            @if ($l->attachment)
                                <a href="{{ route('izin.lampiran', $l) }}" target="_blank" rel="noopener"
                                    class="self-start inline-flex items-center gap-1 font-label-md text-label-md text-primary hover:underline">
                                    <span class="material-symbols-outlined text-[16px]">attach_file</span> Lihat lampiran
                                </a>
                            @endif

                            @if ($l->admin_note)
                                <div class="flex items-start gap-space-xs px-space-sm py-2 rounded-lg {{ $l->status === 'rejected' ? 'bg-error-container/60' : 'bg-surface-container' }}">
                                    <span class="material-symbols-outlined text-[18px] mt-0.5 {{ $l->status === 'rejected' ? 'text-error' : 'text-outline' }}">comment</span>
                                    <div class="flex flex-col">
                                        <span class="font-label-md text-label-md text-on-surface">
                                            Catatan admin{{ $l->reviewer ? ' (' . $l->reviewer->name . ')' : '' }}
                                        </span>
                                        <span class="font-body-sm text-body-sm text-on-surface-variant">{{ $l->admin_note }}</span>
                                    </div>
                                </div>
                            @endif

                            <div class="flex items-center justify-between gap-space-sm pt-1">
                                <span class="font-body-sm text-body-sm text-on-surface-variant">
                                    Diajukan {{ $l->created_at->locale('id')->diffForHumans() }}
                                </span>
                                @if ($l->status === 'pending')
                                    <form method="POST" action="{{ route('izin.batal', $l) }}" class="form-batal">
                                        @csrf @method('PUT')
                                        <button type="submit"
                                            class="h-8 px-space-sm rounded-lg bg-surface-container-high text-error font-label-md text-label-md hover:bg-error-container transition-colors flex items-center gap-1">
                                            <span class="material-symbols-outlined text-[16px]">cancel</span> Batalkan
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center gap-2 text-center py-space-2xl">
                            <span class="material-symbols-outlined text-[48px] text-outline">event_available</span>
                            <p class="font-headline-sm text-headline-sm text-on-surface">Belum ada pengajuan</p>
                            <p class="font-body-md text-body-md text-on-surface-variant">Pengajuan izin atau sakit Anda akan tampil di sini.</p>
                        </div>
                    @endforelse
                </div>

                @if ($leaves->hasPages())
                    <div class="flex items-center justify-between gap-space-sm pt-space-sm border-t border-surface-variant/40">
                        <span class="font-body-sm text-body-sm text-on-surface-variant">Halaman {{ $leaves->currentPage() }} dari {{ $leaves->lastPage() }}</span>
                        <div class="flex items-center gap-space-xs">
                            @if ($leaves->onFirstPage())
                                <span class="h-9 px-space-md rounded-lg bg-surface-container-high text-outline font-label-md text-label-md flex items-center opacity-60">Sebelumnya</span>
                            @else
                                <a href="{{ $leaves->previousPageUrl() }}" class="h-9 px-space-md rounded-lg bg-surface-container-high text-on-surface font-label-md text-label-md hover:bg-surface-variant transition-colors flex items-center">Sebelumnya</a>
                            @endif
                            @if ($leaves->hasMorePages())
                                <a href="{{ $leaves->nextPageUrl() }}" class="h-9 px-space-md rounded-lg bg-surface-container-high text-on-surface font-label-md text-label-md hover:bg-surface-variant transition-colors flex items-center">Berikutnya</a>
                            @else
                                <span class="h-9 px-space-md rounded-lg bg-surface-container-high text-outline font-label-md text-label-md flex items-center opacity-60">Berikutnya</span>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ================= MODAL KONFIRMASI BATAL ================= --}}
    <div id="cancel-modal"
        style="display:none; position:fixed; inset:0; z-index:60; align-items:center; justify-content:center; padding:16px; background:rgba(0,0,0,.45); backdrop-filter:blur(4px);"
        role="alertdialog" aria-modal="true">
        <div class="modal-card w-full max-w-sm rounded-2xl bg-surface-container-lowest shadow-2xl p-space-xl text-center flex flex-col items-center gap-space-sm">
            <div class="w-14 h-14 rounded-full bg-error/10 text-error flex items-center justify-center">
                <span class="material-symbols-outlined text-[28px]">event_busy</span>
            </div>
            <p class="font-headline-md text-headline-md font-bold text-on-surface">Batalkan Pengajuan?</p>
            <p class="font-body-md text-body-md text-on-surface-variant">Pengajuan yang dibatalkan tidak dapat diaktifkan kembali. Anda perlu mengajukan ulang jika masih diperlukan.</p>
            <div class="flex gap-space-sm w-full mt-space-sm">
                <button type="button" id="cancel-no"
                    class="flex-1 h-11 rounded-lg bg-surface-container-high text-on-surface font-label-lg text-label-lg hover:bg-surface-variant transition-colors">Tidak</button>
                <button type="button" id="cancel-yes"
                    class="flex-1 h-11 rounded-lg bg-error text-on-error font-label-lg text-label-lg hover:opacity-90 transition-opacity">Ya, Batalkan</button>
            </div>
        </div>
    </div>
@endsection

@section('extra-scripts')
    <script>
        (function() {
            const RULES = @json($rules);
            const $ = (id) => document.getElementById(id);

            const form = $('leave-form');
            const start = $('start_date');
            const end = $('end_date');
            const urgent = $('is_urgent');
            const urgentWrap = $('urgent-wrap');
            const reason = $('reason');
            const attachment = $('attachment');
            const badge = $('attachment-badge');
            const attError = $('attachment-error');
            const hint = $('date-hint');
            const daysBox = $('days-summary');
            const daysText = $('days-text');
            const submitBtn = $('submit-btn');

            const currentType = () => form.querySelector('input[name="type"]:checked')?.value || 'sakit';
            const parse = (v) => (v ? new Date(v + 'T00:00:00') : null);
            const pad = (n) => String(n).padStart(2, '0');
            const iso = (d) => `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;
            const addDays = (d, n) => { const c = new Date(d); c.setDate(c.getDate() + n); return c; };

            function weekdays(a, b) {
                let n = 0;
                for (let d = new Date(a); d <= b; d = addDays(d, 1)) {
                    const w = d.getDay();
                    if (w !== 0 && w !== 6) n++;
                }
                return n;
            }

            function maxSizeBytes() { return RULES.max_attachment_kb * 1024; }

            function refresh() {
                const type = currentType();
                const isUrgent = type === 'izin' && urgent.checked;

                // Kotak "mendesak" hanya untuk izin
                urgentWrap.style.display = type === 'izin' ? 'flex' : 'none';
                if (type !== 'izin') urgent.checked = false;

                // Batas tanggal mulai
                if (type === 'sakit') {
                    start.min = RULES.sick_min;
                    start.max = RULES.today;
                    hint.textContent = `Sakit dapat diajukan mulai hari ini atau mundur maksimal ${RULES.sick_backdate} hari.`;
                } else {
                    start.min = isUrgent ? RULES.today : RULES.tomorrow;
                    start.removeAttribute('max');
                    hint.textContent = isUrgent
                        ? 'Izin mendesak dapat dimulai hari ini.'
                        : 'Izin keperluan lain diajukan minimal H-1 (mulai besok).';
                }

                // Batas tanggal selesai
                const s = parse(start.value);
                end.min = start.value || start.min;
                if (s) end.max = iso(addDays(s, RULES.max_days - 1));
                else end.removeAttribute('max');

                // Hari kerja
                const e = parse(end.value);
                let workDays = 0;
                if (s && e && e >= s) {
                    workDays = weekdays(s, e);
                    daysBox.style.display = 'flex';
                    daysText.textContent = workDays > 0
                        ? `${workDays} hari kerja akan tercatat (Senin–Jumat)`
                        : 'Rentang ini hanya berisi akhir pekan';
                } else {
                    daysBox.style.display = 'none';
                }

                // Lampiran wajib untuk sakit > 1 hari kerja
                const needAttachment = type === 'sakit' && workDays > 1;
                badge.textContent = needAttachment ? 'Wajib (surat dokter)' : 'Opsional';
                badge.className = 'px-2 py-0.5 rounded-full font-label-sm text-[10px] ' +
                    (needAttachment ? 'bg-error-container text-on-error-container' : 'bg-surface-container-high text-on-surface-variant');

                return { needAttachment, workDays };
            }

            // Hitung karakter alasan
            function countReason() { $('reason-count').textContent = `${reason.value.length}/1000`; }
            reason.addEventListener('input', countReason);
            countReason();

            // Validasi ukuran & tipe lampiran di sisi browser
            attachment.addEventListener('change', () => {
                attError.style.display = 'none';
                const f = attachment.files[0];
                if (!f) return;
                const okType = /\.(jpe?g|png|pdf)$/i.test(f.name);
                if (!okType) {
                    attError.textContent = 'Lampiran harus berupa JPG, PNG, atau PDF.';
                } else if (f.size > maxSizeBytes()) {
                    attError.textContent = 'Ukuran lampiran maksimal 2 MB.';
                } else {
                    return;
                }
                attError.style.display = 'block';
                attachment.value = '';
            });

            form.querySelectorAll('input[name="type"]').forEach((r) => r.addEventListener('change', refresh));
            urgent.addEventListener('change', refresh);
            start.addEventListener('change', () => {
                if (end.value && start.value && end.value < start.value) end.value = start.value;
                refresh();
            });
            end.addEventListener('change', refresh);

            form.addEventListener('submit', (e) => {
                const { needAttachment, workDays } = refresh();
                let message = null;

                if (!start.value || !end.value) message = 'Tanggal mulai dan selesai wajib diisi.';
                else if (end.value < start.value) message = 'Tanggal selesai tidak boleh sebelum tanggal mulai.';
                else if (workDays === 0) message = 'Rentang tanggal yang dipilih hanya berisi akhir pekan.';
                else if (reason.value.trim().length < 10) message = 'Alasan minimal 10 karakter.';
                else if (needAttachment && !attachment.files[0]) message = 'Sakit lebih dari 1 hari kerja wajib melampirkan surat dokter.';

                if (message) {
                    e.preventDefault();
                    hint.textContent = message;
                    hint.className = 'font-body-sm text-body-sm text-error -mt-2';
                    hint.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    return;
                }

                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="material-symbols-outlined animate-spin text-[20px]">progress_activity</span><span>Mengirim...</span>';
            });

            refresh();

            // ---------- Konfirmasi batal (modal, bukan dialog bawaan browser) ----------
            const modal = $('cancel-modal');
            let pendingForm = null;

            document.querySelectorAll('form.form-batal').forEach((f) => {
                f.addEventListener('submit', (e) => {
                    e.preventDefault();
                    pendingForm = f;
                    modal.style.display = 'flex';
                });
            });

            function closeModal() { modal.style.display = 'none'; pendingForm = null; }
            $('cancel-no').addEventListener('click', closeModal);
            $('cancel-yes').addEventListener('click', () => {
                if (!pendingForm) return;
                const f = pendingForm;
                modal.style.display = 'none';
                f.submit();
            });
            modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && modal.style.display === 'flex') closeModal();
            });
        })();
    </script>
@endsection