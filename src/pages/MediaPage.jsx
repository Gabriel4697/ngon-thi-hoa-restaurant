import React, { useState } from 'react';
import { Link } from 'react-router-dom';
import { Helmet } from 'react-helmet';
import { motion, AnimatePresence } from 'framer-motion';
import { X, ChevronLeft, ChevronRight, Play, Image as ImageIcon } from 'lucide-react';
import { useLanguage } from '@/context/LanguageContext';
import ReservationModal from '@/components/ReservationModal';

const BROWN = '#5e4743';
const GOLD = '#ffc952';
const BROWN_DARK = '#3d2e2b';

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

const MEDIA_ITEMS = [
  { id: 1, type: 'photo', url: 'https://images.hostinger.com/c46a9991-da86-4740-82e9-88191c8b3704.png', caption_vi: 'Không gian vườn nhiệt đới', caption_en: 'Tropical Garden Space', caption_zh: '热带花园空间', category_vi: 'Không gian', category_en: 'Interior', category_zh: '室内' },
  { id: 2, type: 'photo', url: 'https://images.hostinger.com/70ecfb52-9475-4a62-8de1-20668b8bfd96.png', caption_vi: 'Biểu diễn âm nhạc truyền thống', caption_en: 'Traditional Music Performance', caption_zh: '传统音乐演奏', category_vi: 'Sự kiện', category_en: 'Events', category_zh: '活动' },
  { id: 3, type: 'photo', url: 'https://images.hostinger.com/940e0864-79ba-47f2-bab9-e480e59d00b6.png', caption_vi: 'Tiệc ăn phong phú', caption_en: 'Abundant Vietnamese Feast', caption_zh: '丰盛的越南宴席', category_vi: 'Ẩm thực', category_en: 'Food', category_zh: '美食' },
  { id: 4, type: 'photo', url: 'https://images.hostinger.com/31e5b023-bf1b-49ca-9e32-8c8117f5eccc.png', caption_vi: 'Hoa sen – biểu tượng Việt Nam', caption_en: 'Lotus – Vietnamese Symbol', caption_zh: '莲花——越南象征', category_vi: 'Không gian', category_en: 'Interior', category_zh: '室内' },
  { id: 5, type: 'photo', url: 'https://images.hostinger.com/769e5477-ac4e-4a68-9321-2d873267c696.png', caption_vi: 'Góc ngồi ấm cúng', caption_en: 'Cozy Seating Corner', caption_zh: '温馨座位角落', category_vi: 'Không gian', category_en: 'Interior', category_zh: '室内' },
  { id: 6, type: 'photo', url: 'https://images.hostinger.com/c58e21f0-10ac-4729-b279-c4d0cb084d5b.png', caption_vi: 'Hải sản tươi sống', caption_en: 'Fresh Seafood', caption_zh: '新鲜海鲜', category_vi: 'Ẩm thực', category_en: 'Food', category_zh: '美食' },
  { id: 7, type: 'photo', url: 'https://images.hostinger.com/8a6da3f1-0e14-4a63-99d5-ffcfd86b73f8.png', caption_vi: 'Sân vườn ngoài trời', caption_en: 'Outdoor Patio Garden', caption_zh: '户外花园露台', category_vi: 'Không gian', category_en: 'Interior', category_zh: '室内' },
  { id: 8, type: 'photo', url: 'https://images.hostinger.com/db8469b1-38df-4d43-b7ba-ee923ce64798.png', caption_vi: 'Bữa tối lãng mạn', caption_en: 'Romantic Dinner Setting', caption_zh: '浪漫晚餐布置', category_vi: 'Ẩm thực', category_en: 'Food', category_zh: '美食' },
  { id: 9, type: 'photo', url: 'https://images.hostinger.com/dd8d528b-154e-4304-8c0b-23b41936c7ef.png', caption_vi: 'Bữa trưa đậm chất Việt', caption_en: 'Authentic Vietnamese Lunch', caption_zh: '正宗越南午餐', category_vi: 'Ẩm thực', category_en: 'Food', category_zh: '美食' },
  { id: 10, type: 'photo', url: 'https://images.hostinger.com/0361f7c0-b13c-42bd-8806-4c5cfa258d47.png', caption_vi: 'Món khai vị tinh tế', caption_en: 'Exquisite Appetizers', caption_zh: '精致开胃菜', category_vi: 'Ẩm thực', category_en: 'Food', category_zh: '美食' },
  { id: 11, type: 'photo', url: 'https://images.hostinger.com/9aa44134-376f-4da6-876f-b136cdcab756.png', caption_vi: 'Tráng miệng ngọt ngào', caption_en: 'Sweet Desserts', caption_zh: '甜蜜甜点', category_vi: 'Ẩm thực', category_en: 'Food', category_zh: '美食' },
  { id: 12, type: 'photo', url: 'https://images.hostinger.com/d3989bce-ced1-4669-a5c2-632cc5283cd2.png', caption_vi: 'Không gian tiệc đêm', caption_en: 'Evening Banquet Ambience', caption_zh: '夜晚宴会氛围', category_vi: 'Không gian', category_en: 'Interior', category_zh: '室内' },
  { id: 13, type: 'video', url: 'https://images.hostinger.com/d3989bce-ced1-4669-a5c2-632cc5283cd2.png', videoUrl: 'https://www.youtube.com/embed/dQw4w9WgXcQ', caption_vi: 'Giới thiệu nhà hàng Ngon Thị Hoa', caption_en: 'Ngon Thi Hoa Restaurant Introduction', caption_zh: 'Ngon Thị Hoa餐厅介绍', category_vi: 'Video', category_en: 'Video', category_zh: '视频' },
  { id: 14, type: 'video', url: 'https://images.hostinger.com/c46a9991-da86-4740-82e9-88191c8b3704.png', videoUrl: 'https://www.youtube.com/embed/dQw4w9WgXcQ', caption_vi: 'Không gian vườn nhiệt đới', caption_en: 'Tropical Garden Tour', caption_zh: '热带花园游览', category_vi: 'Video', category_en: 'Video', category_zh: '视频' },
];

