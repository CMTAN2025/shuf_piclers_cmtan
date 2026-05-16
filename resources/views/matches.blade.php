<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Shuf Picklers — Matches</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,300;0,14..32,400;0,14..32,500;0,14..32,600;0,14..32,700;0,14..32,800;0,14..32,900&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        *, *::before, *::after { font-family: 'Inter', sans-serif; box-sizing: border-box; -webkit-tap-highlight-color: transparent; }

        :root {
            --bg:    #04090f;
            --glass: rgba(255,255,255,.035);
            --border:rgba(255,255,255,.07);
            --sky:   #38bdf8;
            --muted: rgba(148,163,184,.45);
        }

        body {
            background: var(--bg);
            background-image:
                radial-gradient(ellipse 80% 40% at 50% 0%,  rgba(14,165,233,.2)  0%, transparent 60%),
                radial-gradient(ellipse 40% 30% at 85% 85%, rgba(6,182,212,.08)  0%, transparent 55%);
            min-height: 100vh; color: #e2e8f0;
        }

        ::-webkit-scrollbar { width: 3px; }
        ::-webkit-scrollbar-thumb { background: rgba(56,189,248,.2); border-radius: 4px; }

        /* ── Tab bar ── */
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
            text-decoration: none; transition: color .15s; position: relative;
        }
        .tab-item svg { transition: transform .2s cubic-bezier(.34,1.56,.64,1); }
        .tab-item:hover { color: var(--sky); }
        .tab-item:hover svg { transform: translateY(-2px); }
        .tab-item.active { color: var(--sky); }
        .tab-item.active::before {
            content: ''; position: absolute; top: 0; left: 50%; transform: translateX(-50%);
            width: 32px; height: 2px;
            background: linear-gradient(90deg, #0ea5e9, #22d3ee);
            border-radius: 0 0 4px 4px;
        }

        /* ── Card ── */
        .card {
            background: var(--glass);
            border: 1px solid var(--border);
            border-radius: 20px;
            backdrop-filter: blur(20px);
            position: relative; overflow: hidden;
        }
        .card::before {
            content: ''; position: absolute; inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,.04) 0%, transparent 60%);
            pointer-events: none;
        }

        /* ── Eyebrow ── */
        .eyebrow {
            font-size: .6rem; font-weight: 800;
            letter-spacing: .16em; text-transform: uppercase;
            color: rgba(56,189,248,.55);
        }

        /* ── Glow line ── */
        .glow-line {
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(14,165,233,.4), rgba(6,182,212,.4), transparent);
        }

        /* ── Buttons ── */
        .btn-sky {
            background: linear-gradient(135deg, #0ea5e9, #0284c7 60%, #06b6d4);
            border-radius: 14px; color: #fff; font-weight: 700; font-size: .875rem;
            padding: 13px 22px; white-space: nowrap; transition: all .2s; border: none; cursor: pointer;
            box-shadow: 0 4px 20px rgba(14,165,233,.3), inset 0 1px 0 rgba(255,255,255,.15);
        }
        .btn-sky:hover {
            background: linear-gradient(135deg, #38bdf8, #0ea5e9 60%, #22d3ee);
            box-shadow: 0 6px 28px rgba(14,165,233,.5), inset 0 1px 0 rgba(255,255,255,.2);
            transform: translateY(-1px);
        }
        .btn-sky:active { transform: translateY(0); }
        .btn-sky:disabled { opacity: .45; transform: none; cursor: not-allowed; }

        .btn-sm {
            background: linear-gradient(135deg, #0ea5e9, #06b6d4);
            border-radius: 10px; color: #fff; font-weight: 700; font-size: .75rem;
            padding: 8px 16px; border: none; cursor: pointer; transition: all .2s;
            box-shadow: 0 3px 12px rgba(14,165,233,.3);
        }
        .btn-sm:hover { transform: translateY(-1px); box-shadow: 0 5px 18px rgba(14,165,233,.45); }
        .btn-sm:disabled { opacity: .4; transform: none; cursor: not-allowed; }

        /* ── Mode pills ── */
        .mode-pill {
            flex: 1; padding: 9px 0; border-radius: 12px; font-size: .72rem; font-weight: 700;
            letter-spacing: .04em; text-align: center; cursor: pointer; transition: all .18s;
            background: rgba(255,255,255,.04); border: 1px solid rgba(255,255,255,.07);
            color: rgba(148,163,184,.5);
        }
        .mode-pill:hover { color: rgba(148,163,184,.8); border-color: rgba(255,255,255,.12); }
        .mode-pill.active[data-mode="casual"]      { background: rgba(14,165,233,.12); border-color: rgba(56,189,248,.4);  color: #38bdf8; box-shadow: 0 0 16px rgba(14,165,233,.15); }
        .mode-pill.active[data-mode="competitive"] { background: rgba(245,158,11,.1);  border-color: rgba(245,158,11,.35); color: #f59e0b; box-shadow: 0 0 16px rgba(245,158,11,.12); }
        .mode-pill.active[data-mode="ladder"]      { background: rgba(16,185,129,.1);  border-color: rgba(16,185,129,.35); color: #10b981; box-shadow: 0 0 16px rgba(16,185,129,.12); }

        /* ── Player chips ── */
        .chip {
            padding: 6px 12px; border-radius: 999px; font-size: .72rem; font-weight: 600;
            cursor: pointer; transition: all .15s; user-select: none;
            background: rgba(255,255,255,.04); border: 1px solid rgba(255,255,255,.08);
            color: rgba(148,163,184,.6);
        }
        .chip:hover { border-color: rgba(255,255,255,.15); color: rgba(148,163,184,.9); }
        .chip.selected {
            background: rgba(14,165,233,.12); border-color: rgba(56,189,248,.4);
            color: #38bdf8; box-shadow: 0 0 10px rgba(14,165,233,.12);
        }

        /* ── Field ── */
        .field {
            background: rgba(255,255,255,.04); border: 1px solid rgba(255,255,255,.08);
            border-radius: 10px; color: #f1f5f9; padding: 9px 12px;
            font-size: .875rem; font-weight: 600; outline: none; transition: all .2s;
            text-align: center;
        }
        .field:focus { background: rgba(14,165,233,.06); border-color: rgba(56,189,248,.5); box-shadow: 0 0 0 3px rgba(14,165,233,.1); }
        .field::placeholder { color: rgba(148,163,184,.25); font-weight: 400; }

        /* ── Match court card ── */
        .court-card {
            border-radius: 18px; overflow: hidden;
            border: 1px solid rgba(255,255,255,.07);
            background: rgba(255,255,255,.025);
            transition: border-color .2s;
        }
        .court-card:hover { border-color: rgba(56,189,248,.15); }

        .court-header {
            padding: 10px 14px;
            display: flex; align-items: center; gap-8px; gap: 8px;
            border-bottom: 1px solid rgba(255,255,255,.05);
        }

        .court-body {
            display: grid; grid-template-columns: 1fr 44px 1fr;
            align-items: center; padding: 16px 14px; gap: 8px;
        }

        .team-side { display: flex; flex-direction: column; gap: 6px; }
        .team-side.right { text-align: right; align-items: flex-end; }

        .team-label {
            font-size: .58rem; font-weight: 800; letter-spacing: .12em; text-transform: uppercase;
        }

        .player-name {
            font-size: .82rem; font-weight: 600; color: #e2e8f0;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 130px;
        }

        .vs-badge {
            width: 36px; height: 36px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            background: rgba(255,255,255,.05); border: 1px solid rgba(255,255,255,.08);
            font-size: .6rem; font-weight: 900; letter-spacing: .06em;
            color: rgba(148,163,184,.4);
        }

        .court-footer {
            padding: 12px 14px;
            border-top: 1px solid rgba(255,255,255,.05);
            display: flex; align-items: center; gap: 8px;
        }

        /* ── Score display (done) ── */
        .score-display {
            display: flex; align-items: center; gap: 10px;
        }
        .score-num {
            font-size: 1.1rem; font-weight: 900; min-width: 28px; text-align: center;
        }
        .score-sep { color: rgba(148,163,184,.25); font-weight: 300; font-size: 1rem; }

        /* ── Badges ── */
        .badge {
            font-size: .58rem; font-weight: 800; letter-spacing: .08em; text-transform: uppercase;
            padding: 3px 9px; border-radius: 999px;
        }
        .badge-pending { background: rgba(245,158,11,.1); color: #f59e0b; border: 1px solid rgba(245,158,11,.2); }
        .badge-done    { background: rgba(16,185,129,.1);  color: #10b981; border: 1px solid rgba(16,185,129,.2); }

        /* ── Winner glow ── */
        .winner-glow { text-shadow: 0 0 12px currentColor; }

        /* ── Animations ── */
        @keyframes slideUp { from { opacity:0; transform:translateY(12px); } to { opacity:1; transform:translateY(0); } }
        .slide-up { animation: slideUp .24s ease forwards; }

        @keyframes pop { 0% { transform:scale(.94); opacity:0; } 60% { transform:scale(1.02); } 100% { transform:scale(1); opacity:1; } }
        .pop { animation: pop .28s cubic-bezier(.34,1.56,.64,1) forwards; }

        /* ── Empty state ── */
        .empty-icon {
            width: 52px; height: 52px; border-radius: 16px;
            background: rgba(255,255,255,.04); border: 1px solid rgba(255,255,255,.07);
            display: flex; align-items: center; justify-content: center; margin: 0 auto 12px;
        }

        /* ── Count pill ── */
        .count-pill {
            display: inline-flex; align-items: center; justify-content: center;
            min-width: 20px; height: 20px; padding: 0 6px; border-radius: 999px;
            background: rgba(14,165,233,.15); border: 1px solid rgba(56,189,248,.25);
            font-size: .65rem; font-weight: 800; color: #38bdf8;
        }
    </style>
</head>
<body>

<div class="max-w-lg mx-auto px-4 pt-8 pb-28">

    {{-- Page title --}}
    <div class="mb-6">
        <div class="glow-line mb-4" style="width:60px"></div>
        <h1 class="text-2xl font-black text-white tracking-tight">Matchmaking</h1>
        <p class="text-sm mt-1" style="color:rgba(148,163,184,.4)">Generate court pairings &amp; record scores</p>
    </div>

    {{-- Generate card --}}
    <div class="card p-5 mb-4">
        <p class="eyebrow mb-4">New Pairings</p>

        {{-- Mode selector --}}
        <div class="flex gap-2 mb-5">
            <button class="mode-pill active" data-mode="casual">Casual</button>
            <button class="mode-pill" data-mode="competitive">Competitive</button>
            <button class="mode-pill" data-mode="ladder">Ladder</button>
        </div>

        {{-- Mode description --}}
        <p id="mode-desc" class="text-xs mb-4 px-1" style="color:rgba(148,163,184,.4)">
            Random shuffle — great for fun games with any group.
        </p>

        {{-- Player chips --}}
        <div class="flex items-center justify-between mb-2">
            <p class="text-xs font-semibold" style="color:rgba(148,163,184,.4)">Select players <span style="color:rgba(148,163,184,.25)">(min 4)</span></p>
            <button id="select-all-chips" class="text-xs font-semibold" style="color:#38bdf8">Select All</button>
        </div>
        <div id="player-chips" class="flex flex-wrap gap-2 mb-5">
            @foreach($players as $p)
                <button class="chip" data-id="{{ $p->id }}" onclick="toggleChip(this)">
                    {{ $p->name }}
                </button>
            @endforeach
            @if($players->isEmpty())
                <p class="text-xs py-2" style="color:rgba(148,163,184,.3)">
                    No players yet. <a href="/" style="color:#38bdf8;font-weight:600">Add some first →</a>
                </p>
            @endif
        </div>

        <button id="generate-btn" class="btn-sky w-full flex items-center justify-center gap-2">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="16 3 21 3 21 8"/><polyline points="4 20 9 20 9 15"/>
                <path d="M21 3l-7 7-4-4-6 6"/><path d="M9 20l3-3 4 4 5-5"/>
            </svg>
            Generate Pairings
        </button>
    </div>

    {{-- Match list --}}
    <div class="card p-5">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <p class="eyebrow">Courts</p>
                <span id="match-count" class="count-pill">0</span>
            </div>
            <button id="refresh-btn" class="text-xs font-semibold px-3 py-1.5 rounded-lg"
                style="color:#38bdf8;background:rgba(14,165,233,.08);border:1px solid rgba(56,189,248,.15)">
                Refresh
            </button>
        </div>

        <div id="match-list" class="space-y-3">
            <div id="no-matches" class="text-center py-10">
                <div class="empty-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="rgba(148,163,184,.3)" stroke-width="1.5" stroke-linecap="round">
                        <rect x="3" y="3" width="18" height="18" rx="3"/><path d="M3 9h18M9 21V9"/>
                    </svg>
                </div>
                <p class="text-sm font-semibold" style="color:rgba(148,163,184,.35)">No matches yet</p>
                <p class="text-xs mt-1" style="color:rgba(148,163,184,.2)">Generate pairings above to get started</p>
            </div>
        </div>
    </div>

</div>

{{-- Bottom Tab Bar --}}
<nav class="tab-bar">
    <a href="/" class="tab-item">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
        </svg>
        Players
    </a>
    <a href="/matches" class="tab-item active">
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
let activeMode = 'casual';

const modeDescs = {
    casual:      'Random shuffle — great for fun games with any group.',
    competitive: 'Balanced by rating — strongest paired with weakest for fair teams.',
    ladder:      'Top vs bottom — highest rated players face each other.',
};
const modeColors = { casual: '#38bdf8', competitive: '#f59e0b', ladder: '#10b981' };
const modeBg     = { casual: 'rgba(14,165,233,.08)', competitive: 'rgba(245,158,11,.07)', ladder: 'rgba(16,185,129,.07)' };

// Mode pills
document.querySelectorAll('.mode-pill').forEach(btn => {
    btn.addEventListener('click', function () {
        document.querySelectorAll('.mode-pill').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        activeMode = this.dataset.mode;
        document.getElementById('mode-desc').textContent = modeDescs[activeMode];
    });
});

// Chip toggle
function toggleChip(el) { el.classList.toggle('selected'); }

document.getElementById('select-all-chips').addEventListener('click', function () {
    const chips = document.querySelectorAll('.chip');
    const all   = [...chips].every(c => c.classList.contains('selected'));
    chips.forEach(c => c.classList.toggle('selected', !all));
    this.textContent = all ? 'Select All' : 'Deselect All';
});

// Generate
document.getElementById('generate-btn').addEventListener('click', async () => {
    const selected = [...document.querySelectorAll('.chip.selected')].map(c => c.dataset.id);
    if (selected.length < 4) {
        const btn = document.getElementById('generate-btn');
        btn.style.boxShadow = '0 0 0 3px rgba(248,113,113,.35)';
        btn.style.borderColor = 'rgba(248,113,113,.4)';
        setTimeout(() => { btn.style.boxShadow = ''; btn.style.borderColor = ''; }, 700);
        return;
    }
    const btn = document.getElementById('generate-btn');
    btn.disabled = true;
    btn.innerHTML = `<svg class="animate-spin" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg> Generating…`;

    const res     = await fetch('/pairings', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
        body: JSON.stringify({ players: selected, mode: activeMode })
    });
    const matches = await res.json();
    prependMatches(matches);

    btn.disabled = false;
    btn.innerHTML = `<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 3 21 3 21 8"/><polyline points="4 20 9 20 9 15"/><path d="M21 3l-7 7-4-4-6 6"/><path d="M9 20l3-3 4 4 5-5"/></svg> Generate Pairings`;
});

function courtCard(m) {
    const done  = m.ratings_applied;
    const mc    = modeColors[m.mode] || '#38bdf8';
    const mbg   = modeBg[m.mode]    || 'rgba(14,165,233,.08)';

    const footer = done ? `
        <div class="court-footer" style="justify-content:space-between">
            <div class="score-display">
                <span class="score-num winner-glow" style="color:${m.winner==='A'?'#10b981':'rgba(148,163,184,.35)'}">${m.score_a}</span>
                <span class="score-sep">—</span>
                <span class="score-num winner-glow" style="color:${m.winner==='B'?'#10b981':'rgba(148,163,184,.35)'}">${m.score_b}</span>
            </div>
            <span class="badge badge-done">✓ Done</span>
        </div>` : `
        <div class="court-footer" style="flex-wrap:wrap;gap:8px">
            <div style="display:flex;align-items:center;gap:8px;flex:1">
                <input type="number" min="0" max="30" placeholder="—" id="sa-${m.id}"
                    class="field" style="width:52px">
                <span style="color:rgba(148,163,184,.25);font-weight:300;font-size:.9rem">vs</span>
                <input type="number" min="0" max="30" placeholder="—" id="sb-${m.id}"
                    class="field" style="width:52px">
                <span class="badge badge-pending" style="margin-left:4px">Pending</span>
            </div>
            <button onclick="submitScore(${m.id})" class="btn-sm">Submit Score</button>
        </div>`;

    return `<div id="match-${m.id}" class="court-card slide-up">
        <div class="court-header" style="background:${mbg}">
            <span style="width:6px;height:6px;border-radius:50%;background:${mc};box-shadow:0 0 8px ${mc};flex-shrink:0;display:inline-block"></span>
            <span style="font-size:.65rem;font-weight:800;letter-spacing:.1em;text-transform:uppercase;color:${mc}">${m.mode}</span>
            <span style="margin-left:auto;font-size:.65rem;font-weight:600;color:rgba(148,163,184,.3)">Court #${m.id}</span>
        </div>
        <div class="court-body">
            <div class="team-side">
                <span class="team-label" style="color:rgba(56,189,248,.6)">Team A</span>
                <span class="player-name">${m.team_a[0].name}</span>
                <span class="player-name">${m.team_a[1].name}</span>
                <span style="font-size:.65rem;color:rgba(148,163,184,.3);margin-top:2px">
                    ${m.team_a[0].rating.toFixed(2)} · ${m.team_a[1].rating.toFixed(2)}
                </span>
            </div>
            <div class="vs-badge">VS</div>
            <div class="team-side right">
                <span class="team-label" style="color:rgba(245,158,11,.6)">Team B</span>
                <span class="player-name">${m.team_b[0].name}</span>
                <span class="player-name">${m.team_b[1].name}</span>
                <span style="font-size:.65rem;color:rgba(148,163,184,.3);margin-top:2px">
                    ${m.team_b[0].rating.toFixed(2)} · ${m.team_b[1].rating.toFixed(2)}
                </span>
            </div>
        </div>
        ${footer}
    </div>`;
}

function prependMatches(matches) {
    document.getElementById('no-matches')?.remove();
    const list = document.getElementById('match-list');
    matches.slice().reverse().forEach(m => list.insertAdjacentHTML('afterbegin', courtCard(m)));
    updateCount();
}

function updateCount() {
    document.getElementById('match-count').textContent =
        document.querySelectorAll('[id^="match-"]').length;
}

async function submitScore(id) {
    const sa = document.getElementById(`sa-${id}`)?.value;
    const sb = document.getElementById(`sb-${id}`)?.value;
    if (sa === '' || sb === '' || parseInt(sa) === parseInt(sb)) return;

    const btn = document.querySelector(`#match-${id} .btn-sm`);
    if (btn) { btn.disabled = true; btn.textContent = '…'; }

    const res   = await fetch(`/matches/${id}/result`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
        body: JSON.stringify({ score_a: parseInt(sa), score_b: parseInt(sb) })
    });
    const match = await res.json();
    if (match.error) { if (btn) { btn.disabled = false; btn.textContent = 'Submit Score'; } return; }

    const card = document.getElementById(`match-${id}`);
    if (card) card.outerHTML = courtCard(match);
}

async function loadMatches() {
    const res     = await fetch('/api/matches');
    const matches = await res.json();
    if (!matches.length) return;
    document.getElementById('no-matches')?.remove();
    document.getElementById('match-list').innerHTML = matches.map(m => courtCard(m)).join('');
    updateCount();
}

document.getElementById('refresh-btn').addEventListener('click', async () => {
    document.getElementById('match-list').innerHTML = `<div id="no-matches" class="text-center py-10">
        <p class="text-sm" style="color:rgba(148,163,184,.3)">Loading…</p>
    </div>`;
    document.getElementById('match-count').textContent = '0';
    await loadMatches();
});

loadMatches();
</script>
</body>
</html>
