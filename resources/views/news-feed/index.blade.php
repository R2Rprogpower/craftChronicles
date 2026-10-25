<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Конечная лента — CraftChronicles</title>
    <style>
        :root{--ink:#18201d;--muted:#69736e;--paper:#fbfdf9;--line:#dce6df;--green:#235b45;--mint:#dcefe5;--red:#9a3434;--shadow:0 18px 55px #18302614}*{box-sizing:border-box}body{margin:0;background:linear-gradient(135deg,#edf5ee,#f8f4e8);color:var(--ink);font:15px/1.55 system-ui,-apple-system,sans-serif}.shell{width:min(1240px,calc(100% - 28px));margin:auto;padding:34px 0 70px}h1,h2,h3,p{margin-top:0}.hero{display:flex;justify-content:space-between;align-items:end;gap:24px;margin-bottom:20px}.hero h1{font:700 clamp(34px,6vw,64px)/1.02 Georgia,serif;margin-bottom:8px}.hero p{color:var(--muted);max-width:760px}.mode,.badge{display:inline-flex;border-radius:999px;padding:6px 10px;background:var(--mint);color:var(--green);font-size:12px;font-weight:800}.stats{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin:18px 0}.card,.filters,.source-panel,.editor,.topic-block{background:#fffffff0;border:1px solid #fff;border-radius:18px;box-shadow:var(--shadow)}.card{padding:15px}.card strong{display:block;font-size:25px}.card span{color:var(--muted);font-size:12px}.filters{padding:14px;display:grid;grid-template-columns:1.4fr repeat(4,minmax(130px,.7fr)) auto;gap:9px;margin-bottom:18px}.control{width:100%;border:1px solid var(--line);border-radius:10px;padding:10px 12px;background:#fff;color:var(--ink)}.button{border:0;border-radius:10px;padding:11px 15px;background:var(--green);color:#fff;font-weight:800;cursor:pointer}.button.soft{background:#e7eee9;color:#254b3b}.layout{display:grid;grid-template-columns:minmax(0,1fr) 330px;gap:18px;align-items:start}.feed{display:grid;gap:16px}.topic-block{padding:20px}.topic-head{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:8px}.topic-head h2{margin:0;font:700 22px/1.2 Georgia,serif}.article{padding:15px 0;border-top:1px solid var(--line)}.article:first-of-type{border-top:0}.article a{color:var(--ink);font:700 17px/1.35 Georgia,serif;text-decoration:none}.article a:hover{text-decoration:underline;color:var(--green)}.meta{display:flex;flex-wrap:wrap;gap:7px 12px;color:var(--muted);font-size:12px;margin-top:7px}.language{font-weight:900;text-transform:uppercase;color:var(--green)}.source-panel{padding:18px;position:sticky;top:12px;max-height:calc(100vh - 24px);overflow:auto}.source{padding:10px 0;border-top:1px solid var(--line)}.source:first-of-type{border-top:0}.source-name{font-weight:800}.source-meta{font-size:11px;color:var(--muted)}.error{color:var(--red);font-size:11px;margin-top:3px;overflow-wrap:anywhere}.editor{padding:20px;margin-top:18px}.editor-grid{display:grid;grid-template-columns:1fr 180px 110px;gap:9px}.wide{grid-column:1/-1}.status{padding:12px 15px;border-radius:12px;background:#e6f2e9;color:#22583f;margin-bottom:16px}.validation{padding:12px 15px;border-radius:12px;background:#f7e5e5;color:#852d2d;margin-bottom:16px}.empty{padding:48px;text-align:center;color:var(--muted)}.pagination{display:flex;justify-content:space-between;gap:12px;margin-top:16px}.pagination a{color:var(--green);font-weight:800;text-decoration:none}.hint{color:var(--muted);font-size:12px;margin:8px 0 0}@media(max-width:1000px){.filters{grid-template-columns:1fr 1fr}.layout{grid-template-columns:1fr}.source-panel{position:static;max-height:none}}@media(max-width:640px){.hero{display:block}.mode{margin-top:12px}.stats{grid-template-columns:1fr}.filters,.editor-grid{grid-template-columns:1fr}.wide{grid-column:auto}.shell{width:min(100% - 18px,1240px);padding-top:20px}.topic-block{padding:16px}}
    </style>
</head>
<body>
<main class="shell">
    <header class="hero">
        <div><h1>Конечная лента</h1><p>Только ссылки из выбранных источников. Публикации сохраняются в историю и обновляются каждые 12 часов — без пересказов и бесконечного алгоритмического фида.</p></div>
        <span class="mode">{{ $canEdit ? 'Режим редактора' : 'Только просмотр' }}</span>
    </header>

    @if(session('status'))<div class="status">{{ session('status') }}</div>@endif
    @if($errors->any())<div class="validation">{{ $errors->first() }}</div>@endif

    <section class="stats">
        <div class="card"><strong>{{ $recentArticles }}</strong><span>ссылок за последние 12 часов</span></div>
        <div class="card"><strong>{{ $totalArticles }}</strong><span>публикаций сохранено в истории</span></div>
        <div class="card"><strong>{{ $lastUpdatedAt ? \Illuminate\Support\Carbon::parse($lastUpdatedAt)->timezone('Europe/Kyiv')->format('d.m H:i') : '—' }}</strong><span>последнее успешное обновление · Киев</span></div>
    </section>

    <form class="filters" method="get" action="{{ route('news-feed.index') }}">
        <input class="control" name="q" value="{{ request('q') }}" placeholder="Поиск по заголовкам">
        <select class="control" name="topic"><option value="">Все темы</option>@foreach($topics as $topic)<option value="{{ $topic->slug }}" @selected(request('topic') === $topic->slug)>{{ $topic->name }}</option>@endforeach</select>
        <select class="control" name="source"><option value="">Все сайты</option>@foreach($sources as $source)<option value="{{ $source->id }}" @selected((string) request('source') === (string) $source->id)>{{ $source->name }}</option>@endforeach</select>
        <select class="control" name="language"><option value="">Все языки</option>@foreach(['uk'=>'UK','ru'=>'RU','de'=>'DE','en'=>'EN'] as $code=>$label)<option value="{{ $code }}" @selected(request('language') === $code)>{{ $label }}</option>@endforeach</select>
        <select class="control" name="period"><option value="12h" @selected($period === '12h')>12 часов</option><option value="24h" @selected($period === '24h')>24 часа</option><option value="7d" @selected($period === '7d')>7 дней</option><option value="30d" @selected($period === '30d')>30 дней</option><option value="all" @selected($period === 'all')>Вся история</option></select>
        <button class="button" type="submit">Фильтровать</button>
    </form>

    <section class="layout">
        <div class="feed">
            @forelse($articles->getCollection()->groupBy(fn($article) => $article->source->topic->name) as $topicName => $topicArticles)
                <section class="topic-block">
                    <div class="topic-head"><h2>{{ $topicName }}</h2><span class="badge">{{ $topicArticles->count() }}</span></div>
                    @foreach($topicArticles as $article)
                        <article class="article">
                            <a href="{{ $article->url }}" target="_blank" rel="noopener noreferrer">{{ $article->title }}</a>
                            <div class="meta"><span class="language">{{ $article->source->language }}</span><span>{{ $article->source->name }}</span><span>{{ ($article->published_at ?? $article->discovered_at)->timezone('Europe/Kyiv')->format('d.m.Y H:i') }}</span></div>
                        </article>
                    @endforeach
                </section>
            @empty
                <div class="topic-block empty">По выбранным фильтрам публикаций пока нет. @if($canEdit)Нажми «Обновить все источники».@endif</div>
            @endforelse

            @if($articles->hasPages())
                <nav class="pagination"><span>@if($articles->previousPageUrl())<a href="{{ $articles->previousPageUrl() }}">← Новее</a>@endif</span><span>Страница {{ $articles->currentPage() }} из {{ $articles->lastPage() }}</span><span>@if($articles->nextPageUrl())<a href="{{ $articles->nextPageUrl() }}">Старее →</a>@endif</span></nav>
            @endif
        </div>

        <aside class="source-panel">
            <h3>Источники · {{ $sources->count() }}</h3>
            @if($canEdit)
                <form method="post" action="{{ route('news-feed.refresh') }}" onsubmit="this.querySelector('button').disabled=true;this.querySelector('button').textContent='Обновляю…'">@csrf<button class="button" type="submit">Обновить все источники</button></form>
                <p class="hint">Автоматически: в 09:00 и 21:00 по Киеву.</p>
            @endif
            @foreach($sources as $source)
                <div class="source">
                    <div class="source-name"><a href="{{ $source->site_url }}" target="_blank" rel="noopener noreferrer">{{ $source->name }}</a></div>
                    <div class="source-meta">{{ strtoupper($source->language) }} · {{ $source->topic->name }}@if($source->last_success_at) · {{ $source->last_success_at->timezone('Europe/Kyiv')->format('d.m H:i') }}@endif</div>
                    @if($source->last_error)<div class="error">{{ $source->last_error }}</div>@endif
                    @if($canEdit)<form method="post" action="{{ route('news-feed.refresh') }}">@csrf<input type="hidden" name="source_id" value="{{ $source->id }}"><button class="button soft" type="submit">Обновить этот сайт</button></form>@endif
                </div>
            @endforeach
        </aside>
    </section>

    @if($canEdit)
        <section class="editor">
            <h2>Добавить новый сайт</h2>
            <form class="editor-grid" method="post" action="{{ route('news-feed.sources.store') }}">
                @csrf
                <input class="control" name="name" value="{{ old('name') }}" required maxlength="160" placeholder="Название источника">
                <select class="control" name="news_topic_id" required>@foreach($topics as $topic)<option value="{{ $topic->id }}" @selected((string) old('news_topic_id') === (string) $topic->id)>{{ $topic->name }}</option>@endforeach</select>
                <select class="control" name="language" required>@foreach(['uk'=>'UK','ru'=>'RU','de'=>'DE','en'=>'EN'] as $code=>$label)<option value="{{ $code }}" @selected(old('language') === $code)>{{ $label }}</option>@endforeach</select>
                <input class="control wide" name="site_url" value="{{ old('site_url') }}" type="url" required maxlength="2000" placeholder="https://example.com/category/news/">
                <input class="control wide" name="feed_url" value="{{ old('feed_url') }}" type="url" maxlength="2000" placeholder="RSS/Atom URL — можно оставить пустым для автопоиска">
                <div class="wide"><button class="button" type="submit">Добавить источник</button><p class="hint">Если нужна только конкретная рубрика сайта, укажи URL этой рубрики и её отдельный RSS. Система хранит только ссылки и метаданные, не тексты страниц.</p></div>
            </form>
        </section>
    @endif
</main>
</body>
</html>
