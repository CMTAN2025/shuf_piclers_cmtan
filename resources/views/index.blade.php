<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shuf Picklers — by CMTAN</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        * { font-family: 'Inter', sans-serif; }

        :root {
            --primary: #0ea5e9;
            --primary-dark: #0284c7;
            --accent: #06b6d4;
            --bg: #060d18;
        }

        body {
            background: var(--bg);
            background-image:
                radial-gradient(ellipse 90% 55% at 50% -5%, rgba(14,165,233,.18) 0%, transparent 65%),
                radial-gradient(ellipse 55% 40% at 90% 90%, rgba(6,182,212,.08) 0%, transparent 60%),
                radial-gradient(ellipse 40% 30% at 10% 80%, rgba(14,165,233,.06) 0%, transparent 60%);
            min-height: 100vh;
        }

        .glass {
            background: rgba(255,255,255,.04);
            border: 1px solid rgba(255,255,255,.07);
            backdrop-filter: blur(16px);
        }

        .btn-primary {
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 50%, #06b6d4 100%);
            box-shadow: 0 4px 24px rgba(14,165,233,.35);
            transition: all .2s ease;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #38bdf8 0%, #0ea5e9 50%, #22d3ee 100%);
            box-shadow: 0 6px 36px rgba(14,165,233,.55);
            transform: translateY(-1px);
        }
        .btn-primary:active { transform: translateY(0); }

        .btn-add {
            background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 100%);
            box-shadow: 0 4px 16px rgba(14,165,233,.3);
            transition: all .2s ease;
        }
        .btn-add:hover {
            background: linear-gradient(135deg, #38bdf8 0%, #0ea5e9 100%);
            box-shadow: 0 6px 24px rgba(14,165,233,.5);
            transform: translateY(-1px);
        }
        .btn-add:active { transform: translateY(0); }

        .player-item {
            background: rgba(255,255,255,.03);
            border: 1px solid rgba(255,255,255,.06);
            transition: all .2s ease;
        }
        .player-item:hover {
            background: rgba(14,165,233,.07);
            border-color: rgba(14,165,233,.2);
        }
        .player-item.checked {
            background: rgba(14,165,233,.1);
            border-color: rgba(56,189,248,.4);
        }

        .team-card {
            transition: all .25s ease;
        }
        .team-card:hover { transform: translateY(-2px); }

        .input-field {
            background: rgba(255,255,255,.05);
            border: 1px solid rgba(255,255,255,.09);
            color: #f1f5f9;
            transition: all .2s ease;
        }
        .input-field::placeholder { color: rgba(148,163,184,.35); }
        .input-field:focus {
            outline: none;
            background: rgba(14,165,233,.07);
            border-color: rgba(56,189,248,.55);
            box-shadow: 0 0 0 3px rgba(14,165,233,.12);
        }

        .section-label {
            font-size: .62rem;
            font-weight: 700;
            letter-spacing: .14em;
            text-transform: uppercase;
            color: rgba(56,189,248,.6);
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes pop {
            0%   { transform: scale(.93); opacity: 0; }
            60%  { transform: scale(1.03); }
            100% { transform: scale(1); opacity: 1; }
        }
        .fade-in { animation: slideUp .25s ease forwards; }
        .pop-in  { animation: pop .3s ease forwards; }

        .player-checkbox { display: none; }
        .check-box {
            width: 18px; height: 18px; min-width: 18px;
            border-radius: 6px;
            border: 1.5px solid rgba(56,189,248,.3);
            background: rgba(255,255,255,.04);
            display: flex; align-items: center; justify-content: center;
            transition: all .15s ease;
            cursor: pointer;
        }
        .check-box svg { opacity: 0; transform: scale(.5); transition: all .15s ease; }
        .player-item.checked .check-box {
            background: linear-gradient(135deg, #0ea5e9, #06b6d4);
            border-color: #38bdf8;
            box-shadow: 0 0 12px rgba(14,165,233,.5);
        }
        .player-item.checked .check-box svg { opacity: 1; transform: scale(1); }

        .delete-btn {
            width: 28px; height: 28px; min-width: 28px;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            color: rgba(148,163,184,.35);
            transition: all .15s ease;
            cursor: pointer;
            background: transparent;
            border: none;
        }
        .delete-btn:hover {
            color: #f87171;
            background: rgba(248,113,113,.1);
        }

        .glow-line {
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(14,165,233,.5), rgba(6,182,212,.5), transparent);
        }

        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(14,165,233,.25); border-radius: 4px; }

        @keyframes modalIn {
            from { opacity: 0; transform: translateY(24px) scale(.96); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }
        @keyframes overlayIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }
        #modal-overlay.show { animation: overlayIn .2s ease forwards; }
        #modal-box.show     { animation: modalIn .28s cubic-bezier(.22,1,.36,1) forwards; }

        #fab-btn { transition: all .2s ease; }
        #fab-btn:hover { transform: translateY(-2px); box-shadow: 0 14px 44px rgba(14,165,233,.6) !important; }

        * { -webkit-tap-highlight-color: transparent; }
    </style>
</head>
<body class="text-slate-100">

<div class="max-w-lg mx-auto px-4 py-10 pb-16">

    {{-- Header --}}
    <div class="text-center mb-10">
        <img src="/logo.png" alt="Shuffler Picklers" class="mx-auto" style="width:220px;filter:drop-shadow(0 0 40px rgba(14,165,233,.55)) drop-shadow(0 0 16px rgba(6,182,212,.35))">
        <div class="glow-line mx-auto mt-5 mb-2" style="width:100px"></div>
        <p class="text-xs tracking-widest font-semibold" style="color:rgba(56,189,248,.5);letter-spacing:.18em">TEAM SHUFFLER</p>
        <p class="mt-2 text-xs" style="color:rgba(148,163,184,.35)">Developed by <span style="color:rgba(148,163,184,.6);font-weight:600">Carl Mark Tan</span> <span style="color:#38bdf8;font-weight:700">(CMTAN)</span></p>
    </div>

    {{-- Add Player --}}
    <div class="glass rounded-2xl p-5 mb-4">
        <p class="section-label mb-3">Add Player</p>
        <form id="add-form" class="flex gap-2.5">
            <input id="input-name" type="text" placeholder="Player name" required
                autocomplete="off"
                class="input-field flex-1 rounded-xl px-4 py-3 text-sm">
            <button type="submit"
                class="btn-add text-white font-semibold px-5 py-3 rounded-xl text-sm whitespace-nowrap">
                + Add
            </button>
        </form>
    </div>

    {{-- Players --}}
    <div class="glass rounded-2xl p-5 mb-4">
        <div class="flex items-center justify-between mb-3">
            <p class="section-label">Players <span id="player-count" class="normal-case tracking-normal font-semibold text-xs ml-1" style="color:#38bdf8">{{ $players->count() }}</span></p>
            <button id="select-all-btn" type="button"
                class="text-xs font-semibold transition px-2 py-1 rounded-lg" style="color:#38bdf8">
                Select All
            </button>
        </div>

        <ul id="player-list" class="space-y-2 mb-4">
            @forelse($players as $player)
                <li id="player-{{ $player->id }}" class="player-item fade-in rounded-xl px-3.5 py-3 flex items-center gap-3 cursor-pointer"
                    onclick="toggleCheck(this)">
                    <input type="checkbox" class="player-checkbox" value="{{ $player->id }}">
                    <div class="check-box">
                        <svg width="10" height="8" viewBox="0 0 10 8" fill="none">
                            <path d="M1 4L3.5 6.5L9 1" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <span class="font-medium text-sm text-slate-200 flex-1 truncate">{{ $player->name }}</span>
                    <button type="button" class="delete-btn" onclick="event.stopPropagation(); deletePlayer({{ $player->id }})" title="Remove">
                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                            <path d="M1 1L11 11M11 1L1 11" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                    </button>
                </li>
            @empty
                <li id="empty-state" class="text-center py-8">
                    <p class="text-slate-600 text-sm">No players yet.</p>
                    <p class="text-slate-700 text-xs mt-1">Add someone above to get started.</p>
                </li>
            @endforelse
        </ul>

        <button id="shuffle-btn" type="button"
            class="btn-primary w-full text-white font-semibold py-3.5 rounded-xl text-sm flex items-center justify-center gap-2">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="16 3 21 3 21 8"/><polyline points="4 20 9 20 9 15"/>
                <path d="M21 3l-7 7-4-4-6 6"/><path d="M9 20l3-3 4 4 5-5"/>
            </svg>
            Shuffle Teams
        </button>
    </div>

</div>

{{-- FAB --}}
<button id="fab-btn" onclick="openModal()"
    class="hidden fixed bottom-6 right-6 z-40 flex items-center gap-2.5 text-white text-sm font-semibold px-5 py-3.5 rounded-2xl shadow-2xl transition-all"
    style="background:linear-gradient(135deg,#0ea5e9,#06b6d4);box-shadow:0 8px 32px rgba(14,165,233,.5)">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
    </svg>
    View Teams
</button>

{{-- Modal --}}
<div id="modal-overlay" class="hidden fixed inset-0 z-50 flex items-end sm:items-center justify-center p-4"
    style="background:rgba(0,0,0,.7);backdrop-filter:blur(6px)">
    <div id="modal-box" class="w-full max-w-lg rounded-3xl overflow-hidden"
        style="background:#060d18;border:1px solid rgba(14,165,233,.15);box-shadow:0 32px 80px rgba(0,0,0,.7),0 0 60px rgba(14,165,233,.1);max-height:85vh;display:flex;flex-direction:column">
        {{-- Modal header --}}
        <div class="flex items-center justify-between px-6 py-5" style="border-bottom:1px solid rgba(14,165,233,.1)">
            <div class="flex items-center gap-3">
                <img src="/logo.png" alt="logo" style="width:42px;filter:drop-shadow(0 0 8px rgba(168,85,247,.5))">
                <div>
                    <p class="text-white font-bold text-sm">Shuffle Result</p>
                    <p id="modal-meta" class="text-xs" style="color:rgba(56,189,248,.5)"></p>
                </div>
            </div>
            <button onclick="closeModal()" class="delete-btn" style="width:32px;height:32px;border-radius:10px">
                <svg width="13" height="13" viewBox="0 0 12 12" fill="none">
                    <path d="M1 1L11 11M11 1L1 11" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
            </button>
        </div>
        {{-- Modal body --}}
        <div class="overflow-y-auto px-6 py-5">
            <div id="teams-grid" class="grid grid-cols-1 sm:grid-cols-2 gap-3"></div>
        </div>
        {{-- Modal footer --}}
        <div class="px-6 py-4" style="border-top:1px solid rgba(14,165,233,.1)">
            <button onclick="closeModal()" class="btn-primary w-full text-white font-semibold py-3 rounded-xl text-sm">Done</button>
        </div>
    </div>
</div>

<script>
const csrf = document.querySelector('meta[name="csrf-token"]').content;

const teamColors = [
    { accent: '#38bdf8', bg: 'rgba(14,165,233,.09)',  border: 'rgba(14,165,233,.22)',  dot: '#38bdf8' },
    { accent: '#22d3ee', bg: 'rgba(6,182,212,.09)',   border: 'rgba(6,182,212,.22)',   dot: '#22d3ee' },
    { accent: '#f59e0b', bg: 'rgba(245,158,11,.08)',  border: 'rgba(245,158,11,.2)',   dot: '#f59e0b' },
    { accent: '#10b981', bg: 'rgba(16,185,129,.08)',  border: 'rgba(16,185,129,.2)',   dot: '#10b981' },
    { accent: '#f87171', bg: 'rgba(248,113,113,.08)', border: 'rgba(248,113,113,.2)',  dot: '#f87171' },
    { accent: '#818cf8', bg: 'rgba(99,102,241,.09)',  border: 'rgba(99,102,241,.22)',  dot: '#818cf8' },
];

function toggleCheck(li) {
    const cb = li.querySelector('.player-checkbox');
    cb.checked = !cb.checked;
    li.classList.toggle('checked', cb.checked);
    syncSelectAllBtn();
}

function syncSelectAllBtn() {
    const boxes = document.querySelectorAll('.player-checkbox');
    const allChecked = boxes.length > 0 && [...boxes].every(b => b.checked);
    document.getElementById('select-all-btn').textContent = allChecked ? 'Deselect All' : 'Select All';
}

function updateCount() {
    const count = document.querySelectorAll('#player-list li:not(#empty-state)').length;
    document.getElementById('player-count').textContent = count;
}

function syncEmptyState() {
    const list = document.getElementById('player-list');
    const hasPlayers = list.querySelectorAll('li:not(#empty-state)').length > 0;
    const existing = document.getElementById('empty-state');
    if (!hasPlayers && !existing) {
        list.innerHTML = `<li id="empty-state" class="text-center py-8">
            <p class="text-slate-600 text-sm">No players yet.</p>
            <p class="text-slate-700 text-xs mt-1">Add someone above to get started.</p>
        </li>`;
    } else if (hasPlayers && existing) {
        existing.remove();
    }
    updateCount();
}

function playerHTML(p) {
    return `
        <li id="player-${p.id}" class="player-item fade-in rounded-xl px-3.5 py-3 flex items-center gap-3 cursor-pointer"
            onclick="toggleCheck(this)">
            <input type="checkbox" class="player-checkbox" value="${p.id}">
            <div class="check-box">
                <svg width="10" height="8" viewBox="0 0 10 8" fill="none">
                    <path d="M1 4L3.5 6.5L9 1" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <span class="font-medium text-sm text-slate-200 flex-1 truncate">${p.name}</span>
            <button type="button" class="delete-btn" onclick="event.stopPropagation(); deletePlayer(${p.id})" title="Remove">
                <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                    <path d="M1 1L11 11M11 1L1 11" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
            </button>
        </li>`;
}

// Add player
document.getElementById('add-form').addEventListener('submit', async e => {
    e.preventDefault();
    const nameInput = document.getElementById('input-name');
    const name = nameInput.value.trim();
    if (!name) return;

    const btn = e.target.querySelector('button[type=submit]');
    btn.disabled = true;
    btn.textContent = '...';

    const res = await fetch('/players', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
        body: JSON.stringify({ name })
    });
    const player = await res.json();
    document.getElementById('player-list').insertAdjacentHTML('beforeend', playerHTML(player));
    syncEmptyState();
    e.target.reset();
    nameInput.focus();
    btn.disabled = false;
    btn.textContent = '+ Add';
});

