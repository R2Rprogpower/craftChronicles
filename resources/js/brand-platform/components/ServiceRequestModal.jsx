import React, { useEffect, useState } from 'react';

const emptyForm = { name: '', email: '', contact: '', company: '', service_id: '', budget: '', message: '' };

export default function ServiceRequestModal({ open, onClose, services, selectedService }) {
    const [form, setForm] = useState(emptyForm);
    const [state, setState] = useState({ status: 'idle', message: '', errors: {} });

    useEffect(() => {
        if (open) {
            setForm(current => ({ ...current, service_id: selectedService ? String(selectedService) : current.service_id }));
            document.body.classList.add('modal-open');
        }
        return () => document.body.classList.remove('modal-open');
    }, [open, selectedService]);

    useEffect(() => {
        const closeOnEscape = event => event.key === 'Escape' && onClose();
        document.addEventListener('keydown', closeOnEscape);
        return () => document.removeEventListener('keydown', closeOnEscape);
    }, [onClose]);

    if (!open) return null;

    const update = event => setForm({ ...form, [event.target.name]: event.target.value });
    const submit = async event => {
        event.preventDefault();
        setState({ status: 'sending', message: '', errors: {} });
        try {
            const response = await fetch('/service-requests', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ ...form, service_id: form.service_id || null }),
            });
            const payload = await response.json();
            if (!response.ok) throw payload;
            setForm(emptyForm);
            setState({ status: 'success', message: 'Request received. I’ll get back to you with a useful next step.', errors: {} });
        } catch (error) {
            setState({ status: 'error', message: error.message || 'Could not send the request. Please check the fields and try again.', errors: error.errors || {} });
        }
    };

    const errorFor = field => state.errors[field]?.[0];

    return <div className="modal-backdrop" role="presentation" onMouseDown={event => event.target === event.currentTarget && onClose()}>
        <section className="request-modal" role="dialog" aria-modal="true" aria-labelledby="request-title">
            <button className="modal-close" type="button" onClick={onClose} aria-label="Close dialog">×</button>
            <p className="eyebrow">Start a conversation</p><h2 id="request-title">Tell me what you want to move forward.</h2>
            <p className="modal-intro">A short, specific description is enough. No pitch deck required.</p>
            {state.status === 'success' ? <div className="form-success"><strong>Done.</strong><p>{state.message}</p><button className="button secondary" onClick={onClose}>Close</button></div> :
            <form onSubmit={submit} noValidate>
                <div className="form-grid">
                    <label>Name<input name="name" value={form.name} onChange={update} required />{errorFor('name') && <small>{errorFor('name')}</small>}</label>
                    <label>Email<input name="email" type="email" value={form.email} onChange={update} />{errorFor('email') && <small>{errorFor('email')}</small>}</label>
                    <label>Contact (Telegram, etc.)<input name="contact" value={form.contact} onChange={update} />{errorFor('contact') && <small>{errorFor('contact')}</small>}</label>
                    <label>Company <span>optional</span><input name="company" value={form.company} onChange={update} /></label>
                    <label>Service<select name="service_id" value={form.service_id} onChange={update}><option value="">Not sure yet</option>{services.map(service => <option key={service.id} value={service.id}>{service.name}</option>)}</select></label>
                    <label>Budget <span>optional</span><input name="budget" value={form.budget} onChange={update} placeholder="Range or ‘not defined’" /></label>
                </div>
                <label>Context and desired outcome<textarea name="message" value={form.message} onChange={update} rows="5" required />{errorFor('message') && <small>{errorFor('message')}</small>}</label>
                {state.status === 'error' && <p className="form-error">{state.message}</p>}
                <button className="button primary" type="submit" disabled={state.status === 'sending'}>{state.status === 'sending' ? 'Sending…' : 'Send request'}</button>
            </form>}
        </section>
    </div>;
}