function LangSwitcher() {
  const { lang, setLang } = useLanguage();
  const flags = [
    { code: 'vi', flag: '🇻🇳', label: 'VI' },
    { code: 'en', flag: '🇬🇧', label: 'EN' },
    { code: 'zh', flag: '🇨🇳', label: 'ZH' },
    { code: 'ko', flag: '🇰🇷', label: 'KO' },
  ];
  return (
    <div className="flex items-center gap-1">
      {flags.map(({ code, flag, label }) => (
        <button key={code} onClick={() => setLang(code)}
          className={`flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold transition-all ${lang === code ? 'bg-[#ffc952] text-[#5e4743]' : 'text-white/80 hover:text-white'}`}
          title={label}>
          <span>{flag}</span>
          <span className="hidden sm:inline">{label}</span>
        </button>
      ))}
    </div>
  );
}

function Lightbox({ items, index, onClose, onPrev, onNext }) {
  const { t, lang } = useLanguage();
  const item = items[index];
  const captionKey = `caption_${lang}`;
  return (
    <AnimatePresence>
      <motion.div
        initial={{ opacity: 0 }}
        animate={{ opacity: 1 }}
        exit={{ opacity: 0 }}
        className="fixed inset-0 z-50 bg-black/95 flex items-center justify-center"
        onClick={onClose}
      >
        <button onClick={onClose} className="absolute top-5 right-5 z-10 text-white/80 hover:text-white p-2">
          <X size={30} />
        </button>
        <button onClick={(e) => { e.stopPropagation(); onPrev(); }} className="absolute left-4 top-1/2 -translate-y-1/2 z-10 text-white/70 hover:text-white p-2">
          <ChevronLeft size={40} />
        </button>
        <button onClick={(e) => { e.stopPropagation(); onNext(); }} className="absolute right-4 top-1/2 -translate-y-1/2 z-10 text-white/70 hover:text-white p-2">
          <ChevronRight size={40} />
        </button>
        <div className="max-w-5xl w-full mx-8" onClick={(e) => e.stopPropagation()}>
          {item.type === 'video' ? (
            <div className="relative" style={{ paddingBottom: '56.25%' }}>
              <iframe
                src={item.videoUrl}
                className="absolute inset-0 w-full h-full rounded-xl"
                allow="autoplay; encrypted-media"
                allowFullScreen
                title={item[captionKey]}
              />
            </div>
          ) : (
            <img src={item.url} alt={item[captionKey]} className="w-full max-h-[80vh] object-contain rounded-xl" />
          )}
          <p className="text-white/80 text-center mt-4 text-sm">{item[captionKey]}</p>
          <p className="text-center text-xs mt-1" style={{ color: GOLD }}>{index + 1} / {items.length}</p>
        </div>
      </motion.div>
    </AnimatePresence>
  );
}

