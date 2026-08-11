import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { Helmet } from 'react-helmet';
import { motion } from 'framer-motion';
import { ArrowRight, Menu as MenuIcon, X, ChevronDown, CalendarDays, User } from 'lucide-react';
import { AnimatePresence } from 'framer-motion';
import { BLOG_POSTS } from '@/data/blogData';
import ReservationModal from '@/components/ReservationModal';

const BROWN = '#5e4743';
const GOLD = '#ffc952';
const BROWN_DARK = '#3d2e2b';
const IMG_HERO = 'https://www.ngonthihoarestaurant.com/upload/blog/blog-1779346247.jpg';

const MENU_DROPDOWN = [
  { label: 'Thực Đơn Sáng', href: '/?group=sang' },
  { label: 'Thực Đơn Trưa & Tối', href: '/?group=trua_toi' },
  { label: 'Đồ Uống (không cồn)', href: '/?group=do_uong' },
  { label: 'Đồ Uống Có Cồn', href: '/?group=do_uong_co_con' },
  { label: 'Rượu Vang', href: '/menu?group=ruou_vang' },
];

function BlogHeader({ onReservation }) {
  const [scrolled, setScrolled] = useState(false);
  const [mobileOpen, setMobileOpen] = useState(false);
  const [menuDrop, setMenuDrop] = useState(false);
  const navigate = useNavigate();

  React.useEffect(() => {
    const fn = () => setScrolled(window.scrollY > 40);
    window.addEventListener('scroll', fn);
    return () => window.removeEventListener('scroll', fn);
  }, []);

  const lc = `text-[13px] font-semibold tracking-widest uppercase transition-colors ${scrolled ? 'text-[#5e4743] hover:text-[#3d2e2b]' : 'text-white/90 hover:text-white'}`;

  return (
    <header className={`fixed top-0 inset-x-0 z-50 transition-all duration-300 ${scrolled ? 'bg-white shadow-md' : 'bg-gradient-to-b from-black/55 to-transparent'}`}>
      <div className="max-w-[1200px] mx-auto px-6 h-[72px] flex items-center justify-between gap-4">
        <nav className="hidden lg:flex items-center gap-6 flex-1">
          <a href="/" className={lc}>Trang Chủ</a>
          <div className="relative">
            <button onClick={() => setMenuDrop(v => !v)} className={`flex items-center gap-1 ${lc}`}>
              Thực Đơn <ChevronDown size={13} className={`transition-transform ${menuDrop ? 'rotate-180' : ''}`} />
            </button>
            <AnimatePresence>
              {menuDrop && (
                <motion.div initial={{ opacity: 0, y: -6 }} animate={{ opacity: 1, y: 0 }} exit={{ opacity: 0, y: -6 }}
                  className="absolute top-full left-0 mt-2 w-52 bg-white rounded-xl shadow-xl overflow-hidden z-50">
                  {MENU_DROPDOWN.map(m => (
                    <button key={m.label} onClick={() => { navigate(m.href); setMenuDrop(false); }}
                      className="w-full text-left px-5 py-3 text-sm text-neutral-700 hover:text-[#5e4743] transition-colors border-b border-neutral-50 last:border-0">
                      {m.label}
                    </button>
                  ))}
                </motion.div>
              )}
            </AnimatePresence>
          </div>
          <a href="/blog" className={`${lc} border-b-2`} style={{ borderColor: scrolled ? BROWN : GOLD }}>Tin Tức</a>
        </nav>

        <div className="flex-shrink-0">
          <a href="/" aria-label="Ngon Thị Hoa">
            <img src="https://horizons-cdn.hostinger.com/a2bb8e3c-639e-4083-bf32-32f3f7f0cebb/d5bf5030568dea1dbf0b975ffc8f98ce.png"
              alt="Ngon Thị Hoa" className="h-16 w-auto object-contain"
              style={{ filter: scrolled ? 'none' : 'brightness(0) invert(1)' }} />
          </a>
        </div>

        <nav className="hidden lg:flex items-center gap-4 flex-1 justify-end">
          <button onClick={onReservation}
            className={`text-[13px] font-semibold tracking-widest uppercase transition-all px-4 py-[7px] rounded-full border ${scrolled ? 'border-[#5e4743] text-[#5e4743] hover:bg-[#5e4743] hover:text-white' : 'border-white/70 text-white/90 hover:border-white hover:text-white'}`}>
            Đặt Bàn
          </button>
        </nav>

        <button className="lg:hidden p-2" style={{ color: scrolled ? BROWN : '#fff' }} onClick={() => setMobileOpen(true)}>
          <MenuIcon size={26} />
        </button>
      </div>

      {mobileOpen && (
        <div className="fixed inset-0 z-50 flex flex-col" style={{ backgroundColor: BROWN }}>
          <div className="flex items-center justify-between px-6 h-[72px]">
            <a href="/"><img src="https://horizons-cdn.hostinger.com/a2bb8e3c-639e-4083-bf32-32f3f7f0cebb/d5bf5030568dea1dbf0b975ffc8f98ce.png" alt="Logo" className="h-14 w-auto" style={{ filter: 'brightness(0) invert(1)' }} /></a>
            <button onClick={() => setMobileOpen(false)} style={{ color: GOLD }}><X size={28} /></button>
          </div>
          <div className="flex-1 flex flex-col items-center justify-center gap-6 pb-12">
            <a href="/" className="text-white text-lg font-semibold tracking-widest uppercase">Trang Chủ</a>
            <a href="/blog" className="text-lg font-semibold tracking-widest uppercase" style={{ color: GOLD }}>Tin Tức</a>
            <button onClick={() => { onReservation(); setMobileOpen(false); }}
              className="mt-4 px-10 py-3 rounded-full text-base font-semibold uppercase tracking-widest"
              style={{ backgroundColor: GOLD, color: BROWN }}>Đặt Bàn</button>
          </div>
        </div>
      )}
    </header>
  );
}