// Delete player
async function deletePlayer(id) {
    const li = document.getElementById(`player-${id}`);
    if (!li) return;
    li.style.opacity = '.4';
    li.style.pointerEvents = 'none';
    await fetch(`/players/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrf } });
    li.remove();
    syncEmptyState();
    syncSelectAllBtn();
}

// Select All
document.getElementById('select-all-btn').addEventListener('click', function () {
    const boxes = document.querySelectorAll('.player-checkbox');
    const allChecked = [...boxes].every(b => b.checked);
    boxes.forEach(b => {
        b.checked = !allChecked;
        b.closest('li').classList.toggle('checked', !allChecked);
    });
    this.textContent = allChecked ? 'Select All' : 'Deselect All';
});

function openModal() {
    const overlay = document.getElementById('modal-overlay');
    overlay.classList.remove('hidden');
    requestAnimationFrame(() => {
        overlay.classList.add('show');
        document.getElementById('modal-box').classList.add('show');
    });
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    document.getElementById('modal-overlay').classList.add('hidden');
    document.getElementById('modal-overlay').classList.remove('show');
    document.getElementById('modal-box').classList.remove('show');
    document.body.style.overflow = '';
}

document.getElementById('modal-overlay').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});

// Shuffle
document.getElementById('shuffle-btn').addEventListener('click', async () => {
    const checked = [...document.querySelectorAll('.player-checkbox:checked')].map(b => b.value);
    if (checked.length < 2) {
        const btn = document.getElementById('shuffle-btn');
        btn.style.boxShadow = '0 0 0 3px rgba(248,113,113,.4)';
        setTimeout(() => btn.style.boxShadow = '', 600);
        return;
    }

    const btn = document.getElementById('shuffle-btn');
    btn.disabled = true;
    btn.innerHTML = `<svg class="animate-spin" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg> Shuffling...`;

    const res = await fetch('/shuffle', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
        body: JSON.stringify({ players: checked })
    });
    const teams = await res.json();

    const grid = document.getElementById('teams-grid');
    grid.innerHTML = teams.map((team, i) => {
        const c = teamColors[i % teamColors.length];
        const members = team.map(p => `
            <div class="flex items-center gap-2.5 py-2">
                <span class="w-1.5 h-1.5 rounded-full flex-shrink-0" style="background:${c.dot}"></span>
                <span class="text-sm font-medium text-slate-200 flex-1 truncate">${p.name}</span>
            </div>`).join('');
        return `
            <div class="team-card pop-in rounded-2xl p-4" style="background:${c.bg};border:1px solid ${c.border};animation-delay:${i*60}ms">
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-2 h-2 rounded-full" style="background:${c.accent};box-shadow:0 0 8px ${c.accent}"></span>
                    <p class="text-xs font-bold uppercase tracking-widest" style="color:${c.accent}">Team ${i+1}</p>
                    <span class="ml-auto text-xs font-medium" style="color:rgba(148,163,184,.45)">${team.length} player${team.length!==1?'s':''}</span>
                </div>
                <div style="border-top:1px solid rgba(255,255,255,.05)">${members}</div>
            </div>`;
    }).join('');

    document.getElementById('modal-meta').textContent = `${teams.length} teams · ${checked.length} players`;

    const fab = document.getElementById('fab-btn');
    fab.classList.remove('hidden');

    openModal();

    btn.disabled = false;
    btn.innerHTML = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 3 21 3 21 8"/><polyline points="4 20 9 20 9 15"/><path d="M21 3l-7 7-4-4-6 6"/><path d="M9 20l3-3 4 4 5-5"/></svg> Shuffle Teams`;
});
</script>
</body>
</html>
