<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Ruslan — full-stack engineer, technical mentor, and English coach.">
    <title>{{ $page === 'progress' ? 'Brand Progress' : 'Ruslan — Engineer & Mentor' }}</title>
    @viteReactRefresh
    @vite('resources/js/brand-platform/main.jsx')
</head>
<body>
    <div id="brand-platform" data-page="{{ $page }}"></div>
    <script>window.__BRAND_PLATFORM__ = {{ Illuminate\Support\Js::from($payload) }};</script>
</body>
</html>
