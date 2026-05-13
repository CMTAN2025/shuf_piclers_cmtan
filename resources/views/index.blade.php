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

        body {
            background: #080c14;
            background-image:
                radial-gradient(ellipse 80% 50% at 50% -10%, rgba(99,102,241,.18) 0%, transparent 70%),
                radial-gradient(ellipse 60% 40% at 80% 80%, rgba(16,185,129,.08) 0%, transparent 60%);
            min-height: 100vh;
        }

        .glass {
            background: rgba(255,255,255,.04);
            border: 1px solid rgba(255,255,255,.08);
            backdrop-filter: blur(12px);
        }

        .glass-hover:hover {
            background: rgba(255,255,255,.07);
            border-color: rgba(255,255,255,.13);
        }

        .btn-primary {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            box-shadow: 0 4px 24px rgba(99,102,241,.35);
            transition: all .2s ease;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #818cf8 0%, #6366f1 100%);
            box-shadow: 0 6px 32px rgba(99,102,241,.5);
            transform: translateY(-1px);
        }
        .btn-primary:active { transform: translateY(0); }

        .btn-green {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            box-shadow: 0 4px 16px rgba(16,185,129,.3);
            transition: all .2s ease;
        }
        .btn-green:hover {
            background: linear-gradient(135deg, #34d399 0%, #10b981 100%);
            box-shadow: 0 6px 24px rgba(16,185,129,.45);
            transform: translateY(-1px);
        }
        .btn-green:active { transform: translateY(0); }

        .player-item {
            background: rgba(255,255,255,.03);
            border: 1px solid rgba(255,255,255,.07);
            transition: all .2s ease;
        }
        .player-item:hover {
            background: rgba(255,255,255,.06);
            border-color: rgba(255,255,255,.12);
        }
        .player-item.checked {
            background: rgba(99,102,241,.1);
            border-color: rgba(99,102,241,.35);
        }

        .dupr-badge {
            background: linear-gradient(135deg, rgba(16,185,129,.15), rgba(16,185,129,.08));
            border: 1px solid rgba(16,185,129,.25);
            color: #34d399;
        }

        .team-card {
            background: rgba(255,255,255,.03);
            border: 1px solid rgba(255,255,255,.08);
            transition: all .25s ease;
        }
        .team-card:hover { transform: translateY(-2px); }

        .input-field {
            background: rgba(255,255,255,.05);
            border: 1px solid rgba(255,255,255,.1);
            color: #f1f5f9;
            transition: all .2s ease;
        }
        .input-field::placeholder { color: rgba(148,163,184,.5); }
        .input-field:focus {
            outline: none;
            background: rgba(255,255,255,.08);
            border-color: rgba(99,102,241,.6);
            box-shadow: 0 0 0 3px rgba(99,102,241,.12);
        }

        .section-label {
            font-size: .65rem;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: rgba(148,163,184,.6);
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes pop {
            0%   { transform: scale(.95); opacity: 0; }
            60%  { transform: scale(1.02); }
            100% { transform: scale(1); opacity: 1; }
        }
        .fade-in  { animation: slideUp .25s ease forwards; }
        .pop-in   { animation: pop .3s ease forwards; }

        /* Custom checkbox */
        .player-checkbox { display: none; }
        .check-box {
            width: 18px; height: 18px; min-width: 18px;
            border-radius: 6px;
            border: 1.5px solid rgba(148,163,184,.3);
            background: rgba(255,255,255,.04);
            display: flex; align-items: center; justify-content: center;
            transition: all .15s ease;
            cursor: pointer;
        }
        .check-box svg { opacity: 0; transform: scale(.5); transition: all .15s ease; }
        .player-item.checked .check-box {
            background: #6366f1;
            border-color: #6366f1;
            box-shadow: 0 0 10px rgba(99,102,241,.4);
        }
        .player-item.checked .check-box svg { opacity: 1; transform: scale(1); }

        /* Delete btn */
        .delete-btn {
            width: 28px; height: 28px; min-width: 28px;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            color: rgba(148,163,184,.4);
            transition: all .15s ease;
            cursor: pointer;
            background: transparent;
            border: none;
        }
        .delete-btn:hover {
            color: #f87171;
            background: rgba(248,113,113,.1);
        }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,.1); border-radius: 4px; }

        /* Mobile tap highlight */
        * { -webkit-tap-highlight-color: transparent; }
    </style>
</head>
<body class="text-slate-100">

