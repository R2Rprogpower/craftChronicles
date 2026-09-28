<!doctype html>
<html lang="{{ $locale }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#0b1020">
  <meta name="description" content="Практическая презентация об OpenClaw для первокурсников: возможности, автоматизация, безопасность и первый проект.">
  <meta property="og:title" content="OpenClaw – от запроса к действию">
  <meta property="og:description" content="10 слайдов о том, как превратить рутину в управляемые ИИ-процессы.">
  <title>OpenClaw – от запроса к действию</title>
  <link rel="stylesheet" href="{{ asset('css/openclaw-presentation.css') }}">
  <link rel="stylesheet" href="{{ asset('build/css/openclaw-network-header.css') }}">
</head>
<body>
  <x-openclaw-network-header active="presentation" :locale="$locale" />
  <div class="presentation-shell" data-presentation>
    <header class="presentation-bar">
      <a class="brand" href="{{ route('root') }}" aria-label="Craft Chronicles – главная">
        <span class="brand-mark">CC</span>
        <span>CRAFT CHRONICLES</span>
      </a>
      <div class="deck-meta" aria-live="polite">
        <span>OPENCLAW / ПРАКТИЧЕСКОЕ ВВЕДЕНИЕ</span>
        <strong><span data-current>01</span> / 10</strong>
      </div>
      <div class="bar-actions">
        <a class="download-link" href="{{ asset('presentations/openclaw/openclaw-first-years-ua.pptx') }}" download>
          PPTX <span aria-hidden="true">↓</span>
        </a>
        <button class="icon-button" type="button" data-fullscreen aria-label="Открыть во весь экран" title="Во весь экран">
          <span aria-hidden="true">⛶</span>
        </button>
      </div>
    </header>

    <main class="stage">
      <div class="deck" data-deck tabindex="0" aria-label="Презентация OpenClaw, 10 слайдов">
        <section class="slide slide-dark slide-title is-active" data-slide aria-hidden="false">
          <div class="top-rule"></div>
          <div class="slide-content title-grid">
            <div>
              <span class="pill pill-dark">ПРАКТИЧЕСКОЕ ВВЕДЕНИЕ</span>
              <h1>OpenClaw</h1>
              <h2>ИИ, который не только отвечает, а действует</h2>
              <p class="lead">Как превратить рутину в автоматизированные процессы, а любопытство – в реальные эксперименты.</p>
              <div class="title-note">10 слайдов <b>•</b> 15 минут <b>•</b> 1 идея для запуска сегодня</div>
            </div>
            <div class="signal-visual" aria-hidden="true">
              <div class="orbit orbit-large"><div class="orbit-mid"><div class="orbit-core">01</div></div></div>
              <div class="signal-card">
                <div><span>СПРОС</span><b>→</b><span>ДЕЙСТВИЕ</span><b>→</b><span>РЕЗУЛЬТАТ</span></div>
                <strong>мнение → система</strong>
              </div>
            </div>
          </div>
          <div class="slide-footer">OPENCLAW • ВВЕДЕНИЕ ДЛЯ ПЕРЕКУРСНИКОВ <span>01</span></div>
        </section>

        <section class="slide slide-light" data-slide aria-hidden="true">
          <div class="top-rule"></div>
          <div class="slide-content">
            <header class="slide-heading">
              <span>02 • ОСНОВНАЯ ИДЕЯ</span>
              <h2>OpenClaw — это операционный уровень для искусственного интеллекта.</h2>
              <p>Он объединяет языковую модель, инструменты, память и каналы связи у одного управляемого агента.</p>
            </header>
            <div class="flow-grid">
              <article class="flow-card accent-purple"><b>1</b><h3>Понимает</h3><p>естественный язык и контекст задачи</p></article>
              <i>›</i>
              <article class="flow-card accent-blue"><b>2</b><h3>Планы</h3><p>последовательность шагов и проверок</p></article>
              <i>›</i>
              <article class="flow-card accent-cyan"><b>3</b><h3>Действует</h3><p>через файлы, браузер, код, сервисы</p></article>
              <i>›</i>
              <article class="flow-card accent-green"><b>4</b><h3>Возвращает</h3><p>готовый результат и след выполнения</p></article>
            </div>
            <div class="compare-strip">
              <div><span>Обычный чат</span><strong>ответ</strong></div>
              <div><span>OpenClaw</span><strong>ответ + действие + автоматизация</strong></div>
              <em>КЛЮЧЕВАЯ РАЗНИЦА</em>
            </div>
          </div>
          <div class="slide-footer">OPENCLAW • ВВЕДЕНИЕ ДЛЯ ПЕРЕКУРСНИКОВ <span>02</span></div>
        </section>

        <section class="slide slide-dark" data-slide aria-hidden="true">
          <div class="top-rule"></div>
          <div class="slide-content">
            <header class="slide-heading">
              <span>03 • ВОЗМОЖНОСТИ</span>
              <h2>Что можно поручить агенту</h2>
              <p>Начинайте с одного процесса. Добавляйте инструменты только тогда, когда они действительно нужны.</p>
            </header>
            <div class="capability-grid">
              <article class="cap-card accent-cyan"><b>01</b><h3>Исследование</h3><p>собрать источники, сравнить позиции, выделить пробелы</p></article>
              <article class="cap-card accent-purple"><b>02</b><h3>Файлы и документы</h3><p>создать конспект, таблицу, отчет, презентацию</p></article>
              <article class="cap-card accent-blue"><b>03</b><h3>Код и данные</h3><p>объяснить ошибку, запустить анализ, построить прототип</p></article>
              <article class="cap-card accent-yellow"><b>04</b><h3>Браузер</h3><p>пройти многошаговый вебсценарий под вашим контролем</p></article>
              <article class="cap-card accent-green"><b>05</b><h3>Коммуникации</h3><p>подготовить ответ, найти важное, отправить после разрешения</p></article>
              <article class="cap-card accent-red"><b>06</b><h3>Автоматизация</h3><p>напоминать, проверять изменения, запускать регулярные задачи</p></article>
            </div>
          </div>
          <div class="slide-footer">OPENCLAW • ВВЕДЕНИЕ ДЛЯ ПЕРЕКУРСНИКОВ <span>03</span></div>
        </section>

        <section class="slide slide-light" data-slide aria-hidden="true">
          <div class="top-rule"></div>
          <div class="slide-content">
            <header class="slide-heading">
              <span>04 • МЕХАНИКА</span>
              <h2>Как запрос превращается в результат</h2>
              <p>Человек задает цель и пределы. Агент предлагает план, использует разрешенные инструменты и проверяет результат.</p>
            </header>
            <div class="process-line">
              <article><b>01</b><h3>Цель</h3><p>Что должно измениться?</p></article>
              <article><b>02</b><h3>Контекст</h3><p>Данные, правила, дедлайн</p></article>
              <article><b>03</b><h3>План</h3><p>Шаги и инструменты</p></article>
              <article><b>04</b><h3>Действие</h3><p>Выполнение с разрешениями</p></article>
              <article class="is-final"><b>05</b><h3>Проверка</h3><p>Качество, источники, риски</p></article>
            </div>
            <div class="human-loop"><span>ЧЕЛОВЕК В КОНТУРЕ</span><strong>подтверждает внешние действия • корректирует направление • принимает финальное решение</strong></div>
          </div>
          <div class="slide-footer">OPENCLAW • ВВЕДЕНИЕ ДЛЯ ПЕРЕКУРСНИКОВ <span>04</span></div>
        </section>

        <section class="slide slide-dark" data-slide aria-hidden="true">
          <div class="top-rule"></div>
          <div class="slide-content">
            <header class="slide-heading">
              <span>05 • СЦЕНАРИЙ</span>
              <h2>Один день студента с OpenClaw</h2>
              <p>Не «сделай все за меня», а «помоги мне скорее понять, организовать и проверить».</p>
            </header>
            <div class="timeline">
              <article class="accent-cyan"><time>08:00</time><b></b><h3>План дня</h3><p>3 приоритета + дедлайны</p></article>
              <article class="accent-purple"><time>10:30</time><b></b><h3>Перед парой</h3><p>5 тезисов по теме + вопрос</p></article>
              <article class="accent-blue"><time>14:00</time><b></b><h3>Исследование</h3><p>матрица источников и противоречий</p></article>
              <article class="accent-yellow"><time>18:00</time><b></b><h3>Лабораторная</h3><p>разбор ошибки без готовой магии</p></article>
              <article class="accent-green"><time>21:00</time><b></b><h3>Рефлексия</h3><p>что изучил + следующий шаг</p></article>
            </div>
            <div class="result-strip"><span>РЕЗУЛЬТАТ ДНЯ</span><strong>меньше переключений • больше осознанной работы • видимый прогресс</strong></div>
          </div>
          <div class="slide-footer">OPENCLAW • ВВЕДЕНИЕ ДЛЯ ПЕРЕКУРСНИКОВ <span>05</span></div>
        </section>

        <section class="slide slide-light" data-slide aria-hidden="true">
          <div class="top-rule"></div>
          <div class="slide-content">
            <header class="slide-heading">
              <span>06 • АВТОМАТИЗАЦИЯ</span>
              <h2>5 простых идей, с которых можно начать</h2>
              <p>Выбирайте повторяющееся действие с четким входом, понятным результатом и низким риском.</p>
            </header>
            <div class="idea-list">
              <article><b>01</b><h3>Крайний радар</h3><p>ежедневная проверка календаря → список на 7 дней</p><span>5–10 мин</span></article>
              <article><b>02</b><h3>Дайджест курса</h3><p>ссылки и заметки → краткое резюме с вопросами</p><span>10–15 мин</span></article>
              <article><b>03</b><h3>Матрица источников</h3><p>тема → автор, тезис, метод, ограничение, цитата</p><span>15–25 мин</span></article>
              <article><b>04</b><h3>Журнал ошибок</h3><p>лог или скрин → гипотеза, тест, заключение</p><span>10–20 мин</span></article>
              <article><b>05</b><h3>Еженедельный обзор</h3><p>задачи + заметки → прогресс и план на следующей неделе</p><span>15 мин</span></article>
            </div>
            <small class="prototype-note">время на первый прототип, не «раз и навсегда»</small>
          </div>
          <div class="slide-footer">OPENCLAW • ВВЕДЕНИЕ ДЛЯ ПЕРЕКУРСНИКОВ <span>06</span></div>
        </section>

        <section class="slide slide-dark" data-slide aria-hidden="true">
          <div class="top-rule"></div>
          <div class="slide-content">
            <header class="slide-heading">
              <span>07 • КОММУНИКАЦИЯ</span>
              <h2>Формула сильного запроса</h2>
              <p>Качество результата увеличивается, когда вы описываете не только тему, но и критерий готовности.</p>
            </header>
            <div class="prompt-formula">
              <article class="accent-purple"><h3>РОЛЬ</h3><p>Кем быть?</p></article><i>+</i>
              <article class="accent-blue"><h3>КОНТЕКСТ</h3><p>Что уже известно?</p></article><i>+</i>
              <article class="accent-cyan"><h3>РЕЗУЛЬТАТ</h3><p>Что сотворить?</p></article><i>+</i>
              <article class="accent-yellow"><h3>ГРАНИЦЫ</h3><p>Чего не делать?</p></article><i>+</i>
              <article class="accent-green"><h3>ПРОВЕРКА</h3><p>Как доказать качество?</p></article>
            </div>
            <blockquote>
              <span>ПРИМЕР</span>
              <p>«Вы научный сотрудник. Для первокурсника сравните 5 достоверных источников о влиянии генеративного ИИ на образование. Составьте таблицу: тезис, метод, ограничения, ссылки. Не придумывайте цитаты. Отметьте противоречия и предложите 3 вопроса для обсуждения.</p>
              <small>Совет: просите показать неопределенность — это полезнее уверенного тона.</small>
            </blockquote>
          </div>
          <div class="slide-footer">OPENCLAW • ВВЕДЕНИЕ ДЛЯ ПЕРЕКУРСНИКОВ <span>07</span></div>
        </section>

        <section class="slide slide-light" data-slide aria-hidden="true">
          <div class="top-rule"></div>
          <div class="slide-content">
            <header class="slide-heading">
              <span>08 • ОТВЕТСТВЕННОСТЬ</span>
              <h2>ШИ-помощник ≠ автопилот без правил</h2>
              <p>Автоматизация усугубляет как пользу, так и ошибки. Поэтому границы и проверка – часть дизайна процесса.</p>
            </header>
            <div class="safety-grid">
              <article class="accent-purple"><h3>Конфиденциальность</h3><p>Не передавайте персональные данные, ключи доступа, закрытые материалы.</p></article>
              <article class="accent-blue"><h3>Проверка</h3><p>Сверяйте факты, цитаты, формулы и ссылки с первоисточниками.</p></article>
              <article class="accent-red"><h3>Разрешения</h3><p>Отправка, публикация, оплата и удаление — только после подтверждения.</p></article>
              <article class="accent-green"><h3>Добродетель</h3><p>Используйте ИИ, чтобы учиться; не маскируйте чужую работу под собственную.</p></article>
            </div>
            <div class="pause-rule"><span>ПРАВИЛО ПАУЗЫ</span><strong>Если действие трудно отменить или оно влияет на других, остановитесь и подтвердите его вручную.</strong></div>
          </div>
          <div class="slide-footer">OPENCLAW • ВВЕДЕНИЕ ДЛЯ ПЕРЕКУРСНИКОВ <span>08</span></div>
        </section>

        <section class="slide slide-dark" data-slide aria-hidden="true">
          <div class="top-rule"></div>
          <div class="slide-content">
            <header class="slide-heading">
              <span>09 • ПРАКТИКА</span>
              <h2>Первый проект: «Радар дедлайнов»</h2>
              <p>Цель: ежедневно получать краткий список событий и задач на следующие 7 дней.</p>
            </header>
            <div class="project-steps">
              <article class="accent-purple"><b>1</b><h3>Вход</h3><p>календарь или список задач</p></article>
              <article class="accent-blue"><b>2</b><h3>Правило</h3><p>всего 7 дней; сортировать по срочности</p></article>
              <article class="accent-cyan"><b>3</b><h3>Выход</h3><p>3 блока: сегодня / скоро / позже</p></article>
              <article class="accent-green"><b>4</b><h3>Контроль</h3><p>ничего не менять без подтверждения</p></article>
            </div>
            <div class="success-rule"><span>КРИТЕРИЙ УСПЕХА</span><strong>вы ничего не пропустили, список читается за 30 секунд, ошибки легко исправить</strong></div>
            <div class="mvp-note">MVP ≈ 30 минут</div>
          </div>
          <div class="slide-footer">OPENCLAW • ВВЕДЕНИЕ ДЛЯ ПЕРЕКУРСНИКОВ <span>09</span></div>
        </section>

        <section class="slide slide-light slide-finale" data-slide aria-hidden="true">
          <div class="top-rule"></div>
          <div class="slide-content finale-grid">
            <div>
              <span class="pill pill-light">10 • ВАШ ХОД</span>
              <h2>7-дневный эксперимент</h2>
              <p class="lead">Выберите одну повторяющуюся задачу и превратите ее в управляемый процесс.</p>
              <div class="challenge-list">
                <article><b>1</b><strong>Наблюдайте</strong><span>где теряется время</span></article>
                <article><b>2–3</b><strong>Опишите</strong><span>вход, выход, границы</span></article>
                <article><b>4–5</b><strong>Соберите MVP</strong><span>и протестируйте на 3 кейсах</span></article>
                <article><b>6</b><strong>Проверьте</strong><span>ошибки и безопасность</span></article>
                <article><b>7</b><strong>Покажите</strong><span>результат одногруппникам</span></article>
              </div>
            </div>
            <aside class="final-card">
              <div class="final-icon" aria-hidden="true">→</div>
              <h3>Не ждите «идеального» кейса</h3>
              <p>Лучший способ понять агентов – создать маленькую полезную автоматизацию собственноручно.</p>
              <span>ВОПРОС → ЭКСПЕРИМЕНТ → СИСТЕМА</span>
            </aside>
          </div>
          <div class="slide-footer">OPENCLAW • ВВЕДЕНИЕ ДЛЯ ПЕРЕКУРСНИКОВ <span>10</span></div>
        </section>
      </div>
    </main>

    <footer class="presentation-controls">
      <div class="progress-track" aria-hidden="true"><span data-progress></span></div>
      <button type="button" data-prev aria-label="Предыдущий слайд">←</button>
      <span class="control-hint">СТРЕЛКИ / ПРОБЕЛ / СВАЙП</span>
      <button type="button" data-next aria-label="Следующий слайд">→</button>
    </footer>
  </div>
  <script src="{{ asset('js/openclaw-presentation.js') }}" defer></script>
</body>
</html>
