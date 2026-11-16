export function setBusy(form, busy) {
    const submit = form.querySelector('button[type="submit"]');
    if (submit) {
        submit.disabled = busy;
        submit.setAttribute('aria-busy', String(busy));
        if (busy) {
            submit.classList.add('button--loading');
        } else {
            submit.classList.remove('button--loading');
        }
    }
    form.setAttribute('aria-busy', String(busy));
}

export function showMessage(el, type, text) {
    el.textContent = text;
    el.setAttribute('role', type === 'error' ? 'alert' : 'status');
    el.hidden = false;
}

export function announce(text) {
    const live = document.querySelector('[data-live]');
    if (live) {
        live.textContent = text;
    }
}