<!doctype html>
<html lang="{{ $locale }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#07110f">
    <meta name="description" content="OpenClaw як науковий помічник: індексування великих бібліотек, пошук за змістом, відповіді з цитатами та дослідницькі огляди.">
    <title>OpenClaw Research – науковий помічник для великих бібліотек</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Source+Serif+4:opsz,wght@8..60,600;8..60,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('build/css/openclaw-research.css') }}">
    <link rel="stylesheet" href="{{ asset('build/css/openclaw-network-header.css') }}">
</head>
<body>
    <x-openclaw-network-header active="research" :locale="$locale" />

    <main>
        <section class="research-hero page-shell">
            <div class="hero-copy">
                <p class="eyebrow"><span></span> OpenClaw Research</p>
                <h1>Вся бібліотека -<br><em>в одному дослідному діалозі.</em></h1>
                <p class="hero-lead">Науковий помічник індексує книги, статті, архіви та рукописи, знаходить зв'язки між джерелами і повертає відповіді, що перевіряються з точними цитатами.</p>
                <div class="hero-actions">
                    <a class="button button-primary" href="#pilot">Обговорити пілот <span>↗</span></a>
                    <a class="button button-secondary" href="#architecture">Як це працює <span>↓</span></a>
                </div>
                <ul class="hero-notes">
                    <li>Працює з вашою закритою колекцією</li>
                    <li>Кожна теза прив'язана до джерела</li>
                    <li>Дослідник контролює висновки</li>
                </ul>
            </div>

            <div class="library-console" aria-label="Приклад відповіді наукового помічника">
                <div class="console-top"><span>RESEARCH SESSION / 024</span><i></i><i></i><i></i></div>
                <div class="query"><span>ЗАПИТ</span><p>Як змінювалося поняття «авторства» у роботах 1920–1970 років?</p></div>
                <div class="search-state">
                    <div><strong>12 480</strong><span>документів перевірено</span></div>
                    <div><strong>38</strong><span>релевантних фрагментів</span></div>
                </div>
                <div class="answer">
                    <span>СИНТЕЗ</span>
                    <p>У корпусі простежуються три зрушення: від індивідуального творця — до функції тексту, потім до розподіленого виробництва знання.</p>
                    <ol>
                        <li><b>[12]</b> Беньямін, 1936 · с. 27</li>
                        <li><b>[19]</b> Барт, 1967 · с. 4</li>
                        <li><b>[31]</b> Фуко, 1969 · с. 14</li>
                    </ol>
                </div>
                <div class="console-footer"><span>✓ посилання перевірені</span><span>експорт: DOCX · CSV · BIB</span></div>
            </div>
        </section>

        <section class="signal-strip">
            <div class="page-shell signal-grid">
                <article><strong>Мільйони сторінок</strong><span>єдиний індекс замість десятків розрізнених архівів</span></article>
                <article><strong>Відповіді із доказами</strong><span>сторінка, фрагмент та метадані для кожного затвердження</span></article>
                <article><strong>Ваші правила доступу</strong><span>колекції, ролі, журнали запитів та локальне розгортання</span></article>
            </div>
        </section>

        <section class="research-section page-shell" id="architecture">
            <header class="section-heading">
                <p class="eyebrow">Не просто чат із PDF</p>
                <h2>Дослідницька система, яка знає походження кожного факту</h2>
                <p>OpenClaw з'єднує конвеєр підготовки колекції, гібридний пошук та інструменти аналізу в один відтворюваний процес.</p>
            </header>
            <div class="pipeline">
                <article><span>01</span><h3>Прийом колекції</h3><p>PDF, EPUB, скани, каталоги, нотатки та бази даних потрапляють у кероване сховище.</p><small>Файли · API · хмари · NAS</small></article>
                <article><span>02</span><h3>Розпізнавання</h3><p>OCR, очищення, розмітка структури, визначення мови та збереження координат сторінки.</p><small>Текст · таблиці · виноски · ілюстрації</small></article>
                <article><span>03</span><h3>Науковий індекс</h3><p>Повнотекстовий та семантичний пошук доповнюються авторами, датами, темами та зв'язками.</p><small>Ключові слова + зміст + метадані</small></article>
                <article><span>04</span><h3>Агент дослідження</h3><p>Планує пошук, порівнює джерела, відзначає протиріччя та збирає відповідь із цитатами.</p><small>Контроль за людиною на кожному висновку</small></article>
            </div>
        </section>

        <section class="research-section workflow-section">
            <div class="page-shell two-column">
                <header class="section-heading sticky-heading">
                    <p class="eyebrow">Робочі сценарії</p>
                    <h2>Від питання до результату, що перевіряється</h2>
                    <p>Помічник не заміняє дослідника. Він скорочує механічну роботу та залишає людині інтерпретацію.</p>
                </header>
                <div class="workflow-list">
                    <article><b>01</b><div><h3>Огляд літератури</h3><p>Збирає позиції на тему, групує школи, показує розбіжності і прогалини у корпусі.</p></div><span>матриця джерел</span></article>
                    <article><b>02</b><div><h3>Пошук прихованих зв'язків</h3><p>Знаходить, де різні автори описують одну ідею різними термінами, мовами чи десятиліттями.</p></div><span>граф зв'язків</span></article>
                    <article><b>03</b><div><h3>Перевірка гіпотези</h3><p>Шукає підтверджуючі та спростовуючі фрагменти, фіксуючи критерії пошуку та обмеження.</p></div><span>evidence table</span></article>
                    <article><b>04</b><div><h3>Підготовка публікації</h3><p>Створює конспект, хронологію, картки цитат та бібліографію у потрібному форматі.</p></div><span>DOCX · CSV · BibTeX</span></article>
                </div>
            </div>
        </section>

        <section class="research-section page-shell">
            <header class="section-heading compact-heading">
                <p class="eyebrow">Для різних колекцій</p>
                <h2>Одна платформа - різні дослідні середовища</h2>
            </header>
            <div class="use-case-grid">
                <article><span>01</span><h3>Університетська бібліотека</h3><p>Єдиний пошук за книгами, дисертаціями, статтями та внутрішніми архівами кафедр.</p></article>
                <article><span>02</span><h3>Історичний архів</h3><p>OCR рукописів та сканів, пошук варіантів імен, подій та географічних згадок.</p></article>
                <article><span>03</span><h3>R&D-команда</h3><p>Моніторинг нових публікацій, порівняння методів та карта доказів з продуктової гіпотези.</p></article>
                <article><span>04</span><h3>Корпоративне знання</h3><p>Технічні посібники, звіти та дослідження стають доступними з урахуванням ролей.</p></article>
            </div>
        </section>

        <section class="research-section integrity-section">
            <div class="page-shell integrity-layout">
                <div>
                    <p class="eyebrow">Наукова сумлінність</p>
                    <h2>Система показує докази – і чесно повідомляє вам, коли доказів недостатньо.</h2>
                </div>
                <div class="integrity-grid">
                    <article><i>✓</i><h3>Цитата до сторінки</h3><p>Оригінальний фрагмент відкривається поряд із відповіддю.</p></article>
                    <article><i>✓</i><h3>Межі корпусу</h3><p>У відповіді видно, які колекції та періоди були перевірені.</p></article>
                    <article><i>✓</i><h3>Поділ факту та висновку</h3><p>Система позначає прямі докази та інтерпретації.</p></article>
                    <article><i>✓</i><h3>Авторські права</h3><p>Доступ та видавання повних текстів слідують ліцензіям вашої колекції.</p></article>
                </div>
            </div>
        </section>

        <section class="research-section page-shell" id="pilot">
            <div class="pilot-card">
                <div>
                    <p class="eyebrow">Пілотний проект</p>
                    <h2>Почніть з однієї колекції та одного дослідницького питання.</h2>
                    <p>Визначимо джерела, зберемо тестовий індекс, налаштуємо критерії якості та покажемо відповіді на реальних матеріалах вашої команди.</p>
                </div>
                <div class="pilot-steps">
                    <p><span>01</span> Аудит колекції та прав доступу</p>
                    <p><span>02</span> Індексація репрезентативної вибірки</p>
                    <p><span>03</span> Набір контрольних питань та оцінка цитат</p>
                    <p><span>04</span> План масштабування та експлуатації</p>
                    <a class="button button-primary" href="{{ route('openclaw-short', ['lang' => $locale]) }}#request">Обговорити колекцію <span>↗</span></a>
                </div>
            </div>
        </section>
    </main>

    <footer class="research-footer page-shell">
        <strong>OPENCLAW RESEARCH</strong>
        <span>Науковий пошук, який можна перевірити.</span>
        <span>© {{ date('Y') }} Craft Chronicles</span>
    </footer>
</body>
</html>
