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
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('build/css/automation-services.css') }}">
</head>
<body>
    <div class="ambient ambient-one" aria-hidden="true"></div>
    <div class="ambient ambient-two" aria-hidden="true"></div>

    <header class="site-header" data-header>
        <a class="brand" href="#top" aria-label="{{ $content['brand']['name'] }}">
            <span class="brand-icon">{{ $content['brand']['short'] }}</span>
            <span>{{ $content['brand']['name'] }}</span>
        </a>
        <nav data-nav>
            @foreach ($content['navigation'] as $item)
                <a href="{{ $item['href'] }}">{{ $item['label'] }}</a>
            @endforeach
        </nav>
        <a class="header-cta" href="#request">{{ $content['common']['primary_cta'] }}</a>
        <button class="menu-button" type="button" data-menu aria-label="{{ $content['common']['menu_label'] }}"><span></span><span></span></button>
    </header>

    <main id="top">
        <section class="hero section-shell">
            <div class="hero-copy reveal">
                <div class="availability"><i></i>{{ $content['brand']['status'] }}</div>
                <p class="eyebrow">{{ $content['hero']['eyebrow'] }}</p>
                <h1>{{ $content['hero']['title'] }}</h1>
                <p class="hero-lead">{{ $content['hero']['lead'] }}</p>
                <div class="hero-actions">
                    <a class="button button-primary" href="#request">{{ $content['common']['primary_cta'] }} <span>↗</span></a>
                    <a class="button button-ghost" href="#use-cases">{{ $content['common']['secondary_cta'] }} <span>↓</span></a>
                </div>
                <p class="hero-note">{{ $content['hero']['note'] }}</p>
            </div>
            <div class="system-map reveal" data-delay="1" aria-label="{{ $content['hero']['diagram']['engine'] }}">
                <div class="map-grid" aria-hidden="true"></div>
                <div class="map-node node-input"><span>01</span><strong>{{ $content['hero']['diagram']['input'] }}</strong></div>
                <div class="map-line line-one" aria-hidden="true"></div>
                <div class="map-core"><i></i><span>02</span><strong>{{ $content['hero']['diagram']['engine'] }}</strong></div>
                <div class="map-line line-two" aria-hidden="true"></div>
                <div class="map-node node-output"><span>03</span><strong>{{ $content['hero']['diagram']['output'] }}</strong></div>
                <div class="control-pill">✓ {{ $content['hero']['diagram']['control'] }}</div>
            </div>
            <div class="signal-row reveal">
                @foreach ($content['hero']['signals'] as $signal)
                    <article><strong>{{ $signal['value'] }}</strong><span>{{ $signal['label'] }}</span></article>
                @endforeach
            </div>
        </section>

        <section class="trust-strip">
            <div class="section-shell trust-layout">
                <strong>{{ $content['trust']['title'] }}</strong>
                <div>
                    @foreach ($content['trust']['items'] as $item)<span>{{ $item }}</span>@endforeach
                </div>
            </div>
        </section>

        <section class="content-section section-shell" id="services">
            <header class="section-heading reveal">
                <p class="eyebrow">{{ $content['services']['eyebrow'] }}</p>
                <h2>{{ $content['services']['title'] }}</h2>
                <p>{{ $content['services']['description'] }}</p>
            </header>
            <div class="services-grid">
                @foreach ($content['services']['items'] as $service)
                    <article class="service-card reveal" id="service-{{ $service['id'] }}">
                        <div class="card-number">{{ $service['number'] }}</div>
                        <h3>{{ $service['title'] }}</h3>
                        <p>{{ $service['description'] }}</p>
                        <strong class="outcome">→ {{ $service['outcome'] }}</strong>
                        <ul>@foreach ($service['tags'] as $tag)<li>{{ $tag }}</li>@endforeach</ul>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="content-section use-case-section" id="use-cases">
            <div class="section-shell">
                <header class="section-heading reveal">
                    <p class="eyebrow">{{ $content['use_cases']['eyebrow'] }}</p>
                    <h2>{{ $content['use_cases']['title'] }}</h2>
                </header>
                <div class="cases-list">
                    @foreach ($content['use_cases']['items'] as $case)
                        <article class="case-row reveal">
                            <span>{{ $case['segment'] }}</span>
                            <h3>{{ $case['title'] }}</h3>
                            <p>{{ $case['description'] }}</p>
                            <strong>{{ $case['metric'] }}</strong>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="content-section section-shell economics-section">
            <div class="economics-panel reveal">
                <div>
                    <p class="eyebrow">{{ $content['economics']['eyebrow'] }}</p>
                    <h2>{{ $content['economics']['title'] }}</h2>
                    <span class="formula-label">{{ $content['economics']['formula_label'] }}</span>
                    <strong class="formula">{{ $content['economics']['formula'] }}</strong>
                </div>
                <div class="principle-grid">
                    @foreach ($content['economics']['principles'] as $principle)
                        <article><h3>{{ $principle['title'] }}</h3><p>{{ $principle['text'] }}</p></article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="content-section section-shell" id="process">
            <header class="section-heading reveal">
                <p class="eyebrow">{{ $content['process']['eyebrow'] }}</p>
                <h2>{{ $content['process']['title'] }}</h2>
            </header>
            <div class="process-grid">
                @foreach ($content['process']['steps'] as $step)
                    <article class="reveal"><span>{{ $step['number'] }}</span><h3>{{ $step['title'] }}</h3><p>{{ $step['text'] }}</p></article>
                @endforeach
            </div>
        </section>

        <section class="content-section architecture-section">
            <div class="section-shell architecture-layout">
                <header class="section-heading reveal">
                    <p class="eyebrow">{{ $content['architecture']['eyebrow'] }}</p>
                    <h2>{{ $content['architecture']['title'] }}</h2>
                    <p>{{ $content['architecture']['description'] }}</p>
                </header>
                <div class="architecture-stack reveal" data-delay="1">
                    @foreach ($content['architecture']['layers'] as $layer)
                        <article><strong>{{ $layer['title'] }}</strong><div>@foreach ($layer['items'] as $item)<span>{{ $item }}</span>@endforeach</div></article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="content-section section-shell" id="plans">
            <header class="section-heading reveal">
                <p class="eyebrow">{{ $content['plans']['eyebrow'] }}</p>
                <h2>{{ $content['plans']['title'] }}</h2>
                <p>{{ $content['plans']['note'] }}</p>
            </header>
            <div class="plans-grid">
                @foreach ($content['plans']['items'] as $plan)
                    <article class="plan-card {{ $plan['featured'] ? 'is-featured' : '' }} reveal">
                        @if (isset($plan['badge']))<span class="plan-badge">{{ $plan['badge'] }}</span>@endif
                        <h3>{{ $plan['name'] }}</h3>
                        <p>{{ $plan['description'] }}</p>
                        <div class="price"><strong>{{ $plan['setup'] }}</strong><span>{{ $plan['monthly'] }}</span></div>
                        <ul>@foreach ($plan['features'] as $feature)<li><i>✓</i>{{ $feature }}</li>@endforeach</ul>
                        <a class="button {{ $plan['featured'] ? 'button-primary' : 'button-ghost' }}" href="#request">{{ $plan['cta'] }}</a>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="content-section safety-section">
            <div class="section-shell">
                <header class="section-heading reveal">
                    <p class="eyebrow">{{ $content['safety']['eyebrow'] }}</p>
                    <h2>{{ $content['safety']['title'] }}</h2>
                </header>
                <div class="safety-grid">
                    @foreach ($content['safety']['items'] as $item)
                        <article class="reveal"><span>✓</span><h3>{{ $item['title'] }}</h3><p>{{ $item['text'] }}</p></article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="content-section section-shell faq-section">
            <header class="section-heading reveal">
                <p class="eyebrow">{{ $content['faq']['eyebrow'] }}</p>
                <h2>{{ $content['faq']['title'] }}</h2>
            </header>
            <div class="faq-list reveal">
                @foreach ($content['faq']['items'] as $item)
                    <details><summary>{{ $item['question'] }}<span>+</span></summary><p>{{ $item['answer'] }}</p></details>
                @endforeach
            </div>
        </section>

        <section class="request-section" id="request">
            <div class="section-shell request-layout">
                <header class="request-copy reveal">
                    <p class="eyebrow">{{ $content['form']['eyebrow'] }}</p>
                    <h2>{{ $content['form']['title'] }}</h2>
                    <p>{{ $content['form']['description'] }}</p>
                </header>
                <form class="request-form reveal" data-request-form action="{{ route('automation-landing.request') }}" method="post" novalidate>
                    @csrf
                    <div class="form-grid">
                        @foreach (['name', 'company', 'email', 'contact', 'business_type', 'company_size'] as $field)
                            <label><span>{{ $content['form']['fields'][$field]['label'] }}</span><input type="{{ $field === 'email' ? 'email' : 'text' }}" name="{{ $field }}" placeholder="{{ $content['form']['fields'][$field]['placeholder'] }}" {{ $field === 'name' ? 'required' : '' }}></label>
                        @endforeach
                    </div>
                    <p class="contact-hint">{{ $content['form']['contact_hint'] }}</p>
                    <fieldset class="interest-fieldset">
                        <legend>{{ $content['form']['fields']['interests']['label'] }}</legend>
                        <div class="interest-grid">
                            @foreach ($content['form']['interests'] as $interest)
                                <label><input type="checkbox" name="interests[]" value="{{ $interest['id'] }}"><span>{{ $interest['label'] }}</span></label>
                            @endforeach
                        </div>
                    </fieldset>
                    <label><span>{{ $content['form']['fields']['current_stack']['label'] }}</span><textarea name="current_stack" rows="3" placeholder="{{ $content['form']['fields']['current_stack']['placeholder'] }}"></textarea></label>
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

    <footer class="site-footer section-shell">
        <strong>{{ $content['footer']['statement'] }}</strong>
        <span>{{ $content['footer']['location'] }}</span>
        <span>© {{ date('Y') }} {{ $content['footer']['copyright'] }}</span>
    </footer>

    <script src="{{ asset('build/js/automation-services.js') }}" defer></script>
</body>
</html>
