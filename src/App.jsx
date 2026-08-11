import React from 'react';
import { Route, Routes, BrowserRouter as Router } from 'react-router-dom';
import ScrollToTop from './components/ScrollToTop';
import { LanguageProvider } from './context/LanguageContext';
import HomePage from './pages/HomePage';
import MenuPage from './pages/MenuPage';
import BlogPage from './pages/BlogPage';
import BlogPostPage from './pages/BlogPostPage';
import MediaPage from './pages/MediaPage';
import WordPressDownloadPage from './pages/WordPressDownloadPage';

function App() {
    return (
        <LanguageProvider>
            <Router>
                <ScrollToTop />
                <Routes>
                    <Route path="/" element={<HomePage />} />
                    <Route path="/menu" element={<MenuPage />} />
                    <Route path="/blog" element={<BlogPage />} />
                    <Route path="/blog/:slug" element={<BlogPostPage />} />
                    <Route path="/media" element={<MediaPage />} />
                    <Route path="/wordpress" element={<WordPressDownloadPage />} />
                </Routes>
            </Router>
        </LanguageProvider>
    );
}

export default App;
