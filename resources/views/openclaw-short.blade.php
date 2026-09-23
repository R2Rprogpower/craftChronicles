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
    <link rel="stylesheet" href="{{ asset('build/css/openclaw-short.css') }}">
</head>
<body>
    <header class="short-header">
        <a class="short-brand" href="#top">{{ $content['brand']['name'] }}</a>
        <nav>
            <a href="#cases">{{ $content['navigation']['cases'] }}</a>
            <a class="header-button" href="#request">{{ $content['navigation']['contact'] }}</a>
        </nav>
    </header>

    <main id="top">
        <section class="short-hero shell">
            <div class="hero-copy reveal">
                <span class="status">{{ $content['brand']['status'] }}</span>
                <p class="eyebrow">{{ $content['hero']['eyebrow'] }}</p>
                <h1>{{ $content['hero']['title'] }}</h1>
                <p class="lead">{{ $content['hero']['lead'] }}</p>
                <div class="actions">
                    <a class="button button-dark" href="#request">{{ $content['hero']['primary_cta'] }}</a>
                    <a class="button button-light" href="#cases">{{ $content['hero']['secondary_cta'] }}</a>
                </div>
                <p class="hero-note">{{ $content['hero']['note'] }}</p>
            </div>
            <div class="chat-demo reveal">
                <span>{{ $content['hero']['example_label'] }}</span>
                <p class="message owner-message">{{ $content['hero']['example_request'] }}</p>
                <p class="message system-message">{{ $content['hero']['example_result'] }}</p>
            </div>
        </section>

        <section class="cases shell" id="cases">
            <header class="section-title reveal">
                <p class="eyebrow">{{ $content['cases']['eyebrow'] }}</p>
                <h2>{{ $content['cases']['title'] }}</h2>
            </header>
            <div class="case-list">
                @foreach ($content['cases']['items'] as $case)
                    <article class="case reveal">
                        <span>{{ $case['number'] }}</span>
                        <h3>{{ $case['title'] }}</h3>
                        <p>{{ $case['text'] }}</p>
                        <strong>{{ $case['result'] }}</strong>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="delivery">
            <div class="shell">
                <header class="section-title reveal">
                    <p class="eyebrow">{{ $content['delivery']['eyebrow'] }}</p>
                    <h2>{{ $content['delivery']['title'] }}</h2>
                </header>
                <div class="steps">
                    @foreach ($content['delivery']['steps'] as $step)
                        <article class="reveal"><span>{{ $step['number'] }}</span><h3>{{ $step['title'] }}</h3><p>{{ $step['text'] }}</p></article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="offer shell reveal">
            <div>
                <p class="eyebrow">{{ $content['offer']['eyebrow'] }}</p>
                <h2>{{ $content['offer']['title'] }}</h2>
                <p>{{ $content['offer']['text'] }}</p>
            </div>
            <div class="offer-details">
                <strong>{{ $content['offer']['setup'] }}</strong>
                <span>{{ $content['offer']['monthly'] }}</span>
                <ul>
                    @foreach ($content['offer']['features'] as $feature)<li>{{ $feature }}</li>@endforeach
                </ul>
                <a class="button button-accent" href="#request">{{ $content['offer']['cta'] }}</a>
            </div>
        </section>

        <section class="request" id="request">
            <div class="shell request-layout">
                <header class="section-title reveal">
                    <p class="eyebrow">{{ $content['form']['eyebrow'] }}</p>
                    <h2>{{ $content['form']['title'] }}</h2>
                    <p>{{ $content['form']['description'] }}</p>
                </header>
                <form class="short-form reveal" data-request-form action="{{ route('openclaw-short.request') }}" method="post" novalidate>
                    @csrf
                    <div class="form-grid">
                        @foreach (['name', 'company', 'email', 'contact'] as $field)
                            <label><span>{{ $content['form']['fields'][$field]['label'] }}</span><input type="{{ $field === 'email' ? 'email' : 'text' }}" name="{{ $field }}" placeholder="{{ $content['form']['fields'][$field]['placeholder'] }}" {{ $field === 'name' ? 'required' : '' }}></label>
                        @endforeach
                    </div>
                    <p class="contact-hint">{{ $content['form']['contact_hint'] }}</p>
                    <fieldset>
                        <legend>{{ $content['form']['fields']['interests']['label'] }}</legend>
                        <div class="interest-grid">
                            @foreach ($content['form']['interests'] as $interest)
                                <label><input type="checkbox" name="interests[]" value="{{ $interest['id'] }}"><span>{{ $interest['label'] }}</span></label>
                            @endforeach
                        </div>
                    </fieldset>
                    <label><span>{{ $content['form']['fields']['message']['label'] }}</span><textarea name="message" rows="6" minlength="20" required placeholder="{{ $content['form']['fields']['message']['placeholder'] }}"></textarea></label>
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

    <footer class="short-footer shell"><strong>{{ $content['footer']['statement'] }}</strong><span>© {{ date('Y') }} {{ $content['footer']['copyright'] }}</span></footer>
    <script src="{{ asset('build/js/automation-services.js') }}" defer></script>
</body>
</html>
