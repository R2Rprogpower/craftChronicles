@props(['active', 'locale' => 'en'])

@php
    $labels = [
        'en' => ['AI Automation', 'OpenClaw for Business', 'For Developers', 'Research Assistant', 'Presentation'],
        'uk' => ['ШІ-автоматизація', 'OpenClaw для бізнесу', 'Для розробників', 'Науковий помічник', 'Презентація'],
        'ru' => ['AI-автоматизация', 'OpenClaw для бизнеса', 'Для разработчиков', 'Научный помощник', 'Презентация'],
    ][$locale];
    $links = [
        ['key' => 'automation', 'label' => $labels[0], 'route' => 'automation-landing'],
        ['key' => 'openclaw', 'label' => $labels[1], 'route' => 'openclaw-short'],
        ['key' => 'developer', 'label' => $labels[2], 'route' => 'openclaw-developer'],
        ['key' => 'research', 'label' => $labels[3], 'route' => 'openclaw-research'],
        ['key' => 'presentation', 'label' => $labels[4], 'route' => 'presentations.openclaw'],
    ];
@endphp

<header class="oc-network-header" aria-label="Навигация по решениям OpenClaw">
    <a class="oc-network-brand" href="{{ route('root') }}" aria-label="Craft Chronicles — главная">
        <span class="oc-network-mark" aria-hidden="true">CC</span>
        <span>CRAFT CHRONICLES</span>
    </a>
    <nav class="oc-network-links" aria-label="Решения OpenClaw">
        @foreach ($links as $link)
            <a href="{{ route($link['route'], ['lang' => $locale]) }}"
               @class(['is-active' => $active === $link['key']])
               @if ($active === $link['key']) aria-current="page" @endif>
                {{ $link['label'] }}
            </a>
        @endforeach
    </nav>
    <nav class="oc-language-switcher" aria-label="Language">
        @foreach (['en' => 'EN', 'uk' => 'UA', 'ru' => 'RU'] as $code => $label)
            <a href="{{ request()->fullUrlWithQuery(['lang' => $code]) }}"
               @class(['is-active' => $locale === $code])
               @if ($locale === $code) aria-current="true" @endif>{{ $label }}</a>
        @endforeach
    </nav>
</header>
