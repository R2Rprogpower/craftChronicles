import React from 'react';
import SectionHeading from '../components/SectionHeading';
import TagList from '../components/TagList';

export default function PortfolioSection({ items }) {
    return <section id="work"><SectionHeading eyebrow="Selected work" title="Systems, experiments, and knowledge products." copy="Work in progress is shown as work in progress. Credibility beats theatre." /><div className="project-list">{items.map(item => <article className="project-card" key={item.id}><div className="project-meta"><span>{item.category.replaceAll('-', ' ')}</span><span>{item.status.replaceAll('-', ' ')}</span></div><h3>{item.title}</h3><p>{item.short_description}</p><TagList items={item.technologies} /><div className="project-footer"><span>{item.timeline}</span>{item.links?.map(link => <a href={link.url} target="_blank" rel="noreferrer" key={link.url}>{link.label} ↗</a>)}</div></article>)}</div></section>;
}
