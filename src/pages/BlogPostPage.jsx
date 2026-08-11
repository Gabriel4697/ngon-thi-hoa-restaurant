import React, { useState } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { Helmet } from 'react-helmet';
import { motion } from 'framer-motion';
import { ArrowLeft, ArrowRight, CalendarDays, User, ExternalLink } from 'lucide-react';
import { BLOG_POSTS } from '@/data/blogData';
import ReservationModal from '@/components/ReservationModal';

const BROWN = '#5e4743';
const GOLD = '#ffc952';
const BROWN_DARK = '#3d2e2b';

export default function BlogPostPage() {
  const { slug } = useParams();
  const navigate = useNavigate();
  const [reservationOpen, setReservationOpen] = useState(false);

  const post = BLOG_POSTS.find(p => p.slug === slug);
  const related = BLOG_POSTS.filter(p => p.slug !== slug).slice(0, 3);

  if (!post) {
    return (
      <div className="min-h-screen flex flex-col items-center justify-center gap-4">
        <h1 className="font-typewriter text-3xl text-neutral-700">Bài viết không tồn tại</h1>
        <button onClick={() => navigate('/blog')} className="text-sm font-semibold px-6 py-2.5 rounded-full text-white" style={{ backgroundColor: BROWN }}>
          ← Quay lại Blog
        </button>
      </div>
    );
  }

  return (
    <>
      <Helmet>
        <title>{post.title} - Ngon Thị Hoa Restaurant</title>
        <meta name="description" content={post.excerpt} />
      </Helmet>

      {/* Simple top nav */}
      <header className="fixed top-0 inset-x-0 z-50 bg-white/95 shadow-sm backdrop-blur">
        <div className="max-w-[1140px] mx-auto px-5 h-[64px] flex items-center justify-between gap-4">
          <a href="/" aria-label="Logo">
            <img src="https://horizons-cdn.hostinger.com/a2bb8e3c-639e-4083-bf32-32f3f7f0cebb/d5bf5030568dea1dbf0b975ffc8f98ce.png"
              alt="Ngon Thị Hoa" className="h-12 w-auto object-contain" />
          </a>
          <div className="flex items-center gap-3">
            <button onClick={() => navigate('/blog')}
              className="hidden sm:flex items-center gap-2 text-sm font-medium text-neutral-600 hover:text-[#5e4743] transition-colors">
              <ArrowLeft size={15} /> Tin Tức
            </button>
            <button onClick={() => setReservationOpen(true)}
              className="px-5 py-2 rounded-full text-sm font-semibold text-white"
              style={{ backgroundColor: BROWN }}>
              Đặt Bàn
            </button>
          </div>
        </div>
      </header>

      <div className="bg-white min-h-screen pt-16">
        {/* Hero image */}
        <div className="relative h-72 md:h-96 overflow-hidden">
          <img src={post.img} alt={post.title} className="w-full h-full object-cover" />
          <div className="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent" />
          <div className="absolute bottom-0 left-0 right-0 p-8 md:p-12">
            <div className="max-w-3xl">
              <div className="flex items-center gap-3 text-xs text-white/70 mb-3">
                <span className="flex items-center gap-1"><CalendarDays size={12} />{post.date}</span>
                <span>·</span>
                <span className="flex items-center gap-1"><User size={12} />{post.author}</span>
              </div>
              <h1 className="font-typewriter font-bold text-white text-2xl md:text-4xl leading-snug">{post.title}</h1>
            </div>
          </div>
        </div>

        {/* Content */}
        <div className="max-w-3xl mx-auto px-5 py-12">
          <motion.div initial={{ opacity: 0, y: 20 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.6 }}>
            {/* Excerpt */}
            <p className="text-lg text-neutral-600 leading-relaxed mb-8 font-medium border-l-4 pl-5" style={{ borderColor: GOLD }}>
              {post.excerpt}
            </p>

            {/* Full content */}
            <div
              className="prose prose-neutral max-w-none text-neutral-700 leading-relaxed space-y-4"
              style={{ fontSize: '1.0625rem' }}
              dangerouslySetInnerHTML={{ __html: post.content }}
            />

            {/* External source link */}
            {post.url && (
              <div className="mt-8 pt-6 border-t border-neutral-100">
                <a href={post.url} target="_blank" rel="noreferrer"
                  className="inline-flex items-center gap-2 text-sm font-medium transition-colors hover:underline"
                  style={{ color: BROWN }}>
                  <ExternalLink size={14} /> Xem bài viết gốc trên website Ngon Thị Hoa
                </a>
              </div>
            )}

            {/* Reservation CTA */}
            <div className="mt-10 p-8 rounded-2xl text-center" style={{ backgroundColor: '#faf6ef', border: `1px solid #f0e8d8` }}>
              <span className="font-script text-2xl block" style={{ color: BROWN }}>Hẹn gặp bạn tại</span>
              <h3 className="font-typewriter font-bold text-2xl mt-1 mb-3 text-neutral-900">Ngon Thị Hoa Restaurant</h3>
              <p className="text-neutral-500 text-sm mb-5">100 Lê Quang Đạo, Ngũ Hành Sơn, Đà Nẵng</p>
              <button
                onClick={() => setReservationOpen(true)}
                className="px-8 py-3 rounded-full font-semibold text-sm uppercase tracking-wide transition-all hover:opacity-90"
                style={{ backgroundColor: BROWN, color: '#fff' }}>
                Đặt Bàn Ngay
              </button>
            </div>
          </motion.div>
        </div>

        {/* Related posts */}
        {related.length > 0 && (
          <div className="max-w-[1140px] mx-auto px-5 pb-16">
            <div className="brush-divider max-w-[80px] mb-8" />
            <h2 className="font-section-title font-bold text-2xl text-neutral-900 mb-8">Bài viết liên quan</h2>
            <div className="grid sm:grid-cols-3 gap-6">
              {related.map(p => (
                <article
                  key={p.slug}
                  onClick={() => { navigate(`/blog/${p.slug}`); window.scrollTo(0, 0); }}
                  className="cursor-pointer bg-white rounded-2xl overflow-hidden shadow-md border border-neutral-100 hover:shadow-xl transition-shadow group"
                >
                  <div className="overflow-hidden h-40">
                    <img src={p.img} alt={p.title} className="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" />
                  </div>
                  <div className="p-4">
                    <p className="text-xs text-neutral-400 mb-1">{p.date}</p>
                    <h3 className="font-typewriter font-semibold text-sm leading-snug line-clamp-2 group-hover:text-[#5e4743] transition-colors">{p.title}</h3>
                    <span className="inline-flex items-center gap-1 text-xs font-semibold mt-3" style={{ color: BROWN }}>
                      Đọc thêm <ArrowRight size={12} />
                    </span>
                  </div>
                </article>
              ))}
            </div>
          </div>
        )}

        {/* Footer */}
        <footer className="text-neutral-300 pt-10 pb-8" style={{ backgroundColor: BROWN_DARK }}>
          <div className="max-w-[1140px] mx-auto px-5 flex flex-col md:flex-row justify-between items-center gap-4">
            <img src="https://horizons-cdn.hostinger.com/a2bb8e3c-639e-4083-bf32-32f3f7f0cebb/d5bf5030568dea1dbf0b975ffc8f98ce.png"
              alt="Logo" className="h-14 w-auto object-contain" style={{ filter: 'brightness(0) invert(1)' }} />
            <div className="text-sm text-neutral-400 text-center">
              <p>100 Lê Quang Đạo, Ngũ Hành Sơn, Đà Nẵng</p>
              <p>02366 515 100 | info@ngonthihoarestaurant.com</p>
            </div>
            <div className="flex gap-3">
              <button onClick={() => navigate('/blog')} className="text-sm font-semibold" style={{ color: GOLD }}>← Blog</button>
              <a href="/" className="text-sm font-semibold" style={{ color: GOLD }}>Trang Chủ</a>
            </div>
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
