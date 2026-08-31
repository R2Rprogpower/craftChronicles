import React from 'react';
import SectionHeading from '../components/SectionHeading';

export default function ExpertiseSection({ items }) {
    return <section id="about"><SectionHeading eyebrow="What I do" title="Technical depth, product sense, clear communication." /><div className="expertise-grid">{items.map((item, index) => <article key={item.title}><span>0{index + 1}</span><h3>{item.title}</h3><p>{item.description}</p></article>)}</div></section>;
}
