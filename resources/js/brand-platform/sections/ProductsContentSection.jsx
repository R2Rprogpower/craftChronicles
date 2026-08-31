import React from 'react';
import SectionHeading from '../components/SectionHeading';

export default function ProductsContentSection({ products, content }) {
    return <section id="content"><SectionHeading eyebrow="Studio & content" title="Ideas become useful by surviving contact with reality." /><div className="split-grid"><div><h3 className="column-title">Products / experiments</h3>{products.map(item => <article className="compact-card" key={item.id}><span>{item.stage}</span><h4>{item.name}</h4><p>{item.description}</p></article>)}</div><div><h3 className="column-title">Content pipeline</h3>{content.map(item => <article className="compact-card" key={item.id}><span>{item.channel} · {item.status}</span><h4>{item.title}</h4><p>{item.description}</p></article>)}</div></div></section>;
}
