<div id="ticket-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4" role="dialog"
    aria-modal="true" aria-labelledby="tm-title">
    <div class="absolute inset-0 bg-slate-900/50" data-tm-close></div>

    <div class="relative max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-xl bg-white shadow-xl">
        <div class="flex items-start justify-between gap-4 border-b border-slate-100 px-5 py-4">
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <span id="tm-code" class="rounded bg-slate-100 px-1.5 py-0.5 text-xs font-semibold text-slate-800"></span>
                    <span id="tm-status" class="rounded-full px-2.5 py-0.5 text-xs font-medium"></span>
                    <span id="tm-category" class="text-xs text-slate-500"></span>
                </div>
                <h3 id="tm-title" class="mt-2 text-lg font-bold text-slate-900"></h3>
            </div>
            <button type="button" data-tm-close class="text-2xl leading-none text-slate-400 hover:text-slate-700"
                aria-label="Tutup">&times;</button>
        </div>

        <div class="space-y-5 px-5 py-4 text-sm">
            <div>
                <p class="text-xs font-semibold text-slate-500">Deskripsi</p>
                <p id="tm-description" class="mt-1 whitespace-pre-line text-slate-700"></p>
            </div>

            <dl class="grid grid-cols-2 gap-4 sm:grid-cols-3">
                <div>
                    <dt class="text-xs font-semibold text-slate-500">Mulai</dt>
                    <dd id="tm-started" class="mt-0.5 text-slate-800"></dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold text-slate-500">Selesai</dt>
                    <dd id="tm-completed" class="mt-0.5 text-slate-800"></dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold text-slate-500">Durasi</dt>
                    <dd id="tm-duration" class="mt-0.5 text-slate-800"></dd>
                </div>
            </dl>

            <div id="tm-revision-wrap" class="hidden rounded-lg bg-amber-50 px-4 py-3">
                <p class="text-xs font-semibold text-amber-800">Catatan revisi</p>
                <p id="tm-revision" class="mt-1 whitespace-pre-line text-amber-900"></p>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        const modal = document.getElementById('ticket-modal');
        if (!modal) return;

        const $ = (id) => document.getElementById(id);
        const statusStyle = {
            to_do: ['To do', 'bg-slate-100 text-slate-700'],
            on_progress: ['On progress', 'bg-blue-100 text-blue-800'],
            hold: ['Hold', 'bg-amber-100 text-amber-800'],
            revision: ['Revision', 'bg-orange-100 text-orange-800'],
            done: ['Done', 'bg-emerald-100 text-emerald-800'],
            verified: ['Verified', 'bg-emerald-200 text-emerald-900'],
            cancelled: ['Cancelled', 'bg-red-100 text-red-800'],
        };

        function open(t) {
            $('tm-code').textContent = t.code || '-';
            $('tm-title').textContent = t.title || '-';
            $('tm-category').textContent = t.category ? 'Kategori: ' + t.category : '';
            $('tm-description').textContent = t.description || '-';
            $('tm-started').textContent = t.started_at || '-';
            $('tm-completed').textContent = t.completed_at || '-';
            $('tm-duration').textContent = t.duration || '-';

            const st = statusStyle[t.status] || [t.status || '-', 'bg-slate-100 text-slate-700'];
            const badge = $('tm-status');
            badge.textContent = st[0];
            badge.className = 'rounded-full px-2.5 py-0.5 text-xs font-medium ' + st[1];

            $('tm-revision-wrap').classList.toggle('hidden', !t.revision_note);
            $('tm-revision').textContent = t.revision_note || '';

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }

        function close() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }

        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.js-ticket-btn');
            if (btn) {
                e.preventDefault();
                try { open(JSON.parse(btn.dataset.ticket)); } catch (err) { console.error(err); }
                return;
            }
            if (e.target.closest('[data-tm-close]')) close();
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') close();
        });
    })();
</script>