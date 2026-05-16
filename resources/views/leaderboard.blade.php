<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard — Shuf Picklers</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        * { font-family: 'Inter', sans-serif; -webkit-tap-highlight-color: transparent; }
        body {
            background: #060d18;
            background-image:
                radial-gradient(ellipse 90% 55% at 50% -5%, rgba(14,165,233,.18) 0%, transparent 65%),
                radial-gradient(ellipse 55% 40% at 90% 90%, rgba(6,182,212,.08) 0%, transparent 60%);
            min-height: 100vh;
        }
        .glass { background: rgba(255,255,255,.04); border: 1px solid rgba(255,255,255,.07); backdrop-filter: blur(16px); }
        .section-label { font-size: .62rem; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; color: rgba(56,189,248,.6); }
        .glow-line { height: 1px; background: linear-gradient(90deg, transparent, rgba(14,165,233,.5), rgba(6,182,212,.5), transparent); }
        .nav-link { color: rgba(148,163,184,.5); transition: color .15s; }
        .nav-link:hover { color: #38bdf8; }
        .row { background: rgba(255,255,255,.03); border: 1px solid rgba(255,255,255,.06); transition: all .15s; cursor: pointer; }
        .row:hover { background: rgba(14,165,233,.07); border-color: rgba(14,165,233,.2); }
        .rank-1 { color: #f59e0b; }
        .rank-2 { color: #94a3b8; }
        .rank-3 { color: #cd7c3a; }
        .rating-bar-bg { background: rgba(255,255,255,.06); border-radius: 999px; height: 4px; }
        .rating-bar { background: linear-gradient(90deg, #0ea5e9, #06b6d4); border-radius: 999px; height: 4px; transition: width .6s ease; }
        @keyframes slideUp { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }
        .fade-in { animation: slideUp .25s ease forwards; }
        @keyframes modalIn { from { opacity:0; transform:translateY(20px) scale(.97); } to { opacity:1; transform:translateY(0) scale(1); } }
        #hist-overlay.show { animation: overlayIn .2s ease forwards; }
        #hist-box.show { animation: modalIn .28s cubic-bezier(.22,1,.36,1) forwards; }
        @keyframes overlayIn { from { opacity:0; } to { opacity:1; } }
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-thumb { background: rgba(14,165,233,.25); border-radius: 4px; }
        .delta-pos { color: #10b981; }
        .delta-neg { color: #f87171; }
        .btn-primary { background: linear-gradient(135deg,#0ea5e9,#0284c7,#06b6d4); box-shadow:0 4px 24px rgba(14,165,233,.35); transition:all .2s; }
        .btn-primary:hover { background: linear-gradient(135deg,#38bdf8,#0ea5e9,#22d3ee); transform:translateY(-1px); }
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
            color: rgba(148,163,184,.45); font-size: .6rem; font-weight: 600;
            letter-spacing: .06em; text-transform: uppercase;
            text-decoration: none; transition: color .15s; position: relative;
        }
        .tab-item svg { transition: transform .2s cubic-bezier(.34,1.56,.64,1); }
        .tab-item:hover { color: #38bdf8; }
        .tab-item:hover svg { transform: translateY(-2px); }
        .tab-item.active { color: #38bdf8; }
        .tab-item.active::before {
            content: ''; position: absolute; top: 0; left: 50%; transform: translateX(-50%);
            width: 32px; height: 2px;
            background: linear-gradient(90deg, #0ea5e9, #22d3ee);
            border-radius: 0 0 4px 4px;
        }
    </style>
</head>
<body class="text-slate-100">

<div class="max-w-lg mx-auto px-4 pt-8 pb-28">

    {{-- Header --}}
    <div class="text-center mb-8">
        <img src="/logo.png" alt="logo" class="mx-auto" style="width:180px;filter:drop-shadow(0 0 32px rgba(14,165,233,.5))">
        <div class="glow-line mx-auto mt-4 mb-2" style="width:80px"></div>
        <p class="text-xs tracking-widest font-semibold" style="color:rgba(56,189,248,.5);letter-spacing:.18em">LEADERBOARD</p>
    </div>

    {{-- Stats bar --}}
    <div class="grid grid-cols-3 gap-3 mb-4">
        <div class="glass rounded-xl p-3 text-center">
            <p id="stat-players" class="text-xl font-bold text-white">—</p>
            <p class="text-xs mt-0.5" style="color:rgba(148,163,184,.45)">Players</p>
        </div>
        <div class="glass rounded-xl p-3 text-center">
            <p id="stat-matches" class="text-xl font-bold text-white">—</p>
            <p class="text-xs mt-0.5" style="color:rgba(148,163,184,.45)">Matches</p>
        </div>
        <div class="glass rounded-xl p-3 text-center">
            <p id="stat-top" class="text-xl font-bold" style="color:#f59e0b">—</p>
            <p class="text-xs mt-0.5" style="color:rgba(148,163,184,.45)">Top Rating</p>
        </div>
    </div>

    {{-- Table --}}
    <div class="glass rounded-2xl p-5">
        <div class="flex items-center justify-between mb-3">
            <p class="section-label">Rankings</p>
            <button id="refresh-btn" class="text-xs font-semibold px-2 py-1 rounded-lg transition" style="color:#38bdf8">Refresh</button>
        </div>

        {{-- Header row --}}
        <div class="grid gap-2 px-3 pb-2 text-xs font-semibold" style="grid-template-columns:28px 1fr 56px 56px 56px;color:rgba(148,163,184,.4)">
            <span>#</span><span>Player</span><span class="text-center">Rating</span><span class="text-center">W/L</span><span class="text-center">Win%</span>
        </div>

        <div id="lb-list" class="space-y-2">
            <p class="text-center py-8 text-sm" style="color:rgba(148,163,184,.35)">Loading...</p>
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
    <a href="/matches" class="tab-item">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="3" width="18" height="18" rx="3"/>
            <path d="M3 9h18M9 21V9"/>
        </svg>
        Matches
    </a>
    <a href="/leaderboard" class="tab-item active">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
        </svg>
        Leaderboard
    </a>
</nav>

{{-- History Modal --}}
<div id="hist-overlay" class="hidden fixed inset-0 z-50 flex items-end sm:items-center justify-center p-4"
    style="background:rgba(0,0,0,.7);backdrop-filter:blur(6px)">
    <div id="hist-box" class="w-full max-w-md rounded-3xl overflow-hidden"
        style="background:#060d18;border:1px solid rgba(14,165,233,.15);box-shadow:0 32px 80px rgba(0,0,0,.7);max-height:80vh;display:flex;flex-direction:column">
        <div class="flex items-center justify-between px-6 py-5" style="border-bottom:1px solid rgba(14,165,233,.1)">
            <div>
                <p id="hist-name" class="text-white font-bold text-sm"></p>
                <p id="hist-rating" class="text-xs" style="color:rgba(56,189,248,.5)"></p>
            </div>
            <button onclick="closeHist()" style="width:32px;height:32px;border-radius:10px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.08);display:flex;align-items:center;justify-content:center;color:rgba(148,163,184,.5);cursor:pointer">
                <svg width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M1 1L11 11M11 1L1 11" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
            </button>
        </div>
        <div class="overflow-y-auto px-6 py-4">
            <p class="section-label mb-3">Rating History</p>
            <div id="hist-list" class="space-y-2"></div>
        </div>
        <div class="px-6 py-4" style="border-top:1px solid rgba(14,165,233,.1)">
            <button onclick="closeHist()" class="btn-primary w-full text-white font-semibold py-3 rounded-xl text-sm">Close</button>
        </div>
    </div>
</div>

<script>
const csrf = document.querySelector('meta[name="csrf-token"]').content;

function rankColor(rank) {
    if (rank === 1) return '#f59e0b';
    if (rank === 2) return '#94a3b8';
    if (rank === 3) return '#cd7c3a';
    return 'rgba(148,163,184,.4)';
}

function rowHTML(p, maxRating) {
    const barW = maxRating > 0 ? Math.round((p.rating / maxRating) * 100) : 0;
    const ratingColor = p.rank <= 3 ? rankColor(p.rank) : '#38bdf8';
    return `<div class="row fade-in rounded-xl px-3 py-3 grid gap-2 items-center"
                style="grid-template-columns:28px 1fr 56px 56px 56px"
                onclick="openHist(${p.id}, '${p.name.replace(/'/g,"\\'")}', ${p.rating})">
        <span class="text-xs font-bold" style="color:${rankColor(p.rank)}">${p.rank}</span>
        <div class="min-w-0">
            <p class="text-sm font-semibold text-slate-200 truncate">${p.name}</p>
            <div class="rating-bar-bg mt-1.5" style="width:100%">
                <div class="rating-bar" style="width:${barW}%"></div>
            </div>
        </div>
        <span class="text-center text-xs font-bold" style="color:${ratingColor}">${p.rating.toFixed(2)}</span>
        <span class="text-center text-xs" style="color:rgba(148,163,184,.6)">${p.wins}/${p.losses}</span>
        <span class="text-center text-xs font-semibold" style="color:${p.win_rate>=50?'#10b981':'rgba(148,163,184,.5)'}">${p.win_rate}%</span>
    </div>`;
}

async function loadLeaderboard() {
    const res = await fetch('/api/leaderboard');
    const data = await res.json();
    const list = document.getElementById('lb-list');

    if (!data.length) {
        list.innerHTML = `<p class="text-center py-8 text-sm" style="color:rgba(148,163,184,.35)">No data yet. Play some matches first.</p>`;
        return;
    }

    const maxRating = Math.max(...data.map(p => p.rating));
    const totalMatches = data.reduce((s, p) => s + p.matches_played, 0) / 2;

    document.getElementById('stat-players').textContent = data.length;
    document.getElementById('stat-matches').textContent = Math.round(totalMatches);
    document.getElementById('stat-top').textContent = data[0]?.rating.toFixed(2) ?? '—';

    list.innerHTML = data.map(p => rowHTML(p, maxRating)).join('');
}

async function openHist(id, name, rating) {
    document.getElementById('hist-name').textContent = name;
    document.getElementById('hist-rating').textContent = `Current rating: ${rating.toFixed(2)}`;
    document.getElementById('hist-list').innerHTML = `<p class="text-xs text-center py-4" style="color:rgba(148,163,184,.35)">Loading...</p>`;

    const overlay = document.getElementById('hist-overlay');
    overlay.classList.remove('hidden');
    requestAnimationFrame(() => {
        overlay.classList.add('show');
        document.getElementById('hist-box').classList.add('show');
    });
    document.body.style.overflow = 'hidden';

    const res = await fetch(`/players/${id}/history`);
    const data = await res.json();
    const list = document.getElementById('hist-list');

    if (!data.history.length) {
        list.innerHTML = `<p class="text-xs text-center py-4" style="color:rgba(148,163,184,.35)">No match history yet.</p>`;
        return;
    }

    list.innerHTML = data.history.map(h => {
        const pos = h.delta >= 0;
        const modeColors = { casual: '#38bdf8', competitive: '#f59e0b', ladder: '#10b981' };
        const mc = modeColors[h.mode] || '#38bdf8';
        return `<div class="rounded-xl px-3 py-2.5" style="background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.06)">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold uppercase" style="color:${mc}">${h.mode ?? '—'}</span>
                <span class="text-xs" style="color:rgba(148,163,184,.4)">${h.score ?? '—'}</span>
                ${h.won ? '<span class="text-xs font-semibold" style="color:#10b981">WIN</span>' : '<span class="text-xs font-semibold" style="color:#f87171">LOSS</span>'}
                <span class="ml-auto text-xs font-bold ${pos ? 'delta-pos' : 'delta-neg'}">${pos ? '+' : ''}${h.delta.toFixed(2)}</span>
            </div>
            <div class="flex items-center gap-1 mt-1">
                <span class="text-xs" style="color:rgba(148,163,184,.4)">${h.rating_before.toFixed(2)}</span>
                <svg width="10" height="10" viewBox="0 0 10 10" fill="none"><path d="M2 5h6M6 3l2 2-2 2" stroke="rgba(148,163,184,.3)" stroke-width="1.2" stroke-linecap="round"/></svg>
                <span class="text-xs font-semibold" style="color:#38bdf8">${h.rating_after.toFixed(2)}</span>
                <span class="ml-auto text-xs" style="color:rgba(148,163,184,.3)">${h.date.slice(0,10)}</span>
            </div>
        </div>`;
    }).join('');
}

function closeHist() {
    const overlay = document.getElementById('hist-overlay');
    overlay.classList.add('hidden');
    overlay.classList.remove('show');
    document.getElementById('hist-box').classList.remove('show');
    document.body.style.overflow = '';
}

document.getElementById('hist-overlay').addEventListener('click', function(e) {
    if (e.target === this) closeHist();
});

document.getElementById('refresh-btn').addEventListener('click', () => {
    document.getElementById('lb-list').innerHTML = `<p class="text-center py-8 text-sm" style="color:rgba(148,163,184,.35)">Loading...</p>`;
    loadLeaderboard();
});

loadLeaderboard();
</script>
</body>
</html>
