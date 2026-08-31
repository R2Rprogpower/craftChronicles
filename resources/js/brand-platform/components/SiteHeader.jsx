import React from 'react';

export default function SiteHeader({ name, progress = false }) {
    return (
        <header className="site-header">
            <a className="brand-mark" href="/portfolio" aria-label={`${name} home`}><span>R/</span>{name}</a>
            <nav aria-label="Primary navigation">
                {progress ? <a href="/portfolio">Portfolio</a> : <>
                    <a href="#work">Work</a><a href="#services">Services</a><a href="#content">Content</a>
                </>}
                <a className={progress ? 'active' : ''} href="/progress">Progress</a>
            </nav>
        </header>
    );
}
