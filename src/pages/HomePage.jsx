import React, { useState, useEffect, useRef } from 'react';
import { Link, useNavigate, useSearchParams } from 'react-router-dom';
import { useLanguage } from '@/context/LanguageContext';
import { motion, AnimatePresence } from 'framer-motion';
import {
  Phone, MapPin, ArrowRight, ChevronLeft, ChevronRight,
  Menu as MenuIcon, X, Play, Star, Facebook,
  ChevronDown, Mail, Briefcase, Send, CheckCircle, Upload, ZoomIn,
} from 'lucide-react';
import MenuImageLightbox from '@/components/MenuImageLightbox';
import pb from '@/lib/pocketbaseClient';
import { MENU_GROUPS } from './MenuPage';
import { BLOG_POSTS } from '@/data/blogData';
import ReservationModal from '@/components/ReservationModal';

const IMG = {
  heroGarden: 'https://images.hostinger.com/c46a9991-da86-4740-82e9-88191c8b3704.png',
  musicians: 'https://images.hostinger.com/70ecfb52-9475-4a62-8de1-20668b8bfd96.png',
  spread: 'https://images.hostinger.com/940e0864-79ba-47f2-bab9-e480e59d00b6.png',
  lotus: 'https://images.hostinger.com/31e5b023-bf1b-49ca-9e32-8c8117f5eccc.png',
  cozy: 'https://images.hostinger.com/769e5477-ac4e-4a68-9321-2d873267c696.png',
  crab: 'https://images.hostinger.com/c58e21f0-10ac-4729-b279-c4d0cb084d5b.png',
  patio: 'https://images.hostinger.com/8a6da3f1-0e14-4a63-99d5-ffcfd86b73f8.png',
  dinner: 'https://images.hostinger.com/db8469b1-38df-4d43-b7ba-ee923ce64798.png',
  lunch: 'https://images.hostinger.com/dd8d528b-154e-4304-8c0b-23b41936c7ef.png',
  appetizer: 'https://images.hostinger.com/0361f7c0-b13c-42bd-8806-4c5cfa258d47.png',
  dessert: 'https://images.hostinger.com/9aa44134-376f-4da6-876f-b136cdcab756.png',
  videoWall: 'https://images.hostinger.com/d3989bce-ced1-4669-a5c2-632cc5283cd2.png',
};

const BROWN = '#5e4743';
const GOLD = '#ffc952';
const BROWN_DARK = '#3d2e2b';
const RED = '#5e4743'; // kept for compatibility

// MENU_DROPDOWN and NEWS_DROPDOWN now use translated labels via t() in Header

const NAV = [
  { label: 'TRANG CHỦ', href: '#home' },
  { label: 'VỀ CHÚNG TÔI', href: '#about' },
  { label: 'TIN TỨC & TUYỂN DỤNG', href: '#news' },
  { label: 'CẢM NHẬN', href: '#reviews' },
];

function SectionTitle({ label, title, light }) {
  return (
    <div className="text-center">
      <span className="font-script text-sm md:text-base block" style={{ color: light ? GOLD : BROWN }}>{label}</span>
      <div className="flex items-center justify-center gap-3 mt-3 mb-2">
        <div className="brush-divider flex-1 max-w-[60px]" />
        <h3
          className={`font-section-title font-bold uppercase text-3xl md:text-5xl ${light ? 'text-white' : 'text-neutral-900'}`}
          style={{ letterSpacing: '0.18em' }}
        >
          {title}
        </h3>
        <div className="brush-divider flex-1 max-w-[60px]" />
      </div>
    </div>
  );
}

function Logo({ dark }) {
  return (
    <a href="/" className="flex items-center select-none group" aria-label="Ngon Thị Hoa">
      <img
        src="https://horizons-cdn.hostinger.com/a2bb8e3c-639e-4083-bf32-32f3f7f0cebb/d5bf5030568dea1dbf0b975ffc8f98ce.png"
        alt="Ngon Thị Hoa – Tropical Garden"
        className="h-16 w-auto object-contain transition-opacity group-hover:opacity-85"
        style={{ filter: dark ? 'none' : 'brightness(0) invert(1)' }}
      />
    </a>
  );
}

function LangSwitcher({ scrolled }) {
  const { lang, setLang } = useLanguage();
  const [open, setOpen] = useState(false);
  const ref = React.useRef(null);
  const flags = [
    { code: 'vi', flag: '🇻🇳', label: 'Tiếng Việt' },
    { code: 'en', flag: '🇬🇧', label: 'English' },
    { code: 'zh', flag: '🇨🇳', label: '中文' },
    { code: 'ko', flag: '🇰🇷', label: '한국어' },
  ];
  const current = flags.find(f => f.code === lang) || flags[0];
  useEffect(() => {
    const fn = (e) => { if (ref.current && !ref.current.contains(e.target)) setOpen(false); };
    document.addEventListener('mousedown', fn);
    return () => document.removeEventListener('mousedown', fn);
  }, []);
  return (
    <div className="relative" ref={ref}>
      <button onClick={() => setOpen(v => !v)}
        className={`flex items-center gap-1.5 px-2 py-1.5 rounded-full text-[13px] font-semibold transition-all ${scrolled ? 'text-[#5e4743] hover:bg-[#5e4743]/10' : 'text-white hover:bg-white/10'}`}
        title={current.label}>
        <span className="text-xl leading-none">{current.flag}</span>
        <ChevronDown size={12} className={`transition-transform ${open ? 'rotate-180' : ''}`} />
      </button>
      <AnimatePresence>
        {open && (
          <motion.div initial={{ opacity: 0, y: -6 }} animate={{ opacity: 1, y: 0 }} exit={{ opacity: 0, y: -6 }} transition={{ duration: 0.15 }}
            className="absolute right-0 top-full mt-2 w-40 bg-white rounded-xl shadow-xl overflow-hidden z-50">
            {flags.map(({ code, flag, label }) => (
              <button key={code} onClick={() => { setLang(code); setOpen(false); }}
                className={`w-full flex items-center gap-3 px-4 py-3 text-sm transition-colors hover:bg-[#ffc952]/20 ${lang === code ? 'text-[#5e4743] font-semibold' : 'text-neutral-700'}`}>
                <span className="text-lg">{flag}</span>
                <span>{label}</span>
              </button>
            ))}
          </motion.div>
        )}
      </AnimatePresence>
    </div>
  );
}

