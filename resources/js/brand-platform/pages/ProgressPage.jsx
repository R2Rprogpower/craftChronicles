import React from 'react';
import SiteHeader from '../components/SiteHeader';
import SectionHeading from '../components/SectionHeading';

function StatusPill({ status }) { return <span className={`status status-${status}`}>{status.replaceAll('-', ' ')}</span>; }

export default function ProgressPage({ data }) {
    const completed = data.areas.flatMap(area => area.milestones).filter(item => item.status === 'completed').length;
    const total = data.areas.flatMap(area => area.milestones).length;
    return <div className="page-shell progress-page"><SiteHeader name={data.profile.name} progress /><main><section className="progress-hero"><p className="eyebrow">Public build log</p><h1>Personal brand progress, without fake certainty.</h1><p>This page tracks the real state of the platform, content, products, audience, and sales foundation.</p><div className="progress-summary"><div><strong>{data.areas.length}</strong><span>active directions</span></div><div><strong>{completed}/{total}</strong><span>milestones completed</span></div><div><strong>{Math.round(data.areas.reduce((sum, area) => sum + area.progress, 0) / data.areas.length)}%</strong><span>average progress</span></div></div></section><section><SectionHeading eyebrow="Current state" title="Every direction has a next concrete move." /><div className="progress-grid">{data.areas.map(area => <article className="progress-card" key={area.id}><header><div><h2>{area.name}</h2><StatusPill status={area.status} /></div><strong>{area.progress}%</strong></header><div className="progress-track"><span style={{ width: `${area.progress}%` }}></span></div><p>{area.summary}</p><div className="progress-columns"><div><h3>Milestones</h3><ul className="check-list">{area.milestones.map(item => <li className={item.status === 'completed' ? 'done' : ''} key={item.id}><span>{item.status === 'completed' ? '✓' : '·'}</span>{item.title}</li>)}</ul></div><div><h3>Next steps</h3><ul>{area.next_steps?.map(step => <li key={step}>{step}</li>)}</ul></div></div></article>)}</div></section></main><footer><span>Updated from structured project data.</span><a href="/portfolio">Back to portfolio →</a></footer></div>;
}
