<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Shuf Picklers — Players</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,300;0,14..32,400;0,14..32,500;0,14..32,600;0,14..32,700;0,14..32,800;0,14..32,900&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        *, *::before, *::after { font-family: 'Inter', sans-serif; box-sizing: border-box; -webkit-tap-highlight-color: transparent; }

        :root {
            --bg:       #04090f;
            --surface:  #080f1a;
            --glass:    rgba(255,255,255,.035);
            --border:   rgba(255,255,255,.07);
            --sky:      #38bdf8;
            --sky-dim:  rgba(56,189,248,.55);
            --cyan:     #22d3ee;
            --muted:    rgba(148,163,184,.45);
            --muted-lo: rgba(148,163,184,.2);
        }

        html, body { height: 100%; }

        body {
            background: var(--bg);
            background-image:
                radial-gradient(ellipse 80% 40% at 50% 0%,   rgba(14,165,233,.22) 0%, transparent 60%),
                radial-gradient(ellipse 40% 30% at 85% 85%,  rgba(6,182,212,.09)  0%, transparent 55%),
                radial-gradient(ellipse 35% 25% at 10% 70%,  rgba(14,165,233,.06) 0%, transparent 55%);
            min-height: 100vh;
            color: #e2e8f0;
        }

        /* ── Scrollbar ── */
        ::-webkit-scrollbar { width: 3px; }
        ::-webkit-scrollbar-thumb { background: rgba(56,189,248,.2); border-radius: 4px; }

        /* ── Bottom nav ── */
        .tab-bar {
            position: fixed; bottom: 0; left: 0; right: 0; z-index: 50;
            background: rgba(4,9,15,.92);
            border-top: 1px solid rgba(56,189,248,.1);
            backdrop-filter: blur(20px);
            padding-bottom: env(safe-area-inset-bottom);
            display: flex;
        }
        .tab-item {
            flex: 1; display: flex; flex-direction: column; align-items: center;
            gap: 3px; padding: 10px 0 8px;
            color: var(--muted); font-size: .6rem; font-weight: 600;
            letter-spacing: .06em; text-transform: uppercase;
            text-decoration: none; transition: color .15s;
            position: relative;
        }
        .tab-item svg { transition: transform .2s cubic-bezier(.34,1.56,.64,1); }
        .tab-item:hover { color: var(--sky); }
        .tab-item:hover svg { transform: translateY(-2px); }
        .tab-item.active { color: var(--sky); }
        .tab-item.active::before {
            content: '';
            position: absolute; top: 0; left: 50%; transform: translateX(-50%);
            width: 32px; height: 2px;
            background: linear-gradient(90deg, #0ea5e9, #22d3ee);
            border-radius: 0 0 4px 4px;
        }

        /* ── Glass card ── */
        .card {
            background: var(--glass);
            border: 1px solid var(--border);
            border-radius: 20px;
            backdrop-filter: blur(20px);
            position: relative;
            overflow: hidden;
        }
        .card::before {
            content: '';
            position: absolute; inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,.04) 0%, transparent 60%);
            pointer-events: none;
        }

        /* ── Input ── */
        .field {
            background: rgba(255,255,255,.04);
            border: 1px solid rgba(255,255,255,.08);
            border-radius: 14px;
            color: #f1f5f9;
            padding: 13px 16px;
            font-size: .875rem;
            font-weight: 500;
            width: 100%;
            transition: border-color .2s, box-shadow .2s, background .2s;
            outline: none;
        }
        .field::placeholder { color: rgba(148,163,184,.3); }
        .field:focus {
            background: rgba(14,165,233,.06);
            border-color: rgba(56,189,248,.5);
            box-shadow: 0 0 0 3px rgba(14,165,233,.1), inset 0 1px 0 rgba(255,255,255,.05);
        }

        /* ── Buttons ── */
        .btn-sky {
            background: linear-gradient(135deg, #0ea5e9, #0284c7 60%, #06b6d4);
            border-radius: 14px;
            color: #fff;
            font-weight: 700;
            font-size: .875rem;
            padding: 13px 22px;
            white-space: nowrap;
            transition: all .2s;
            box-shadow: 0 4px 20px rgba(14,165,233,.3), inset 0 1px 0 rgba(255,255,255,.15);
            border: none; cursor: pointer;
        }
        .btn-sky:hover {
            background: linear-gradient(135deg, #38bdf8, #0ea5e9 60%, #22d3ee);
            box-shadow: 0 6px 28px rgba(14,165,233,.5), inset 0 1px 0 rgba(255,255,255,.2);
            transform: translateY(-1px);
        }
        .btn-sky:active { transform: translateY(0); }
        .btn-sky:disabled { opacity: .45; transform: none; cursor: not-allowed; }

        /* ── Section label ── */
        .eyebrow {
            font-size: .6rem; font-weight: 800;
            letter-spacing: .16em; text-transform: uppercase;
            color: rgba(56,189,248,.55);
        }

        /* ── Player row ── */
        .player-row {
            background: rgba(255,255,255,.025);
            border: 1px solid rgba(255,255,255,.055);
            border-radius: 14px;
            padding: 11px 14px;
            display: flex; align-items: center; gap: 12px;
            cursor: pointer;
            transition: background .15s, border-color .15s, transform .15s;
            user-select: none;
        }
        .player-row:hover {
            background: rgba(14,165,233,.06);
            border-color: rgba(56,189,248,.18);
        }
        .player-row:active { transform: scale(.99); }
        .player-row.checked {
            background: rgba(14,165,233,.09);
            border-color: rgba(56,189,248,.35);
            box-shadow: inset 0 0 0 1px rgba(56,189,248,.12);
        }

        /* ── Checkbox ── */
        .player-checkbox { display: none; }
        .check-ring {
            width: 20px; height: 20px; min-width: 20px;
            border-radius: 7px;
            border: 1.5px solid rgba(56,189,248,.25);
            background: rgba(255,255,255,.03);
            display: flex; align-items: center; justify-content: center;
            transition: all .15s cubic-bezier(.34,1.56,.64,1);
        }
        .check-ring svg { opacity: 0; transform: scale(.4); transition: all .15s cubic-bezier(.34,1.56,.64,1); }
        .player-row.checked .check-ring {
            background: linear-gradient(135deg, #0ea5e9, #06b6d4);
            border-color: #38bdf8;
            box-shadow: 0 0 14px rgba(14,165,233,.45);
        }
        .player-row.checked .check-ring svg { opacity: 1; transform: scale(1); }

        /* ── Avatar ── */
        .avatar {
            width: 34px; height: 34px; min-width: 34px;
            border-radius: 10px;
            background: linear-gradient(135deg, rgba(14,165,233,.2), rgba(6,182,212,.15));
            border: 1px solid rgba(56,189,248,.15);
            display: flex; align-items: center; justify-content: center;
            font-size: .75rem; font-weight: 800; color: #38bdf8;
            letter-spacing: -.01em;
        }

        /* ── Delete btn ── */
        .del-btn {
            width: 30px; height: 30px; min-width: 30px;
            border-radius: 9px;
            display: flex; align-items: center; justify-content: center;
            color: rgba(148,163,184,.25);
            background: transparent; border: none; cursor: pointer;
            transition: all .15s;
        }
        .del-btn:hover { color: #f87171; background: rgba(248,113,113,.1); }

        /* ── Glow divider ── */
        .glow-line {
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(14,165,233,.4), rgba(6,182,212,.4), transparent);
        }

        /* ── Animations ── */
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .slide-up { animation: slideUp .22s ease forwards; }

        @keyframes shake {
            0%,100% { transform: translateX(0); }
            25%      { transform: translateX(-4px); }
            75%      { transform: translateX(4px); }
        }
        .shake { animation: shake .3s ease; }

        /* ── Checked count pill ── */
        .count-pill {
            display: inline-flex; align-items: center; justify-content: center;
            min-width: 20px; height: 20px; padding: 0 6px;
            border-radius: 999px;
            background: rgba(14,165,233,.15);
            border: 1px solid rgba(56,189,248,.25);
            font-size: .65rem; font-weight: 800;
            color: #38bdf8;
        }

        /* ── Empty state ── */
        .empty-icon {
            width: 52px; height: 52px;
            border-radius: 16px;
            background: rgba(255,255,255,.04);
            border: 1px solid rgba(255,255,255,.07);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 12px;
        }
    </style>
</head>
<body>

<div class="max-w-lg mx-auto px-4 pt-8 pb-28">

    {{-- Header --}}
    <div class="text-center mb-8">
        <img src="/logo.png" alt="Shuf Picklers"
            style="width:200px;margin:0 auto;filter:drop-shadow(0 0 36px rgba(14,165,233,.5)) drop-shadow(0 0 12px rgba(6,182,212,.3))">
        <div class="glow-line mx-auto mt-5 mb-3" style="width:90px"></div>
        <p class="eyebrow" style="letter-spacing:.2em">Pickleball Team Shuffler</p>
        <p class="mt-2 text-xs" style="color:rgba(148,163,184,.3)">
            by <span style="color:rgba(148,163,184,.55);font-weight:600">Carl Mark Tan</span>
            <span style="color:#38bdf8;font-weight:700"> (CMTAN)</span>
        </p>
    </div>

    {{-- Add Player card --}}
    <div class="card p-5 mb-3">
        <p class="eyebrow mb-3">Add Player</p>
        <form id="add-form" class="flex gap-2.5">
            <input id="input-name" type="text" placeholder="Enter player name…"
                autocomplete="off" required class="field" style="border-radius:14px">
            <button type="submit" class="btn-sky">+ Add</button>
        </form>
    </div>

    {{-- Players card --}}
    <div class="card p-5">
        {{-- Card header --}}
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <p class="eyebrow">Roster</p>
                <span id="player-count" class="count-pill">{{ $players->count() }}</span>
            </div>
            <div class="flex items-center gap-1">
                <span id="checked-count" class="text-xs font-semibold" style="color:rgba(56,189,248,.5)"></span>
                <button id="select-all-btn" type="button"
                    class="text-xs font-semibold px-3 py-1.5 rounded-lg transition"
                    style="color:#38bdf8;background:rgba(14,165,233,.08);border:1px solid rgba(56,189,248,.15)">
                    Select All
                </button>
            </div>
        </div>

        {{-- List --}}
        <ul id="player-list" class="space-y-2 mb-4" style="min-height:48px">
            @forelse($players as $player)
                <li id="player-{{ $player->id }}"
                    class="player-row slide-up"
                    onclick="toggleCheck(this)">
                    <input type="checkbox" class="player-checkbox" value="{{ $player->id }}">
                    <div class="check-ring">
                        <svg width="10" height="8" viewBox="0 0 10 8" fill="none">
                            <path d="M1 4L3.5 6.5L9 1" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div class="avatar">{{ strtoupper(substr($player->name, 0, 2)) }}</div>
                    <span class="font-semibold text-sm text-slate-200 flex-1 truncate">{{ $player->name }}</span>
                    <button type="button" class="del-btn"
                        onclick="event.stopPropagation(); deletePlayer({{ $player->id }})" title="Remove">
                        <svg width="11" height="11" viewBox="0 0 12 12" fill="none">
                            <path d="M1 1L11 11M11 1L1 11" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                    </button>
                </li>
            @empty
                <li id="empty-state">
                    <div class="text-center py-10">
                        <div class="empty-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="rgba(148,163,184,.3)" stroke-width="1.5" stroke-linecap="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                        </div>
                        <p class="text-sm font-semibold" style="color:rgba(148,163,184,.4)">No players yet</p>
                        <p class="text-xs mt-1" style="color:rgba(148,163,184,.2)">Add someone above to get started</p>
                    </div>
                </li>
            @endforelse
        </ul>

        {{-- Hint --}}
        <p id="select-hint" class="text-xs text-center mb-3 transition-all" style="color:rgba(148,163,184,.3)">
            Select players, then head to <span style="color:#38bdf8">Matches</span> to generate pairings
        </p>

        {{-- Go to Matches CTA --}}
        <a href="/matches" id="go-matches-btn"
            class="btn-sky w-full flex items-center justify-center gap-2 text-sm"
            style="border-radius:14px;padding:13px;text-decoration:none;display:flex">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="16 3 21 3 21 8"/><polyline points="4 20 9 20 9 15"/>
                <path d="M21 3l-7 7-4-4-6 6"/><path d="M9 20l3-3 4 4 5-5"/>
            </svg>
            Go to Matches
        </a>
    </div>

</div>

{{-- Bottom Tab Bar --}}
<nav class="tab-bar">
    <a href="/" class="tab-item active">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
        </svg>
        Players
    </a>
    <a href="/matches" class="tab-item">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="3" width="18" height="18" rx="3"/>
            <path d="M3 9h18M9 21V9"/>
        </svg>
        Matches
    </a>
    <a href="/leaderboard" class="tab-item">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
        </svg>
        Leaderboard
    </a>
</nav>

<script>
const csrf = document.querySelector('meta[name="csrf-token"]').content;

function getInitials(name) {
    return name.trim().slice(0, 2).toUpperCase();
}

function toggleCheck(li) {
    const cb = li.querySelector('.player-checkbox');
    cb.checked = !cb.checked;
    li.classList.toggle('checked', cb.checked);
    syncUI();
}

function syncUI() {
    const boxes   = document.querySelectorAll('.player-checkbox');
    const checked = [...boxes].filter(b => b.checked);
    const all     = boxes.length > 0 && checked.length === boxes.length;

    document.getElementById('select-all-btn').textContent = all ? 'Deselect All' : 'Select All';

    const hint = document.getElementById('checked-count');
    hint.textContent = checked.length > 0 ? `${checked.length} selected` : '';
}

function updateCount() {
    const n = document.querySelectorAll('#player-list li:not(#empty-state)').length;
    document.getElementById('player-count').textContent = n;
}

function syncEmptyState() {
    const list       = document.getElementById('player-list');
    const hasPlayers = list.querySelectorAll('li:not(#empty-state)').length > 0;
    const existing   = document.getElementById('empty-state');
    if (!hasPlayers && !existing) {
        list.insertAdjacentHTML('beforeend', `<li id="empty-state">
            <div class="text-center py-10">
                <div class="empty-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="rgba(148,163,184,.3)" stroke-width="1.5" stroke-linecap="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
                <p class="text-sm font-semibold" style="color:rgba(148,163,184,.4)">No players yet</p>
                <p class="text-xs mt-1" style="color:rgba(148,163,184,.2)">Add someone above to get started</p>
            </div>
        </li>`);
    } else if (hasPlayers && existing) {
        existing.remove();
    }
    updateCount();
}

function playerHTML(p) {
    return `<li id="player-${p.id}" class="player-row slide-up" onclick="toggleCheck(this)">
        <input type="checkbox" class="player-checkbox" value="${p.id}">
        <div class="check-ring">
            <svg width="10" height="8" viewBox="0 0 10 8" fill="none">
                <path d="M1 4L3.5 6.5L9 1" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <div class="avatar">${getInitials(p.name)}</div>
        <span class="font-semibold text-sm text-slate-200 flex-1 truncate">${p.name}</span>
        <button type="button" class="del-btn" onclick="event.stopPropagation(); deletePlayer(${p.id})" title="Remove">
            <svg width="11" height="11" viewBox="0 0 12 12" fill="none">
                <path d="M1 1L11 11M11 1L1 11" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            </svg>
        </button>
    </li>`;
}

// Add player
document.getElementById('add-form').addEventListener('submit', async e => {
    e.preventDefault();
    const input = document.getElementById('input-name');
    const name  = input.value.trim();
    if (!name) return;

    const btn = e.target.querySelector('button[type=submit]');
    btn.disabled = true; btn.textContent = '…';

    const res    = await fetch('/players', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
        body: JSON.stringify({ name })
    });
    const player = await res.json();

    document.getElementById('player-list').insertAdjacentHTML('beforeend', playerHTML(player));
    syncEmptyState();
    e.target.reset();
    input.focus();
    btn.disabled = false; btn.textContent = '+ Add';
});

// Delete player
async function deletePlayer(id) {
    const li = document.getElementById(`player-${id}`);
    if (!li) return;
    li.style.opacity = '.35';
    li.style.pointerEvents = 'none';
    await fetch(`/players/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrf } });
    li.remove();
    syncEmptyState();
    syncUI();
}

// Select All
document.getElementById('select-all-btn').addEventListener('click', function () {
    const boxes     = document.querySelectorAll('.player-checkbox');
    const allChecked = [...boxes].every(b => b.checked);
    boxes.forEach(b => {
        b.checked = !allChecked;
        b.closest('li').classList.toggle('checked', !allChecked);
    });
    syncUI();
});
</script>
</body>
</html>