function Header({ onContact, onReservation }) {
  const { t } = useLanguage();
  const [open, setOpen] = useState(false);
  const [scrolled, setScrolled] = useState(false);
  const [menuOpen, setMenuOpen] = useState(false);
  const [newsOpen, setNewsOpen] = useState(false);
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
  const menuRef = useRef(null);
  const newsRef = useRef(null);
  const navigate = useNavigate();

  const MENU_DROPDOWN = [
    { label: t('menu_sang'), href: '/?group=sang' },
    { label: t('menu_trua_toi'), href: '/?group=trua_toi' },
    { label: t('menu_do_uong'), href: '/?group=do_uong' },
    { label: t('menu_do_uong_co_con'), href: '/?group=do_uong_co_con' },
    { label: t('menu_ruou_vang'), href: '/menu?group=ruou_vang' },
  ];

  useEffect(() => {
    const onScroll = () => setScrolled(window.scrollY > 60);
    window.addEventListener('scroll', onScroll);
    return () => window.removeEventListener('scroll', onScroll);
  }, []);
  useEffect(() => {
    const handler = (e) => {
      if (menuRef.current && !menuRef.current.contains(e.target)) setMenuOpen(false);
      if (newsRef.current && !newsRef.current.contains(e.target)) setNewsOpen(false);
    };
    document.addEventListener('mousedown', handler);
    return () => document.removeEventListener('mousedown', handler);
  }, []);
  const linkCls = `text-[13px] font-semibold tracking-widest uppercase transition-colors ${scrolled ? 'text-[#5e4743] hover:text-[#3d2e2b]' : 'text-white/90 hover:text-white'}`;
  return (
    <header
      className={`fixed top-0 inset-x-0 z-50 transition-all duration-300 ${scrolled ? 'bg-white shadow-md' : 'bg-gradient-to-b from-black/55 to-transparent'}`}
    >
      <div className="max-w-[1200px] mx-auto px-6 h-[72px] flex items-center justify-between gap-4">
        {/* Left nav */}
        <nav className="hidden lg:flex items-center gap-6 flex-1">
          <a href="/" className={linkCls}>{t('nav_home')}</a>
          {/* Menu dropdown */}
          <div className="relative" ref={menuRef}
            onMouseEnter={() => setMenuOpen(true)}
            onMouseLeave={() => setMenuOpen(false)}
          >
            <button
              onClick={() => setMenuOpen((v) => !v)}
              className={`flex items-center gap-1 text-[13px] font-semibold tracking-widest uppercase transition-colors ${scrolled ? 'text-[#5e4743] hover:text-[#3d2e2b]' : 'text-white/90 hover:text-white'}`}
            >
              {t('nav_menu')} <ChevronDown size={14} className={`transition-transform duration-200 ${menuOpen ? 'rotate-180' : ''}`} />
            </button>
            {menuOpen && (
              <div
                className="absolute top-full left-0 mt-1 w-56 bg-white rounded-lg shadow-xl overflow-hidden border border-neutral-100"
                style={{ zIndex: 9999 }}
              >
                {MENU_DROPDOWN.map((m) => (
                  <button key={m.label} onClick={() => { navigate(m.href); setMenuOpen(false); }}
                    className="w-full text-left px-5 py-3 text-[13px] font-medium text-neutral-700 hover:bg-[#ffc95218] hover:text-[#5e4743] transition-colors border-b border-neutral-50 last:border-0">
                    {m.label}
                  </button>
                ))}
              </div>
            )}
          </div>
          {/* Media link */}
          <Link to="/media" className={linkCls}>{t('nav_media')}</Link>
          {/* News dropdown */}
          <div className="relative" ref={newsRef}
            onMouseEnter={() => setNewsOpen(true)}
            onMouseLeave={() => setNewsOpen(false)}
          >
            <button
              onClick={() => setNewsOpen((v) => !v)}
              className={`flex items-center gap-1 text-[13px] font-semibold tracking-widest uppercase transition-colors ${scrolled ? 'text-[#5e4743] hover:text-[#3d2e2b]' : 'text-white/90 hover:text-white'}`}
            >
              {t('nav_news_recruit')} <ChevronDown size={14} className={`transition-transform duration-200 ${newsOpen ? 'rotate-180' : ''}`} />
            </button>
            {newsOpen && (
              <div
                className="absolute top-full left-0 mt-1 w-52 bg-white rounded-lg shadow-xl overflow-hidden border border-neutral-100"
                style={{ zIndex: 9999 }}
              >
                  <button onClick={() => { navigate('/blog'); setNewsOpen(false); }}
                    className="w-full text-left px-5 py-3 text-[13px] font-medium text-neutral-700 hover:bg-[#ffc95218] hover:text-[#5e4743] transition-colors border-b border-neutral-50">
                    {t('nav_news')}
                  </button>
                  <button onClick={() => { navigate('/#news-recruit'); setNewsOpen(false); const el = document.getElementById('news'); if (el) el.scrollIntoView({ behavior: 'smooth' }); }}
                    className="w-full text-left px-5 py-3 text-[13px] font-medium text-neutral-700 hover:bg-[#ffc95218] hover:text-[#5e4743] transition-colors">
                    {t('nav_recruit')}
                  </button>
              </div>
            )}
          </div>
        </nav>

        {/* Logo – center */}
        <div className="flex-shrink-0"><Logo dark={scrolled} /></div>

        {/* Right nav */}
        <nav className="hidden lg:flex items-center gap-3 flex-1 justify-end">
          <LangSwitcher scrolled={scrolled} />
          <button
            onClick={onContact}
            className={`text-[13px] font-semibold tracking-widest uppercase transition-all px-4 py-[7px] rounded-full border ${scrolled ? 'border-[#5e4743] text-[#5e4743] hover:bg-[#5e4743] hover:text-white' : 'border-white/70 text-white/90 hover:border-white hover:text-white'}`}
          >
            {t('nav_contact')}
          </button>
          <button
            onClick={onReservation}
            className="px-5 py-[7px] rounded-full text-[13px] font-semibold tracking-widest uppercase transition-all"
            style={{ backgroundColor: GOLD, color: BROWN }}
            onMouseEnter={(e) => { e.currentTarget.style.backgroundColor = BROWN; e.currentTarget.style.color = GOLD; }}
            onMouseLeave={(e) => { e.currentTarget.style.backgroundColor = GOLD; e.currentTarget.style.color = BROWN; }}
          >
            {t('nav_book')}
          </button>
        </nav>

        {/* Mobile hamburger */}
        <button className="lg:hidden p-2" style={{ color: scrolled ? BROWN : '#fff' }} onClick={() => setOpen(true)} aria-label="Menu">
          <MenuIcon size={26} />
        </button>
      </div>

      {/* Mobile full-screen overlay */}
      {open && (
        <div className="fixed inset-0 z-50 flex flex-col" style={{ backgroundColor: BROWN }}>
          <div className="flex items-center justify-between px-6 h-[72px]">
            <Logo dark={false} />
            <div className="flex items-center gap-3">
              <LangSwitcher scrolled={false} />
              <button onClick={() => setOpen(false)} aria-label="Close" style={{ color: GOLD }}><X size={28} /></button>
            </div>
          </div>
          <div className="flex-1 flex flex-col items-center justify-center gap-6 pb-12">
            <a href="/" onClick={() => setOpen(false)} className="text-white text-lg font-semibold tracking-widest uppercase">{t('nav_home')}</a>
            <div className="flex flex-col items-center gap-1 w-full px-8">
              <button
                onClick={() => setMobileMenuOpen((v) => !v)}
                className="text-white text-lg font-semibold tracking-widest uppercase flex items-center gap-2"
              >
                {t('nav_menu')} <ChevronDown size={16} className={`transition-transform duration-200 ${mobileMenuOpen ? 'rotate-180' : ''}`} />
              </button>
              {mobileMenuOpen && (
                <div className="mt-2 w-full bg-white/10 rounded-lg overflow-hidden">
                  {MENU_DROPDOWN.map((m) => (
                    <button key={m.label} onClick={() => { navigate(m.href); setOpen(false); setMobileMenuOpen(false); }}
                      className="w-full text-center py-3 text-[15px] font-medium border-b border-white/10 last:border-0"
                      style={{ color: GOLD }}>{m.label}</button>
                  ))}
                </div>
              )}
            </div>
            <Link to="/media" onClick={() => setOpen(false)} className="text-white text-lg font-semibold tracking-widest uppercase">{t('nav_media')}</Link>
            <Link to="/blog" onClick={() => setOpen(false)} className="text-white text-lg font-semibold tracking-widest uppercase">{t('nav_news')}</Link>
            <button onClick={() => { onContact(); setOpen(false); }} className="text-white text-lg font-semibold tracking-widest uppercase">{t('nav_contact')}</button>
            <button
              onClick={() => { onReservation(); setOpen(false); }}
              className="mt-4 px-10 py-3 rounded-full text-base font-semibold uppercase tracking-widest"
              style={{ backgroundColor: GOLD, color: BROWN }}
            >
              {t('nav_book')}
            </button>
          </div>
        </div>
      )}
    </header>
  );
}

const SLIDE_IMAGES = [IMG.heroGarden, IMG.spread, IMG.cozy];
const SLIDE_CAPTION_KEYS = ['hero_caption_1', 'hero_caption_2', 'hero_caption_3'];

function Hero() {
  const { t } = useLanguage();
  const [i, setI] = useState(0);
  useEffect(() => {
    const timer = setInterval(() => setI((p) => (p + 1) % SLIDE_IMAGES.length), 5500);
    return () => clearInterval(timer);
  }, []);
  const go = (d) => setI((p) => (p + d + SLIDE_IMAGES.length) % SLIDE_IMAGES.length);
  return (
    <section id="home" className="relative min-h-[100dvh] flex items-center justify-center overflow-hidden">
      {SLIDE_IMAGES.map((src, idx) => (
        <div key={idx}
          className="absolute inset-0 bg-cover bg-center transition-opacity duration-[1200ms]"
          style={{ backgroundImage: `url(${src})`, opacity: idx === i ? 1 : 0 }} />
      ))}
      <div className="absolute inset-0 bg-black/45" />
      <motion.div
        key={i}
        initial={{ opacity: 0, y: 30 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.9 }}
        className="relative z-10 text-center px-5"
      >
        <span className="font-typewriter text-sm md:text-base tracking-[0.35em] uppercase block mb-4" style={{ color: GOLD }}>
          {t(SLIDE_CAPTION_KEYS[i])}
        </span>
        <h1 className="font-section-title font-black text-white uppercase text-5xl md:text-8xl tracking-wide leading-none">
          Ngon Thị Hoa
        </h1>
        <p className="text-white/85 mt-4 tracking-[0.35em] text-sm md:text-base uppercase">Restaurant</p>
        <a href="#menu"
          className="inline-block mt-9 px-9 py-3.5 text-sm tracking-widest uppercase transition-colors duration-300 font-semibold"
          style={{ border: `2px solid ${GOLD}`, color: GOLD }}
          onMouseEnter={(e) => { e.currentTarget.style.backgroundColor = GOLD; e.currentTarget.style.color = BROWN; }}
          onMouseLeave={(e) => { e.currentTarget.style.backgroundColor = 'transparent'; e.currentTarget.style.color = GOLD; }}>
          {t('hero_view_menu')}
        </a>
      </motion.div>
      <button onClick={() => go(-1)} className="absolute left-4 md:left-8 top-1/2 -translate-y-1/2 z-10 text-white/70 hover:text-white" aria-label="Prev"><ChevronLeft size={42} /></button>
      <button onClick={() => go(1)} className="absolute right-4 md:right-8 top-1/2 -translate-y-1/2 z-10 text-white/70 hover:text-white" aria-label="Next"><ChevronRight size={42} /></button>
      <div className="absolute bottom-8 inset-x-0 flex justify-center gap-2.5 z-10">
        {SLIDE_IMAGES.map((_, idx) => (
          <button key={idx} onClick={() => setI(idx)}
            className={`w-2.5 h-2.5 rounded-full transition-all ${idx === i ? 'w-6' : 'bg-white/60'}`} style={idx === i ? { backgroundColor: GOLD } : {}} aria-label={`Slide ${idx + 1}`} />
        ))}
      </div>
      {/* Floating CTAs — right side */}
      <div className="fixed right-5 bottom-6 z-40 flex flex-col gap-3 items-center">
        <a href="https://wa.me/84967220100" target="_blank" rel="noopener noreferrer"
          className="w-[52px] h-[52px] rounded-full flex items-center justify-center shadow-lg text-white transition-transform hover:scale-110"
          style={{ backgroundColor: '#25D366' }} aria-label="WhatsApp">
          <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12.004 2C6.477 2 2 6.477 2 12.004c0 1.77.459 3.436 1.262 4.888L2 22l5.227-1.243A9.961 9.961 0 0012.004 22C17.523 22 22 17.523 22 12.004 22 6.477 17.523 2 12.004 2zm0 18.007a7.96 7.96 0 01-4.084-1.125l-.293-.174-3.042.724.748-2.97-.19-.303A7.964 7.964 0 014.003 12c0-4.41 3.59-8 7.999-8 4.41 0 8 3.59 8 8s-3.59 8.004-8 8.004z"/></svg>
        </a>
        <a href="https://fb.me/ngonthihoa" target="_blank" rel="noopener noreferrer"
          className="w-[52px] h-[52px] rounded-full flex items-center justify-center shadow-lg text-white transition-transform hover:scale-110"
          style={{ backgroundColor: '#0084FF' }} aria-label="Messenger">
          <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.477 2 2 6.145 2 11.259c0 2.849 1.371 5.394 3.526 7.095V21.5l3.35-1.851c.894.247 1.843.381 2.824.381 5.524 0 10-4.145 10-9.271C21.7 6.145 17.523 2 12 2zm1.019 12.478l-2.544-2.715-4.965 2.715 5.46-5.797 2.608 2.715 4.9-2.715-5.459 5.797z"/></svg>
        </a>
        <a href="tel:023665151000"
          className="w-[52px] h-[52px] rounded-full text-white flex items-center justify-center shadow-lg animate-pulse transition-transform hover:scale-110"
          style={{ backgroundColor: BROWN }} aria-label="Call">
          <Phone size={24} />
        </a>
      </div>
    </section>
  );
}

