import React from 'react';

export default function TagList({ items = [] }) {
    return <ul className="tag-list" aria-label="Technologies">{items.map(item => <li key={item}>{item}</li>)}</ul>;
}
