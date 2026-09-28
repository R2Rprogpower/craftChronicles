<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#07110f">
    <meta name="description" content="OpenClaw как научный помощник: индексирование больших библиотек, поиск по смыслу, ответы с цитатами и исследовательские обзоры.">
    <title>OpenClaw Research — научный помощник для больших библиотек</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Source+Serif+4:opsz,wght@8..60,600;8..60,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('build/css/openclaw-research.css') }}">
    <link rel="stylesheet" href="{{ asset('build/css/openclaw-network-header.css') }}">
</head>
<body>
    <x-openclaw-network-header active="research" />

    <main>
        <section class="research-hero page-shell">
            <div class="hero-copy">
                <p class="eyebrow"><span></span> OpenClaw Research</p>
                <h1>Вся библиотека —<br><em>в одном исследовательском диалоге.</em></h1>
                <p class="hero-lead">Научный помощник индексирует книги, статьи, архивы и рукописи, находит связи между источниками и возвращает проверяемые ответы с точными цитатами.</p>
                <div class="hero-actions">
                    <a class="button button-primary" href="#pilot">Обсудить пилот <span>↗</span></a>
                    <a class="button button-secondary" href="#architecture">Как это работает <span>↓</span></a>
                </div>
                <ul class="hero-notes">
                    <li>Работает с вашей закрытой коллекцией</li>
                    <li>Каждый тезис привязан к источнику</li>
                    <li>Исследователь контролирует выводы</li>
                </ul>
            </div>

            <div class="library-console" aria-label="Пример ответа научного помощника">
                <div class="console-top"><span>RESEARCH SESSION / 024</span><i></i><i></i><i></i></div>
                <div class="query"><span>ЗАПРОС</span><p>Как менялось понятие «авторства» в работах 1920–1970 годов?</p></div>
                <div class="search-state">
                    <div><strong>12 480</strong><span>документов проверено</span></div>
                    <div><strong>38</strong><span>релевантных фрагментов</span></div>
                </div>
                <div class="answer">
                    <span>СИНТЕЗ</span>
                    <p>В корпусе прослеживаются три сдвига: от индивидуального создателя — к функции текста, затем к распределённому производству знания…</p>
                    <ol>
                        <li><b>[12]</b> Беньямин, 1936 · с. 27</li>
                        <li><b>[19]</b> Барт, 1967 · с. 4</li>
                        <li><b>[31]</b> Фуко, 1969 · с. 14</li>
                    </ol>
                </div>
                <div class="console-footer"><span>✓ ссылки проверены</span><span>экспорт: DOCX · CSV · BIB</span></div>
            </div>
        </section>

        <section class="signal-strip">
            <div class="page-shell signal-grid">
                <article><strong>Миллионы страниц</strong><span>единый индекс вместо десятков разрозненных архивов</span></article>
                <article><strong>Ответы с доказательствами</strong><span>страница, фрагмент и метаданные для каждого утверждения</span></article>
                <article><strong>Ваши правила доступа</strong><span>коллекции, роли, журналы запросов и локальное развёртывание</span></article>
            </div>
        </section>

        <section class="research-section page-shell" id="architecture">
            <header class="section-heading">
                <p class="eyebrow">Не просто чат с PDF</p>
                <h2>Исследовательская система, которая знает происхождение каждого факта</h2>
                <p>OpenClaw соединяет конвейер подготовки коллекции, гибридный поиск и инструменты анализа в один воспроизводимый процесс.</p>
            </header>
            <div class="pipeline">
                <article><span>01</span><h3>Приём коллекции</h3><p>PDF, EPUB, сканы, каталоги, заметки и базы данных попадают в управляемое хранилище.</p><small>Файлы · API · облака · NAS</small></article>
                <article><span>02</span><h3>Распознавание</h3><p>OCR, очистка, разметка структуры, определение языка и сохранение координат страницы.</p><small>Текст · таблицы · сноски · иллюстрации</small></article>
                <article><span>03</span><h3>Научный индекс</h3><p>Полнотекстовый и семантический поиск дополняются авторами, датами, темами и связями.</p><small>Ключевые слова + смысл + метаданные</small></article>
                <article><span>04</span><h3>Агент исследования</h3><p>Планирует поиск, сравнивает источники, отмечает противоречия и собирает ответ с цитатами.</p><small>Контроль человеком на каждом выводе</small></article>
            </div>
        </section>

        <section class="research-section workflow-section">
            <div class="page-shell two-column">
                <header class="section-heading sticky-heading">
                    <p class="eyebrow">Рабочие сценарии</p>
                    <h2>От вопроса до проверяемого результата</h2>
                    <p>Помощник не подменяет исследователя. Он сокращает механическую работу и оставляет человеку интерпретацию.</p>
                </header>
                <div class="workflow-list">
                    <article><b>01</b><div><h3>Обзор литературы</h3><p>Собирает позиции по теме, группирует школы, показывает расхождения и пробелы в корпусе.</p></div><span>матрица источников</span></article>
                    <article><b>02</b><div><h3>Поиск скрытых связей</h3><p>Находит, где разные авторы описывают одну идею разными терминами, языками или десятилетиями.</p></div><span>граф связей</span></article>
                    <article><b>03</b><div><h3>Проверка гипотезы</h3><p>Ищет подтверждающие и опровергающие фрагменты, фиксируя критерии поиска и ограничения.</p></div><span>evidence table</span></article>
                    <article><b>04</b><div><h3>Подготовка публикации</h3><p>Создаёт конспект, хронологию, карточки цитат и библиографию в нужном формате.</p></div><span>DOCX · CSV · BibTeX</span></article>
                </div>
            </div>
        </section>

        <section class="research-section page-shell">
            <header class="section-heading compact-heading">
                <p class="eyebrow">Для разных коллекций</p>
                <h2>Одна платформа — разные исследовательские среды</h2>
            </header>
            <div class="use-case-grid">
                <article><span>01</span><h3>Университетская библиотека</h3><p>Единый поиск по книгам, диссертациям, статьям и внутренним архивам кафедр.</p></article>
                <article><span>02</span><h3>Исторический архив</h3><p>OCR рукописей и сканов, поиск вариантов имён, событий и географических упоминаний.</p></article>
                <article><span>03</span><h3>R&amp;D-команда</h3><p>Мониторинг новых публикаций, сравнение методов и карта доказательств по продуктовой гипотезе.</p></article>
                <article><span>04</span><h3>Корпоративное знание</h3><p>Технические руководства, отчёты и исследования становятся доступными с учётом ролей.</p></article>
            </div>
        </section>

        <section class="research-section integrity-section">
            <div class="page-shell integrity-layout">
                <div>
                    <p class="eyebrow">Научная добросовестность</p>
                    <h2>Система показывает доказательства — и честно сообщает, когда их недостаточно.</h2>
                </div>
                <div class="integrity-grid">
                    <article><i>✓</i><h3>Цитата до страницы</h3><p>Оригинальный фрагмент открывается рядом с ответом.</p></article>
                    <article><i>✓</i><h3>Границы корпуса</h3><p>В ответе видно, какие коллекции и периоды были проверены.</p></article>
                    <article><i>✓</i><h3>Разделение факта и вывода</h3><p>Система маркирует прямые свидетельства и интерпретации.</p></article>
                    <article><i>✓</i><h3>Авторские права</h3><p>Доступ и выдача полных текстов следуют лицензиям вашей коллекции.</p></article>
                </div>
            </div>
        </section>

        <section class="research-section page-shell" id="pilot">
            <div class="pilot-card">
                <div>
                    <p class="eyebrow">Пилотный проект</p>
                    <h2>Начните с одной коллекции и одного исследовательского вопроса.</h2>
                    <p>Определим источники, соберём тестовый индекс, настроим критерии качества и покажем ответы на реальных материалах вашей команды.</p>
                </div>
                <div class="pilot-steps">
                    <p><span>01</span> Аудит коллекции и прав доступа</p>
                    <p><span>02</span> Индексация репрезентативной выборки</p>
                    <p><span>03</span> Набор контрольных вопросов и оценка цитат</p>
                    <p><span>04</span> План масштабирования и эксплуатации</p>
                    <a class="button button-primary" href="{{ route('openclaw-short') }}#request">Обсудить коллекцию <span>↗</span></a>
                </div>
            </div>
        </section>
    </main>

    <footer class="research-footer page-shell">
        <strong>OPENCLAW RESEARCH</strong>
        <span>Научный поиск, который можно проверить.</span>
        <span>© {{ date('Y') }} Craft Chronicles</span>
    </footer>
</body>
</html>
