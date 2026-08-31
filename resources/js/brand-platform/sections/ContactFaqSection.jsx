import React from 'react';

export default function ContactFaqSection({ profile, onHire }) {
    return <><section id="contact" className="contact-panel"><div><p className="eyebrow">Contact</p><h2>{profile.contact.heading}</h2><p>{profile.contact.description}</p><button className="button light" onClick={onHire}>Start a conversation</button></div><div className="contact-links">{profile.social_links?.map(link => <a key={link.url} href={link.url} target="_blank" rel="noreferrer">{link.label}<span>↗</span></a>)}<p>{profile.contact.preferred_channel}</p></div></section><section id="faq"><div className="faq-layout"><div><p className="eyebrow">FAQ</p><h2>Useful answers before we talk.</h2></div><div>{profile.faq?.map(item => <details key={item.question}><summary>{item.question}</summary><p>{item.answer}</p></details>)}</div></div></section></>;
}
