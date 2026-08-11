import React, { useState } from 'react';
import { Helmet } from 'react-helmet';
import { Download, CheckCircle, FileCode, Package, Database, BookOpen, ChevronDown, ChevronRight } from 'lucide-react';

const steps = [
  {
    title: '1. Cài đặt WordPress',
    content: [
      'Tải WordPress từ wordpress.org/download',
      'Upload lên hosting qua FTP hoặc File Manager',
      'Tạo database MySQL mới trên cPanel/Plesk',
      'Chạy wizard cài đặt tại yourdomain.com/wp-admin/install.php',
      'Đặt Site Title: "Ngon Thị Hoa - Tropical Garden"',
    ],
  },
  {
    title: '2. Cài đặt Plugin Đa Ngôn Ngữ (Polylang - Miễn phí)',
    content: [
      'Vào WP Admin > Plugins > Add New',
      'Tìm kiếm "Polylang" và cài đặt',
      'Kích hoạt và cấu hình 4 ngôn ngữ: Tiếng Việt (vi), English (en), 中文 (zh), 한국어 (ko)',
      'Hoặc dùng WPML (trả phí) tại wpml.org nếu cần tính năng cao hơn',
      'Plugin Ngon Thị Hoa v2.0 tương thích đầy đủ với cả Polylang và WPML',
    ],
  },
  {
    title: '3. Cài đặt Theme Ngon Thị Hoa',
    content: [
      'Tải file ngonthihoa-theme.tar.gz từ trang này',
      'Giải nén thành thư mục ngonthihoa-theme/',
      'Vào WP Admin > Appearance > Themes > Add New > Upload Theme',
      'Chọn file .zip (nén lại thành .zip trước)',
      'Kích hoạt theme sau khi upload',
    ],
  },
  {
    title: '4. Cài đặt Plugin Ngon Thị Hoa',
    content: [
      'Tải file ngonthihoa-plugin.tar.gz từ trang này',
      'Giải nén và nén lại thành ngonthihoa-plugin.zip',
      'Vào WP Admin > Plugins > Add New > Upload Plugin',
      'Upload và kích hoạt plugin',
      'Plugin sẽ tự động tạo các trang: /menu, /media, /tuyen-dung',
    ],
  },
  {
    title: '5. Cấu hình Logo & Nội dung',
    content: [
      'Vào Appearance > Customize > Site Identity > Upload logo',
      'Vào Appearance > Customize > Restaurant Settings để điền thông tin nhà hàng',
      'Tạo trang chủ: Pages > Add New, chọn template "Home Page"',
      'Vào Settings > Reading > set trang chủ là trang vừa tạo',
    ],
  },
  {
    title: '6. Import Dữ Liệu Mẫu',
    content: [
      'Vào WP Admin > Ngon Thị Hoa > Import Dữ Liệu',
      'Click "Import Blog Posts" để import 6 bài viết',
      'Click "Import Menu Items" để import 19 món ăn mẫu',
      'Click "Import Vị Trí Tuyển Dụng" để import 3 vị trí',
      'Thêm hình ảnh thực tế qua Media Library',
    ],
  },
  {
    title: '7. Cấu hình Email Thông Báo',
    content: [
      'Vào Ngon Thị Hoa > Cài Đặt',
      'Điền email nhận thông báo đặt bàn, liên hệ, ứng tuyển',
      'Cài thêm plugin SMTP (WP Mail SMTP) để email hoạt động ổn định',
      'Khuyến nghị dùng Gmail App Password hoặc SendGrid',
    ],
  },
];

const features = [
  { icon: '🎨', title: 'Custom Theme', desc: 'Thiết kế brand Ngon Thị Hoa, responsive hoàn toàn, brand colors nâu/vàng' },
  { icon: '🍽️', title: 'Menu Management', desc: '5 nhóm thực đơn, hệ thống categories, giá, mô tả đa ngôn ngữ' },
  { icon: '📅', title: 'Reservation System', desc: 'Form đặt bàn → lưu DB + email thông báo tự động cho nhà hàng' },
  { icon: '📨', title: 'Contact Forms', desc: 'Form liên hệ + ứng tuyển, upload CV, email notification' },
  { icon: '📰', title: 'Blog & News', desc: '6 bài viết mẫu, categories, related posts, SEO-friendly slugs' },
  { icon: '🖼️', title: 'Media Gallery', desc: 'Gallery hình ảnh + video, lightbox, filter theo loại' },
  { icon: '💼', title: 'Recruitment', desc: 'Quản lý vị trí tuyển dụng, form ứng tuyển, upload CV' },
  { icon: '🌐', title: 'Multilingual Ready', desc: 'Tương thích Polylang & WPML cho VI/EN/ZH' },
  { icon: '📊', title: 'Admin Dashboard', desc: 'Dashboard thống kê, quản lý đặt bàn/liên hệ/ứng tuyển dễ dàng' },
  { icon: '📥', title: 'Data Import', desc: 'Import 1-click: blog posts, menu items, vị trí tuyển dụng' },
];