<div class="max-w-lg mx-auto px-4 py-10 pb-16">

    {{-- Header --}}
    <div class="text-center mb-10">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl mb-4"
             style="background: linear-gradient(135deg, rgba(99,102,241,.2), rgba(16,185,129,.15)); border: 1px solid rgba(99,102,241,.25);">
            <span class="text-3xl">🏓</span>
        </div>
        <h1 class="text-3xl font-bold tracking-tight text-white">Shuf Picklers</h1>
        <p class="text-slate-500 mt-1.5 text-sm font-medium">Pickleball team shuffler</p>
        <p class="mt-3 text-xs text-slate-600">Developed by <span class="text-slate-400 font-semibold">Carl Mark Tan</span> <span class="font-bold" style="color:#6366f1">(CMTAN)</span></p>
    </div>

    {{-- Add Player --}}
    <div class="glass rounded-2xl p-5 mb-4">
        <p class="section-label mb-3">Add Player</p>
        <form id="add-form" class="flex flex-col sm:flex-row gap-2.5">
            <input id="input-name" type="text" placeholder="Player name" required
                autocomplete="off"
                class="input-field flex-1 rounded-xl px-4 py-3 text-sm">
            <div class="flex gap-2.5">
                <input id="input-dupr" type="number" step="0.01" min="0" max="8" placeholder="DUPR"
                    class="input-field w-full sm:w-24 rounded-xl px-4 py-3 text-sm">
                <button type="submit"
                    class="btn-green text-white font-semibold px-5 py-3 rounded-xl text-sm whitespace-nowrap">
                    + Add
                </button>
            </div>
        </form>
    </div>

    {{-- Players --}}
    <div class="glass rounded-2xl p-5 mb-4">
        <div class="flex items-center justify-between mb-3">
            <p class="section-label">Players <span id="player-count" class="text-indigo-400 normal-case tracking-normal font-semibold text-xs ml-1">{{ $players->count() }}</span></p>
            <button id="select-all-btn" type="button"
                class="text-xs font-semibold text-indigo-400 hover:text-indigo-300 transition px-2 py-1 rounded-lg hover:bg-indigo-500/10">
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
                    @if($player->dupr)
                        <span class="dupr-badge text-xs font-mono font-semibold px-2.5 py-1 rounded-lg">
                            {{ number_format($player->dupr, 2) }}
                        </span>
                    @endif
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

    {{-- Teams --}}
    <div id="teams-section" class="hidden">
        <div class="flex items-center gap-3 mb-3 px-1">
            <div class="flex-1 h-px bg-white/5"></div>
            <p class="section-label">Result</p>
            <div class="flex-1 h-px bg-white/5"></div>
        </div>
        <div id="teams-grid" class="grid grid-cols-1 sm:grid-cols-2 gap-3"></div>
    </div>

</div>

<script>
const csrf = document.querySelector('meta[name="csrf-token"]').content;

const teamColors = [
    { accent: '#818cf8', bg: 'rgba(99,102,241,.08)', border: 'rgba(99,102,241,.2)', dot: '#818cf8' },
    { accent: '#34d399', bg: 'rgba(16,185,129,.08)', border: 'rgba(16,185,129,.2)', dot: '#34d399' },
    { accent: '#f472b6', bg: 'rgba(244,114,182,.08)', border: 'rgba(244,114,182,.2)', dot: '#f472b6' },
    { accent: '#fb923c', bg: 'rgba(251,146,60,.08)',  border: 'rgba(251,146,60,.2)',  dot: '#fb923c' },
    { accent: '#38bdf8', bg: 'rgba(56,189,248,.08)',  border: 'rgba(56,189,248,.2)',  dot: '#38bdf8' },
    { accent: '#a78bfa', bg: 'rgba(167,139,250,.08)', border: 'rgba(167,139,250,.2)', dot: '#a78bfa' },
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
    const dupr = p.dupr
        ? `<span class="dupr-badge text-xs font-mono font-semibold px-2.5 py-1 rounded-lg">${parseFloat(p.dupr).toFixed(2)}</span>`
        : '';
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
            ${dupr}
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
    const duprInput = document.getElementById('input-dupr');
    const name = nameInput.value.trim();
    const dupr = duprInput.value;
    if (!name) return;

    const btn = e.target.querySelector('button[type=submit]');
    btn.disabled = true;
    btn.textContent = '...';

    const res = await fetch('/players', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
        body: JSON.stringify({ name, dupr: dupr || null })
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
            <div class="flex items-center gap-2.5 py-1.5">
                <span class="w-1.5 h-1.5 rounded-full flex-shrink-0" style="background:${c.dot}"></span>
                <span class="text-sm font-medium text-slate-200 flex-1 truncate">${p.name}</span>
                ${p.dupr ? `<span class="text-xs font-mono font-semibold" style="color:${c.dot}">${parseFloat(p.dupr).toFixed(2)}</span>` : ''}
            </div>`).join('');
        return `
            <div class="team-card pop-in rounded-2xl p-4" style="background:${c.bg}; border:1px solid ${c.border}; animation-delay:${i * 60}ms">
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-2 h-2 rounded-full" style="background:${c.accent}; box-shadow:0 0 8px ${c.accent}"></span>
                    <p class="text-xs font-bold uppercase tracking-widest" style="color:${c.accent}">Team ${i + 1}</p>
                    <span class="ml-auto text-xs text-slate-600 font-medium">${team.length} player${team.length !== 1 ? 's' : ''}</span>
                </div>
                <div class="divide-y divide-white/5">${members}</div>
            </div>`;
    }).join('');

    document.getElementById('teams-section').classList.remove('hidden');
    document.getElementById('teams-section').scrollIntoView({ behavior: 'smooth', block: 'nearest' });

    btn.disabled = false;
    btn.innerHTML = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 3 21 3 21 8"/><polyline points="4 20 9 20 9 15"/><path d="M21 3l-7 7-4-4-6 6"/><path d="M9 20l3-3 4 4 5-5"/></svg> Shuffle Teams`;
});
</script>
</body>
</html>
