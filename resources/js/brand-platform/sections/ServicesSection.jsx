import React from 'react';
import SectionHeading from '../components/SectionHeading';

export default function ServicesSection({ items, onSelect }) {
    return <section id="services"><SectionHeading eyebrow="Services" title="Focused engagements with a concrete outcome." /><div className="service-grid">{items.map(service => <article key={service.id}><h3>{service.name}</h3><p>{service.description}</p><ul>{service.benefits?.map(benefit => <li key={benefit}>{benefit}</li>)}</ul><p className="engagement">{service.engagement_format}</p><button className="text-button" onClick={() => onSelect(service.id)}>{service.cta_label} →</button></article>)}</div></section>;
}