function Reveal({ children, delay = 0 }) {
  return (
    <motion.div
      initial={{ opacity: 0, y: 40 }}
      whileInView={{ opacity: 1, y: 0 }}
      viewport={{ once: true, margin: '-80px' }}
      transition={{ duration: 0.7, delay }}
    >
      {children}
    </motion.div>
  );
}

const HIGHLIGHT_KEYS = [
  { icon: '🌿', titleKey: 'h1_title', descKey: 'h1_desc', img: 'https://images.hostinger.com/c931d1b0-621b-47bb-bb01-b4f7c07905ed.png' },
  { icon: '🏆', titleKey: 'h2_title', descKey: 'h2_desc', img: 'https://images.hostinger.com/258a883b-85da-4537-86ab-b342499e3f66.png' },
  { icon: '🍽️', titleKey: 'h3_title', descKey: 'h3_desc', img: 'https://images.hostinger.com/237ea48f-d47c-442f-8bef-84a9e1cce438.png' },
  { icon: '🎵', titleKey: 'h4_title', descKey: 'h4_desc', img: 'https://images.hostinger.com/6bc2b932-a56c-421f-b25f-d33d6e7008d7.png' },
  { icon: '✅', titleKey: 'h5_title', descKey: 'h5_desc', img: 'https://images.hostinger.com/07faeb57-ee2b-484f-9b66-3c74ec0a2c2b.png' },
  { icon: '📍', titleKey: 'h6_title', descKey: 'h6_desc', img: 'https://images.hostinger.com/4b07fd5f-3038-4c64-ab6f-10befeb1fef6.png' },
];

function Welcome() {
  const { t } = useLanguage();
  return (
    <section id="about" className="py-24" style={{ backgroundColor: '#faf6ef' }}>
      <div className="max-w-[1140px] mx-auto px-5">
        <Reveal>
          <div className="text-center mb-16">
            <span className="font-script text-sm md:text-base" style={{ color: BROWN }}>{t('about_discover')}</span>
            <h2 className="font-section-title font-bold text-neutral-900 text-4xl md:text-5xl mt-4 mb-6" style={{ letterSpacing: '0.1em' }}>
              {t('about_title')}
            </h2>
            <div className="brush-divider max-w-[120px] mx-auto" />
          </div>
        </Reveal>
        <div className="grid md:grid-cols-2 gap-12 items-center mb-16">
          <Reveal>
            <div className="rounded-2xl overflow-hidden hov-zoom shadow-xl">
              <img src={IMG.musicians} alt="Ngon Thị Hoa - Tropical Garden" className="w-full h-[420px] object-cover" />
            </div>
          </Reveal>
          <Reveal delay={0.15}>
            <div>
              <h3 className="font-section-title font-bold text-2xl mb-4" style={{ color: BROWN }}>{t('about_history')}</h3>
              <p className="text-neutral-600 leading-relaxed mb-4">{t('about_desc')}</p>
              <p className="text-neutral-600 leading-relaxed mb-4">{t('about_body1')}</p>
              <p className="text-neutral-600 leading-relaxed mb-6" dangerouslySetInnerHTML={{ __html: t('about_body2').replace('"The Best of Vietnam 2026"', '<strong>"The Best of Vietnam 2026"</strong>') }} />
              <a href="#menu" className="inline-flex items-center gap-2 text-sm tracking-[0.2em] uppercase font-semibold transition-colors" style={{ color: BROWN }}>
                {t('about_view_menu')} <ArrowRight size={16} />
              </a>
            </div>
          </Reveal>
        </div>
        <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
          {HIGHLIGHT_KEYS.map((h, i) => (
            <Reveal key={h.titleKey} delay={i * 0.08}>
              <div className="rounded-2xl overflow-hidden shadow-md hover:shadow-xl transition-shadow group">
                <div className="relative h-44 overflow-hidden">
                  <img src={h.img} alt={t(h.titleKey)} className="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" />
                  <div className="absolute inset-0" style={{ background: 'linear-gradient(to bottom, rgba(255,201,82,0) 40%, rgba(255,201,82,0.85) 100%)' }} />
                </div>
                <div className="bg-white px-6 pt-4 pb-6">
                  <div className="text-3xl mb-2">{h.icon}</div>
                  <h4 className="font-typewriter font-semibold text-neutral-900 text-lg mb-2">{t(h.titleKey)}</h4>
                  <p className="text-neutral-500 text-sm leading-relaxed">{t(h.descKey)}</p>
                </div>
              </div>
            </Reveal>
          ))}
        </div>
      </div>
    </section>
  );
}

const _UNUSED_BLOG_POSTS = [
  {
    slug: 'ngon-thi-hoa-the-best-of-vietnam-2026',
    title: 'Ngon Thị Hoa Restaurant Được Vinh Danh "THE BEST OF VIETNAM 2026"',
    date: '21/05/2026',
    img: 'https://www.ngonthihoarestaurant.com/upload/blog/blog-1779346247.jpg',
    excerpt: 'Việc được trao tặng danh hiệu "THE BEST OF VIETNAM 2026" là dấu ấn quan trọng trong hành trình phát triển của Ngon Thị Hoa Restaurant – thương hiệu ẩm thực đang ngày càng khẳng định vị thế bằng sự chỉn chu trong chất lượng món ăn, phong cách phục vụ và trải nghiệm khách hàng.',
    url: 'https://www.ngonthihoarestaurant.com/vi/blog/ngon-thi-hoa-restaurant-duoc-vinh-danh-the-best-of-vietnam-2026',
  },
  {
    slug: 'dau-bep-ngon-tinh-tuy',
    title: 'Đầu Bếp NGON: Người Đem Tinh Túy Đến Thực Khách',
    date: '18/03/2024',
    img: 'https://www.ngonthihoarestaurant.com/upload/blog/blog-1710730848.png',
    excerpt: 'Tại gian bếp Ngon, với niềm đam mê với ẩm thực Việt, những người đầu bếp NGON vẫn hàng ngày tạo nên những món ăn tinh túy, đặc sắc và đậm chất truyền thống.',
    url: 'https://www.ngonthihoarestaurant.com/vi/blog/dau-bep-ngon-nguoi-dem-tinh-tuy-den-thuc-khach',
  },
  {
    slug: 'long-den-hoi-an',
    title: 'Lồng Đèn Hội An Và Giá Trị Truyền Thống Việt',
    date: '18/03/2024',
    img: 'https://www.ngonthihoarestaurant.com/upload/blog/blog-1710729439.jpg',
    excerpt: 'Đến NGON Thị Hoa ắt hẳn bạn dễ dàng bắt gặp những chiếc đèn lồng đủ sắc, kiểu dáng, hoa văn được giăng lối từ cổng cho đến mọi ngóc ngách nhà hàng.',
    url: 'https://www.ngonthihoarestaurant.com/vi/blog/long-den-hoi-an-va-gia-tri-truyen-thong-viet',
  },
  {
    slug: 'khong-gian-ngon-mua-ha',
    title: 'Không Gian Ngon Thị Hoa: Điểm Hẹn An Nhiên Cho Mùa Hạ',
    date: '12/03/2024',
    img: 'https://www.ngonthihoarestaurant.com/upload/blog/blog-1710226672.png',
    excerpt: 'Đà Nẵng đã bước vào những ngày hạ, những tia nắng chói chang đã len lỏi qua những kẽ lá tán cây trên khắp phố phường. Phố NGON cũng như Đà Nẵng, đang rực rỡ hơn bao giờ hết.',
    url: 'https://www.ngonthihoarestaurant.com/vi/blog/khong-gian-ngon-thi-hoa-diem-hen-an-nhien-cho-mua-ha-ruc-ro',
  },
  {
    slug: 'hoa-gao-pho-ngon',
    title: 'Đỏ Rực Sắc Màu Hoa Gạo Trong Lòng Phố NGON',
    date: '12/03/2024',
    img: 'https://www.ngonthihoarestaurant.com/upload/blog/blog-1710219799.png',
    excerpt: 'Đỏ rực sắc màu hoa gạo đã trở thành một phần không thể thiếu trong lòng phố NGON. Bước vào không gian Ngon Thị Hoa, ta sẽ bị cuốn hút vào sắc đỏ tươi rực của những cánh hoa gạo.',
    url: 'https://www.ngonthihoarestaurant.com/vi/blog/do-ruc-sac-mau-hoa-gao-trong-long-pho-ngon',
  },
  {
    slug: 'cay-trai-mua-ha',
    title: 'Cây Trái Mùa Hạ - Níu Giữ Kí Ức Thơ',
    date: '28/07/2023',
    img: 'https://www.ngonthihoarestaurant.com/upload/blog/blog-1690525079.jpg',
    excerpt: 'Mùa hạ mang đến những loại trái cây tươi ngon, gợi nhớ ký ức tuổi thơ ngọt ngào. Tại Ngon Thị Hoa, chúng tôi trân trọng từng hương vị mùa hè qua những món tráng miệng tinh tế.',
    url: 'https://www.ngonthihoarestaurant.com/vi/blog/cay-trai-mua-ha-niu-giu-ki-uc-tho',
  },
];