export default function MediaPage() {
  const { t, lang } = useLanguage();
  const [filter, setFilter] = useState('all');
  const [lightboxIndex, setLightboxIndex] = useState(null);
  const [resOpen, setResOpen] = useState(false);

  const filtered = MEDIA_ITEMS.filter(item => {
    if (filter === 'photos') return item.type === 'photo';
    if (filter === 'videos') return item.type === 'video';
    return true;
  });

  const openLightbox = (idx) => setLightboxIndex(idx);
  const closeLightbox = () => setLightboxIndex(null);
  const prevItem = () => setLightboxIndex(i => (i - 1 + filtered.length) % filtered.length);
  const nextItem = () => setLightboxIndex(i => (i + 1) % filtered.length);

  const captionKey = `caption_${lang}`;
  const categoryKey = `category_${lang}`;

  return (
    <div className="min-h-screen bg-[#faf6ef]">
      <Helmet>
        <title>{t('media_page_title')}</title>
        <meta name="description" content={t('media_page_desc')} />
      </Helmet>

      {/* Header */}
      <header className="fixed top-0 inset-x-0 z-40 bg-white shadow-md">
        <div className="max-w-[1200px] mx-auto px-6 h-[72px] flex items-center justify-between gap-4">
          <Logo dark={true} />
          <nav className="hidden md:flex items-center gap-6">
            <Link to="/" className="text-[13px] font-semibold tracking-widest uppercase text-[#5e4743] hover:text-[#3d2e2b]">{t('nav_home')}</Link>
            <Link to="/menu" className="text-[13px] font-semibold tracking-widest uppercase text-[#5e4743] hover:text-[#3d2e2b]">{t('nav_menu')}</Link>
            <Link to="/media" className="text-[13px] font-semibold tracking-widest uppercase" style={{ color: GOLD }}>{t('nav_media')}</Link>
            <Link to="/blog" className="text-[13px] font-semibold tracking-widest uppercase text-[#5e4743] hover:text-[#3d2e2b]">{t('nav_news')}</Link>
          </nav>
          <div className="flex items-center gap-3">
            <LangSwitcher />
            <button onClick={() => setResOpen(true)}
              className="hidden md:block px-5 py-2 rounded-full text-[13px] font-semibold tracking-widest uppercase"
              style={{ backgroundColor: GOLD, color: BROWN }}>
              {t('nav_book')}
            </button>
          </div>
        </div>
      </header>

      {/* Hero */}
      <div className="pt-[72px]">
        <div className="relative h-64 md:h-80 bg-cover bg-center flex items-center justify-center overflow-hidden"
          style={{ backgroundImage: `url(https://images.hostinger.com/c46a9991-da86-4740-82e9-88191c8b3704.png)` }}>
          <div className="absolute inset-0 bg-black/55" />
          <div className="relative z-10 text-center px-5">
            <span className="font-script text-3xl md:text-4xl block mb-2" style={{ color: GOLD }}>{t('media_label')}</span>
            <h1 className="font-typewriter font-black text-white uppercase text-4xl md:text-6xl tracking-wide">{t('media_title')}</h1>
            <p className="text-white/75 mt-3 text-sm max-w-xl mx-auto">{t('media_subtitle')}</p>
          </div>
        </div>
      </div>

      {/* Filter tabs */}
      <div className="max-w-[1200px] mx-auto px-5 py-10">
        <div className="flex justify-center gap-3 mb-10">
          {[
            { key: 'all', label: t('media_all') },
            { key: 'photos', label: t('media_photos') },
            { key: 'videos', label: t('media_videos') },
          ].map(({ key, label }) => (
            <button key={key} onClick={() => setFilter(key)}
              className="px-7 py-2.5 rounded-full text-sm font-semibold tracking-wide uppercase transition-all"
              style={filter === key ? { backgroundColor: BROWN, color: '#fff' } : { backgroundColor: '#f0e8d8', color: BROWN }}>
              {label}
            </button>
          ))}
        </div>

        {/* Grid */}
        <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
          {filtered.map((item, idx) => (
            <motion.div
              key={item.id}
              initial={{ opacity: 0, scale: 0.95 }}
              whileInView={{ opacity: 1, scale: 1 }}
              viewport={{ once: true }}
              transition={{ duration: 0.4, delay: idx * 0.04 }}
              className="relative group cursor-pointer rounded-xl overflow-hidden aspect-square shadow-md"
              onClick={() => openLightbox(idx)}
            >
              <img src={item.url} alt={item[captionKey]} className="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" />
              {/* Overlay */}
              <div className="absolute inset-0 bg-black/0 group-hover:bg-black/45 transition-all duration-300 flex flex-col items-center justify-center gap-2">
                {item.type === 'video' ? (
                  <Play size={36} className="text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300" fill="white" />
                ) : (
                  <ImageIcon size={28} className="text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300" />
                )}
                <p className="text-white text-xs font-medium text-center px-3 opacity-0 group-hover:opacity-100 transition-opacity duration-300 line-clamp-2">
                  {item[captionKey]}
                </p>
              </div>
              {/* Type badge */}
              {item.type === 'video' && (
                <div className="absolute top-2 left-2 px-2 py-0.5 rounded-full text-[10px] font-bold text-white" style={{ backgroundColor: BROWN }}>
                  VIDEO
                </div>
              )}
              {/* Category badge */}
              <div className="absolute bottom-2 left-2 px-2 py-0.5 rounded-full text-[10px] font-medium text-white bg-black/50 backdrop-blur-sm">
                {item[categoryKey]}
              </div>
            </motion.div>
          ))}
        </div>
      </div>

      {/* Lightbox */}
      {lightboxIndex !== null && (
        <Lightbox
          items={filtered}
          index={lightboxIndex}
          onClose={closeLightbox}
          onPrev={prevItem}
          onNext={nextItem}
        />
      )}

      {/* Footer */}
      <footer className="text-neutral-300 pt-12 pb-8 mt-8" style={{ backgroundColor: BROWN_DARK }}>
        <div className="max-w-[1140px] mx-auto px-5 text-center">
          <img src="https://horizons-cdn.hostinger.com/a2bb8e3c-639e-4083-bf32-32f3f7f0cebb/d5bf5030568dea1dbf0b975ffc8f98ce.png" alt="Ngon Thị Hoa" className="h-16 mx-auto object-contain mb-4" style={{ filter: 'brightness(0) invert(1)' }} />
          <p className="text-xs" style={{ color: '#ffc95299' }}>{t('footer_copy')}</p>
        </div>
      </footer>

      {resOpen && <ReservationModal onClose={() => setResOpen(false)} />}
    </div>
  );
}
