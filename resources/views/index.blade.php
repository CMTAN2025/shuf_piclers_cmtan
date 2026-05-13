<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shuf Picklers</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .fade-in { animation: fadeIn .2s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: none; } }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 text-white">

<div class="max-w-2xl mx-auto px-4 py-12">

    {{-- Header --}}
    <div class="text-center mb-10">
        <div class="text-5xl mb-3">🏓</div>
        <h1 class="text-4xl font-bold tracking-tight">Shuf Picklers</h1>
        <p class="text-slate-400 mt-1 text-sm">Shuffle your pickleball teams in style</p>
    </div>

    {{-- Add Player --}}
    <div class="bg-slate-800 border border-slate-700 rounded-2xl p-6 mb-6 shadow-xl">
        <h2 class="text-sm font-semibold uppercase tracking-widest text-slate-400 mb-4">Add Player</h2>
        <form id="add-form" class="flex gap-3">
            <input id="input-name" type="text" placeholder="Player name" required
                class="flex-1 bg-slate-700 border border-slate-600 rounded-xl px-4 py-2.5 text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-green-500 transition">
            <input id="input-dupr" type="number" step="0.01" placeholder="DUPR"
                class="w-28 bg-slate-700 border border-slate-600 rounded-xl px-4 py-2.5 text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-green-500 transition">
            <button type="submit"
                class="bg-green-500 hover:bg-green-400 text-slate-900 font-semibold px-5 py-2.5 rounded-xl text-sm transition active:scale-95">
                Add
            </button>
        </form>
    </div>

    {{-- Players + Shuffle --}}
    <div class="bg-slate-800 border border-slate-700 rounded-2xl p-6 mb-6 shadow-xl">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-semibold uppercase tracking-widest text-slate-400">Players</h2>
            <button id="select-all-btn" type="button"
                class="text-xs text-indigo-400 hover:text-indigo-300 font-medium transition">
                Select All
            </button>
        </div>

        <ul id="player-list" class="space-y-2 mb-5">
            @forelse($players as $player)
                <li id="player-{{ $player->id }}" class="fade-in flex items-center justify-between bg-slate-700/50 border border-slate-600/50 rounded-xl px-4 py-3">
                    <label class="flex items-center gap-3 cursor-pointer flex-1">
                        <input type="checkbox" class="player-checkbox w-4 h-4 accent-green-500 cursor-pointer" value="{{ $player->id }}">
                        <span class="font-medium text-sm">{{ $player->name }}</span>
                        @if($player->dupr)
                            <span class="ml-auto mr-3 text-xs bg-green-500/20 text-green-400 border border-green-500/30 px-2 py-0.5 rounded-full font-mono">
                                {{ number_format($player->dupr, 2) }}
                            </span>
                        @else
                            <span class="ml-auto mr-3 text-xs text-slate-500">No DUPR</span>
                        @endif
                    </label>
                    <button type="button" onclick="deletePlayer({{ $player->id }})"
                        class="text-slate-500 hover:text-red-400 transition text-xs px-2 py-1 rounded-lg hover:bg-red-500/10"
                        title="Remove player">✕</button>
                </li>
            @empty
                <li id="empty-state" class="text-slate-500 text-sm text-center py-4">No players yet. Add some above!</li>
            @endforelse
        </ul>

        <button id="shuffle-btn" type="button"
            class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-semibold py-3 rounded-xl text-sm transition active:scale-95 shadow-lg shadow-indigo-900/40">
            🔀 Shuffle Teams
        </button>
    </div>

    {{-- Teams Result --}}
    <div id="teams-section" class="hidden bg-slate-800 border border-slate-700 rounded-2xl p-6 shadow-xl">
        <h2 class="text-sm font-semibold uppercase tracking-widest text-slate-400 mb-4">Teams</h2>
        <div id="teams-grid" class="grid grid-cols-1 sm:grid-cols-2 gap-4"></div>
    </div>

</div>

<script>
const csrf = document.querySelector('meta[name="csrf-token"]').content;

