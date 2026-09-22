@extends('layouts.app')

@section('title', 'Buat Tiket Baru')
@section('breadcrumb', 'Tiket / Buat Tiket Baru')

@section('content')
    @php
        // $nextNumber dikirim dari TicketController@create (hanya perkiraan nomor tiket berikutnya)
        $sectionClass = 'bg-surface-container-lowest p-space-lg rounded-xl shadow-sm space-y-space-md';
        $inputClass = 'w-full h-11 px-space-sm rounded-lg bg-surface-container-low text-on-surface font-body-md text-body-md focus:bg-surface-container-lowest focus:ring-2 focus:ring-primary outline-none transition-all shadow-sm';
        $numberClass = 'w-7 h-7 rounded-lg bg-primary/10 text-primary flex items-center justify-center font-label-sm text-label-sm font-bold';
        $errorClass = 'font-body-sm text-body-sm text-error flex items-center gap-1';
    @endphp

    <div class="flex flex-col w-full gap-space-lg">

        {{-- Breadcrumb --}}
        <nav aria-label="Breadcrumb"
            class="flex items-center gap-space-xs font-label-md text-label-md text-on-surface-variant">
            <a class="hover:text-primary transition-colors" href="{{ route('dashboard') }}">Presensi &amp; Penugasan</a>
            <span class="material-symbols-outlined text-[16px] text-outline-variant">chevron_right</span>
            <a class="hover:text-primary transition-colors" href="{{ route('tickets') }}">Tiket</a>
            <span class="material-symbols-outlined text-[16px] text-outline-variant">chevron_right</span>
            <span class="font-semibold text-primary">Buat Tiket Baru</span>
        </nav>

        {{-- Header halaman --}}
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-space-md">
            <div class="space-y-1">
                <div class="flex items-center gap-space-xs flex-wrap">
                    <span
                        class="px-2 py-0.5 rounded-full bg-primary/10 text-primary font-label-sm text-label-sm uppercase tracking-wider">Tiket
                        Baru</span>
                    <span
                        class="px-2 py-0.5 rounded-full bg-surface-container-high text-on-surface-variant font-label-sm text-label-sm font-mono">#{{ $nextNumber }}</span>
                    <span class="text-outline-variant text-body-sm">•</span>
                    <span
                        class="flex items-center gap-1 font-label-sm text-label-sm text-on-surface-variant font-medium">
                        <span class="material-symbols-outlined text-[14px]">calendar_today</span>
                        {{ now()->translatedFormat('l, d F Y') }}
                    </span>
                </div>
                <h1 class="font-headline-lg text-headline-lg text-on-surface tracking-tight">
                    Formulir Pembuatan Tiket Penugasan Baru
                </h1>
                <p class="font-body-md text-body-md text-on-surface-variant max-w-3xl">
                    Isi formulir secara lengkap untuk mencatat instruksi kerja lapangan, aktivasi, atau pemeliharaan
                    jaringan fiber optic.
                </p>
                <div class="flex items-center gap-2 pt-1 font-body-sm text-body-sm text-on-surface-variant">
                    <span class="material-symbols-outlined text-[16px] text-primary">person</span>
                    <span>Pembuat: <strong class="text-on-surface font-medium">{{ auth()->user()->name }}</strong>
                        (Peserta PKL)</span>
                </div>
            </div>
            <div class="flex items-center gap-space-sm self-start lg:self-center">
                <a href="{{ route('tickets') }}"
                    class="inline-flex items-center gap-2 h-[42px] px-4 rounded-lg bg-surface-container-lowest text-on-surface font-label-lg text-label-lg shadow-sm hover:bg-surface-container-low transition-all active:scale-[0.98]">
                    <span class="material-symbols-outlined text-[18px] text-on-surface-variant">close</span>
                    <span>Batal</span>
                </a>
            </div>
        </div>

        <form id="ticket-form" method="POST" action="{{ route('ticket.store') }}" enctype="multipart/form-data"
            class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg items-start">
            @csrf

            {{-- ===================== KOLOM KIRI: FORM ===================== --}}
            <div class="lg:col-span-8 space-y-space-lg">

                {{-- 1. Kategori & Informasi Pekerjaan --}}
                <section class="{{ $sectionClass }}">
                    <div class="flex items-center justify-between gap-space-sm">
                        <div class="flex items-center gap-2.5">
                            <span class="{{ $numberClass }}">1</span>
                            <div>
                                <h2 class="font-headline-sm text-headline-sm text-on-surface font-semibold">Kategori &amp;
                                    Informasi Pekerjaan</h2>
                                <p class="font-body-sm text-body-sm text-on-surface-variant">Pilih lingkup aktivitas
                                    penugasan teknis dan tetapkan prioritas operasional.</p>
                            </div>
                        </div>
                        <span
                            class="px-2 py-0.5 rounded-full font-label-sm text-label-sm bg-primary/10 text-primary shrink-0">Wajib
                            Diisi</span>
                    </div>

                    {{-- Judul --}}
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label class="font-label-md text-label-md text-on-surface font-medium"
                                for="ticket-title">Judul Pekerjaan / Tiket Penugasan</label>
                            <span class="font-label-sm text-label-sm text-on-surface-variant"><span
                                    id="title-char-count">0</span>/100 karakter</span>
                        </div>
                        <input id="ticket-title" name="title" type="text" maxlength="100" required
                            class="{{ $inputClass }} @error('title') ring-2 ring-error @enderror"
                            placeholder="Contoh: Jumpering ODF &amp; Patchcord Splicing Ruang Server Lt. 2"
                            value="{{ old('title') }}" />
                        @error('title')
                            <p class="{{ $errorClass }}">
                                <span class="material-symbols-outlined text-[16px]">error</span>{{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Deskripsi --}}
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label class="font-label-md text-label-md text-on-surface font-medium"
                                for="ticket-desc">Deskripsi Rincian Pekerjaan &amp; SOP</label>
                            <span class="font-label-sm text-label-sm text-primary font-semibold">Min. 30 karakter</span>
                        </div>
                        <textarea id="ticket-desc" name="description" rows="4" required
                            class="w-full p-space-sm rounded-lg bg-surface-container-low text-on-surface font-body-md text-body-md focus:bg-surface-container-lowest focus:ring-2 focus:ring-primary outline-none transition-all shadow-sm resize-none @error('description') ring-2 ring-error @enderror"
                            placeholder="Tuliskan spesifikasi teknis, nomor core, kendala lapangan, atau catatan khusus untuk pengawas...">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="{{ $errorClass }}">
                                <span class="material-symbols-outlined text-[16px]">error</span>{{ $message }}
                            </p>
                        @enderror
                        <div class="flex items-center gap-1.5 font-body-sm text-body-sm text-on-surface-variant">
                            <span class="material-symbols-outlined text-[16px] text-primary">info</span>
                            <span>Gunakan terminologi standar (ODF, ODP, dBm, Core Color Code) guna mempermudah audit
                                QA.</span>
                        </div>
                    </div>
                </section>

                {{-- 2. Lampiran SOP / SPK --}}
                <section class="{{ $sectionClass }}">
                    <div class="flex items-center justify-between gap-space-sm">
                        <div class="flex items-center gap-2.5">
                            <span class="{{ $numberClass }}">2</span>
                            <div>
                                <h2 class="font-headline-sm text-headline-sm text-on-surface font-semibold">Lampiran
                                    Dokumen SOP / SPK</h2>
                                <p class="font-body-sm text-body-sm text-on-surface-variant">Unggah dokumen Surat
                                    Perintah Kerja (SPK), single-line diagram, atau surat izin lokasi.</p>
                            </div>
                        </div>
                        <span
                            class="px-2 py-0.5 rounded-full font-label-sm text-label-sm bg-surface-container-high text-on-surface-variant shrink-0">PDF,
                            PNG, JPG (Maks 10MB)</span>
                    </div>

                    <label id="drop-zone" for="attachments"
                        class="p-space-lg rounded-xl bg-surface-container-low flex flex-col items-center justify-center text-center cursor-pointer hover:bg-surface-container transition-all focus-within:ring-2 focus-within:ring-primary">
                        <input id="attachments" type="file" name="attachments[]" accept=".pdf,.png,.jpg,.jpeg"
                            multiple class="sr-only" />
                        <span
                            class="w-12 h-12 rounded-full bg-primary/10 text-primary flex items-center justify-center mb-2">
                            <span class="material-symbols-outlined text-[28px]">cloud_upload</span>
                        </span>
                        <span class="font-label-md text-label-md text-on-surface font-semibold">Tarik &amp; lepas file
                            dokumen pendukung di sini</span>
                        <span class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">atau <strong
                                class="text-primary">pilih file dari perangkat</strong></span>
                    </label>

                    {{-- Error lampiran --}}
                    @error('attachments')
                        <p class="{{ $errorClass }}">
                            <span class="material-symbols-outlined text-[16px]">error</span>{{ $message }}
                        </p>
                    @enderror
                    @foreach ($errors->get('attachments.*') as $messages)
                        @foreach ($messages as $message)
                            <p class="{{ $errorClass }}">
                                <span class="material-symbols-outlined text-[16px]">error</span>{{ $message }}
                            </p>
                        @endforeach
                    @endforeach

                    {{-- Daftar file yang dipilih (diisi lewat JS) --}}
                    <div id="attachment-list" class="space-y-2"></div>

                    <template id="attachment-template">
                        <div
                            class="p-space-sm rounded-lg bg-surface-container-low flex items-center justify-between gap-space-sm">
                            <div class="flex items-center gap-3 min-w-0">
                                <div
                                    class="w-10 h-10 rounded-lg bg-primary/10 text-primary flex items-center justify-center shrink-0">
                                    <span class="js-icon material-symbols-outlined text-[22px]">picture_as_pdf</span>
                                </div>
                                <div class="min-w-0">
                                    <span
                                        class="js-name font-label-md text-label-md text-on-surface font-semibold block truncate"></span>
                                    <span class="js-size font-body-sm text-body-sm text-on-surface-variant"></span>
                                </div>
                            </div>
                            <button type="button" title="Hapus file"
                                class="js-remove w-8 h-8 rounded-lg bg-surface-container-high text-on-surface-variant hover:text-error flex items-center justify-center transition-all shrink-0">
                                <span class="material-symbols-outlined text-[18px]">delete</span>
                            </button>
                        </div>
                    </template>
                </section>
            </div>

            {{-- ===================== KOLOM KANAN: AKSI ===================== --}}
            <div class="lg:col-span-4 space-y-space-lg">
                <div class="{{ $sectionClass }}">
                    <button id="btn-submit-ticket" type="submit"
                        class="w-full h-12 rounded-lg bg-primary-container hover:bg-primary disabled:opacity-60 disabled:pointer-events-none text-on-primary font-headline-sm text-headline-sm font-bold transition-all shadow-sm hover:shadow-md active:scale-[0.98] flex items-center justify-center gap-2">
                        <span class="js-label">Terbitkan &amp; Tugaskan Tiket</span>
                        <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                    </button>
                    <p class="font-body-sm text-body-sm text-on-surface-variant text-center leading-relaxed">
                        Tiket akan tersimpan dengan status To Do dan langsung tampil di daftar tiket penugasan.
                    </p>
                    <div class="pt-space-xs flex items-center justify-center gap-4 text-on-surface-variant flex-wrap">
                        <span class="flex items-center gap-1 font-label-sm text-label-sm">
                            <span class="material-symbols-outlined text-[16px] text-primary">lock</span>
                            Enkripsi ISO 27001
                        </span>
                        <span class="flex items-center gap-1 font-label-sm text-label-sm">
                            <span class="material-symbols-outlined text-[16px] text-primary">cloud_done</span>
                            Auto-Sync SIM-PKL
                        </span>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('extra-scripts')
    <script>
        (function() {
            // ---- Counter karakter judul ----
            const titleInput = document.getElementById('ticket-title');
            const charCount = document.getElementById('title-char-count');
            if (titleInput && charCount) {
                const updateCount = function() {
                    charCount.textContent = titleInput.value.length;
                };
                titleInput.addEventListener('input', updateCount);
                updateCount();
            }

            // ---- Lampiran: pilih / drag & drop / hapus file sebelum dikirim ----
            const form = document.getElementById('ticket-form');
            const fileInput = document.getElementById('attachments');
            const dropZone = document.getElementById('drop-zone');
            const fileList = document.getElementById('attachment-list');
            const template = document.getElementById('attachment-template');
            let selectedFiles = [];

            function formatSize(bytes) {
                if (bytes >= 1048576) return (bytes / 1048576).toFixed(1) + ' MB';
                return Math.max(1, Math.round(bytes / 1024)) + ' KB';
            }

            // Samakan isi <input type="file"> dengan daftar selectedFiles
            function syncInput() {
                const transfer = new DataTransfer();
                selectedFiles.forEach(function(file) {
                    transfer.items.add(file);
                });
                fileInput.files = transfer.files;
            }

            function render() {
                fileList.innerHTML = '';
                selectedFiles.forEach(function(file, index) {
                    const item = template.content.firstElementChild.cloneNode(true);
                    item.querySelector('.js-name').textContent = file.name;
                    item.querySelector('.js-size').textContent = formatSize(file.size);
                    item.querySelector('.js-icon').textContent =
                        file.type.startsWith('image/') ? 'image' : 'picture_as_pdf';
                    item.querySelector('.js-remove').addEventListener('click', function() {
                        selectedFiles.splice(index, 1);
                        syncInput();
                        render();
                    });
                    fileList.appendChild(item);
                });
            }

            function addFiles(files) {
                Array.from(files).forEach(function(file) {
                    const duplicate = selectedFiles.some(function(existing) {
                        return existing.name === file.name && existing.size === file.size;
                    });
                    if (!duplicate) selectedFiles.push(file);
                });
                syncInput();
                render();
            }

            fileInput.addEventListener('change', function() {
                addFiles(fileInput.files);
            });

            ['dragenter', 'dragover'].forEach(function(eventName) {
                dropZone.addEventListener(eventName, function(event) {
                    event.preventDefault();
                    dropZone.classList.add('ring-2', 'ring-primary');
                });
            });

            ['dragleave', 'drop'].forEach(function(eventName) {
                dropZone.addEventListener(eventName, function(event) {
                    event.preventDefault();
                    dropZone.classList.remove('ring-2', 'ring-primary');
                });
            });

            dropZone.addEventListener('drop', function(event) {
                addFiles(event.dataTransfer.files);
            });
            
            form.addEventListener('submit', function() {
                const button = document.getElementById('btn-submit-ticket');
                button.disabled = true;
                button.querySelector('.js-label').textContent = 'Menyimpan...';
            });
        })();
    </script>
@endsection