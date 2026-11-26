<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Семейная летопись</title>
    <link rel="stylesheet" href="{{ asset('css/family-chronicles.css') }}">
</head>
<body>
<main class="fc-shell" id="familyChronicles"
      data-state-url="{{ route('family-chronicles.state', [], false) }}"
      data-people-url="{{ route('family-chronicles.people.store', [], false) }}"
      data-person-update-template="{{ route('family-chronicles.people.update', ['person' => '__ID__'], false) }}"
      data-relative-store-template="{{ route('family-chronicles.relatives.store', ['person' => '__ID__'], false) }}"
      data-relative-delete-template="{{ route('family-chronicles.relatives.destroy', ['relative' => '__ID__'], false) }}"
      data-answers-url="{{ route('family-chronicles.answers.store', [], false) }}"
      data-attachment-store-template="{{ route('family-chronicles.attachments.store', ['answer' => '__ID__'], false) }}"
      data-attachment-delete-template="{{ route('family-chronicles.attachments.destroy', ['attachment' => '__ID__'], false) }}">
    <header class="fc-header">
        <div>
            <p class="fc-eyebrow">Craft Chronicles</p>
            <h1>Семейная летопись</h1>
            <p class="fc-lead">Сохраняйте воспоминания спокойно, по одному вопросу за раз.</p>
        </div>
        <div class="fc-header-meta">
            <span>{{ auth()->user()->name }}</span>
            <a href="{{ route('dashboard') }}">В кабинет</a>
        </div>
    </header>

    <section class="fc-card fc-person-panel" aria-labelledby="peopleTitle">
        <div>
            <h2 id="peopleTitle">О чьей жизни рассказываем?</h2>
            <div class="fc-person-list" id="personList" aria-live="polite"></div>
        </div>
        <details class="fc-add-person" id="addPersonDetails">
            <summary>+ Добавить человека</summary>
            <form id="personForm">
                <label>Имя
                    <input name="name" required maxlength="120" autocomplete="name">
                </label>
                <label>Дата рождения
                    <input name="birth_date" type="date" max="{{ now()->toDateString() }}">
                </label>
                <label>Кем приходится
                    <input name="relationship" maxlength="80" placeholder="Например: мама">
                </label>
                <button class="fc-button fc-button-primary" type="submit">Сохранить</button>
            </form>
        </details>
    </section>

    <section class="fc-card fc-progress" aria-label="Прогресс">
        <div class="fc-progress-copy">
            <strong id="progressLabel">0 из {{ $questionCount }}</strong>
            <span>вопросов получили ответ</span>
        </div>
        <div class="fc-progress-track" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
            <span id="progressBar"></span>
        </div>
    </section>

    <section class="fc-workspace" id="workspace" hidden>
        <details class="fc-card fc-profile" id="profileDetails">
            <summary>Профиль человека и родственники</summary>
            <p class="fc-profile-intro">Эти данные помогают скрывать неподходящие вопросы и подставлять имена родственников. Поля можно оставить пустыми.</p>

            <form id="profileForm" class="fc-profile-form">
                <label>Имя
                    <input name="name" required maxlength="120">
                </label>
                <label>Дата рождения
                    <input name="birth_date" type="date" max="{{ now()->toDateString() }}">
                </label>
                <label>Кем приходится вам
                    <input name="relationship" maxlength="80" placeholder="Например: мама">
                </label>
                <label>Пол
                    <select name="gender">
                        <option value="">Не указано</option>
                        <option value="female">Женский</option>
                        <option value="male">Мужской</option>
                        <option value="other">Другой</option>
                        <option value="unspecified">Не хочу указывать</option>
                    </select>
                </label>
                <label>Место рождения
                    <input name="birth_place" maxlength="160">
                </label>
                <label>Где живёт или жил
                    <input name="residence" maxlength="160">
                </label>
                <label>Профессия или занятие
                    <input name="occupation" maxlength="160">
                </label>
                <label class="fc-profile-notes">Дополнительные сведения
                    <textarea name="profile_notes" rows="3" maxlength="5000" placeholder="Например: переезды, языки, вера, важные особенности биографии"></textarea>
                </label>
                <label class="fc-check fc-profile-notes">
                    <input name="relatives_complete" type="checkbox" value="1">
                    <span>Список близких родственников заполнен полностью. Тогда вопросы о несуществующих родственниках можно скрыть.</span>
                </label>
                <div class="fc-profile-notes">
                    <button class="fc-button fc-button-primary" type="submit">Сохранить профиль</button>
                </div>
            </form>

            <div class="fc-relatives">
                <h3>Родственники</h3>
                <div id="relativeList" class="fc-relative-list"></div>
                <form id="relativeForm" class="fc-relative-form">
                    <label>Кем приходится
                        <select name="relationship" id="relativeRelationship" required></select>
                    </label>
                    <label>Имя
                        <input name="name" maxlength="120" placeholder="Можно оставить пустым">
                    </label>
                    <label>Год рождения
                        <input name="birth_year" type="number" min="1850" max="{{ now()->year }}" inputmode="numeric">
                    </label>
                    <button class="fc-button fc-button-secondary" type="submit">Добавить родственника</button>
                </form>
            </div>
        </details>

        <nav class="fc-modes" aria-label="Режим вопросов">
            <button type="button" class="fc-mode is-active" data-mode="daily">Вопрос дня</button>
            <button type="button" class="fc-mode" data-mode="story">История по порядку</button>
            <button type="button" class="fc-mode" data-mode="random">Случайный вопрос</button>
        </nav>

        <section class="fc-card fc-filters" aria-label="Фильтры вопросов">
            <label>Тема
                <select id="categoryFilter"><option value="">Все темы</option></select>
            </label>
            <label>Период жизни
                <select id="stageFilter"><option value="">Все периоды</option></select>
            </label>
            <button class="fc-button fc-button-secondary" type="button" id="pickRandom">Другой вопрос</button>
        </section>

        <article class="fc-card fc-question" id="questionCard" aria-live="polite">
            <div class="fc-question-meta">
                <span id="questionCategory"></span>
                <span id="questionStage"></span>
                <span class="fc-status" id="answerStatus">Не начато</span>
            </div>
            <h2 id="questionText">Выберите человека, чтобы начать.</h2>

            <label class="fc-answer-label" for="answerText">Ваш рассказ</label>
            <textarea id="answerText" rows="10" maxlength="100000" placeholder="Можно писать свободно и возвращаться к ответу позже. Черновик сохранится автоматически."></textarea>
            <div class="fc-save-line">
                <span id="saveState">Изменений нет</span>
                <span id="characterCount">0 знаков</span>
            </div>

            <section class="fc-attachments" aria-labelledby="attachmentsTitle">
                <div>
                    <h3 id="attachmentsTitle">Фото, аудио, видео или документ</h3>
                    <p>До 50 МБ на файл. Материалы доступны только владельцу аккаунта.</p>
                </div>
                <label class="fc-file-button">
                    <input id="attachmentInput" type="file" accept="image/jpeg,image/png,image/webp,audio/*,video/mp4,video/webm,application/pdf">
                    Добавить файл
                </label>
                <div class="fc-attachment-list" id="attachmentList"></div>
            </section>

            <div class="fc-actions">
                <button class="fc-button fc-button-primary" type="button" id="markAnswered">Сохранить ответ</button>
                <button class="fc-button fc-button-secondary" type="button" id="deferQuestion">Отложить</button>
                <button class="fc-button fc-button-quiet" type="button" id="skipQuestion">Пропустить</button>
                <button class="fc-button fc-button-next" type="button" id="nextQuestion">Следующий →</button>
            </div>
        </article>
    </section>

    <section class="fc-card fc-empty" id="emptyState">
        <h2>Добавьте первого человека</h2>
        <p>Это может быть ваша мама, папа, бабушка, дедушка или вы сами. Для каждого человека будет отдельная история.</p>
    </section>

    <div class="fc-toast" id="toast" role="status" aria-live="polite"></div>
</main>
<script src="{{ asset('js/family-chronicles.js') }}" defer></script>
</body>
</html>
