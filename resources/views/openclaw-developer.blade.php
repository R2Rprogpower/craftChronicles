<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="{{ $content['meta']['theme_color'] }}">
    <meta name="description" content="{{ $content['meta']['description'] }}">
    <title>{{ $content['meta']['title'] }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500;600&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('build/css/openclaw-developer.css') }}">
</head>
<body>
    <header class="dev-header" data-header>
        <a class="dev-brand" href="#top">{{ $content['brand']['name'] }}</a>
        <nav data-nav>
            @foreach ($content['navigation'] as $item)<a href="{{ $item['href'] }}">{{ $item['label'] }}</a>@endforeach
        </nav>
        <a class="header-cta" href="#request">{{ $content['common']['form_cta'] }}</a>
        <button class="menu-button" type="button" data-menu aria-label="{{ $content['common']['form_cta'] }}"><span></span><span></span></button>
    </header>

    <main id="top">
        <section class="dev-hero dev-shell">
            <div class="hero-copy reveal">
                <span class="status"><i></i>{{ $content['brand']['status'] }}</span>
                <p class="eyebrow">{{ $content['hero']['eyebrow'] }}</p>
                <h1>{{ $content['hero']['title'] }}</h1>
                <p class="lead">{{ $content['hero']['lead'] }}</p>
                <div class="hero-actions">
                    <a class="button button-primary" href="#request">{{ $content['common']['primary_cta'] }}</a>
                    <a class="button button-secondary" href="#pipeline">{{ $content['common']['secondary_cta'] }}</a>
                </div>
                <p class="hero-note">{{ $content['hero']['note'] }}</p>
            </div>
            <div class="terminal reveal">
                <div class="terminal-bar"><span></span><span></span><span></span><strong>{{ $content['hero']['terminal']['request_label'] }}</strong></div>
                <p class="terminal-request">{{ $content['hero']['terminal']['request'] }}</p>
                <ol>
                    @foreach ($content['hero']['terminal']['steps'] as $step)<li><i>✓</i>{{ $step }}</li>@endforeach
                </ol>
                <button type="button" disabled>{{ $content['hero']['terminal']['approval'] }}</button>
            </div>
            <div class="signal-grid reveal">
                @foreach ($content['hero']['signals'] as $signal)<article><strong>{{ $signal['value'] }}</strong><span>{{ $signal['label'] }}</span></article>@endforeach
            </div>
        </section>

        <section class="audience-strip">
            <div class="dev-shell audience-layout">
                <header>
                    <p class="eyebrow">{{ $content['audience']['eyebrow'] }}</p>
                    <h2>{{ $content['audience']['title'] }}</h2>
                </header>
                <ul>@foreach ($content['audience']['items'] as $item)<li>{{ $item }}</li>@endforeach</ul>
            </div>
        </section>

        <section class="dev-section dev-shell" id="scenarios">
            <header class="section-heading reveal">
                <p class="eyebrow">{{ $content['scenarios']['eyebrow'] }}</p>
                <h2>{{ $content['scenarios']['title'] }}</h2>
            </header>
            <div class="scenario-grid">
                @foreach ($content['scenarios']['items'] as $scenario)
                    <article class="scenario-card reveal"><span>{{ $scenario['number'] }}</span><h3>{{ $scenario['title'] }}</h3><p>{{ $scenario['text'] }}</p></article>
                @endforeach
            </div>
        </section>

        <section class="dev-section pipeline-section" id="pipeline">
            <div class="dev-shell">
                <header class="section-heading reveal">
                    <p class="eyebrow">{{ $content['pipeline']['eyebrow'] }}</p>
                    <h2>{{ $content['pipeline']['title'] }}</h2>
                </header>
                <div class="pipeline-list">
                    @foreach ($content['pipeline']['steps'] as $step)
                        <article class="reveal"><span>{{ $step['number'] }}</span><h3>{{ $step['title'] }}</h3><p>{{ $step['text'] }}</p></article>
                    @endforeach
                </div>
                <h3 class="provider-title reveal">{{ $content['pipeline']['providers_title'] }}</h3>
                <div class="provider-grid">
                    @foreach ($content['pipeline']['providers'] as $provider)<article class="reveal"><strong>{{ $provider['name'] }}</strong><p>{{ $provider['text'] }}</p></article>@endforeach
                </div>
            </div>
        </section>

        <section class="dev-section dev-shell gates-section">
            <header class="section-heading reveal">
                <p class="eyebrow">{{ $content['gates']['eyebrow'] }}</p>
                <h2>{{ $content['gates']['title'] }}</h2>
            </header>
            <div class="gate-grid">
                @foreach ($content['gates']['items'] as $gate)
                    <article class="reveal"><h3>{{ $gate['title'] }}</h3><ul>@foreach ($gate['checks'] as $check)<li>{{ $check }}</li>@endforeach</ul></article>
                @endforeach
            </div>
        </section>

        <section class="dev-section security-section" id="security">
            <div class="dev-shell">
                <header class="section-heading reveal">
                    <p class="eyebrow">{{ $content['security']['eyebrow'] }}</p>
                    <h2>{{ $content['security']['title'] }}</h2>
                </header>
                <div class="security-grid">
                    @foreach ($content['security']['items'] as $item)<article class="reveal"><span>{{ $content['security']['marker'] }}</span><h3>{{ $item['title'] }}</h3><p>{{ $item['text'] }}</p></article>@endforeach
                </div>
                <h3 class="risk-title reveal">{{ $content['security']['risk_title'] }}</h3>
                <div class="risk-table reveal">
                    @foreach ($content['security']['risk_levels'] as $risk)
                        <article><strong>{{ $risk['level'] }}</strong><span>{{ $risk['examples'] }}</span><p>{{ $risk['approval'] }}</p></article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="dev-section dev-shell production-section">
            <header class="section-heading reveal">
                <p class="eyebrow">{{ $content['production']['eyebrow'] }}</p>
                <h2>{{ $content['production']['title'] }}</h2>
            </header>
            <div class="production-grid">
                @foreach ($content['production']['items'] as $item)<article class="reveal"><h3>{{ $item['title'] }}</h3><p>{{ $item['text'] }}</p></article>@endforeach
            </div>
        </section>

        <section class="dev-section boundary-section">
            <div class="dev-shell">
                <header class="section-heading reveal">
                    <p class="eyebrow">{{ $content['boundary']['eyebrow'] }}</p>
                    <h2>{{ $content['boundary']['title'] }}</h2>
                </header>
                <div class="boundary-grid">
                    <article class="allow reveal"><h3>{{ $content['boundary']['agent_title'] }}</h3><ul>@foreach ($content['boundary']['agent_items'] as $item)<li>{{ $item }}</li>@endforeach</ul></article>
                    <article class="deny reveal"><h3>{{ $content['boundary']['human_title'] }}</h3><ul>@foreach ($content['boundary']['human_items'] as $item)<li>{{ $item }}</li>@endforeach</ul></article>
                </div>
                <p class="boundary-note reveal">{{ $content['boundary']['note'] }}</p>
            </div>
        </section>

        <section class="dev-section dev-shell" id="offer">
            <div class="offer-panel reveal">
                <div>
                    <p class="eyebrow">{{ $content['offer']['eyebrow'] }}</p>
                    <h2>{{ $content['offer']['title'] }}</h2>
                    <p>{{ $content['offer']['description'] }}</p>
                    <small>{{ $content['offer']['note'] }}</small>
                </div>
                <div class="offer-details">
                    <strong>{{ $content['offer']['setup'] }}</strong>
                    <span>{{ $content['offer']['monthly'] }}</span>
                    <em>{{ $content['offer']['timeline'] }}</em>
                    <ul>@foreach ($content['offer']['features'] as $feature)<li>{{ $feature }}</li>@endforeach</ul>
                    <a class="button button-accent" href="#request">{{ $content['offer']['cta'] }}</a>
                </div>
            </div>
        </section>

        <section class="request-section" id="request">
            <div class="dev-shell request-layout">
                <header class="section-heading reveal">
                    <p class="eyebrow">{{ $content['form']['eyebrow'] }}</p>
                    <h2>{{ $content['form']['title'] }}</h2>
                    <p>{{ $content['form']['description'] }}</p>
                </header>
                <form class="developer-form reveal" data-request-form action="{{ route('openclaw-developer.request') }}" method="post" novalidate>
                    @csrf
                    <div class="form-grid">
                        @foreach (['name', 'company', 'email', 'contact', 'company_size'] as $field)
                            <label><span>{{ $content['form']['fields'][$field]['label'] }}</span><input type="{{ $field === 'email' ? 'email' : 'text' }}" name="{{ $field }}" placeholder="{{ $content['form']['fields'][$field]['placeholder'] }}" {{ $field === 'name' ? 'required' : '' }}></label>
                        @endforeach
                    </div>
                    <p class="contact-hint">{{ $content['form']['contact_hint'] }}</p>
                    <fieldset>
                        <legend>{{ $content['form']['fields']['interests']['label'] }}</legend>
                        <div class="interest-grid">
                            @foreach ($content['form']['interests'] as $interest)<label><input type="checkbox" name="interests[]" value="{{ $interest['id'] }}"><span>{{ $interest['label'] }}</span></label>@endforeach
                        </div>
                    </fieldset>
                    <label><span>{{ $content['form']['fields']['current_stack']['label'] }}</span><textarea name="current_stack" rows="4" placeholder="{{ $content['form']['fields']['current_stack']['placeholder'] }}"></textarea></label>
                    <label><span>{{ $content['form']['fields']['budget']['label'] }}</span><select name="budget">@foreach ($content['form']['budgets'] as $budget)<option value="{{ $budget['value'] }}">{{ $budget['label'] }}</option>@endforeach</select></label>
                    <label><span>{{ $content['form']['fields']['message']['label'] }}</span><textarea name="message" rows="7" minlength="20" required placeholder="{{ $content['form']['fields']['message']['placeholder'] }}"></textarea></label>
                    <label class="honeypot" aria-hidden="true"><span>{{ $content['form']['fields']['website'] }}</span><input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                    <label class="consent"><input type="checkbox" name="consent" value="1" required><span>{{ $content['form']['fields']['consent'] }}</span></label>
                    <div class="form-footer">
                        <button class="button button-accent" type="submit" data-submit data-idle="{{ $content['form']['submit'] }}" data-sending="{{ $content['form']['sending'] }}">{{ $content['form']['submit'] }}</button>
                        <p class="form-status" data-form-status role="status" data-success-title="{{ $content['form']['success_title'] }}" data-success-text="{{ $content['form']['success_text'] }}" data-error="{{ $content['form']['error_text'] }}"></p>
                    </div>
                </form>
            </div>
        </section>
    </main>

    <footer class="dev-footer dev-shell"><strong>{{ $content['footer']['statement'] }}</strong><span>© {{ date('Y') }} {{ $content['footer']['copyright'] }}</span></footer>
    <script src="{{ asset('build/js/automation-services.js') }}" defer></script>
</body>
</html>