function playerHTML(p) {
    const dupr = p.dupr
        ? `<span class="ml-auto mr-3 text-xs bg-green-500/20 text-green-400 border border-green-500/30 px-2 py-0.5 rounded-full font-mono">${parseFloat(p.dupr).toFixed(2)}</span>`
        : `<span class="ml-auto mr-3 text-xs text-slate-500">No DUPR</span>`;
    return `
        <li id="player-${p.id}" class="fade-in flex items-center justify-between bg-slate-700/50 border border-slate-600/50 rounded-xl px-4 py-3">
            <label class="flex items-center gap-3 cursor-pointer flex-1">
                <input type="checkbox" class="player-checkbox w-4 h-4 accent-green-500 cursor-pointer" value="${p.id}">
                <span class="font-medium text-sm">${p.name}</span>
                ${dupr}
            </label>
            <button type="button" onclick="deletePlayer(${p.id})"
                class="text-slate-500 hover:text-red-400 transition text-xs px-2 py-1 rounded-lg hover:bg-red-500/10"
                title="Remove player">✕</button>
        </li>`;
}

function syncEmptyState() {
    const list = document.getElementById('player-list');
    const existing = document.getElementById('empty-state');
    const hasPlayers = list.querySelectorAll('li:not(#empty-state)').length > 0;
    if (!hasPlayers && !existing) {
        list.innerHTML = '<li id="empty-state" class="text-slate-500 text-sm text-center py-4">No players yet. Add some above!</li>';
    } else if (hasPlayers && existing) {
        existing.remove();
    }
}

// Add player
document.getElementById('add-form').addEventListener('submit', async e => {
    e.preventDefault();
    const name = document.getElementById('input-name').value.trim();
    const dupr = document.getElementById('input-dupr').value;
    const res = await fetch('/players', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
        body: JSON.stringify({ name, dupr: dupr || null })
    });
    const player = await res.json();
    const list = document.getElementById('player-list');
    list.insertAdjacentHTML('beforeend', playerHTML(player));
    syncEmptyState();
    e.target.reset();
});

// Delete player
async function deletePlayer(id) {
    await fetch(`/players/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': csrf }
    });
    document.getElementById(`player-${id}`)?.remove();
    syncEmptyState();
}

// Select All toggle
document.getElementById('select-all-btn').addEventListener('click', function () {
    const boxes = document.querySelectorAll('.player-checkbox');
    const allChecked = [...boxes].every(b => b.checked);
    boxes.forEach(b => b.checked = !allChecked);
    this.textContent = allChecked ? 'Select All' : 'Deselect All';
});

// Shuffle
document.getElementById('shuffle-btn').addEventListener('click', async () => {
    const checked = [...document.querySelectorAll('.player-checkbox:checked')].map(b => b.value);
    if (checked.length < 2) return;
    const res = await fetch('/shuffle', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
        body: JSON.stringify({ players: checked })
    });
    const teams = await res.json();
    const grid = document.getElementById('teams-grid');
    const colors = ['indigo', 'pink', 'amber', 'teal', 'violet', 'cyan'];
    grid.innerHTML = teams.map((team, i) => {
        const color = colors[i % colors.length];
        const members = team.map(p => `
            <li class="flex items-center gap-2 text-sm">
                <span class="w-2 h-2 rounded-full bg-${color}-400 inline-block"></span>
                <span class="font-medium">${p.name}</span>
                ${p.dupr ? `<span class="ml-auto text-xs text-green-400 font-mono">${parseFloat(p.dupr).toFixed(2)}</span>` : ''}
            </li>`).join('');
        return `
            <div class="fade-in bg-slate-700/50 border border-slate-600/50 rounded-xl p-4">
                <p class="text-xs font-semibold uppercase tracking-widest text-${color}-400 mb-3">Team ${i + 1}</p>
                <ul class="space-y-2">${members}</ul>
            </div>`;
    }).join('');
    document.getElementById('teams-section').classList.remove('hidden');
});
</script>
</body>
</html>
