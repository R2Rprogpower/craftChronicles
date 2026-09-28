@props(['active'])

@php
    $links = [
        ['key' => 'automation', 'label' => 'AI-автоматизация', 'route' => 'automation-landing'],
        ['key' => 'openclaw', 'label' => 'OpenClaw для бизнеса', 'route' => 'openclaw-short'],
        ['key' => 'developer', 'label' => 'Для разработчиков', 'route' => 'openclaw-developer'],
        ['key' => 'research', 'label' => 'Научный помощник', 'route' => 'openclaw-research'],
        ['key' => 'presentation', 'label' => 'Презентация', 'route' => 'presentations.openclaw'],
    ];
@endphp

<header class="oc-network-header" aria-label="Навигация по решениям OpenClaw">
    <a class="oc-network-brand" href="{{ route('root') }}" aria-label="Craft Chronicles — главная">
        <span class="oc-network-mark" aria-hidden="true">CC</span>
        <span>CRAFT CHRONICLES</span>
    </a>
    <nav class="oc-network-links" aria-label="Решения OpenClaw">
        @foreach ($links as $link)
            <a href="{{ route($link['route']) }}"
               @class(['is-active' => $active === $link['key']])
               @if ($active === $link['key']) aria-current="page" @endif>
                {{ $link['label'] }}
            </a>
        @endforeach
    </nav>
</header>