function AccordionStep({ step, index }) {
  const [open, setOpen] = useState(index === 0);
  return (
    <div style={{ border: '1px solid rgba(94,71,67,0.15)', borderRadius: 10, overflow: 'hidden', marginBottom: 10 }}>
      <button
        onClick={() => setOpen(o => !o)}
        style={{ width: '100%', padding: '16px 20px', display: 'flex', alignItems: 'center', justifyContent: 'space-between', background: open ? '#faf6ef' : '#fff', border: 'none', cursor: 'pointer', textAlign: 'left' }}
      >
        <span style={{ fontWeight: 700, color: '#5e4743', fontSize: 15 }}>{step.title}</span>
        {open ? <ChevronDown size={18} color="#5e4743" /> : <ChevronRight size={18} color="#5e4743" />}
      </button>
      {open && (
        <div style={{ padding: '16px 20px', background: '#fff' }}>
          <ul style={{ margin: 0, paddingLeft: 20 }}>
            {step.content.map((item, i) => (
              <li key={i} style={{ fontSize: 14, color: '#555', marginBottom: 6, lineHeight: 1.6 }}>{item}</li>
            ))}
          </ul>
        </div>
      )}
    </div>
  );
}

export default function WordPressDownloadPage() {
  return (
    <div style={{ minHeight: '100vh', background: '#faf6ef', fontFamily: "'DM Sans', sans-serif" }}>
      <Helmet>
        <title>WordPress Package - Ngon Thị Hoa</title>
        <meta name="description" content="Tải về WordPress theme và plugin cho nhà hàng Ngon Thị Hoa - Tropical Garden" />
      </Helmet>

      {/* Header */}
      <div style={{ background: 'linear-gradient(135deg, #5e4743 0%, #3d2e2b 100%)', padding: '60px 20px', textAlign: 'center', color: '#fff' }}>
        <p style={{ fontFamily: "'Dancing Script', cursive", fontSize: 20, color: '#ffc952', marginBottom: 8, letterSpacing: '0.1em' }}>Ngon Thị Hoa</p>
        <h1 style={{ fontFamily: "'Playfair Display', serif", fontSize: 'clamp(28px,5vw,48px)', color: '#fff', marginBottom: 16 }}>WordPress Package</h1>
        <p style={{ opacity: 0.75, maxWidth: 560, margin: '0 auto', fontSize: 16, lineHeight: 1.7 }}>
          Theme & Plugin v2.0 hoàn chỉnh để chuyển website Ngon Thị Hoa sang WordPress — quản lý thực đơn, đặt bàn, liên hệ, tuyển dụng, blog, media gallery, SEO, GA4, Facebook Pixel, Google Ads, Analytics Dashboard và hỗ trợ đầy đủ 4 ngôn ngữ VI/EN/ZH/KO.
        </p>
      </div>

      <div style={{ maxWidth: 1100, margin: '0 auto', padding: '60px 20px' }}>

        {/* Download Cards */}
        <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(280px, 1fr))', gap: 24, marginBottom: 64 }}>
          {[
            {
              icon: <FileCode size={32} color="#ffc952" />,
              title: 'WordPress Theme',
              subtitle: 'ngonthihoa-theme.tar.gz',
              desc: 'Custom theme v2.0: header, footer, templates, brand colors, UTM Thu Phap Thien An font, responsive design, hỗ trợ 4 ngôn ngữ VI/EN/ZH/KO.',
              file: '/wp-package/ngonthihoa-theme.tar.gz',
              size: '~24 KB',
              color: '#5e4743',
            },
            {
              icon: <Package size={32} color="#ffc952" />,
              title: 'WordPress Plugin',
              subtitle: 'ngonthihoa-plugin.tar.gz',
              desc: 'Plugin v2.0: Custom Post Types, SEO Schema, GA4/FB Pixel/Google Ads, Analytics Dashboard, Export CSV/JSON, reCAPTCHA, đa ngôn ngữ.',
              file: '/wp-package/ngonthihoa-plugin.tar.gz',
              size: '~39 KB',
              color: '#3d2e2b',
            },
            {
              icon: <Database size={32} color="#ffc952" />,
              title: 'Full Package',
              subtitle: 'ngonthihoa-full-package.tar.gz',
              desc: 'Gói đầy đủ v2.0: Theme + Plugin + SQL migration. Bao gồm tất cả tính năng: SEO, Analytics, đa ngôn ngữ, bảo mật, caching.',
              file: '/wp-package/ngonthihoa-full-package.tar.gz',
              size: '~62 KB',
              color: '#5e4743',
              highlight: true,
            },
          ].map((pkg, i) => (
            <div key={i} style={{
              background: '#fff',
              borderRadius: 16,
              padding: 28,
              boxShadow: pkg.highlight ? '0 8px 32px rgba(94,71,67,0.18)' : '0 4px 16px rgba(94,71,67,0.08)',
              border: pkg.highlight ? '2px solid #ffc952' : '1px solid rgba(94,71,67,0.1)',
              display: 'flex', flexDirection: 'column', gap: 16,
              position: 'relative',
            }}>
              {pkg.highlight && (
                <div style={{ position: 'absolute', top: -14, left: '50%', transform: 'translateX(-50%)', background: '#ffc952', color: '#3d2e2b', fontSize: 12, fontWeight: 700, padding: '4px 16px', borderRadius: 20, letterSpacing: '0.05em' }}>
                  KHUYẾN NGHỊ
                </div>
              )}
              <div style={{ display: 'flex', alignItems: 'center', gap: 12 }}>
                <div style={{ background: pkg.color, borderRadius: 12, padding: 12, display: 'flex' }}>
                  {pkg.icon}
                </div>
                <div>
                  <h3 style={{ fontFamily: "'Playfair Display', serif", color: '#5e4743', fontSize: 18, margin: 0 }}>{pkg.title}</h3>
                  <p style={{ fontSize: 12, color: '#999', margin: 0, fontFamily: 'monospace' }}>{pkg.subtitle}</p>
                </div>
              </div>
              <p style={{ fontSize: 14, color: '#666', lineHeight: 1.6, margin: 0 }}>{pkg.desc}</p>
              <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', marginTop: 'auto' }}>
                <span style={{ fontSize: 12, color: '#aaa' }}>Size: {pkg.size}</span>
                <a
                  href={pkg.file}
                  download
                  style={{ display: 'flex', alignItems: 'center', gap: 8, background: '#ffc952', color: '#3d2e2b', fontWeight: 700, fontSize: 13, padding: '10px 20px', borderRadius: 8, textDecoration: 'none', transition: 'background .2s' }}
                  onMouseOver={e => e.currentTarget.style.background = '#e6b548'}
                  onMouseOut={e => e.currentTarget.style.background = '#ffc952'}
                >
                  <Download size={16} />
                  Tải Xuống
                </a>
              </div>
            </div>
          ))}
        </div>

        {/* Features Grid */}
        <div style={{ marginBottom: 64 }}>
          <h2 style={{ fontFamily: "'Playfair Display', serif", color: '#5e4743', fontSize: 28, textAlign: 'center', marginBottom: 8 }}>Tính Năng Bao Gồm</h2>
          <div style={{ width: 60, height: 3, background: 'linear-gradient(90deg,transparent,#ffc952,transparent)', margin: '0 auto 40px' }} />
          <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(200px, 1fr))', gap: 16 }}>
            {features.map((f, i) => (
              <div key={i} style={{ background: '#fff', borderRadius: 12, padding: 20, boxShadow: '0 2px 8px rgba(94,71,67,0.06)' }}>
                <div style={{ fontSize: 28, marginBottom: 10 }}>{f.icon}</div>
                <h4 style={{ color: '#5e4743', fontSize: 14, fontWeight: 700, marginBottom: 6 }}>{f.title}</h4>
                <p style={{ fontSize: 13, color: '#777', lineHeight: 1.5, margin: 0 }}>{f.desc}</p>
              </div>
            ))}
          </div>
        </div>

        {/* Installation Guide */}
        <div style={{ marginBottom: 64 }}>
          <div style={{ display: 'flex', alignItems: 'center', gap: 12, marginBottom: 8 }}>
            <BookOpen size={24} color="#ffc952" />
            <h2 style={{ fontFamily: "'Playfair Display', serif", color: '#5e4743', fontSize: 28, margin: 0 }}>Hướng Dẫn Cài Đặt</h2>
          </div>
          <div style={{ width: 60, height: 3, background: 'linear-gradient(90deg,transparent,#ffc952,transparent)', marginBottom: 32 }} />
          {steps.map((step, i) => <AccordionStep key={i} step={step} index={i} />)}
        </div>

        {/* Requirements */}
        <div style={{ background: '#fff', borderRadius: 16, padding: 32, boxShadow: '0 4px 16px rgba(94,71,67,0.08)', marginBottom: 64 }}>
          <h2 style={{ fontFamily: "'Playfair Display', serif", color: '#5e4743', fontSize: 22, marginBottom: 20 }}>Yêu Cầu Hệ Thống</h2>
          <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(200px, 1fr))', gap: 16 }}>
            {[
              ['WordPress', '5.8+'],
              ['PHP', '7.4+'],
              ['MySQL', '5.7+'],
              ['Plugin Đa Ngôn Ngữ', 'Polylang (free) hoặc WPML'],
              ['SMTP Plugin', 'WP Mail SMTP (khuyến nghị)'],
              ['Hosting', 'Bất kỳ shared/VPS hosting'],
            ].map(([name, ver], i) => (
              <div key={i} style={{ display: 'flex', alignItems: 'center', gap: 10 }}>
                <CheckCircle size={18} color="#27ae60" />
                <div>
                  <span style={{ fontWeight: 600, color: '#5e4743', fontSize: 14 }}>{name}</span>
                  <span style={{ fontSize: 13, color: '#888', marginLeft: 6 }}>{ver}</span>
                </div>
              </div>
            ))}
          </div>
        </div>

        {/* Structure */}
        <div style={{ background: '#fff', borderRadius: 16, padding: 32, boxShadow: '0 4px 16px rgba(94,71,67,0.08)' }}>
          <h2 style={{ fontFamily: "'Playfair Display', serif", color: '#5e4743', fontSize: 22, marginBottom: 20 }}>Cấu Trúc File</h2>
          <pre style={{ background: '#faf6ef', padding: 24, borderRadius: 10, fontSize: 13, lineHeight: 1.8, overflow: 'auto', color: '#4a4a4a' }}>{`ngonthihoa-full-package/
├── ngonthihoa-theme/              # WordPress Theme
│   ├── style.css                  # Theme metadata + CSS (22KB)
│   ├── functions.php              # Theme functions, AJAX, Customizer
│   ├── header.php                 # Site header + navigation
│   ├── footer.php                 # Site footer
│   ├── index.php                  # Default template
│   ├── front-page.php             # Homepage (Hero, About, Menu, Blog, Media)
│   ├── single.php                 # Blog post template
│   ├── template-parts/
│   │   ├── modal-reservation.php  # Đặt bàn modal
│   │   └── modal-contact.php      # Liên hệ modal
│   ├── page-templates/
│   │   ├── page-menu.php          # Menu page với tabs
│   │   ├── page-media.php         # Media gallery + lightbox
│   │   └── page-recruitment.php   # Tuyển dụng + form ứng tuyển
│   └── assets/
│       └── js/theme.js            # Slider, tabs, modals, AJAX
│
├── ngonthihoa-plugin/             # WordPress Plugin
│   ├── ngonthihoa-plugin.php      # Plugin entry point
│   └── includes/
│       ├── post-types.php         # 6 Custom Post Types
│       ├── taxonomies.php         # Menu Group, Category, Media Type
│       ├── meta-boxes.php         # Custom fields cho tất cả CPT
│       ├── admin.php              # Dashboard, columns, settings
│       ├── ajax.php               # Dynamic menu loading
│       └── import-data.php        # Import blog/menu/jobs data
│
└── sql-migration/
    └── README.sql                 # Hướng dẫn migration SQL`}</pre>
        </div>

      </div>

      {/* Footer */}
      <div style={{ background: '#3d2e2b', padding: '32px 20px', textAlign: 'center', color: 'rgba(255,255,255,0.5)', fontSize: 14 }}>
        <p>© {new Date().getFullYear()} Ngon Thị Hoa – Tropical Garden &nbsp;|&nbsp; 100 Lê Quang Đạo, Ngũ Hành Sơn, Đà Nẵng</p>
      </div>
    </div>
  );
}
