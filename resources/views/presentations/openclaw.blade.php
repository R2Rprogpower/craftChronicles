<!doctype html>
<html lang="uk">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#0b1020">
  <meta name="description" content="Практична презентація про OpenClaw для першокурсників: можливості, автоматизації, безпека та перший проєкт.">
  <meta property="og:title" content="OpenClaw — від запиту до дії">
  <meta property="og:description" content="10 слайдів про те, як перетворити рутину на керовані ШІ-процеси.">
  <title>OpenClaw — від запиту до дії</title>
  <link rel="stylesheet" href="{{ asset('css/openclaw-presentation.css') }}">
</head>
<body>
  <div class="presentation-shell" data-presentation>
    <header class="presentation-bar">
      <a class="brand" href="{{ route('root') }}" aria-label="Craft Chronicles — головна">
        <span class="brand-mark">CC</span>
        <span>CRAFT CHRONICLES</span>
      </a>
      <div class="deck-meta" aria-live="polite">
        <span>OPENCLAW / ПРАКТИЧНИЙ ВСТУП</span>
        <strong><span data-current>01</span> / 10</strong>
      </div>
      <div class="bar-actions">
        <a class="download-link" href="{{ asset('presentations/openclaw/openclaw-first-years-ua.pptx') }}" download>
          PPTX <span aria-hidden="true">↓</span>
        </a>
        <button class="icon-button" type="button" data-fullscreen aria-label="Відкрити на весь екран" title="На весь екран">
          <span aria-hidden="true">⛶</span>
        </button>
      </div>
    </header>

    <main class="stage">
      <div class="deck" data-deck tabindex="0" aria-label="Презентація OpenClaw, 10 слайдів">
        <section class="slide slide-dark slide-title is-active" data-slide aria-hidden="false">
          <div class="top-rule"></div>
          <div class="slide-content title-grid">
            <div>
              <span class="pill pill-dark">ПРАКТИЧНИЙ ВСТУП</span>
              <h1>OpenClaw</h1>
              <h2>ШІ, який не лише відповідає — а діє</h2>
              <p class="lead">Як перетворити рутину на автоматизовані процеси, а цікавість — на реальні експерименти.</p>
              <div class="title-note">10 слайдів <b>•</b> 15 хвилин <b>•</b> 1 ідея для запуску сьогодні</div>
            </div>
            <div class="signal-visual" aria-hidden="true">
              <div class="orbit orbit-large"><div class="orbit-mid"><div class="orbit-core">01</div></div></div>
              <div class="signal-card">
                <div><span>ЗАПИТ</span><b>→</b><span>ДІЯ</span><b>→</b><span>РЕЗУЛЬТАТ</span></div>
                <strong>думка → система</strong>
              </div>
            </div>
          </div>
          <div class="slide-footer">OPENCLAW • ВСТУП ДЛЯ ПЕРШОКУРСНИКІВ <span>01</span></div>
        </section>

        <section class="slide slide-light" data-slide aria-hidden="true">
          <div class="top-rule"></div>
          <div class="slide-content">
            <header class="slide-heading">
              <span>02 • ОСНОВНА ІДЕЯ</span>
              <h2>OpenClaw — це операційний шар для ШІ</h2>
              <p>Він поєднує мовну модель, інструменти, пам’ять і канали зв’язку в одного керованого агента.</p>
            </header>
            <div class="flow-grid">
              <article class="flow-card accent-purple"><b>1</b><h3>Розуміє</h3><p>природну мову та контекст задачі</p></article>
              <i>›</i>
              <article class="flow-card accent-blue"><b>2</b><h3>Планує</h3><p>послідовність кроків і перевірок</p></article>
              <i>›</i>
              <article class="flow-card accent-cyan"><b>3</b><h3>Діє</h3><p>через файли, браузер, код, сервіси</p></article>
              <i>›</i>
              <article class="flow-card accent-green"><b>4</b><h3>Повертає</h3><p>готовий результат і слід виконання</p></article>
            </div>
            <div class="compare-strip">
              <div><span>Звичайний чат</span><strong>відповідь</strong></div>
              <div><span>OpenClaw</span><strong>відповідь + дія + автоматизація</strong></div>
              <em>КЛЮЧОВА РІЗНИЦЯ</em>
            </div>
          </div>
          <div class="slide-footer">OPENCLAW • ВСТУП ДЛЯ ПЕРШОКУРСНИКІВ <span>02</span></div>
        </section>

        <section class="slide slide-dark" data-slide aria-hidden="true">
          <div class="top-rule"></div>
          <div class="slide-content">
            <header class="slide-heading">
              <span>03 • МОЖЛИВОСТІ</span>
              <h2>Що можна доручити агенту</h2>
              <p>Починайте з одного процесу. Додавайте інструменти тільки тоді, коли вони справді потрібні.</p>
            </header>
            <div class="capability-grid">
              <article class="cap-card accent-cyan"><b>01</b><h3>Дослідження</h3><p>зібрати джерела, порівняти позиції, виділити прогалини</p></article>
              <article class="cap-card accent-purple"><b>02</b><h3>Файли й документи</h3><p>створити конспект, таблицю, звіт, презентацію</p></article>
              <article class="cap-card accent-blue"><b>03</b><h3>Код і дані</h3><p>пояснити помилку, запустити аналіз, побудувати прототип</p></article>
              <article class="cap-card accent-yellow"><b>04</b><h3>Браузер</h3><p>пройти багатокроковий вебсценарій під вашим контролем</p></article>
              <article class="cap-card accent-green"><b>05</b><h3>Комунікації</h3><p>підготувати відповідь, знайти важливе, надіслати після дозволу</p></article>
              <article class="cap-card accent-red"><b>06</b><h3>Автоматизації</h3><p>нагадувати, перевіряти зміни, запускати регулярні задачі</p></article>
            </div>
          </div>
          <div class="slide-footer">OPENCLAW • ВСТУП ДЛЯ ПЕРШОКУРСНИКІВ <span>03</span></div>
        </section>

        <section class="slide slide-light" data-slide aria-hidden="true">
          <div class="top-rule"></div>
          <div class="slide-content">
            <header class="slide-heading">
              <span>04 • МЕХАНІКА</span>
              <h2>Як запит перетворюється на результат</h2>
              <p>Людина задає ціль і межі. Агент пропонує план, використовує дозволені інструменти та перевіряє результат.</p>
            </header>
            <div class="process-line">
              <article><b>01</b><h3>Ціль</h3><p>Що має змінитися?</p></article>
              <article><b>02</b><h3>Контекст</h3><p>Дані, правила, дедлайн</p></article>
              <article><b>03</b><h3>План</h3><p>Кроки та інструменти</p></article>
              <article><b>04</b><h3>Дія</h3><p>Виконання з дозволами</p></article>
              <article class="is-final"><b>05</b><h3>Перевірка</h3><p>Якість, джерела, ризики</p></article>
            </div>
            <div class="human-loop"><span>ЛЮДИНА В КОНТУРІ</span><strong>підтверджує зовнішні дії • коригує напрям • приймає фінальне рішення</strong></div>
          </div>
          <div class="slide-footer">OPENCLAW • ВСТУП ДЛЯ ПЕРШОКУРСНИКІВ <span>04</span></div>
        </section>

        <section class="slide slide-dark" data-slide aria-hidden="true">
          <div class="top-rule"></div>
          <div class="slide-content">
            <header class="slide-heading">
              <span>05 • СЦЕНАРІЙ</span>
              <h2>Один день студента з OpenClaw</h2>
              <p>Не «зроби все за мене», а «допоможи мені швидше зрозуміти, організувати й перевірити».</p>
            </header>
            <div class="timeline">
              <article class="accent-cyan"><time>08:00</time><b></b><h3>План дня</h3><p>3 пріоритети + дедлайни</p></article>
              <article class="accent-purple"><time>10:30</time><b></b><h3>Перед парою</h3><p>5 тез із теми + питання</p></article>
              <article class="accent-blue"><time>14:00</time><b></b><h3>Дослідження</h3><p>матриця джерел і суперечностей</p></article>
              <article class="accent-yellow"><time>18:00</time><b></b><h3>Лабораторна</h3><p>розбір помилки без готової «магії»</p></article>
              <article class="accent-green"><time>21:00</time><b></b><h3>Рефлексія</h3><p>що вивчив + наступний крок</p></article>
            </div>
            <div class="result-strip"><span>РЕЗУЛЬТАТ ДНЯ</span><strong>менше перемикань • більше усвідомленої роботи • видимий прогрес</strong></div>
          </div>
          <div class="slide-footer">OPENCLAW • ВСТУП ДЛЯ ПЕРШОКУРСНИКІВ <span>05</span></div>
        </section>

        <section class="slide slide-light" data-slide aria-hidden="true">
          <div class="top-rule"></div>
          <div class="slide-content">
            <header class="slide-heading">
              <span>06 • АВТОМАТИЗАЦІЯ</span>
              <h2>5 ідей, з яких легко почати</h2>
              <p>Обирайте повторювану дію з чітким входом, зрозумілим результатом і низьким ризиком.</p>
            </header>
            <div class="idea-list">
              <article><b>01</b><h3>Радар дедлайнів</h3><p>щоденна перевірка календаря → список на 7 днів</p><span>5–10 хв</span></article>
              <article><b>02</b><h3>Дайджест курсу</h3><p>посилання й нотатки → коротке резюме з питаннями</p><span>10–15 хв</span></article>
              <article><b>03</b><h3>Матриця джерел</h3><p>тема → автор, теза, метод, обмеження, цитата</p><span>15–25 хв</span></article>
              <article><b>04</b><h3>Журнал помилок</h3><p>лог або скрин → гіпотеза, тест, висновок</p><span>10–20 хв</span></article>
              <article><b>05</b><h3>Щотижневий огляд</h3><p>задачі + нотатки → прогрес і план наступного тижня</p><span>15 хв</span></article>
            </div>
            <small class="prototype-note">час на перший прототип, не «раз і назавжди»</small>
          </div>
          <div class="slide-footer">OPENCLAW • ВСТУП ДЛЯ ПЕРШОКУРСНИКІВ <span>06</span></div>
        </section>

        <section class="slide slide-dark" data-slide aria-hidden="true">
          <div class="top-rule"></div>
          <div class="slide-content">
            <header class="slide-heading">
              <span>07 • КОМУНІКАЦІЯ</span>
              <h2>Формула сильного запиту</h2>
              <p>Якість результату зростає, коли ви описуєте не лише тему, а й критерій готовності.</p>
            </header>
            <div class="prompt-formula">
              <article class="accent-purple"><h3>РОЛЬ</h3><p>Ким бути?</p></article><i>+</i>
              <article class="accent-blue"><h3>КОНТЕКСТ</h3><p>Що вже відомо?</p></article><i>+</i>
              <article class="accent-cyan"><h3>РЕЗУЛЬТАТ</h3><p>Що створити?</p></article><i>+</i>
              <article class="accent-yellow"><h3>МЕЖІ</h3><p>Що не робити?</p></article><i>+</i>
              <article class="accent-green"><h3>ПЕРЕВІРКА</h3><p>Як довести якість?</p></article>
            </div>
            <blockquote>
              <span>ПРИКЛАД</span>
              <p>«Ти — дослідницький асистент. Для першокурсника порівняй 5 надійних джерел про вплив генеративного ШІ на освіту. Створи таблицю: теза, метод, обмеження, посилання. Не вигадуй цитат. Познач суперечності та запропонуй 3 питання для дискусії».</p>
              <small>Порада: просіть показати невизначеність — це корисніше за впевнений тон.</small>
            </blockquote>
          </div>
          <div class="slide-footer">OPENCLAW • ВСТУП ДЛЯ ПЕРШОКУРСНИКІВ <span>07</span></div>
        </section>

        <section class="slide slide-light" data-slide aria-hidden="true">
          <div class="top-rule"></div>
          <div class="slide-content">
            <header class="slide-heading">
              <span>08 • ВІДПОВІДАЛЬНІСТЬ</span>
              <h2>ШІ-помічник ≠ автопілот без правил</h2>
              <p>Автоматизація посилює як користь, так і помилки. Тому межі та перевірка — частина дизайну процесу.</p>
            </header>
            <div class="safety-grid">
              <article class="accent-purple"><h3>Приватність</h3><p>Не передавайте персональні дані, ключі доступу, закриті матеріали.</p></article>
              <article class="accent-blue"><h3>Перевірка</h3><p>Звіряйте факти, цитати, формули та посилання з першоджерелами.</p></article>
              <article class="accent-red"><h3>Дозволи</h3><p>Надсилання, публікація, оплата й видалення — тільки після підтвердження.</p></article>
              <article class="accent-green"><h3>Доброчесність</h3><p>Використовуйте ШІ, щоб вчитися; не маскуйте чужу роботу під власну.</p></article>
            </div>
            <div class="pause-rule"><span>ПРАВИЛО ПАУЗИ</span><strong>Якщо дію важко скасувати або вона впливає на інших — зупиніться й підтвердьте її вручну.</strong></div>
          </div>
          <div class="slide-footer">OPENCLAW • ВСТУП ДЛЯ ПЕРШОКУРСНИКІВ <span>08</span></div>
        </section>

        <section class="slide slide-dark" data-slide aria-hidden="true">
          <div class="top-rule"></div>
          <div class="slide-content">
            <header class="slide-heading">
              <span>09 • ПРАКТИКА</span>
              <h2>Перший проєкт: «Радар дедлайнів»</h2>
              <p>Мета: раз на день отримувати короткий список подій і задач на наступні 7 днів.</p>
            </header>
            <div class="project-steps">
              <article class="accent-purple"><b>1</b><h3>Вхід</h3><p>календар або список задач</p></article>
              <article class="accent-blue"><b>2</b><h3>Правило</h3><p>лише 7 днів; сортувати за терміновістю</p></article>
              <article class="accent-cyan"><b>3</b><h3>Вихід</h3><p>3 блоки: сьогодні / скоро / пізніше</p></article>
              <article class="accent-green"><b>4</b><h3>Контроль</h3><p>нічого не змінювати без підтвердження</p></article>
            </div>
            <div class="success-rule"><span>КРИТЕРІЙ УСПІХУ</span><strong>ви нічого не пропустили, список читається за 30 секунд, помилки легко виправити</strong></div>
            <div class="mvp-note">MVP ≈ 30 хвилин</div>
          </div>
          <div class="slide-footer">OPENCLAW • ВСТУП ДЛЯ ПЕРШОКУРСНИКІВ <span>09</span></div>
        </section>

        <section class="slide slide-light slide-finale" data-slide aria-hidden="true">
          <div class="top-rule"></div>
          <div class="slide-content finale-grid">
            <div>
              <span class="pill pill-light">10 • ВАШ ХІД</span>
              <h2>7-денний експеримент</h2>
              <p class="lead">Оберіть одну повторювану задачу — і перетворіть її на керований процес.</p>
              <div class="challenge-list">
                <article><b>1</b><strong>Спостерігайте</strong><span>де губиться час</span></article>
                <article><b>2–3</b><strong>Опишіть</strong><span>вхід, вихід, межі</span></article>
                <article><b>4–5</b><strong>Зберіть MVP</strong><span>і протестуйте на 3 кейсах</span></article>
                <article><b>6</b><strong>Перевірте</strong><span>помилки та безпеку</span></article>
                <article><b>7</b><strong>Покажіть</strong><span>результат одногрупникам</span></article>
              </div>
            </div>
            <aside class="final-card">
              <div class="final-icon" aria-hidden="true">→</div>
              <h3>Не чекайте «ідеального» кейсу</h3>
              <p>Найкращий спосіб зрозуміти агентів — створити маленьку корисну автоматизацію власноруч.</p>
              <span>ПИТАННЯ → ЕКСПЕРИМЕНТ → СИСТЕМА</span>
            </aside>
          </div>
          <div class="slide-footer">OPENCLAW • ВСТУП ДЛЯ ПЕРШОКУРСНИКІВ <span>10</span></div>
        </section>
      </div>
    </main>

    <footer class="presentation-controls">
      <div class="progress-track" aria-hidden="true"><span data-progress></span></div>
      <button type="button" data-prev aria-label="Попередній слайд">←</button>
      <span class="control-hint">СТРІЛКИ / ПРОБІЛ / СВАЙП</span>
      <button type="button" data-next aria-label="Наступний слайд">→</button>
    </footer>
  </div>
  <script src="{{ asset('js/openclaw-presentation.js') }}" defer></script>
</body>
</html>
