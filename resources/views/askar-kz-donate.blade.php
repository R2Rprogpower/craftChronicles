<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta name="theme-color" content="#0a0b0a">
  <meta name="description" content="Все способы поддержать стримера AASKAR в одном месте.">
  <meta property="og:title" content="Поддержать AASKAR">
  <meta property="og:description" content="Выберите удобную валюту и сервис для доната.">
  <meta property="og:image" content="https://static-cdn.jtvnw.net/jtv_user_pictures/d6ad972c-9dcd-409f-9a1d-da36f2960c7b-profile_image-300x300.png">
  <title>Поддержать AASKAR — Craft Chronicles</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Unbounded:wght@600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/askar-kz-donate.css') }}">
</head>
<body>
  <div class="grain" aria-hidden="true"></div>
  <main class="donate-shell">
    <header class="profile">
      <a class="craft" href="{{ route('askar-kz') }}" aria-label="Открыть профиль AASKAR">
        <span>CC</span> CRAFT CHRONICLES
      </a>
      <div class="avatar-wrap">
        <div class="orbit" aria-hidden="true"></div>
        <img src="https://static-cdn.jtvnw.net/jtv_user_pictures/d6ad972c-9dcd-409f-9a1d-da36f2960c7b-profile_image-300x300.png" alt="Аватар AASKAR" width="112" height="112">
        <span class="live-dot" aria-label="Twitch Affiliate"></span>
      </div>
      <p class="eyebrow">ПОДДЕРЖАТЬ СТРИМЕРА</p>
      <h1>AAS<span>KAR</span></h1>
      <p class="intro">Выбери удобную валюту — дальше откроется официальный сервис оплаты.</p>
    </header>

    <nav class="donate-list" aria-label="Способы поддержки">
      <a class="donate-card streamlabs" href="https://streamlabs.com/askarick" target="_blank" rel="noopener noreferrer">
        <span class="currency">$</span>
        <span class="copy"><strong>StreamLabs</strong><small>Карты и международные платежи</small></span>
        <span class="arrow" aria-hidden="true">↗</span>
      </a>
      <a class="donate-card donatello" href="https://donatello.to/askar" target="_blank" rel="noopener noreferrer">
        <span class="currency">₴</span>
        <span class="copy"><strong>Donatello</strong><small>Поддержка в гривне</small></span>
        <span class="arrow" aria-hidden="true">↗</span>
      </a>
      <a class="donate-card tribute-rub" href="https://t.me/tribute/app?startapp=d913" target="_blank" rel="noopener noreferrer">
        <span class="currency">₽</span>
        <span class="copy"><strong>Tribute</strong><small>Оплата в рублях через Telegram</small></span>
        <span class="arrow" aria-hidden="true">↗</span>
      </a>
      <a class="donate-card tribute-eur" href="https://t.me/tribute/app?startapp=d8ZT" target="_blank" rel="noopener noreferrer">
        <span class="currency">€</span>
        <span class="copy"><strong>Tribute</strong><small>Оплата в евро через Telegram</small></span>
        <span class="arrow" aria-hidden="true">↗</span>
      </a>
      <a class="donate-card crypto" href="https://tourniquet.app/donate/Askar" target="_blank" rel="noopener noreferrer">
        <span class="currency">₿</span>
        <span class="copy"><strong>КриптоДонат</strong><small>Поддержка криптовалютой</small></span>
        <span class="arrow" aria-hidden="true">↗</span>
      </a>
    </nav>

    <footer>
      <p>Спасибо за поддержку эфиров <span>♥</span></p>
      <a href="https://www.twitch.tv/aaskar" target="_blank" rel="noopener noreferrer">twitch.tv/aaskar ↗</a>
    </footer>
  </main>
</body>
</html>
