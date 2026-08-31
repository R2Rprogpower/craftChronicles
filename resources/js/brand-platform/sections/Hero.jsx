import React from 'react';

export default function Hero({ profile, onHire }) {
    return <section className="hero"><div className="hero-copy"><p className="eyebrow">Engineering · Mentoring · Communication</p><h1>{profile.headline}</h1><p className="hero-lead">{profile.bio}</p><div className="hero-actions"><button className="button primary" onClick={onHire}>Work with me</button><a className="button secondary" href="#work">Explore work</a></div><div className="availability"><span></span>{profile.availability}</div></div><aside className="hero-note"><span className="note-index">01 / PRINCIPLE</span><blockquote>“Build the smallest coherent system that can earn the next decision.”</blockquote><p>{profile.location}</p></aside></section>;
}
