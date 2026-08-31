import React from 'react';
import { createRoot } from 'react-dom/client';
import PortfolioPage from './pages/PortfolioPage';
import ProgressPage from './pages/ProgressPage';
import '../../css/brand-platform.css';

const rootElement = document.getElementById('brand-platform');
const Page = rootElement.dataset.page === 'progress' ? ProgressPage : PortfolioPage;

createRoot(rootElement).render(<Page data={window.__BRAND_PLATFORM__} />);
