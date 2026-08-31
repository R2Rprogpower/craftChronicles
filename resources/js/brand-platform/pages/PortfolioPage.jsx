import React, { useState } from 'react';
import SiteHeader from '../components/SiteHeader';
import ServiceRequestModal from '../components/ServiceRequestModal';
import Hero from '../sections/Hero';
import ExpertiseSection from '../sections/ExpertiseSection';
import PortfolioSection from '../sections/PortfolioSection';
import ServicesSection from '../sections/ServicesSection';
import ProductsContentSection from '../sections/ProductsContentSection';
import ContactFaqSection from '../sections/ContactFaqSection';

export default function PortfolioPage({ data }) {
    const [modal, setModal] = useState({ open: false, service: null });
    const open = service => setModal({ open: true, service });
    return <><div className="page-shell"><SiteHeader name={data.profile.name} /><main><Hero profile={data.profile} onHire={() => open(null)} /><ExpertiseSection items={data.profile.expertise} /><PortfolioSection items={data.portfolio} /><ServicesSection items={data.services} onSelect={open} /><ProductsContentSection products={data.products} content={data.content} /><ContactFaqSection profile={data.profile} onHire={() => open(null)} /></main><footer><span>© {new Date().getFullYear()} {data.profile.name}</span><span>Built as a product, not a brochure.</span></footer></div><ServiceRequestModal open={modal.open} selectedService={modal.service} services={data.services} onClose={() => setModal({ open: false, service: null })} /></>;
}