const JOB_KEYS = [
  { id: 1, titleKey: 'job1_title', deptKey: 'job1_dept', typeKey: 'job1_type', descKey: 'job1_desc', reqKeys: ['job1_r1','job1_r2','job1_r3'] },
  { id: 2, titleKey: 'job2_title', deptKey: 'job2_dept', typeKey: 'job2_type', descKey: 'job2_desc', reqKeys: ['job2_r1','job2_r2','job2_r3'] },
  { id: 3, titleKey: 'job3_title', deptKey: 'job3_dept', typeKey: 'job3_type', descKey: 'job3_desc', reqKeys: ['job3_r1','job3_r2','job3_r3'] },
  { id: 4, titleKey: 'job4_title', deptKey: 'job4_dept', typeKey: 'job4_type', descKey: 'job4_desc', reqKeys: ['job4_r1','job4_r2','job4_r3'] },
  { id: 5, titleKey: 'job5_title', deptKey: 'job5_dept', typeKey: 'job5_type', descKey: 'job5_desc', reqKeys: ['job5_r1','job5_r2','job5_r3'] },
];

function JobApplicationModal({ job, onClose }) {
  const { t } = useLanguage();
  const [form, setForm] = useState({ full_name: '', email: '', phone: '', cover_letter: '' });
  const [cvFile, setCvFile] = useState(null);
  const [submitting, setSubmitting] = useState(false);
  const [done, setDone] = useState(false);
  const set = (k) => (e) => setForm((p) => ({ ...p, [k]: e.target.value }));
  const submit = async (e) => {
    e.preventDefault();
    setSubmitting(true);
    try {
      const fd = new FormData();
      fd.append('full_name', form.full_name);
      fd.append('email', form.email);
      fd.append('phone', form.phone);
      fd.append('position', job.title);
      fd.append('cover_letter', form.cover_letter);
      if (cvFile) fd.append('cv_file', cvFile);
      await pb.collection('job_applications').create(fd);
      setDone(true);
    } catch (err) {
      console.error(err);
    } finally {
      setSubmitting(false);
    }
  };
  const inputCls = 'w-full px-4 py-3 border border-neutral-200 rounded-xl text-sm focus:outline-none focus:border-[#5e4743] bg-white';
  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60" onClick={onClose}>
      <div className="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto" onClick={(e) => e.stopPropagation()}>
        <div className="flex items-center justify-between p-6 border-b" style={{ borderColor: '#f0e8d8' }}>
          <div>
            <h3 className="font-typewriter font-bold text-xl" style={{ color: '#5e4743' }}>{t('apply_title')}</h3>
            <p className="text-sm text-neutral-500 mt-1">{job.title}</p>
          </div>
          <button onClick={onClose} className="p-2 rounded-full hover:bg-neutral-100"><X size={20} /></button>
        </div>
        <div className="p-6">
          {done ? (
            <div className="text-center py-8">
              <CheckCircle size={48} className="mx-auto mb-4" style={{ color: '#5e4743' }} />
              <h4 className="font-typewriter font-bold text-xl mb-2">{t('apply_success_title')}</h4>
              <p className="text-neutral-500">{t('apply_success_msg')}</p>
              <button onClick={onClose} className="mt-6 px-6 py-2.5 rounded-full text-sm font-semibold text-white" style={{ backgroundColor: '#5e4743' }}>{t('apply_close')}</button>
            </div>
          ) : (
            <form onSubmit={submit} className="flex flex-col gap-4">
              <div><label className="block text-xs font-medium text-neutral-500 mb-1.5">{t('apply_name')}</label><input required value={form.full_name} onChange={set('full_name')} className={inputCls} placeholder={t('res_placeholder_name')} /></div>
              <div><label className="block text-xs font-medium text-neutral-500 mb-1.5">{t('apply_email')}</label><input required type="email" value={form.email} onChange={set('email')} className={inputCls} placeholder="example@email.com" /></div>
              <div><label className="block text-xs font-medium text-neutral-500 mb-1.5">{t('apply_phone')}</label><input required value={form.phone} onChange={set('phone')} className={inputCls} placeholder="0901 234 567" /></div>
              <div><label className="block text-xs font-medium text-neutral-500 mb-1.5">{t('apply_cv')}</label>
                <label className="flex items-center gap-3 w-full px-4 py-3 border border-dashed border-neutral-300 rounded-xl cursor-pointer hover:border-[#5e4743] transition-colors">
                  <Upload size={16} className="text-neutral-400 flex-shrink-0" />
                  <span className="text-sm text-neutral-400">{cvFile ? cvFile.name : t('apply_cv_placeholder')}</span>
                  <input type="file" className="hidden" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" onChange={(e) => setCvFile(e.target.files[0])} />
                </label>
              </div>
              <div><label className="block text-xs font-medium text-neutral-500 mb-1.5">{t('apply_cover')}</label><textarea value={form.cover_letter} onChange={set('cover_letter')} rows={4} className={inputCls} placeholder={t('apply_cover_placeholder')} /></div>
              <button type="submit" disabled={submitting} className="flex items-center justify-center gap-2 py-3 rounded-full font-semibold text-sm text-white transition-opacity disabled:opacity-60" style={{ backgroundColor: '#5e4743' }}>
                {submitting ? t('apply_sending') : <><Send size={15} /> {t('apply_submit')}</>}
              </button>
            </form>
          )}
        </div>
      </div>
    </div>
  );
}

