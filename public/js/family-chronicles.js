(() => {
    const root = document.getElementById('familyChronicles');
    if (!root) return;

    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    const urls = {
        state: root.dataset.stateUrl,
        people: root.dataset.peopleUrl,
        personUpdate: root.dataset.personUpdateTemplate,
        relativeStore: root.dataset.relativeStoreTemplate,
        relativeDelete: root.dataset.relativeDeleteTemplate,
        answers: root.dataset.answersUrl,
        attachmentStore: root.dataset.attachmentStoreTemplate,
        attachmentDelete: root.dataset.attachmentDeleteTemplate,
    };
    const els = Object.fromEntries([
        'personList', 'personForm', 'addPersonDetails', 'workspace', 'emptyState', 'progressLabel',
        'progressBar', 'categoryFilter', 'stageFilter', 'pickRandom', 'questionCategory',
        'questionStage', 'answerStatus', 'questionText', 'answerText', 'saveState',
        'characterCount', 'attachmentInput', 'attachmentList', 'markAnswered', 'deferQuestion',
        'skipQuestion', 'nextQuestion', 'toast',
        'profileDetails', 'profileForm', 'relativeList', 'relativeForm', 'relativeRelationship',
    ].map(id => [id, document.getElementById(id)]));

    const state = {
        data: null,
        personId: null,
        mode: 'daily',
        currentKey: null,
        saveTimer: null,
        saveSequence: 0,
        toastTimer: null,
    };

    async function request(url, options = {}) {
        const response = await fetch(url, {
            credentials: 'same-origin',
            ...options,
            headers: {
                Accept: 'application/json',
                'X-CSRF-TOKEN': csrf,
                ...(options.headers || {}),
            },
        });
        const payload = await response.json().catch(() => ({}));
        if (!response.ok) {
            const errors = payload.errors ? Object.values(payload.errors).flat().join(' ') : null;
            throw new Error(errors || payload.message || 'Не удалось выполнить действие.');
        }
        return payload;
    }

    async function load(personId = null) {
        const query = personId ? `?person_id=${encodeURIComponent(personId)}` : '';
        state.data = await request(urls.state + query);
        state.personId = state.data.active_person_id;
        renderPeople();
        renderFilters();
        renderProgress();
        renderProfile();
        els.workspace.hidden = !state.personId;
        els.emptyState.hidden = Boolean(state.personId);
        if (state.personId) chooseForMode();
    }

    function renderPeople() {
        els.personList.innerHTML = '';
        state.data.people.forEach(person => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = `fc-person${person.id === state.personId ? ' is-active' : ''}`;
            button.textContent = person.relationship ? `${person.name} · ${person.relationship}` : person.name;
            button.addEventListener('click', () => load(person.id).catch(showError));
            els.personList.append(button);
        });
    }

    function activePerson() {
        return state.data.people.find(person => person.id === state.personId) || null;
    }

    function renderProfile() {
        const person = activePerson();
        if (!person) return;

        const fields = ['name', 'birth_date', 'relationship', 'gender', 'birth_place', 'residence', 'occupation', 'profile_notes'];
        fields.forEach(name => {
            const input = els.profileForm.elements.namedItem(name);
            let value = person[name] ?? '';
            if (name === 'birth_date' && value) value = String(value).slice(0, 10);
            input.value = value;
        });
        els.profileForm.elements.namedItem('relatives_complete').checked = Boolean(person.relatives_complete);

        fillSelect(els.relativeRelationship, state.data.relationship_types || {}, 'Выберите родство');
        renderRelatives(person.relatives || []);
    }

    function renderRelatives(relatives) {
        els.relativeList.innerHTML = '';
        if (!relatives.length) {
            const empty = document.createElement('p');
            empty.className = 'fc-relative-empty';
            empty.textContent = 'Пока никого не добавили. До отметки «список заполнен» вопросы не будут скрываться.';
            els.relativeList.append(empty);
            return;
        }

        relatives.forEach(relative => {
            const row = document.createElement('div');
            row.className = 'fc-relative';
            const copy = document.createElement('span');
            const label = state.data.relationship_types?.[relative.relationship] || relative.relationship;
            const details = [relative.name, relative.birth_year].filter(Boolean).join(', ');
            copy.textContent = details ? `${label}: ${details}` : label;
            const remove = document.createElement('button');
            remove.type = 'button';
            remove.textContent = 'Удалить';
            remove.addEventListener('click', () => removeRelative(relative.id).catch(showError));
            row.append(copy, remove);
            els.relativeList.append(row);
        });
    }

    function fillSelect(select, options, placeholder) {
        const current = select.value;
        select.innerHTML = `<option value="">${placeholder}</option>`;
        Object.entries(options).forEach(([value, label]) => {
            const option = document.createElement('option');
            option.value = value;
            option.textContent = label;
            select.append(option);
        });
        if ([...select.options].some(option => option.value === current)) select.value = current;
    }

    function renderFilters() {
        fillSelect(els.categoryFilter, state.data.categories || {}, 'Все темы');
        fillSelect(els.stageFilter, state.data.stages || {}, 'Все периоды');
    }

    function filteredQuestions() {
        const category = els.categoryFilter.value;
        const stage = els.stageFilter.value;
        const questions = state.data.questions.filter(question =>
            (!category || question.category === category) && (!stage || question.stage === stage)
        );
        if (state.mode === 'story') {
            const stageOrder = Object.keys(state.data.stages || {});
            questions.sort((left, right) => {
                const byStage = stageOrder.indexOf(left.stage) - stageOrder.indexOf(right.stage);
                return byStage || left.category.localeCompare(right.category, 'ru') || left.key.localeCompare(right.key, 'ru');
            });
        }
        return questions;
    }

    function chooseForMode() {
        const questions = filteredQuestions();
        if (!questions.length) {
            state.currentKey = null;
            els.questionText.textContent = 'Для этих фильтров пока нет вопросов.';
            els.answerText.value = '';
            disableQuestion(true);
            return;
        }
        disableQuestion(false);
        if (state.mode === 'daily') {
            const daily = questions.find(q => q.key === state.data.daily_question_key);
            state.currentKey = (daily || questions[0]).key;
        } else if (state.mode === 'random') {
            const unanswered = questions.filter(q => state.data.answers[q.key]?.status !== 'answered');
            const pool = unanswered.length ? unanswered : questions;
            state.currentKey = pool[Math.floor(Math.random() * pool.length)].key;
        } else {
            const currentVisible = questions.some(q => q.key === state.currentKey);
            if (!currentVisible) {
                state.currentKey = (questions.find(q => state.data.answers[q.key]?.status !== 'answered') || questions[0]).key;
            }
        }
        renderQuestion();
    }

    function currentQuestion() {
        return state.data.questions.find(question => question.key === state.currentKey);
    }

    function renderQuestion() {
        const question = currentQuestion();
        if (!question) return;
        const answer = state.data.answers[question.key] || {status: 'new', text: '', attachments: []};
        const localDraft = localStorage.getItem(draftKey(question.key));
        els.questionCategory.textContent = question.category_label;
        els.questionStage.textContent = question.stage_label;
        els.questionText.textContent = question.text;
        els.answerText.value = localDraft ?? answer.text ?? '';
        els.answerStatus.textContent = statusLabel(answer.status);
        els.saveState.textContent = answer.updated_at ? 'Сохранено' : 'Изменений нет';
        updateCharacterCount();
        renderAttachments(answer.attachments || []);
    }

    function statusLabel(status) {
        return ({new: 'Не начато', draft: 'Черновик', answered: 'Отвечено', skipped: 'Пропущено', deferred: 'Отложено'})[status] || status;
    }

    function disableQuestion(disabled) {
        [els.answerText, els.attachmentInput, els.markAnswered, els.deferQuestion, els.skipQuestion, els.nextQuestion]
            .forEach(element => element.disabled = disabled);
    }

    async function saveAnswer(status = 'draft', quiet = false) {
        const question = currentQuestion();
        if (!question) return null;
        const sequence = ++state.saveSequence;
        els.saveState.textContent = 'Сохраняем…';
        const payload = await request(urls.answers, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                person_id: state.personId,
                question_key: question.key,
                status,
                text: els.answerText.value,
            }),
        });
        state.data.answers[question.key] = payload.answer;
        localStorage.removeItem(draftKey(question.key));
        if (sequence === state.saveSequence) els.saveState.textContent = payload.message;
        els.answerStatus.textContent = statusLabel(payload.answer.status);
        renderProgress();
        if (!quiet) toast(payload.message);
        return payload.answer;
    }

    function scheduleAutosave() {
        updateCharacterCount();
        localStorage.setItem(draftKey(state.currentKey), els.answerText.value);
        els.saveState.textContent = 'Есть несохранённые изменения';
        clearTimeout(state.saveTimer);
        const currentStatus = state.data.answers[state.currentKey]?.status;
        const autosaveStatus = currentStatus === 'answered' ? 'answered' : 'draft';
        state.saveTimer = setTimeout(() => saveAnswer(autosaveStatus, true).catch(showError), 900);
    }

    function draftKey(questionKey) {
        return `family-chronicles:draft:${state.personId || 'none'}:${questionKey || 'none'}`;
    }

    function updateCharacterCount() {
        els.characterCount.textContent = `${els.answerText.value.length.toLocaleString('ru-RU')} знаков`;
    }

    function nextQuestion() {
        const questions = filteredQuestions();
        if (!questions.length) return;
        if (state.mode === 'random') return chooseForMode();
        const index = Math.max(0, questions.findIndex(q => q.key === state.currentKey));
        state.currentKey = questions[(index + 1) % questions.length].key;
        renderQuestion();
        document.getElementById('questionCard').scrollIntoView({behavior: 'smooth', block: 'start'});
    }

    function renderProgress() {
        const total = state.data.questions.length;
        const answered = Object.values(state.data.answers).filter(answer => answer.status === 'answered').length;
        const percent = total ? Math.round(answered / total * 100) : 0;
        els.progressLabel.textContent = `${answered} из ${total}`;
        els.progressBar.style.width = `${percent}%`;
        els.progressBar.parentElement.setAttribute('aria-valuenow', String(percent));
    }

    function renderAttachments(attachments) {
        els.attachmentList.innerHTML = '';
        attachments.forEach(attachment => {
            const row = document.createElement('div');
            row.className = 'fc-attachment';
            const link = document.createElement('a');
            link.href = attachment.download_url;
            link.textContent = `${attachment.name} · ${formatBytes(attachment.size)}`;
            const remove = document.createElement('button');
            remove.type = 'button';
            remove.textContent = 'Удалить';
            remove.addEventListener('click', () => removeAttachment(attachment.id).catch(showError));
            row.append(link, remove);
            els.attachmentList.append(row);
        });
    }

    async function uploadAttachment(file) {
        let answer = state.data.answers[state.currentKey];
        if (!answer?.id) answer = await saveAnswer('draft', true);
        const form = new FormData();
        form.append('file', file);
        els.saveState.textContent = 'Загружаем файл…';
        const payload = await request(urls.attachmentStore.replace('__ID__', answer.id), {method: 'POST', body: form});
        answer.attachments = [...(answer.attachments || []), payload.attachment];
        renderAttachments(answer.attachments);
        els.saveState.textContent = 'Файл загружен';
        toast('Файл добавлен');
    }

    async function removeAttachment(id) {
        await request(urls.attachmentDelete.replace('__ID__', id), {method: 'DELETE'});
        const answer = state.data.answers[state.currentKey];
        answer.attachments = answer.attachments.filter(item => item.id !== id);
        renderAttachments(answer.attachments);
        toast('Файл удалён');
    }

    async function removeRelative(id) {
        await request(urls.relativeDelete.replace('__ID__', id), {method: 'DELETE'});
        await load(state.personId);
        toast('Родственник удалён');
    }

    function formatBytes(bytes) {
        if (!bytes) return '0 Б';
        const units = ['Б', 'КБ', 'МБ', 'ГБ'];
        const index = Math.min(Math.floor(Math.log(bytes) / Math.log(1024)), units.length - 1);
        return `${(bytes / 1024 ** index).toFixed(index ? 1 : 0)} ${units[index]}`;
    }

    function toast(message, error = false) {
        clearTimeout(state.toastTimer);
        els.toast.textContent = message;
        els.toast.classList.toggle('is-error', error);
        els.toast.classList.add('is-visible');
        state.toastTimer = setTimeout(() => els.toast.classList.remove('is-visible'), 3500);
    }

    function showError(error) {
        toast(error?.message || 'Произошла ошибка.', true);
        els.saveState.textContent = 'Не сохранено — попробуйте ещё раз';
    }

    els.personForm.addEventListener('submit', async event => {
        event.preventDefault();
        const form = new FormData(els.personForm);
        const payload = Object.fromEntries([...form.entries()].filter(([, value]) => value !== ''));
        try {
            const result = await request(urls.people, {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(payload),
            });
            els.personForm.reset();
            els.addPersonDetails.open = false;
            await load(result.person.id);
            toast('Человек добавлен');
        } catch (error) { showError(error); }
    });

    els.profileForm.addEventListener('submit', async event => {
        event.preventDefault();
        const form = new FormData(els.profileForm);
        const payload = Object.fromEntries(form.entries());
        payload.relatives_complete = els.profileForm.elements.namedItem('relatives_complete').checked;

        try {
            await request(urls.personUpdate.replace('__ID__', state.personId), {
                method: 'PATCH',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(payload),
            });
            await load(state.personId);
            toast('Профиль сохранён, список вопросов обновлён');
        } catch (error) { showError(error); }
    });

    els.relativeForm.addEventListener('submit', async event => {
        event.preventDefault();
        const form = new FormData(els.relativeForm);
        const payload = Object.fromEntries([...form.entries()].filter(([, value]) => value !== ''));

        try {
            await request(urls.relativeStore.replace('__ID__', state.personId), {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(payload),
            });
            els.relativeForm.reset();
            await load(state.personId);
            els.profileDetails.open = true;
            toast('Родственник добавлен');
        } catch (error) { showError(error); }
    });

    document.querySelectorAll('.fc-mode').forEach(button => button.addEventListener('click', () => {
        document.querySelectorAll('.fc-mode').forEach(item => item.classList.remove('is-active'));
        button.classList.add('is-active');
        state.mode = button.dataset.mode;
        els.pickRandom.hidden = state.mode !== 'random';
        chooseForMode();
    }));
    [els.categoryFilter, els.stageFilter].forEach(select => select.addEventListener('change', chooseForMode));
    els.answerText.addEventListener('input', scheduleAutosave);
    els.markAnswered.addEventListener('click', () => saveAnswer('answered').catch(showError));
    els.deferQuestion.addEventListener('click', () => saveAnswer('deferred').then(nextQuestion).catch(showError));
    els.skipQuestion.addEventListener('click', () => saveAnswer('skipped').then(nextQuestion).catch(showError));
    els.nextQuestion.addEventListener('click', nextQuestion);
    els.pickRandom.addEventListener('click', chooseForMode);
    els.attachmentInput.addEventListener('change', () => {
        const [file] = els.attachmentInput.files;
        if (file) uploadAttachment(file).catch(showError).finally(() => { els.attachmentInput.value = ''; });
    });
    els.pickRandom.hidden = true;
    load().catch(showError);
})();