function Reveal({ children, delay = 0 }) {
  return (
    <motion.div initial={{ opacity: 0, y: 30 }} whileInView={{ opacity: 1, y: 0 }}
      viewport={{ once: true, margin: '-60px' }} transition={{ duration: 0.6, delay }}>
      {children}
    </motion.div>
  );
}

export default function BlogPage() {
  const [reservationOpen, setReservationOpen] = useState(false);
  const navigate = useNavigate();
  const featured = BLOG_POSTS[0];
  const rest = BLOG_POSTS.slice(1);

  return (
    <>
      <Helmet>
        <title>Tin Tức - Ngon Thị Hoa Restaurant</title>
        <meta name="description" content="Cập nhật tin tức mới nhất từ Ngon Thị Hoa Restaurant – nhà hàng ẩm thực Việt Nam tại Đà Nẵng." />
      </Helmet>

      <div className="bg-white min-h-screen">
        <BlogHeader onReservation={() => setReservationOpen(true)} />

        {/* Hero */}
        <div className="relative h-64 md:h-80 flex items-center justify-center overflow-hidden"
          style={{ backgroundImage: `url(${IMG_HERO})`, backgroundSize: 'cover', backgroundPosition: 'center' }}>
          <div className="absolute inset-0 bg-black/55" />
          <div className="relative z-10 text-center px-4">
            <span className="font-script text-3xl md:text-5xl block" style={{ color: GOLD }}>Cập nhật</span>
            <h1 className="font-typewriter font-black text-white uppercase text-4xl md:text-6xl tracking-wide mt-1">Tin Tức</h1>
          </div>
        </div>

        <div className="max-w-[1140px] mx-auto px-5 py-16">
          {/* Featured post */}
          <Reveal>
            <article
              onClick={() => navigate(`/blog/${featured.slug}`)}
              className="cursor-pointer grid md:grid-cols-2 gap-8 items-center mb-16 group"
            >
              <div className="overflow-hidden rounded-2xl shadow-xl" style={{ aspectRatio: '16/10' }}>
                <img src={featured.img} alt={featured.title} className="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" />
              </div>
              <div>
                <span className="inline-block text-xs font-semibold px-3 py-1 rounded-full mb-3" style={{ backgroundColor: '#f0e8d8', color: BROWN }}>Nổi bật</span>
                <h2 className="font-typewriter font-bold text-2xl md:text-3xl text-neutral-900 leading-snug mb-3 group-hover:text-[#5e4743] transition-colors">
                  {featured.title}
                </h2>
                <div className="flex items-center gap-4 text-xs text-neutral-400 mb-4">
                  <span className="flex items-center gap-1"><CalendarDays size={12} />{featured.date}</span>
                  <span className="flex items-center gap-1"><User size={12} />{featured.author}</span>
                </div>
                <p className="text-neutral-500 leading-relaxed mb-5">{featured.excerpt}</p>
                <span className="inline-flex items-center gap-2 text-sm font-semibold uppercase tracking-wide" style={{ color: BROWN }}>
                  Đọc thêm <ArrowRight size={15} />
                </span>
              </div>
            </article>
          </Reveal>

          <div className="brush-divider max-w-[120px] mb-16" />

          {/* Grid */}
          <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
            {rest.map((post, i) => (
              <Reveal key={post.slug} delay={i * 0.07}>
                <article
                  onClick={() => navigate(`/blog/${post.slug}`)}
                  className="cursor-pointer bg-white rounded-2xl overflow-hidden shadow-md border border-neutral-100 hover:shadow-xl transition-shadow flex flex-col h-full group"
                >
                  <div className="overflow-hidden h-48">
                    <img src={post.img} alt={post.title} className="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" />
                  </div>
                  <div className="p-5 flex flex-col flex-1">
                    <div className="flex items-center gap-3 text-xs text-neutral-400 mb-2">
                      <span className="flex items-center gap-1"><CalendarDays size={11} />{post.date}</span>
                    </div>
                    <h3 className="font-typewriter font-semibold text-neutral-900 text-base mb-3 line-clamp-2 leading-snug group-hover:text-[#5e4743] transition-colors">
                      {post.title}
                    </h3>
                    <p className="text-neutral-500 text-sm leading-relaxed mb-4 flex-1 line-clamp-3">{post.excerpt}</p>
                    <div className="flex items-center justify-between mt-auto pt-3 border-t border-neutral-100">
                      <span className="inline-flex items-center gap-1 text-xs font-semibold uppercase tracking-wide" style={{ color: BROWN }}>
                        Đọc thêm <ArrowRight size={12} />
                      </span>
                      <button
                        onClick={e => { e.stopPropagation(); setReservationOpen(true); }}
                        className="text-xs px-3 py-1.5 rounded-full font-semibold"
                        style={{ backgroundColor: GOLD, color: BROWN }}>
                        Đặt Bàn
                      </button>
                    </div>
                  </div>
                </article>
              </Reveal>
            ))}
          </div>
        </div>

        {/* Footer */}
        <footer className="text-neutral-300 pt-12 pb-8 mt-8" style={{ backgroundColor: BROWN_DARK }}>
          <div className="max-w-[1140px] mx-auto px-5 flex flex-col md:flex-row justify-between items-center gap-4">
            <img src="https://horizons-cdn.hostinger.com/a2bb8e3c-639e-4083-bf32-32f3f7f0cebb/d5bf5030568dea1dbf0b975ffc8f98ce.png"
              alt="Logo" className="h-16 w-auto object-contain" style={{ filter: 'brightness(0) invert(1)' }} />
            <div className="text-sm text-neutral-400 text-center">
              <p>100 Lê Quang Đạo, Ngũ Hành Sơn, Đà Nẵng</p>
              <p>02366 515 100 | info@ngonthihoarestaurant.com</p>
            </div>
            <a href="/" className="text-sm font-semibold uppercase tracking-wide" style={{ color: GOLD }}>← Trang Chủ</a>
          </div>
          <div className="max-w-[1140px] mx-auto px-5 mt-6 pt-6 border-t text-center text-xs" style={{ borderColor: BROWN, color: '#ffc95299' }}>
            © {new Date().getFullYear()} Ngon Thị Hoa Restaurant · All rights reserved.
          </div>
        </footer>
      </div>

      {reservationOpen && <ReservationModal onClose={() => setReservationOpen(false)} />}
    </>
  );
}
