<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Craft Chess</title>
    <style>
        :root{color-scheme:dark}*{box-sizing:border-box}body{margin:0;font:16px system-ui;background:#111827;color:#e5e7eb}main{max-width:900px;margin:auto;padding:36px 20px}h1{font-size:42px;margin:0 0 6px;color:#f3d9b1}.card{background:#1f2937;border:1px solid #374151;border-radius:14px;padding:20px;margin:20px 0}input,button,a.button{border:0;border-radius:8px;padding:11px 15px;font:inherit}input{background:#111827;color:white;border:1px solid #4b5563}button,a.button{background:#b58863;color:white;font-weight:700;cursor:pointer;text-decoration:none;display:inline-block}.game{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:13px 0;border-top:1px solid #374151}.muted{color:#9ca3af}.errors{color:#fca5a5}
    </style>
</head>
<body><main>
    <h1>Craft Chess</h1><div class="muted">Two players. Unlimited spectators. Zero paperwork.</div>
    @if ($errors->any())<div class="errors">{{ $errors->first() }}</div>@endif
    <section class="card">
        <h2>Your session profile</h2>
        <form method="post" action="{{ route('chess.profile') }}">@csrf
            <input name="name" value="{{ old('name', $profile?->name) }}" maxlength="30" placeholder="Your name" required>
            <button>{{ $profile ? 'Rename' : 'Create profile' }}</button>
        </form>
        @if($profile)<form method="post" action="{{ route('chess.create') }}" style="margin-top:16px">@csrf<button>Create a game</button></form>@endif
    </section>
    <section class="card"><h2>Games</h2>
        @forelse($games as $game)
            <div class="game"><div><strong>{{ $game->white?->name ?? 'Open seat' }} vs {{ $game->black?->name ?? 'Open seat' }}</strong><div class="muted">{{ ucfirst($game->status) }} · {{ $game->updated_at->diffForHumans() }}</div></div><a class="button" href="{{ route('chess.game', $game) }}">Open</a></div>
        @empty <p class="muted">No games yet. Make history, or at least a blunder.</p> @endforelse
    </section>
</main></body></html>
