const header = document.querySelector('[data-header]');
const menuButton = document.querySelector('[data-menu]');
const nav = document.querySelector('[data-nav]');

menuButton?.addEventListener('click', () => {
    header?.classList.toggle('menu-open');
});

nav?.querySelectorAll('a').forEach((link) => link.addEventListener('click', () => {
    header?.classList.remove('menu-open');
}));

const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
        if (entry.isIntersecting) {
            entry.target.classList.add('is-visible');
            observer.unobserve(entry.target);
        }
    });
}, { threshold: 0.12 });

document.querySelectorAll('.reveal').forEach((element) => observer.observe(element));

const form = document.querySelector('[data-request-form]');

form?.addEventListener('submit', async (event) => {
    event.preventDefault();

    const submit = form.querySelector('[data-submit]');
    const status = form.querySelector('[data-form-status]');
    const fields = form.querySelectorAll('input, select, textarea, button');

    status.textContent = '';
    status.className = 'form-status';

    if (!form.reportValidity()) {
        return;
    }

    fields.forEach((field) => { field.disabled = true; });
    submit.textContent = submit.dataset.sending;

    try {
        const response = await fetch(form.action, {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: new FormData(form),
        });

        if (!response.ok) {
            throw new Error(`Request failed: ${response.status}`);
        }

        form.reset();
        status.innerHTML = `<strong>${status.dataset.successTitle}</strong><span>${status.dataset.successText}</span>`;
        status.classList.add('is-success');
    } catch (error) {
        status.textContent = status.dataset.error;
        status.classList.add('is-error');
    } finally {
        fields.forEach((field) => { field.disabled = false; });
        submit.textContent = submit.dataset.idle;
    }
});