function NewsRecruitment({ onReservation }) {
  const { t } = useLanguage();
  const [tab, setTab] = useState('news');
  const [selectedJob, setSelectedJob] = useState(null);
  const JOB_POSITIONS = JOB_KEYS.map(j => ({
    ...j,
    title: t(j.titleKey),
    dept: t(j.deptKey),
    type: t(j.typeKey),
    desc: t(j.descKey),
    requirements: j.reqKeys.map(k => t(k)),
  }));
  return (
    <section id="news" className="py-24 bg-white">
      <div className="max-w-[1140px] mx-auto px-5">
        <Reveal>
          <div className="text-center mb-12">
            <span className="font-script text-sm md:text-base" style={{ color: '#5e4743' }}>{t('news_label')}</span>
            <h2 className="font-section-title font-bold text-neutral-900 text-4xl md:text-5xl mt-4" style={{ letterSpacing: '0.1em' }}>
              {t('news_title')}
            </h2>
            <div className="brush-divider max-w-[120px] mx-auto mt-6" />
          </div>
        </Reveal>
        <div className="flex justify-center gap-3 mb-12">
          {['news', 'jobs'].map((tb) => (
            <button key={tb} onClick={() => setTab(tb)}
              className="px-8 py-3 rounded-full text-sm font-semibold tracking-wide uppercase transition-all"
              style={tab === tb ? { backgroundColor: '#5e4743', color: '#fff' } : { backgroundColor: '#f0e8d8', color: '#5e4743' }}>
              {tb === 'news' ? t('news_tab') : t('jobs_tab')}
            </button>
          ))}
        </div>
        {tab === 'news' && (
          <div>
            <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
              {BLOG_POSTS.slice(0, 6).map((p, i) => (
                <Reveal key={p.slug} delay={i * 0.07}>
                  <article className="bg-white rounded-2xl overflow-hidden shadow-md border border-neutral-100 hover:shadow-xl transition-shadow flex flex-col h-full">
                    <div className="hov-zoom overflow-hidden h-48">
                      <img src={p.img} alt={p.title} className="w-full h-full object-cover" />
                    </div>
                    <div className="p-5 flex flex-col flex-1">
                      <p className="text-xs text-neutral-400 mb-2">{p.date}</p>
                      <h3 className="font-typewriter font-semibold text-neutral-900 text-base mb-3 line-clamp-2 leading-snug">{p.title}</h3>
                      <p className="text-neutral-500 text-sm leading-relaxed mb-4 flex-1 line-clamp-3">{p.excerpt}</p>
                      <div className="flex items-center gap-3 mt-auto pt-3 border-t border-neutral-100">
                        <Link to={`/blog/${p.slug}`}
                          className="inline-flex items-center gap-1 text-xs tracking-wide uppercase font-semibold transition-colors hover:underline"
                          style={{ color: '#5e4743' }}>
                          {t('news_read_more')} <ArrowRight size={13} />
                        </Link>
                        <button onClick={(e) => { e.stopPropagation(); onReservation && onReservation(); }}
                          className="ml-auto text-xs px-3 py-1.5 rounded-full font-semibold"
                          style={{ backgroundColor: '#ffc952', color: '#5e4743' }}>
                          {t('news_book')}
                        </button>
                      </div>
                    </div>
                  </article>
                </Reveal>
              ))}
            </div>
            <div className="text-center mt-10">
              <Link to="/blog"
                className="inline-flex items-center gap-2 px-8 py-3 rounded-full text-sm font-semibold uppercase tracking-wide border-2 transition-all hover:text-white"
                style={{ borderColor: '#5e4743', color: '#5e4743' }}
                onMouseEnter={(e) => { e.currentTarget.style.backgroundColor='#5e4743'; e.currentTarget.style.color='#fff'; }}
                onMouseLeave={(e) => { e.currentTarget.style.backgroundColor=''; e.currentTarget.style.color='#5e4743'; }}>
                {t('news_view_all')} <ArrowRight size={16} />
              </Link>
            </div>
          </div>
        )}
        {tab === 'jobs' && (
          <div className="flex flex-col gap-5">
            {JOB_POSITIONS.map((job, i) => (
              <Reveal key={job.id} delay={i * 0.07}>
                <div className="bg-white rounded-2xl border border-neutral-200 p-6 hover:shadow-lg transition-shadow">
                  <div className="flex flex-col sm:flex-row sm:items-center gap-4">
                    <div className="flex-1">
                      <div className="flex flex-wrap gap-2 mb-2">
                        <span className="text-xs px-2.5 py-1 rounded-full font-medium" style={{ backgroundColor: '#f0e8d8', color: '#5e4743' }}>{job.dept}</span>
                        <span className="text-xs px-2.5 py-1 rounded-full font-medium bg-neutral-100 text-neutral-600">{job.type}</span>
                      </div>
                      <h3 className="font-typewriter font-bold text-neutral-900 text-xl mb-2">{job.title}</h3>
                      <p className="text-neutral-500 text-sm leading-relaxed mb-3">{job.desc}</p>
                      <ul className="flex flex-col gap-1">
                        {job.requirements.map((r) => (
                          <li key={r} className="text-sm text-neutral-600 flex items-start gap-2">
                            <span style={{ color: '#ffc952' }} className="mt-0.5 flex-shrink-0">&#9679;</span>{r}
                          </li>
                        ))}
                      </ul>
                    </div>
                    <div className="flex-shrink-0">
                      <button onClick={() => setSelectedJob(job)}
                        className="flex items-center gap-2 px-6 py-3 rounded-full text-sm font-semibold text-white whitespace-nowrap"
                        style={{ backgroundColor: '#5e4743' }}>
                        <Briefcase size={15} /> {t('jobs_apply_btn')}
                      </button>
                    </div>
                  </div>
                </div>
              </Reveal>
            ))}
          </div>
        )}
      </div>
      {selectedJob && <JobApplicationModal job={selectedJob} onClose={() => setSelectedJob(null)} />}
    </section>
  );
}

function ContactModal({ onClose }) {
  const { t } = useLanguage();
  const [form, setForm] = useState({ full_name: '', email: '', phone: '', subject: '', message: '' });
  const [submitting, setSubmitting] = useState(false);
  const [done, setDone] = useState(false);
  const set = (k) => (e) => setForm((p) => ({ ...p, [k]: e.target.value }));
  const submit = async (e) => {
    e.preventDefault();
    setSubmitting(true);
    try {
      await pb.collection('contact_messages').create(form);
      setDone(true);
    } catch (err) {
      console.error(err);
    } finally {
      setSubmitting(false);
    }
  };
  const inputCls = 'w-full px-4 py-3 border border-neutral-200 rounded-xl text-sm focus:outline-none focus:border-[#5e4743] bg-white';
  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60" onClick={onClose}>
      <div className="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto" onClick={(e) => e.stopPropagation()}>
        <div className="flex items-center justify-between p-6 border-b" style={{ borderColor: '#f0e8d8' }}>
          <div>
            <h3 className="font-typewriter font-bold text-xl" style={{ color: '#5e4743' }}>{t('contact_title')}</h3>
            <p className="text-sm text-neutral-500 mt-1">{t('contact_subtitle')}</p>
          </div>
          <button onClick={onClose} className="p-2 rounded-full hover:bg-neutral-100"><X size={20} /></button>
        </div>
        <div className="p-6">
          {done ? (
            <div className="text-center py-8">
              <CheckCircle size={48} className="mx-auto mb-4" style={{ color: '#5e4743' }} />
              <h4 className="font-typewriter font-bold text-xl mb-2">{t('contact_success_title')}</h4>
              <p className="text-neutral-500">{t('contact_success_msg')}</p>
              <button onClick={onClose} className="mt-6 px-6 py-2.5 rounded-full text-sm font-semibold text-white" style={{ backgroundColor: '#5e4743' }}>{t('contact_close')}</button>
            </div>
          ) : (
            <form onSubmit={submit} className="flex flex-col gap-4">
              <div><label className="block text-xs font-medium text-neutral-500 mb-1.5">{t('contact_name')}</label><input required value={form.full_name} onChange={set('full_name')} className={inputCls} placeholder={t('res_placeholder_name')} /></div>
              <div><label className="block text-xs font-medium text-neutral-500 mb-1.5">{t('contact_email')}</label><input required type="email" value={form.email} onChange={set('email')} className={inputCls} placeholder="example@email.com" /></div>
              <div><label className="block text-xs font-medium text-neutral-500 mb-1.5">{t('contact_phone')}</label><input value={form.phone} onChange={set('phone')} className={inputCls} placeholder="0901 234 567" /></div>
              <div><label className="block text-xs font-medium text-neutral-500 mb-1.5">{t('contact_subject')}</label><input required value={form.subject} onChange={set('subject')} className={inputCls} placeholder={t('contact_subject_placeholder')} /></div>
              <div><label className="block text-xs font-medium text-neutral-500 mb-1.5">{t('contact_message')}</label><textarea required value={form.message} onChange={set('message')} rows={4} className={inputCls} placeholder={t('contact_message_placeholder')} /></div>
              <button type="submit" disabled={submitting} className="flex items-center justify-center gap-2 py-3 rounded-full font-semibold text-sm text-white transition-opacity disabled:opacity-60" style={{ backgroundColor: '#5e4743' }}>
                {submitting ? t('contact_sending') : <><Send size={15} /> {t('contact_send')}</>}
              </button>
            </form>
          )}
        </div>
      </div>
    </div>
  );
}

const REVIEW_DATA = [
  { textKey: 'review1_text', name: 'Mạnh Viên Vân' },
  { textKey: 'review2_text', name: 'Thu Hà' },
  { textKey: 'review3_text', name: 'Nguyễn Hoàng' },
];

function Testimonials() {
  const { t } = useLanguage();
  const [i, setI] = useState(0);
  useEffect(() => {
    const timer = setInterval(() => setI((p) => (p + 1) % REVIEW_DATA.length), 6000);
    return () => clearInterval(timer);
  }, []);
  return (
    <section id="reviews" className="bg-white py-24">
      <div className="max-w-3xl mx-auto px-5 text-center">
        <SectionTitle label={t('testimonials_label')} title={t('testimonials_title')} />
        <motion.div key={i} initial={{ opacity: 0 }} animate={{ opacity: 1 }} transition={{ duration: 0.6 }} className="mt-12">
          <div className="w-24 h-24 mx-auto rounded-full overflow-hidden ring-4" style={{ borderColor: RED, boxShadow: `0 0 0 3px ${RED}` }}>
            <img src={IMG.dessert} alt="" className="w-full h-full object-cover" />
          </div>
          <p className="text-neutral-600 leading-relaxed text-lg mt-8 max-w-xl mx-auto">{t(REVIEW_DATA[i].textKey)}</p>
          <div className="flex justify-center gap-1 mt-5">
            {Array.from({ length: 5 }).map((_, k) => <Star key={k} size={20} fill={RED} color={RED} />)}
          </div>
          <p className="uppercase tracking-[0.2em] text-sm text-neutral-800 mt-5">{REVIEW_DATA[i].name}</p>
        </motion.div>
        <div className="flex justify-center gap-2.5 mt-10">
          {REVIEW_DATA.map((_, idx) => (
            <button key={idx} onClick={() => setI(idx)}
              className={`w-2.5 h-2.5 rounded-full transition-all ${idx === i ? 'bg-[#5e4743]' : 'bg-neutral-300'}`} aria-label={`Review ${idx + 1}`} />
          ))}
        </div>
      </div>

      {/* Google Maps Reviews Embed */}
      <div className="max-w-[1140px] mx-auto px-5 mt-16">
        <div className="text-center mb-8">
          <span className="font-script text-sm md:text-base" style={{ color: RED }}>{t('google_label')}</span>
          <h3 className="font-section-title font-bold text-neutral-900 text-3xl md:text-4xl mt-4" style={{ letterSpacing: '0.1em' }}>{t('google_title')}</h3>
          <p className="text-neutral-500 text-sm mt-3">{t('google_desc')}</p>
        </div>

        <div className="grid md:grid-cols-[1fr_360px] gap-8 items-start">
          {/* Map Embed */}
          <div className="rounded-2xl overflow-hidden shadow-xl" style={{ height: '420px' }}>
            <iframe
              title="Ngon Thị Hoa Google Maps Reviews"
              src="https://maps.google.com/maps?q=Ngon+Thi+Hoa+Restaurant+100+Le+Quang+Dao+Ngu+Hanh+Son+Da+Nang&output=embed&hl=vi&z=17"
              width="100%"
              height="100%"
              style={{ border: 0 }}
              loading="lazy"
              allowFullScreen
              referrerPolicy="no-referrer-when-downgrade"
            />
          </div>

          {/* Info Panel */}
          <div className="flex flex-col gap-5">
            {/* Rating Card */}
            <div className="bg-neutral-50 rounded-2xl p-6 shadow-sm border border-neutral-100 text-center">
              <div className="flex justify-center gap-1 mb-2">
                {Array.from({ length: 5 }).map((_, k) => <Star key={k} size={22} fill="#ffc952" color="#ffc952" />)}
              </div>
              <p className="text-5xl font-bold text-neutral-900 font-typewriter mt-1">4.8</p>
              <p className="text-neutral-500 text-sm mt-1">{t('google_rating_desc')}</p>
              <a
                href="https://maps.google.com/?q=Ngon+Thi+Hoa+Restaurant+100+Le+Quang+Dao+Ngu+Hanh+Son+Da+Nang&hl=vi"
                target="_blank"
                rel="noreferrer"
                className="inline-flex items-center gap-2 mt-4 px-6 py-2.5 rounded-full text-sm font-semibold text-white transition-colors hover:opacity-90"
                style={{ backgroundColor: RED }}
              >
                <MapPin size={15} /> {t('google_view')}
              </a>
            </div>

            {/* Write Review CTA */}
            <div className="rounded-2xl p-6 shadow-sm text-center" style={{ backgroundColor: '#1d3226' }}>
              <p className="font-typewriter font-semibold text-white text-lg mb-2">{t('google_cta_title')}</p>
              <p className="text-white/60 text-sm mb-4">{t('google_cta_desc')}</p>
              <a
                href="https://maps.google.com/?q=Ngon+Thi+Hoa+Restaurant+100+Le+Quang+Dao+Ngu+Hanh+Son+Da+Nang"
                target="_blank"
                rel="noreferrer"
                className="inline-flex items-center gap-2 px-6 py-2.5 rounded-full text-sm font-semibold text-neutral-900 transition-all hover:scale-105"
                style={{ backgroundColor: '#ffc952' }}
              >
                <Star size={15} fill="#1d3226" color="#1d3226" /> {t('google_write')}
              </a>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}

function MediaTeaser() {
  const { t } = useLanguage();
  return (
    <section id="gallery" className="relative min-h-[70vh] flex items-center justify-center bg-cover bg-center bg-fixed" style={{ backgroundImage: `url(${IMG.videoWall})` }}>
      <div className="absolute inset-0 bg-black/50" />
      <div className="relative z-10 text-center px-5">
        <SectionTitle label={t('media_label')} title={t('media_title')} light />
        <p className="text-white/75 mt-4 text-sm max-w-md mx-auto">{t('media_subtitle')}</p>
        <div className="flex flex-col sm:flex-row items-center justify-center gap-4 mt-8">
          <Link to="/media"
            className="inline-flex items-center gap-2 px-8 py-3.5 text-sm tracking-widest uppercase font-typewriter transition-all"
            style={{ border: `2px solid ${GOLD}`, color: GOLD }}
            onMouseEnter={(e) => { e.currentTarget.style.backgroundColor = GOLD; e.currentTarget.style.color = BROWN; }}
            onMouseLeave={(e) => { e.currentTarget.style.backgroundColor = 'transparent'; e.currentTarget.style.color = GOLD; }}>
            <Play size={16} fill="currentColor" /> {t('media_view_all')}
          </Link>
        </div>
      </div>
    </section>
  );
}

const GOOGLE_MAPS_URL = 'https://share.google/Hva4u6cCiek1ZZhfA';

const GOOGLE_REVIEWS = [
  { textKey: 'review1_text', name: 'Mạnh Viên Vân', rating: 5 },
  { textKey: 'review2_text', name: 'Thu Hà', rating: 5 },
  { textKey: 'review3_text', name: 'Nguyễn Hoàng', rating: 5 },
];

function GoogleReviews() {
  const { t } = useLanguage();
  const [active, setActive] = useState(0);
  useEffect(() => {
    const timer = setInterval(() => setActive((p) => (p + 1) % GOOGLE_REVIEWS.length), 6000);
    return () => clearInterval(timer);
  }, []);
  return (
    <section className="py-20" style={{ backgroundColor: '#faf6ef' }}>
      <div className="max-w-[1140px] mx-auto px-5">
        <Reveal>
          <div className="text-center mb-12">
            <span className="font-script text-sm md:text-base block" style={{ color: BROWN }}>{t('google_section_label')}</span>
            <h2 className="font-section-title font-bold text-neutral-900 text-4xl md:text-5xl mt-4" style={{ letterSpacing: '0.1em' }}>
              {t('google_section_title')}
            </h2>
            <div className="brush-divider max-w-[120px] mx-auto mt-4" />
          </div>
        </Reveal>

        <div className="grid md:grid-cols-[320px_1fr] gap-10 items-start">
          {/* Rating panel */}
          <Reveal>
            <div className="bg-white rounded-2xl shadow-lg p-8 text-center border border-neutral-100">
              <div className="flex justify-center mb-2">
                <svg viewBox="0 0 74 24" className="h-6" aria-label="Google">
                  <path d="M9.24 8.19v2.46h5.88c-.18 1.38-.64 2.39-1.34 3.1-.86.86-2.2 1.8-4.54 1.8-3.62 0-6.45-2.92-6.45-6.54s2.83-6.54 6.45-6.54c1.95 0 3.38.77 4.43 1.76L15.4 2.5C13.86 1.04 11.82 0 9.24 0 4.28 0 .11 4.04.11 9s4.17 9 9.13 9c2.68 0 4.7-.88 6.28-2.52 1.62-1.62 2.13-3.91 2.13-5.75 0-.57-.04-1.1-.13-1.54H9.24z" fill="#4285F4"/>
                  <path d="M25 6.19c-3.21 0-5.83 2.44-5.83 5.81 0 3.34 2.62 5.81 5.83 5.81s5.83-2.46 5.83-5.81c0-3.37-2.62-5.81-5.83-5.81zm0 9.33c-1.76 0-3.28-1.45-3.28-3.52s1.52-3.52 3.28-3.52 3.28 1.46 3.28 3.52-1.52 3.52-3.28 3.52z" fill="#EA4335"/>
                  <path d="M53.58 7.49h-.09c-.57-.68-1.67-1.3-3.06-1.3C47.53 6.19 45 8.72 45 12c0 3.26 2.53 5.81 5.43 5.81 1.39 0 2.49-.62 3.06-1.32h.09v.81c0 2.22-1.19 3.41-3.1 3.41-1.56 0-2.53-1.12-2.93-2.07l-2.22.92c.64 1.54 2.33 3.43 5.15 3.43 2.99 0 5.52-1.76 5.52-6.05V6.49h-2.42v1zm-2.93 8.03c-1.76 0-3.1-1.5-3.1-3.52 0-2.05 1.34-3.52 3.1-3.52 1.74 0 3.1 1.5 3.1 3.54-.01 2.03-1.36 3.5-3.1 3.5z" fill="#4285F4"/>
                  <path d="M38 6.19c-3.21 0-5.83 2.44-5.83 5.81 0 3.34 2.62 5.81 5.83 5.81s5.83-2.46 5.83-5.81c0-3.37-2.62-5.81-5.83-5.81zm0 9.33c-1.76 0-3.28-1.45-3.28-3.52s1.52-3.52 3.28-3.52 3.28 1.46 3.28 3.52-1.52 3.52-3.28 3.52z" fill="#FBBC05"/>
                  <path d="M58 .24h2.51v17.57H58z" fill="#34A853"/>
                  <path d="M63.93 14.45c-.65 0-1.11-.3-1.42-.89l3.91-1.62-.13-.33c-.24-.64-.98-1.83-2.48-1.83-1.49 0-2.73 1.17-2.73 2.89 0 1.62 1.22 2.89 2.87 2.89 1.32 0 2.09-.81 2.41-1.28l-1.98-1.32c-.33.46-.78.49-1.45.49zm-.1-3.54c.51 0 .95.26 1.09.63l-2.61 1.08c-.03-1.17.85-1.71 1.52-1.71z" fill="#EA4335"/>
                </svg>
              </div>
              <div className="text-7xl font-black font-typewriter mt-4" style={{ color: BROWN }}>4.8</div>
              <div className="flex justify-center gap-1 my-3">
                {Array.from({ length: 5 }).map((_, k) => <Star key={k} size={22} fill={GOLD} color={GOLD} />)}
              </div>
              <p className="text-neutral-500 text-sm mb-6">{t('google_review_count')}</p>
              <a href={GOOGLE_MAPS_URL} target="_blank" rel="noreferrer"
                className="block w-full py-3 rounded-full text-sm font-semibold text-white text-center transition-opacity hover:opacity-90 mb-3"
                style={{ backgroundColor: BROWN }}>
                {t('google_view_all_reviews')}
              </a>
              <a href={GOOGLE_MAPS_URL} target="_blank" rel="noreferrer"
                className="block w-full py-3 rounded-full text-sm font-semibold text-center transition-all border-2 hover:text-white"
                style={{ borderColor: GOLD, color: BROWN }}
                onMouseEnter={(e) => { e.currentTarget.style.backgroundColor = GOLD; }}
                onMouseLeave={(e) => { e.currentTarget.style.backgroundColor = 'transparent'; }}>
                <span className="flex items-center justify-center gap-2"><Star size={14} fill={GOLD} color={GOLD} /> {t('google_write_review')}</span>
              </a>
            </div>
          </Reveal>

          {/* Reviews carousel */}
          <Reveal delay={0.1}>
            <div className="flex flex-col gap-4">
              <AnimatePresence mode="wait">
                <motion.div key={active}
                  initial={{ opacity: 0, y: 16 }} animate={{ opacity: 1, y: 0 }} exit={{ opacity: 0, y: -16 }}
                  transition={{ duration: 0.45 }}
                  className="bg-white rounded-2xl p-8 shadow-md border border-neutral-100">
                  <div className="flex items-center gap-1 mb-4">
                    {Array.from({ length: GOOGLE_REVIEWS[active].rating }).map((_, k) => <Star key={k} size={18} fill={GOLD} color={GOLD} />)}
                  </div>
                  <p className="text-neutral-700 text-base leading-relaxed mb-6 italic">&ldquo;{t(GOOGLE_REVIEWS[active].textKey)}&rdquo;</p>
                  <div className="flex items-center gap-3">
                    <div className="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm" style={{ backgroundColor: BROWN }}>
                      {GOOGLE_REVIEWS[active].name.charAt(0)}
                    </div>
                    <div>
                      <p className="font-semibold text-neutral-900 text-sm">{GOOGLE_REVIEWS[active].name}</p>
                      <p className="text-xs text-neutral-400">Google Review</p>
                    </div>
                    <div className="ml-auto">
                      <svg viewBox="0 0 24 24" className="w-6 h-6" fill="none">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15v-4H7l5-8v4h4l-5 8z" fill="#4285F4"/>
                      </svg>
                    </div>
                  </div>
                </motion.div>
              </AnimatePresence>

              {/* Dots */}
              <div className="flex justify-center gap-2.5 mt-2">
                {GOOGLE_REVIEWS.map((_, idx) => (
                  <button key={idx} onClick={() => setActive(idx)}
                    className={`w-2.5 h-2.5 rounded-full transition-all ${idx === active ? 'w-6' : 'bg-neutral-300'}`}
                    style={idx === active ? { backgroundColor: BROWN } : {}}
                    aria-label={`Review ${idx + 1}`} />
                ))}
              </div>

              {/* Map embed */}
              <div className="rounded-2xl overflow-hidden shadow-md mt-2" style={{ height: '220px' }}>
                <iframe
                  title="Ngon Thị Hoa Google Maps"
                  src="https://maps.google.com/maps?q=Ngon+Thi+Hoa+Restaurant+100+Le+Quang+Dao+Ngu+Hanh+Son+Da+Nang&output=embed&hl=vi&z=16"
                  width="100%" height="100%"
                  style={{ border: 0 }}
                  loading="lazy" allowFullScreen referrerPolicy="no-referrer-when-downgrade"
                />
              </div>
            </div>
          </Reveal>
        </div>
      </div>
    </section>
  );
}

function Footer() {
  const { t } = useLanguage();
  return (
    <footer id="contact" className="text-neutral-300 pt-16 pb-8" style={{ backgroundColor: BROWN_DARK }}>
      <div className="max-w-[1140px] mx-auto px-5 grid md:grid-cols-3 gap-10">
        <div>
          <img src="https://horizons-cdn.hostinger.com/a2bb8e3c-639e-4083-bf32-32f3f7f0cebb/d5bf5030568dea1dbf0b975ffc8f98ce.png" alt="Ngon Thị Hoa – Tropical Garden" className="w-24 h-24 object-contain" style={{ filter: 'brightness(0) invert(1)' }} />
          <p className="text-sm leading-relaxed mt-3 text-neutral-400">{t('footer_desc')}</p>
          <div className="flex gap-3 mt-5">
            <a href="https://www.facebook.com/NgonThiHoa" target="_blank" rel="noreferrer"
              className="w-9 h-9 rounded-full border border-neutral-600 flex items-center justify-center transition-all hover:scale-110"
              onMouseEnter={(e)=>{e.currentTarget.style.backgroundColor=GOLD;e.currentTarget.style.borderColor=GOLD;e.currentTarget.style.color=BROWN;}} onMouseLeave={(e)=>{e.currentTarget.style.backgroundColor='';e.currentTarget.style.borderColor='';e.currentTarget.style.color='';}} aria-label="Facebook">
              <Facebook size={16} />
            </a>
            <a href="https://www.tripadvisor.com.vn/Restaurant_Review-g298085-d20139533-Reviews-Ngon_Thi_Hoa_Restaurant-Da_Nang.html" target="_blank" rel="noreferrer"
              className="w-9 h-9 rounded-full border border-neutral-600 flex items-center justify-center transition-all hover:scale-110"
              onMouseEnter={(e)=>{e.currentTarget.style.backgroundColor=GOLD;e.currentTarget.style.borderColor=GOLD;e.currentTarget.style.color=BROWN;}} onMouseLeave={(e)=>{e.currentTarget.style.backgroundColor='';e.currentTarget.style.borderColor='';e.currentTarget.style.color='';}} aria-label="Tripadvisor">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12.006 4.295c-2.67 0-5.338.784-7.648 2.35H1l1.966 2.148a6.4 6.4 0 00-.878 3.262 6.437 6.437 0 006.44 6.438 6.42 6.42 0 004.481-1.807l1.006 1.104 1.005-1.104a6.416 6.416 0 004.482 1.807 6.437 6.437 0 006.44-6.438 6.4 6.4 0 00-.878-3.262L23 6.645h-3.358c-2.31-1.566-4.978-2.35-7.636-2.35zM12 6.357c1.39 0 2.72.254 3.95.73C14.594 7.48 13.34 8.48 12 8.48c-1.338 0-2.594-1-3.95-1.394A10.396 10.396 0 0112 6.357zM6.528 9.016a4.456 4.456 0 110 8.912 4.456 4.456 0 010-8.912zm10.944 0a4.456 4.456 0 110 8.912 4.456 4.456 0 010-8.912zM6.528 11a2.472 2.472 0 100 4.944A2.472 2.472 0 006.528 11zm10.944 0a2.472 2.472 0 100 4.944 2.472 2.472 0 000-4.944zm-10.944.988a1.484 1.484 0 110 2.968 1.484 1.484 0 010-2.968zm10.944 0a1.484 1.484 0 110 2.968 1.484 1.484 0 010-2.968z"/></svg>
            </a>
          </div>
        </div>
        <div>
          <div className="flex items-center gap-3 mb-5"><div className="brush-divider w-6" /><h5 className="font-typewriter uppercase tracking-widest text-white">{t('footer_contact')}</h5></div>
          <ul className="space-y-3 text-sm text-neutral-400">
            <li className="flex gap-3"><MapPin size={18} className="shrink-0 mt-0.5" style={{ color: GOLD }} /> 100 Lê Quang Đạo, Ngũ Hành Sơn, Đà Nẵng</li>
            <li className="flex gap-3"><Phone size={18} className="shrink-0 mt-0.5" style={{ color: GOLD }} /> 02366 515 100 | 0967 220 100 | 098 481 88 80</li>
            <li className="flex gap-3"><span className="shrink-0 mt-0.5 text-sm" style={{ color: GOLD }}>@</span> info@ngonthihoarestaurant.com</li>
            <li className="flex gap-3"><span className="shrink-0 mt-0.5 text-sm" style={{ color: GOLD }}>W</span> www.ngonthihoarestaurant.com</li>
          </ul>
        </div>
        <div>
          <div className="flex items-center gap-3 mb-5"><div className="brush-divider w-6" /><h5 className="font-typewriter uppercase tracking-widest text-white">{t('footer_hours')}</h5></div>
          <ul className="space-y-2 text-sm text-neutral-400">
            <li className="flex justify-between border-b border-neutral-800 pb-2"><span>Hằng ngày / Daily</span><span>6:30 - 22:00</span></li>
          </ul>
        </div>
      </div>

      <div className="max-w-[1140px] mx-auto px-5 mt-8 flex justify-center">
        <img src="https://images.hostinger.com/bc1f6ef2-9cc1-4966-a917-06912b6c3c74.png" alt="" className="h-14 opacity-50" />
      </div>
      <div className="max-w-[1140px] mx-auto px-5 mt-6 pt-6 border-t text-center text-xs" style={{ borderColor: BROWN, color: '#ffc95299' }}>
        {t('footer_copy')}
      </div>
    </footer>
  );
}


function OurMenu() {
  const { t } = useLanguage();
  const [lightbox, setLightbox] = useState(null);
  const [searchParams] = useSearchParams();
  const groupParam = searchParams.get('group');
  const initialGroup = MENU_GROUPS[groupParam] ? groupParam : 'sang';
  const [activeGroup, setActiveGroup] = useState(initialGroup);
  const group = MENU_GROUPS[activeGroup] || MENU_GROUPS.sang;
  const [activeCat, setActiveCat] = useState(group.categories[0].id);
  const cat = group.categories.find((c) => c.id === activeCat) || group.categories[0];
  useEffect(() => {
    if (groupParam && MENU_GROUPS[groupParam]) {
      setActiveGroup(groupParam);
      setActiveCat(MENU_GROUPS[groupParam].categories[0].id);
      setTimeout(() => { const el = document.getElementById('menu'); if (el) el.scrollIntoView({ behavior: 'smooth' }); }, 300);
    }
  }, [groupParam]);
  const handleGroupChange = (gid) => { setActiveGroup(gid); setActiveCat(MENU_GROUPS[gid].categories[0].id); };
  return (
    <section id="menu" className="py-24" style={{ backgroundColor: '#1d3226' }}>
      <div className="max-w-[1240px] mx-auto px-5">
        <div className="text-center mb-10">
          <span className="font-script text-sm md:text-base" style={{ color: GOLD }}>{t('menu_label')}</span>
          <h2 className="font-section-title font-bold text-white text-4xl md:text-5xl mt-4" style={{ letterSpacing: '0.1em' }}>{t('menu_title')}</h2>
        </div>
        <div className="flex flex-wrap justify-center gap-3 mb-10">
          {Object.values(MENU_GROUPS).map((g) => (
            <button key={g.id} onClick={() => handleGroupChange(g.id)}
              className="px-6 py-2.5 rounded-full text-sm font-semibold uppercase tracking-wide transition-all"
              style={activeGroup === g.id ? { backgroundColor: GOLD, color: '#1d3226' } : { backgroundColor: 'rgba(255,255,255,0.1)', color: '#fff' }}>
              {g.label}
            </button>
          ))}
        </div>
        <div className="grid lg:grid-cols-[240px_1fr] gap-8">
          <div className="flex lg:flex-col gap-2 overflow-x-auto lg:overflow-visible pb-2">
            {group.categories.map((c) => (
              <button key={c.id} onClick={() => setActiveCat(c.id)}
                className="text-left px-4 py-3 rounded-xl text-xs font-medium transition-all whitespace-nowrap lg:whitespace-normal flex-shrink-0"
                style={activeCat === c.id ? { backgroundColor: GOLD, color: '#1d3226' } : { backgroundColor: 'rgba(255,255,255,0.06)', color: '#ffffffcc' }}>
                {c.label}
              </button>
            ))}
          </div>
          <div className="rounded-2xl p-6 md:p-10" style={{ border: '2px solid ' + GOLD, backgroundColor: 'rgba(0,0,0,0.2)' }}>
            <h3 className="font-typewriter font-bold text-2xl md:text-3xl text-center mb-2" style={{ color: GOLD }}>{cat.label}</h3>
            {cat.en && <p className="text-center text-sm mb-8" style={{ color: '#ffffff88' }}>{cat.en}</p>}
            {cat.note && <p className="text-center text-xs mb-6 italic" style={{ color: '#ffffff99' }}>{cat.note}</p>}
            {cat.menuImages ? (
                <div className={cat.menuImages.length > 1 ? 'grid md:grid-cols-2 gap-4' : 'flex flex-col items-center'}>
                  {cat.menuImages.map((imgUrl, idx) => (
                    <button
                      key={idx}
                      type="button"
                      onClick={() => setLightbox({ images: cat.menuImages, index: idx })}
                      className="group relative rounded-xl overflow-hidden shadow-xl cursor-zoom-in block w-full"
                      style={{ border: '1px solid rgba(240,193,75,0.3)' }}
                      aria-label={`Phóng to ${cat.label} - ${idx + 1}`}
                    >
                      <img src={imgUrl} alt={`${cat.label} - ${idx + 1}`} className="w-full h-auto object-contain transition-transform duration-500 group-hover:scale-[1.02]" loading="lazy" />
                      <span className="pointer-events-none absolute inset-0 flex items-center justify-center bg-black/0 group-hover:bg-black/25 transition-colors">
                        <span className="opacity-0 group-hover:opacity-100 transition-opacity rounded-full p-3" style={{ backgroundColor: GOLD, color: '#3d2e2b' }}>
                          <ZoomIn size={22} />
                        </span>
                      </span>
                    </button>
                  ))}
                </div>
            ) : cat.subSections ? (
              <div>
                {cat.shotBottle && (
                  <div className="grid grid-cols-3 text-xs font-semibold mb-3 px-1" style={{ color: '#ffffff99' }}>
                    <span className="col-span-1">Name</span><span className="text-center">Shot</span><span className="text-right">Bottle</span>
                  </div>
                )}
                {cat.subSections.map((sec, si) => (
                  <div key={si} className="mb-6">
                    <p className="text-xs font-semibold uppercase tracking-widest mb-3" style={{ color: GOLD }}>{sec.heading}</p>
                    <div className="grid md:grid-cols-2 gap-x-10 gap-y-4">
                      {(sec.dishes || []).map((d, i) => (
                        <div key={i} className="flex flex-wrap items-baseline gap-x-3 pb-3" style={{ borderBottom: '1px dashed rgba(255,255,255,0.12)' }}>
                          <div className="flex-1 min-w-[50%]">
                            <p className="text-white font-medium text-sm leading-snug">{d.name}</p>
                            {d.desc && <p className="text-xs mt-1 leading-relaxed" style={{ color: '#ffffff70' }}>{d.desc}</p>}
                          </div>
                          <div className="text-right text-xs font-semibold" style={{ color: GOLD }}>
                            {d.shotPrice && <div>Shot: {d.shotPrice}</div>}
                            {d.bottlePrice && <div>Bottle: {d.bottlePrice}</div>}
                            {d.price && <div>{d.price}</div>}
                          </div>
                        </div>
                      ))}
                    </div>
                  </div>
                ))}
                {(cat.dishes || []).length > 0 && (
                  <div className="grid md:grid-cols-2 gap-x-10 gap-y-4 mt-4">
                    {cat.dishes.map((d, i) => (
                      <div key={i} className="flex flex-wrap items-baseline gap-x-3 pb-3" style={{ borderBottom: '1px dashed rgba(255,255,255,0.12)' }}>
                        <div className="flex-1 min-w-[60%]">
                          <p className="text-white font-medium text-sm leading-snug">{d.name}</p>
                          {d.desc && <p className="text-xs mt-1 leading-relaxed" style={{ color: '#ffffff70' }}>{d.desc}</p>}
                        </div>
                        <div className="text-right text-sm font-semibold" style={{ color: GOLD }}>{d.price}</div>
                      </div>
                    ))}
                  </div>
                )}
              </div>
            ) : (
              <div className="grid md:grid-cols-2 gap-x-10 gap-y-5">
                {(cat.dishes || []).map((d, i) => (
                  <div key={i} className="flex flex-wrap items-baseline gap-x-3 pb-3" style={{ borderBottom: '1px dashed rgba(255,255,255,0.12)' }}>
                    <div className="flex-1 min-w-[60%]">
                      <p className="text-white font-medium text-sm leading-snug">{d.name}</p>
                      {d.desc && <p className="text-xs mt-1 leading-relaxed" style={{ color: '#ffffff70' }}>{d.desc}</p>}
                    </div>
                    <div className="text-right text-sm font-semibold" style={{ color: GOLD }}>
                      {d.shot && <div className="text-xs">Shot: {d.shot}</div>}
                      {d.bottle && <div className="text-xs">Bottle: {d.bottle}</div>}
                      {d.price && <div>{d.price}</div>}
                    </div>
                  </div>
                ))}
              </div>
            )}
            <p className="text-center text-xs mt-8 italic" style={{ color: '#ffffff60' }}>{t('menu_vat')}</p>
          </div>
        </div>
        {lightbox && (
          <MenuImageLightbox images={lightbox.images} initialIndex={lightbox.index} onClose={() => setLightbox(null)} />
        )}
      </div>
    </section>
  );
}

export default function HomePage() {
  const [contactOpen, setContactOpen] = useState(false);
  const [reservationOpen, setReservationOpen] = useState(false);
  const openReservation = () => setReservationOpen(true);
  return (
    <div className="bg-white">
      <Header onContact={() => setContactOpen(true)} onReservation={openReservation} />
      <Hero />
      <Welcome />
      <OurMenu />
      <NewsRecruitment onReservation={openReservation} />
      <MediaTeaser />
      <GoogleReviews />
      <Footer />
      {contactOpen && <ContactModal onClose={() => setContactOpen(false)} />}
      {reservationOpen && <ReservationModal onClose={() => setReservationOpen(false)} />}
    </div>
  );
}
