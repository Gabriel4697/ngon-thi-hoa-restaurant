import React, { useState, useEffect, useRef, useCallback, memo } from 'react';
import { useSearchParams, useNavigate } from 'react-router-dom';
import { Helmet } from 'react-helmet';
import { motion, AnimatePresence } from 'framer-motion';
import {
  Phone, MapPin, Facebook,
  Menu as MenuIcon, X, ChevronDown, ChevronLeft, ChevronRight, ZoomIn,
} from 'lucide-react';
import ReservationModal from '@/components/ReservationModal';
import MenuImageLightbox from '@/components/MenuImageLightbox';

/* ── shared palette ──────────────────────────────────────────────── */
const BROWN = '#5e4743';
const GOLD = '#ffc952';
const BROWN_DARK = '#3d2e2b';
const RED = '#5e4743'; // kept for compat
const GREEN = '#1d3226';

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
};

/* ── menu data ───────────────────────────────────────────────────── */
export const MENU_GROUPS = {
  sang: {
    id: 'sang', label: 'Sáng', en: 'Breakfast',
    categories: [
      { id: 'noodles', label: 'BÚN / MÌ / PHỞ', en: 'Noodles', img: IMG.dinner, menuImages: [
        'https://horizons-cdn.hostinger.com/a2bb8e3c-639e-4083-bf32-32f3f7f0cebb/1befed09ec513cd34930e9a3daf91da8.jpg',
        'https://horizons-cdn.hostinger.com/a2bb8e3c-639e-4083-bf32-32f3f7f0cebb/843ab048272904756e8e8ded46bf0bf3.jpg',
      ], dishes: [
        { name: 'Phở Bò Tái Nạm', desc: 'Medium rare beef and flank Pho', price: '75.000 vnd' },
        { name: 'Phở Nạm Gầu', desc: 'Beef brisket and flank Pho', price: '75.000 vnd' },
        { name: 'Phở Bò Tái Nạm Gầu', desc: 'Pho with Rare Beef, Brisket and Fatty Brisket', price: '80.000 vnd' },
        { name: 'Phở Gà', desc: 'Chicken Pho', price: '75.000 vnd' },
        { name: 'Bún Chả Hà Nội', desc: '"Hà Nội" style noodle soup with grilled meatballs and pork belly', price: '80.000 vnd' },
        { name: 'Bún Riêu Ốc', desc: 'Snail and crab paste noodles soup', price: '85.000 vnd' },
        { name: 'Bún Riêu Cua', desc: 'Crab paste noodles soup', price: '75.000 vnd' },
        { name: 'Mỳ Quảng Tôm Thịt', desc: '"Quang Nam" style noodles with shrimp, pork, special stock', price: '75.000 vnd' },
        { name: 'Mỳ Quảng Gà', desc: '"Quang Nam" style noodles with chicken, special stock', price: '75.000 vnd' },
        { name: 'Bún Thịt Nướng', desc: '"Huế" style noodles with grilled pork and some pickled, vegetables', price: '75.000 vnd' },
        { name: 'Cao Lầu', desc: '"Cao Lầu" Quang Nam style noodles with char siu pork vegetable special soup', price: '75.000 vnd' },
        { name: 'Bún Bò Tái Nạm', desc: '"Huế" style noodle soup medium rare beef and flank', price: '75.000 vnd' },
        { name: 'Bún Bò Tái Nạm Chả Cua', desc: '"Huế" style noodle medium rare beef and crab paste', price: '80.000 vnd' },
        { name: 'Bún Giò Bò Tái', desc: '"Huế" style noodle soup with rare beef and pork knuckle', price: '85.000 vnd' },
      ] },
      { id: 'bo-trung', label: 'BÒ VÀ TRỨNG', en: 'Beef and Eggs', img: IMG.appetizer, menuImages: [
        'https://horizons-cdn.hostinger.com/a2bb8e3c-639e-4083-bf32-32f3f7f0cebb/191f53b2dea82cd23f1f33e642673aca.jpg',
      ], dishes: [
        { name: 'Bánh Mỳ Ớp La, Xúc Xích Đức, Thịt Ba Rọi, Cà Chua Nướng', desc: 'Fried eggs with sausage, bacon, grilled tomato and bread', price: '115.000 vnd' },
        { name: 'Bánh Mỳ Trứng Khuấy, Xúc Xích Đức, Thịt Ba Rọi, Cà Chua Nướng', desc: 'Scrambled eggs with sausage, bacon, grilled tomato and bread', price: '115.000 vnd' },
        { name: 'Bánh Mỳ Trứng Cuộn, Xúc Xích Đức, Thịt Ba Rọi, Cà Chua Nướng', desc: 'Fried omelette eggs with sausage, bacon, grilled tomato with bread', price: '115.000 vnd' },
        { name: 'Bánh Bèo', desc: 'Vietnamese steamed rice cake with pork meat, mushroom, shrimp', price: '60.000 vnd' },
        { name: 'Bò Né', desc: 'Sizzling Beef', price: '135.000 vnd' },
        { name: 'Bánh Mỳ Thịt Nướng', desc: 'Vietnamese "Bánh Mì" with grilled pork, Pate and some pickled', price: '65.000 vnd' },
      ] },
      { id: 'dac-biet-sang', label: 'MÓN TRUYỀN THỐNG ĐẶC BIỆT', en: 'Vietnamese Traditional Special Food', img: IMG.spread, menuImages: [
        'https://horizons-cdn.hostinger.com/a2bb8e3c-639e-4083-bf32-32f3f7f0cebb/b4e4d7f384c1661c41b47b8b35c61b87.jpg',
      ], dishes: [
        { name: 'Bún Chả Hà Nội Đặc Biệt', desc: 'Hà Nội style noodle with grilled pork belly, meatballs and spring rolls', price: '95.000 vnd' },
        { name: 'Phở Đặc Biệt', desc: 'Hà Nội style flat noodle with beef, chicken special soup', price: '95.000 vnd' },
        { name: 'Mỳ Quảng Đặc Biệt', desc: '"Quang Nam" style noodles with chicken, beef, shrimp, egg', price: '95.000 vnd' },
        { name: 'Bún Thịt Nướng Đặc Biệt', desc: 'Huế style noodle with grilled pork, spring rolls, some pickled, vegetables', price: '95.000 vnd' },
        { name: 'Bún Bò Đặc Biệt', desc: 'Huế style noodle with beef, crabmeat, beef shank', price: '95.000 vnd' },
        { name: 'Bún Riêu Cua Ốc Đặc Biệt', desc: 'Hà Nội style noodle with snail, crab paste, tofu and tomato', price: '95.000 vnd' },
      ] },
      { id: 'kem', label: 'KEM CÁC LOẠI', en: 'Ice Cream – 60.000 vnd / Box', img: IMG.dessert, menuImages: [
        'https://horizons-cdn.hostinger.com/a2bb8e3c-639e-4083-bf32-32f3f7f0cebb/450443c6efb5f81ac5ea90095d5909d7.jpg',
      ], dishes: [
        { name: 'Choco Chips / Sô Cô Chíp', desc: 'Chocolate Chip Ice Cream', price: '60.000 vnd / box' },
        { name: 'Coco Bella / Dừa Bella', desc: 'Coconut Ice Cream', price: '60.000 vnd / box' },
        { name: 'Mango / Xoài', desc: 'Mango Ice Cream', price: '60.000 vnd / box' },
        { name: 'Matcha / Trà Matcha', desc: 'Matcha Green Tea Ice Cream', price: '60.000 vnd / box' },
        { name: 'Vanilla Bella / Vani Bella', desc: 'Vanilla Ice Cream', price: '60.000 vnd / box' },
        { name: 'Strawberry / Dâu', desc: 'Strawberry Ice Cream', price: '60.000 vnd / box' },
        { name: 'Durian / Sầu Riêng', desc: 'Durian Ice Cream', price: '60.000 vnd / box' },
        { name: 'Passion Fruit / Chanh Dây', desc: 'Passion Fruit Ice Cream', price: '60.000 vnd / box' },
      ] },
      { id: 'trang-mieng-sang', label: 'TRÁNG MIỆNG', en: 'Dessert', img: IMG.crab, menuImages: [
        'https://horizons-cdn.hostinger.com/a2bb8e3c-639e-4083-bf32-32f3f7f0cebb/703063ae5a0bdb4cda77f75f05392293.jpg',
      ], dishes: [
        { name: 'Chè Hoa Cau', desc: 'Vietnamese Mung Bean Sweet Soup', price: '35.000 vnd' },
        { name: 'Chè Xoa Xoa Hạt Chia', desc: 'Chia Seed Sweet Soup', price: '55.000 vnd' },
        { name: 'Chè Đậu Đỏ', desc: 'Red Bean Sweet Soup', price: '35.000 vnd' },
        { name: 'Sữa Chua Dâu Tây', desc: 'Fresh Strawberry Yogurt', price: '45.000 vnd' },
        { name: 'Sữa Chua Xoài', desc: 'Fresh Mango Yogurt', price: '45.000 vnd' },
        { name: 'Sữa Chua Chanh Dây', desc: 'Fresh Passion Fruit Yogurt', price: '45.000 vnd' },
        { name: 'Trái Cây Theo Mùa – Size M', desc: 'Seasonal Fruit Platter – Medium', price: '165.000 vnd' },
        { name: 'Trái Cây Theo Mùa – Size L', desc: 'Seasonal Fruit Platter – Large', price: '195.000 vnd' },
      ] },
    ],
  },
  trua_toi: {
    id: 'trua_toi', label: 'Trưa và Tối', en: 'Lunch & Dinner',
    categories: [
      { id: 'khai-vi', label: 'KHAI VỊ & SALAD', en: 'Appetizers & Salad', img: IMG.lotus,
        menuImages: ['https://horizons-cdn.hostinger.com/a2bb8e3c-639e-4083-bf32-32f3f7f0cebb/29043e7f2fafb682520d3d4af3303481.jpg'],
        dishes: [
        { name: 'Gỏi Ngũ Sắc Tôm Thịt', desc: 'Shrimp, pork meat and five kinds of vegetables salad', price: '165.000 vnd' },
        { name: 'Xà Lách Rong Nho Hải Sản', desc: 'Seafood and grape seaweed salad', price: '165.000 vnd' },
        { name: 'Gỏi Bắp Bò Bóp Chuối Xanh', desc: 'Beef and green banana salad', price: '165.000 vnd' },
        { name: 'Gỏi Xoài Hải Sản', desc: 'Mango and seafood salad', price: '165.000 vnd' },
        { name: 'Gỏi Bưởi Hải Sản', desc: 'Pomelo salad with seafood', price: '185.000 vnd' },
        { name: 'Xà Lách Cá Ngừ', desc: 'Tuna salad', price: '165.000 vnd' },
        { name: 'Gỏi Hoa Chuối Tôm Thịt', desc: 'Banana blossom salad with shrimp and pork', price: '165.000 vnd' },
        { name: 'Vả Trộn Tôm Thịt Kiểu Huế', desc: 'Mixed Roxburgh fig with shrimp and pork Hue Style', price: '165.000 vnd' },
      ] },
      { id: 'truyen-thong', label: 'MÓN ĂN TRUYỀN THỐNG', en: 'Traditional Food', img: IMG.spread,
        menuImages: ['https://horizons-cdn.hostinger.com/a2bb8e3c-639e-4083-bf32-32f3f7f0cebb/ea725e55ba3b293ae53478ca80959d2e.jpg'],
        dishes: [
        { name: 'Mẹt Cuốn Tươi Ngon', desc: 'Bamboo tray of Vietnamese rolls', price: '195.000 vnd' },
        { name: 'Mẹt Chiên Ngon', desc: 'Bamboo tray of crispy spring rolls', price: '195.000 vnd' },
        { name: 'Gỏi Cuốn Tôm', desc: 'Fresh spring rolls with shrimp', price: '135.000 vnd' },
        { name: 'Gỏi Cuốn Tôm Thịt Tươi', desc: 'Fresh spring rolls with shrimp and pork', price: '135.000 vnd' },
        { name: 'Gỏi Cuốn Cá Hồi', desc: 'Fresh spring rolls with salmon', price: '145.000 vnd' },
        { name: 'Nem Rán Hà Nội', desc: 'Ha Noi deep fried spring rolls', price: '135.000 vnd' },
        { name: 'Chả Giò Miền Trung', desc: 'Central Vietnamese deep fried spring rolls', price: '135.000 vnd' },
        { name: 'Chả Giò Hải Sản', desc: 'Deep fried seafood spring rolls', price: '135.000 vnd' },
      ] },
      { id: 'bun-nuoc', label: 'MÓN ĂN TRUYỀN THỐNG (BÚN)', en: 'Traditional Noodle', img: IMG.patio,
        menuImages: ['https://horizons-cdn.hostinger.com/a2bb8e3c-639e-4083-bf32-32f3f7f0cebb/796ee3d105e28ecd163136a9350144fe.jpg'],
        dishes: [
        { name: 'Bún Riêu Ốc', desc: 'Snail and Crab Paste noodles soup', price: '85.000 vnd' },
        { name: 'Bún Riêu Cua', desc: 'Crab paste noodles soup', price: '80.000 vnd' },
        { name: 'Bún Chả Hà Nội', desc: '"Ha Noi" Style noodles with grilled pork belly and meatballs', price: '85.000 vnd' },
        { name: 'Bún Thịt Nướng', desc: '"Hue" Style noodles with grilled pork and some pickled vegetables', price: '80.000 vnd' },
        { name: 'Bún Bò Huế', desc: '"Hue" Style noodles with beef, Crab paste, beef shank special soup', price: '80.000 vnd' },
        { name: 'Mỳ Quảng Gà / Tôm, Thịt', desc: '"Quang Nam" Style noodles with chicken/beef, special soup', price: '80.000 vnd' },
        { name: 'Cao Lầu', desc: '"Cao Lau" Quang Nam style noodles with char siu pork vegetable special soup', price: '80.000 vnd' },
        { name: 'Phở Bò / Gà', desc: '"Ha Noi" Style Flat noodle with Beef/Chicken', price: '80.000 vnd' },
      ] },
      { id: 'heo', label: 'HEO', en: 'Pork', img: IMG.appetizer,
        menuImages: ['https://horizons-cdn.hostinger.com/a2bb8e3c-639e-4083-bf32-32f3f7f0cebb/db7e84e9adbf0866ca57bb2c0327d9c1.jpg'],
        dishes: [
        { name: 'Ba Chỉ Luộc Dưa Giá Tôm Chua', desc: 'Boiled pork belly with sour shrimp paste and pickled bean sprouts', price: '175.000 vnd' },
        { name: 'Thịt Heo Quay Giòn Da', desc: 'Roasted crispy pork belly, Pickled bean sprouts', price: '185.000 vnd' },
        { name: 'Ba Chỉ Rang Cháy Cạnh', desc: 'Deep fried pork belly with Chef sauce', price: '175.000 vnd' },
        { name: 'Thịt Heo Kho Vả', desc: 'Vietnamese braised pork belly with figs', price: '185.000 vnd' },
        { name: 'Thịt Heo Kho Hột Vịt', desc: 'Braised pork and eggs', price: '185.000 vnd' },
        { name: 'Sườn Xào Chua Ngọt', desc: 'Sauteed pork ribs with sweet and sour sauce', price: '175.000 vnd' },
        { name: 'Heo Xiên Nướng', desc: 'Grilled pork skewer', price: '175.000 vnd' },
        { name: 'Sườn Heo Nướng Muối Ớt', desc: 'Grilled pork ribs with salt and chili', price: '175.000 vnd' },
      ] },
      { id: 'vit', label: 'VỊT', en: 'Duck', img: IMG.dinner,
        menuImages: ['https://horizons-cdn.hostinger.com/a2bb8e3c-639e-4083-bf32-32f3f7f0cebb/906e7d5316a73e3bd6a0aafe4d361c64.jpg'],
        dishes: [
        { name: 'Ức Vịt Xông Khói Sốt Cam', desc: 'Smoked duck breast with orange sauce', price: '175.000 vnd' },
        { name: 'Vịt Cháy Tỏi', desc: 'Stir fried duck with garlic', price: '195.000 vnd' },
        { name: 'Ức Vịt Ủ Khô Nướng Mật Ong', desc: 'Dry-aged honey roasted duck breast', price: '245.000 vnd' },
        { name: 'Ức Vịt Ủ Khô Áp Chảo Sốt Me', desc: 'Pan-seared dry-aged duck breast with tamarind sauce', price: '245.000 vnd' },
      ] },
      { id: 'trung-dau', label: 'TRỨNG & ĐẬU KHUÔN - MÓN SÚP', en: 'Eggs & Tofu – Soup', img: IMG.musicians,
        menuImages: ['https://horizons-cdn.hostinger.com/a2bb8e3c-639e-4083-bf32-32f3f7f0cebb/27ecd01c5a2f7f3b7e4da00253ebfc25.jpg'],
        dishes: [
        { name: 'Trứng Đúc Thịt', desc: 'Fried egg with minced pork', price: '85.000 vnd' },
        { name: 'Trứng Đúc Nghêu', desc: 'Fried egg with clams', price: '85.000 vnd' },
        { name: 'Trứng Chiên', desc: 'Fried egg', price: '75.000 vnd' },
        { name: 'Đậu Khuôn Chiên (V)', desc: 'Deep fried tofu with soya sauce', price: '70.000 vnd' },
        { name: 'Đậu Phụ, Ớt Chuông Xào Chua Ngọt (V)', desc: 'Stir-fried Tofu and Bell Peppers with Sweet and Sour sauce', price: '105.000 vnd' },
        { name: 'Đậu Hủ Non Sốt Thịt Bằm', desc: 'Soft tofu with minced pork', price: '145.000 vnd' },
      ] },

      { id: 'bo', label: 'BÒ', en: 'Beef', img: IMG.crab,
        menuImages: ['https://horizons-cdn.hostinger.com/a2bb8e3c-639e-4083-bf32-32f3f7f0cebb/f3567bb6bd73147277b1b0f35e8f3090.jpg'],
        dishes: [
        { name: 'Bò Mỹ Bít Tết Khoai Tây Chiên', desc: 'American beef with French fries and salad', price: '195.000 vnd' },
        { name: 'Thăn Ngoại Bò Úc Nướng (200gr)', desc: 'Grilled Australia Beef Striploin Steak with vegetables', price: '255.000 vnd' },
        { name: 'Bò Xiên Nướng', desc: 'Grilled beef skewer', price: '195.000 vnd' },
        { name: 'Bò Cuốn Lá Lốt', desc: 'Grilled beef with lolot leaf', price: '175.000 vnd' },
        { name: 'Bò Lúc Lắc Khoai Tây', desc: 'Vietnamese shaking beef with french fries', price: '195.000 vnd' },
        { name: 'Bò Kéo Pháo', desc: 'Stir fried beef with eggplant', price: '185.000 vnd' },
        { name: 'Bê Non Hấp Sả', desc: 'Steamed veal with lemongrass', price: '175.000 vnd' },
        { name: 'Bò Xào Hành Cần', desc: 'Stir-fried beef with onions and celery', price: '175.000 vnd' },
      ] },
      { id: 'banh', label: 'MÓN ĂN TRUYỀN THỐNG (BÁNH)', en: 'Traditional Dishes', img: IMG.dessert,
        menuImages: ['https://horizons-cdn.hostinger.com/a2bb8e3c-639e-4083-bf32-32f3f7f0cebb/22e14fc30846a62d8f21c3ec2f5ffcdb.jpg'],
        dishes: [
        { name: 'Chả Giò Chay (V)', desc: 'Deep fried vegetarian spring rolls', price: '135.000 vnd' },
        { name: 'Nem Nướng', desc: 'Grilled pork paste', price: '135.000 vnd' },
        { name: 'Chả Ram Tôm Đất', desc: 'Deep fried crispy shrimp minced pork spring rolls', price: '155.000 vnd' },
        { name: 'Bánh Ướt Heo Quay', desc: 'Steamed rice pancake with roasted pork', price: '195.000 vnd' },
        { name: 'Bánh Tráng Thịt Heo', desc: 'Vietnamese rice paper rolls with boiled pork and vegetables', price: '195.000 vnd' },
        { name: 'Bánh Xèo Tôm Thịt / Bò', desc: 'Southern-style crispy pancake with shrimp and pork/beef', price: '115.000 vnd' },
        { name: 'Bánh Bèo', desc: 'Vietnamese steamed rice cake with pork meat, mushroom, shrimp', price: '60.000 vnd' },
        { name: 'Bánh Lọc', desc: '"Banh Loc" cake with shrimp and pork meat inside', price: '60.000 vnd' },
        { name: 'Bánh Hỏi Nem Nướng', desc: 'Vietnamese Grilled Pork with fine rice Vermicelli', price: '165.000 vnd' },
        { name: 'Bánh Hỏi Thịt Nướng', desc: 'Grilled Pork with fine rice Vermicelli', price: '165.000 vnd' },
        { name: 'Bánh Hỏi Vịt Quay', desc: 'Roasted duck with fine rice Vermicelli', price: '195.000 vnd' },
      ] },
      { id: 'rau', label: 'MÓN RAU', en: 'Vegetables', img: IMG.patio,
        menuImages: ['https://horizons-cdn.hostinger.com/a2bb8e3c-639e-4083-bf32-32f3f7f0cebb/7665ca3dcf07dbce9415417272da5543.jpg'],
        dishes: [
        { name: 'Rau Rừng Xào Tỏi', desc: 'Stir-fried wild vegetables with garlic', price: '80.000 vnd' },
        { name: 'Bí Nụ Xào Thịt Bò', desc: 'Stir-fried young pumpkin with beef', price: '145.000 vnd' },
        { name: 'Rau Muống Xào Tỏi', desc: 'Stir-fried morning glory with garlic', price: '75.000 vnd' },
        { name: 'Khổ Qua Xào Trứng', desc: 'Sauteed bitter melon with egg', price: '80.000 vnd' },
        { name: 'Đậu Nành Nhật Lắc Muối (V)', desc: 'Steamed edamame with salt', price: '75.000 vnd' },
        { name: 'Cải Thìa, Nấm Xào Dầu Hào', desc: 'Sauteed Bokchoy with oyster sauce', price: '85.000 vnd' },
        { name: 'Khoai Tây Chiên (V)', desc: 'French fries', price: '75.000 vnd' },
        { name: 'Ngũ Quả Luộc Kho Quẹt', desc: 'Boiled mix vegetables with caramel and soya sauce', price: '75.000 vnd' },
        { name: 'Rau Tập Tàng Luộc Chấm Tôm Kho Đánh', desc: 'Boiled mix vegetables with shrimp sauce', price: '80.000 vnd' },
      ] },
      { id: 'ga', label: 'GÀ', en: 'Chicken', img: IMG.appetizer, note: '220.000 VND / 1/2 con — 440.000 VND / con',
        menuImages: ['https://horizons-cdn.hostinger.com/a2bb8e3c-639e-4083-bf32-32f3f7f0cebb/25345e8d311d28ed70da6ae76d8c62d4.jpg'],
        dishes: [
        { name: 'Gà Roti', desc: 'Rotisserie chicken with sauce', price: '220.000 vnd' },
        { name: 'Gà Chiên Mắm', desc: 'Deep fried chicken with fish sauce', price: '220.000 vnd' },
        { name: 'Gà Hấp Hành', desc: 'Steamed Chicken with green onion', price: '220.000 vnd' },
        { name: 'Gà Xé Trộn Hành và Rau Răm', desc: 'Shredded chicken with onion, laksa leaves', price: '220.000 vnd' },
        { name: 'Gà Nướng Mọi', desc: 'Grilled chicken', price: '220.000 vnd' },
        { name: 'Gà Nướng Muối Ớt', desc: 'Grilled chicken with salt and chili', price: '220.000 vnd' },
        { name: 'Cánh Gà Nướng Mật Ong', desc: 'Honey grilled chicken wings', price: '165.000 vnd' },
        { name: 'Gà Xiên Nướng', desc: 'Grilled chicken skewer', price: '165.000 vnd' },
        { name: 'Cánh Gà Chiên Mắm', desc: 'Fried Chicken Wings with Fish Sauce', price: '175.000 vnd' },
      ] },
      { id: 'oc-ngheu', label: 'ỐC HƯƠNG - NGHÊU', en: 'Babylonia Snail – Clam', img: IMG.crab, menuImages: ['https://horizons-cdn.hostinger.com/a2bb8e3c-639e-4083-bf32-32f3f7f0cebb/9b706c3528db35bb6ab2254b513e7edd.jpg'], dishes: [
        { name: 'Ốc Hương Nướng Mọi', desc: 'Grilled spotted Babylon snail', price: '225.000 vnd' },
        { name: 'Ốc Hương Hấp Sả', desc: 'Steamed Babylon snail with lemongrass', price: '225.000 vnd' },
        { name: 'Nghêu Hấp Sả', desc: 'Steamed clams with lemongrass', price: '155.000 vnd' },
        { name: 'Nghêu Xào Lá Quế', desc: 'Stir-fried clams with basil', price: '155.000 vnd' },
      ] },
      { id: 'ca-bien', label: 'CÁ', en: 'Fish', img: IMG.dinner, menuImages: ['https://horizons-cdn.hostinger.com/a2bb8e3c-639e-4083-bf32-32f3f7f0cebb/e196fbb658c7db0c6eb076b8aa44fe68.jpg','https://horizons-cdn.hostinger.com/a2bb8e3c-639e-4083-bf32-32f3f7f0cebb/c7493f419061f6609551b021e690b0b1.jpg'], dishes: [
        { name: 'Cá Mú Hấp Xí Dầu', desc: 'Steamed grouper with soya sauce', price: '855.000 vnd' },
        { name: 'Cá Chim Trắng Hấp Xí Dầu', desc: 'Steamed silver pomfret with soya sauce', price: '495.000 vnd' },
        { name: 'Cá Hồi Nướng Sốt Chanh Dây', desc: 'Grilled salmon with passion fruit sauce', price: '255.000 vnd' },
        { name: 'Cá Chình Nướng Mọi', desc: 'Grilled eel fish', price: '265.000 vnd' },
        { name: 'Cá Chẽm Nướng Sốt Chanh Dây', desc: 'Grilled seabass with passion fruit sauce', price: '195.000 vnd' },
        { name: 'Cá Chẽm Hấp Xí Dầu', desc: 'Steamed seabass with soya sauce', price: '195.000 vnd' },
        { name: 'Cá Chình Nướng Nghệ', desc: 'Grilled eel fish with turmeric', price: '265.000 vnd' },
        { name: 'Cá Hồi Ủ Khô Nướng Xiên', desc: 'Grilled dry-aged salmon skewers', price: '175.000 vnd' },
        { name: 'Cá Chim Trắng Nướng Muối Ớt', desc: 'Grilled silver pomfret with salt and chili', price: '495.000 vnd' },
        { name: 'Cá Dĩa Hấp Xí Dầu', desc: 'Steamed rabbit fish with soy sauce', price: '255.000 vnd' },
        { name: 'Cá Dĩa Nướng Muối Ớt', desc: 'Grilled rabbit fish with salt and chili sauce', price: '255.000 vnd' },
        { name: 'Cá Chình Om Chuối Đậu', desc: 'Conger eel stew with green banana and tofu', price: '285.000 vnd' },
        { name: 'Cá Dĩa Kho Tiêu', desc: 'Braised rabbit fish with pepper in clay pot', price: '255.000 vnd' },
        { name: 'Cá Điêu Hồng Chiên Xù Mắm Xoài', desc: 'Deep fried Red Tilapia with mango salad sauce', price: '225.000 vnd' },
        { name: 'Cá Lóc Chiên Xù Sốt Mắm Xoài', desc: 'Deep fried snakehead fish with mango and fish sauce', price: '225.000 vnd' },
        { name: 'Cá Bông Lau Kho Tộ', desc: 'Braised Chinese Pangasius Krempfi in clay pot', price: '165.000 vnd' },
        { name: 'Cá Lóc Kho Tộ', desc: 'Braised snakehead fish in clay pot', price: '165.000 vnd' },
        { name: 'Cá Bớp Kho Ớt Xanh / Dưa Cải', desc: 'Braised cobia with green chili / pickled mustard greens', price: '185.000 vnd' },
      ] },
      { id: 'canh', label: 'CANH', en: 'Broth', img: IMG.lunch, menuImages: ['https://horizons-cdn.hostinger.com/a2bb8e3c-639e-4083-bf32-32f3f7f0cebb/771d701d4826b89aa6d65bf4304416d4.jpg'], dishes: [
        { name: 'Canh Chua Cá Bông Lau', desc: 'Chinese Pangasius Krempfi sweet and sour broth', price: '165.000 vnd' },
        { name: 'Canh Chua Cá Dĩa', desc: 'Rabbit fish sweet and sour broth', price: '195.000 vnd' },
        { name: 'Canh Chua Nghêu', desc: 'Clam sweet and sour broth', price: '165.000 vnd' },
        { name: 'Canh Chua Cá Lóc', desc: 'Sweet and sour broth with snakehead fish', price: '165.000 vnd' },
        { name: 'Canh Chua Cá Bớp', desc: 'Sweet and sour broth with cobia fish', price: '185.000 vnd' },
        { name: 'Canh Cua Mồng Tơi', desc: 'Crab paste and Malabar spinach broth', price: '155.000 vnd' },
        { name: 'Canh Khổ Qua Nấu Tôm', desc: 'Bitter melon broth with shrimp', price: '155.000 vnd' },
        { name: 'Canh Rong Biển Đậu Hủ Non (V)', desc: 'Seaweed broth with young tofu', price: '155.000 vnd' },
        { name: 'Canh Nghêu Rau Muống', desc: 'Morning glory and clams broth', price: '155.000 vnd' },
      ] },
      { id: 'lau', label: 'LẦU', en: 'Hotpot', img: IMG.spread, menuImages: ['https://horizons-cdn.hostinger.com/a2bb8e3c-639e-4083-bf32-32f3f7f0cebb/60204b15a08736cf9fd88978f11bc8d4.jpg'], dishes: [
        { name: 'Lẩu Thập Cẩm', desc: 'Mixed seafood, beef, fish and vegetables hotpot', price: '695.000 vnd' },
        { name: 'Lẩu Cá Mú Chua Cay Nguyên Con', desc: 'Sour and spicy whole grouper hotpot', price: '895.000 vnd' },
        { name: 'Lẩu Bò Riêu Cua', desc: 'Beef and crab paste hotpot', price: '595.000 vnd' },
        { name: 'Lẩu Cá Dĩa', desc: 'Rabbitfish hotpot', price: '595.000 vnd' },
        { name: 'Lẩu Cá Bớp Chua Cay', desc: 'Sour and spicy cobia hotpot', price: '575.000 vnd' },
        { name: 'Lẩu Gà Lá Giang / Nấm', desc: 'Chicken hotpot with river-leaf creeper or mushroom', price: '575.000 vnd' },
        { name: 'Lẩu Cá Chim', desc: 'Silver Pomfret hotpot', price: '595.000 vnd' },
        { name: 'Lẩu Nấm (V)', desc: 'Mushroom hotpot', price: '455.000 vnd' },
      ] },
      { id: 'mi', label: 'MÌ', en: 'Noodle', img: IMG.patio, menuImages: ['https://horizons-cdn.hostinger.com/a2bb8e3c-639e-4083-bf32-32f3f7f0cebb/e0e0818e106079cabe31e3982fa564d5.jpg'], dishes: [
        { name: 'Miến Xào Cua', desc: 'Stir-fried vermicelli with crab meat', price: '225.000 vnd' },
        { name: 'Mì Xào Hải Sản', desc: 'Stir-fried noodles with seafood and vegetables', price: '185.000 vnd' },
        { name: 'Mì Xào Bò', desc: 'Stir-fried noodles with beef and vegetables', price: '185.000 vnd' },
        { name: 'Mì Xào Nấm Và Rau Củ (V)', desc: 'Stir-fried noodles with mushroom and vegetables', price: '165.000 vnd' },
      ] },
      { id: 'com', label: 'CƠM', en: 'Rice', img: IMG.appetizer, menuImages: ['https://horizons-cdn.hostinger.com/a2bb8e3c-639e-4083-bf32-32f3f7f0cebb/97be446a4d5828a202353b3573c7f080.jpg'], dishes: [
        { name: 'Cơm Tay Cầm Gà', desc: 'Fried rice in clay pot with chicken', price: '175.000 vnd' },
        { name: 'Cơm Chiên Trái Thơm Hải Sản', desc: 'Seafood Pineapple Fried rice', price: '185.000 vnd' },
        { name: 'Cơm Tay Cầm Thịt Xá Xíu', desc: 'Fried rice in clay pot with char siu pork', price: '175.000 vnd' },
        { name: 'Cơm Chiên Hải Sản', desc: 'Fried rice with seafood', price: '165.000 vnd' },
        { name: 'Cơm Chiên Cá Mặn', desc: 'Fried rice with salted fish', price: '165.000 vnd' },
        { name: 'Cơm Chiên Dương Châu', desc: 'Yangzhou fried rice', price: '165.000 vnd' },
        { name: 'Cơm Chiên Gà Xẻ', desc: 'Shredded chicken fried rice', price: '165.000 vnd' },
        { name: 'Cơm Bò Lúc Lắc', desc: 'Fried rice with vietnamese shaking beef', price: '185.000 vnd' },
        { name: 'Cơm Gà Quay', desc: 'Deep fried chicken with fried rice with egg', price: '165.000 vnd' },
        { name: 'Cơm Chiên Trứng', desc: 'Fried rice with egg', price: '115.000 vnd' },
        { name: 'Cơm Trắng', desc: 'Steamed rice', price: '45.000 vnd' },
        { name: 'Cơm Niêu', desc: 'Vietnamese Clay Pot Rice', price: '30.000 vnd' },
      ] },
      { id: 'tom', label: 'TÔM SÚ / TÔM HÙM', en: 'Prawn – Lobster', img: IMG.musicians, menuImages: ['https://horizons-cdn.hostinger.com/a2bb8e3c-639e-4083-bf32-32f3f7f0cebb/8d07eb47c77ce2745c4a1ff1185ca8c5.jpg'], dishes: [
        { name: 'Tôm Cuộn Ba Rọi', desc: 'Bacon wrapped shrimp', price: '165.000 vnd' },
        { name: 'Tôm Hùm Nướng Mọi / Phô Mai / Bơ Tỏi (500g)', desc: 'Grilled Lobster / cheese / butter & garlic', price: '655.000 vnd' },
        { name: 'Tôm Sú Hấp', desc: 'Steamed prawn', price: '285.000 vnd / 350gr — 395.000 vnd / 500gr' },
        { name: 'Tôm Sú Sốt Me', desc: 'Sauteed prawn with tamarind sauce', price: '285.000 vnd / 350gr — 395.000 vnd / 500gr' },
        { name: 'Tôm Sú Xốc Tỏi', desc: 'Sauteed prawn with garlic', price: '285.000 vnd / 350gr — 395.000 vnd / 500gr' },
        { name: 'Tôm Sú Nướng', desc: 'Grilled prawn', price: '285.000 vnd / 350gr — 395.000 vnd / 500gr' },
        { name: 'Tôm Sú Rang Muối', desc: 'Stir-fried prawn with salt', price: '285.000 vnd / 350gr — 395.000 vnd / 500gr' },
        { name: 'Tôm Sú Tẩm Bột Chiên Xù', desc: 'Deep-fried tempura prawn', price: '285.000 vnd / 350gr' },
      ] },
      { id: 'muc', label: 'MỰC', en: 'Squid', img: IMG.dessert, menuImages: ['https://horizons-cdn.hostinger.com/a2bb8e3c-639e-4083-bf32-32f3f7f0cebb/285cad682314b8d82a4821ae144a12ce.jpg'], dishes: [
        { name: 'Mực Cơm Hấp Hành', desc: 'Steamed squid with onions', price: '175.000 vnd' },
        { name: 'Mực Cơm Chiên Mắm', desc: 'Deep fried squid with fish sauce', price: '195.000 vnd' },
        { name: 'Mực Chiên Xù', desc: 'Tempura squid', price: '165.000 vnd' },
        { name: 'Mực Xào Thơm Cà', desc: 'Stir-fried squid with tomato and pineapple', price: '185.000 vnd' },
        { name: 'Mực Tươi Hấp', desc: 'Steamed squid', price: '285.000 vnd / 350gr — 395.000 vnd / 500gr' },
        { name: 'Mực Một Nắng Nướng', desc: 'Charcoal grilled squid (dried)', price: '305.000 vnd / 350gr — 425.000 vnd / 500gr' },
        { name: 'Mực Tươi Nướng', desc: 'Charcoal grilled fresh squid', price: '285.000 vnd / 350gr — 395.000 vnd / 500gr' },
      ] },
      { id: 'hau-so-bao', label: 'HÀU - SÒ ĐIỆP - BÀO NGƯ', en: 'Oyster – Scallop – Abalone', img: IMG.lotus, menuImages: ['https://horizons-cdn.hostinger.com/a2bb8e3c-639e-4083-bf32-32f3f7f0cebb/d7b61c889b42ee0fc2de218e26823941.jpg'], dishes: [
        { name: 'Hàu Nướng Phô Mai', desc: 'Grilled oysters with cheese', price: '175.000 vnd' },
        { name: 'Combo Hàu 3 Vị', desc: 'Grilled oyster with green onion oil, cheese, sausage', price: '195.000 vnd' },
        { name: 'Hàu Nướng Mỡ Hành', desc: 'Grilled oysters with onion oil', price: '175.000 vnd' },
        { name: 'Sò Điệp Nhật Nướng Mỡ Hành', desc: 'Grilled Japanese scallops with onion oil', price: '235.000 vnd / 4 con — 345.000 vnd / 6 con' },
        { name: 'Sò Điệp Nhật Nướng Phô Mai', desc: 'Grilled Japanese scallops with cheese', price: '235.000 vnd / 4 con — 345.000 vnd / 6 con' },
        { name: 'Bào Ngư Sốt Bơ Tỏi', desc: 'Sauteed abalone with butter and garlic sauce', price: '235.000 vnd' },
        { name: 'Bào Ngư Nấm Sốt Dầu Hào', desc: 'Sauteed abalone with mushroom and oyster sauce', price: '235.000 vnd' },
        { name: 'Bào Ngư Nướng Mỡ Hành', desc: 'Grilled abalone with green onion and oil', price: '235.000 vnd' },
      ] },
      { id: 'kem-toi', label: 'KEM CÁC LOẠI', en: 'Ice Cream – 60.000 vnd / Box', img: IMG.dessert, menuImages: [
        'https://horizons-cdn.hostinger.com/a2bb8e3c-639e-4083-bf32-32f3f7f0cebb/db0a9016a5aec29edb084fcb5050883e.jpg',
      ], dishes: [
        { name: 'CHOCO CHIPS / SỐ CỐ CHIP', desc: 'Chocolate Chip Ice Cream', price: '60.000 vnd / BOX' },
        { name: 'COCO BELLA / DỪA BELLA', desc: 'Coconut Ice Cream', price: '60.000 vnd / BOX' },
        { name: 'MANGO / XOÀI', desc: 'Mango Ice Cream', price: '60.000 vnd / BOX' },
        { name: 'MATCHA / TRÀ MATCHA', desc: 'Matcha Green Tea Ice Cream', price: '60.000 vnd / BOX' },
        { name: 'VANILLA BELLA / VANI BELLA', desc: 'Vanilla Ice Cream', price: '60.000 vnd / BOX' },
        { name: 'STRAWBERRY / DÂU', desc: 'Strawberry Ice Cream', price: '60.000 vnd / BOX' },
        { name: 'DURIAN / SẦU RIÊNG', desc: 'Durian Ice Cream', price: '60.000 vnd / BOX' },
        { name: 'PASSION FRUIT / CHANH DÂY', desc: 'Passion Fruit Ice Cream', price: '60.000 vnd / BOX' },
      ] },
      { id: 'trang-mieng-toi', label: 'TRÁNG MIỆNG', en: 'Dessert', img: IMG.crab, menuImages: [
        'https://horizons-cdn.hostinger.com/a2bb8e3c-639e-4083-bf32-32f3f7f0cebb/30c3c0133b397a6b6456ba05d378e4d3.jpg',
      ], dishes: [
        { name: 'CHÉ HOA CẦU', desc: 'Vietnamese Mung Bean Sweet Soup', price: '35.000 vnd' },
        { name: 'CHÉ XOA XOA HẠT CHIA', desc: 'Chia Seed Sweet Soup', price: '55.000 vnd' },
        { name: 'CHÉ ĐẬU ĐỎ', desc: 'Red Bean Sweet Soup', price: '35.000 vnd' },
        { name: 'SỮA CHUA ĐẬU TÂY', desc: 'Fresh Strawberry Yogurt', price: '45.000 vnd' },
        { name: 'SỮA CHUA XOÀI', desc: 'Fresh Mango Yogurt', price: '45.000 vnd' },
        { name: 'SỮA CHUA CHANH DÂY', desc: 'Fresh Passion Fruit Yogurt', price: '45.000 vnd' },
        { name: 'TRÁI CÂY / SEASONAL FRUIT (SIZE M)', desc: 'Seasonal fresh fruit platter – Medium', price: '165.000 vnd' },
        { name: 'TRÁI CÂY / SEASONAL FRUIT (SIZE L)', desc: 'Seasonal fresh fruit platter – Large', price: '195.000 vnd' },
      ] },
    ],
  },
  do_uong_co_con: {
    id: 'do_uong_co_con', label: 'Đồ Uống Có Cồn', en: 'Alcoholic Beverages',
    categories: [
      { id: 'spirit', label: 'SPIRIT BY SHOT / BOTTLE', en: 'Whisky, Vodka, Tequila, Rhum, Gin, Cognac, Liqueur, Soju', img: IMG.dinner, menuImages: ['https://horizons-cdn.hostinger.com/a2bb8e3c-639e-4083-bf32-32f3f7f0cebb/d02be4fdd376e3a829a538b4cbe78a6f.jpg', 'https://horizons-cdn.hostinger.com/a2bb8e3c-639e-4083-bf32-32f3f7f0cebb/4988b13f0b007ea91bf1aa868879dc06.jpg'] },
      { id: 'classic-cocktail', label: 'CLASSIC COCKTAIL', en: 'Classic Cocktail', img: IMG.musicians, menuImages: ['https://horizons-cdn.hostinger.com/a2bb8e3c-639e-4083-bf32-32f3f7f0cebb/0564cdebe361fecb95933f6b867c01a9.jpg'] },
      { id: 'signature-cocktail', label: 'SIGNATURE COCKTAIL', en: 'Signature Cocktail', img: IMG.appetizer, menuImages: ['https://horizons-cdn.hostinger.com/a2bb8e3c-639e-4083-bf32-32f3f7f0cebb/1977d99c12a1093f69bfa0a217b184bc.jpg'] },
      { id: 'beer', label: 'BEER', en: 'Bia Lon & Chai', img: IMG.cozy, menuImages: ['https://horizons-cdn.hostinger.com/a2bb8e3c-639e-4083-bf32-32f3f7f0cebb/38a8be817258a1b3ec12618aa6e2fe80.jpg'] },
      { id: 'draught-beer', label: 'DRAUGHT BEER & SOFT DRINK', en: 'Bia Tươi, Nước Khoáng & Nước Ngọt', img: IMG.patio, menuImages: ['https://horizons-cdn.hostinger.com/a2bb8e3c-639e-4083-bf32-32f3f7f0cebb/fdac2ce4cf84dabd2f4ac0804bb23d50.jpg'] },
    ],
  },
  ruou_vang: {
    id: 'ruou_vang',
    label: 'Rượu Vang',
    en: 'Wine',
    categories: [
      { id: 'wine-by-glass', label: 'RƯỢU VANG THEO LY', en: 'Red & White Wine by Glass + Sauvignon Blanc', img: IMG.cozy,
        menuImages: ['https://horizons-cdn.hostinger.com/a2bb8e3c-639e-4083-bf32-32f3f7f0cebb/5aa54b98b4eec9f64a943e4b2552130f.jpg'] },
      { id: 'riesling-white', label: 'RIESLING / GEWÜRZTRAMINER / WHITE BLEND', en: 'White Wine — Riesling, Gewürztraminer, White Wine Blend', img: IMG.patio,
        menuImages: ['https://horizons-cdn.hostinger.com/a2bb8e3c-639e-4083-bf32-32f3f7f0cebb/919d30b1ec3243aed91ac99eae403f67.jpg'] },
      { id: 'pinot-grigio-chardonnay', label: 'PINOT GRIGIO / CHARDONNAY', en: 'White Wine — Pinot Grigio & Chardonnay', img: IMG.patio,
        menuImages: ['https://horizons-cdn.hostinger.com/a2bb8e3c-639e-4083-bf32-32f3f7f0cebb/f2bb8c877b79efccd83cfe8d2ec42d56.jpg'] },
      { id: 'champagne-sparkling', label: 'CHAMPAGNE & SPARKLING WINE', en: 'Champagne & Sparkling Wine', img: IMG.lotus,
        menuImages: ['https://horizons-cdn.hostinger.com/a2bb8e3c-639e-4083-bf32-32f3f7f0cebb/36813c8d5be293c46c795c87e3928cf1.jpg'] },
      { id: 'pinot-noir-shiraz', label: 'PINOT NOIR / SHIRAZ', en: 'Red Wine — Pinot Noir & Shiraz', img: IMG.dinner,
        menuImages: ['https://horizons-cdn.hostinger.com/a2bb8e3c-639e-4083-bf32-32f3f7f0cebb/e937564dd0d1ae6001fe3016b232fffe.jpg'] },
      { id: 'negroamaro-malbec', label: 'NEGROAMARO / MALBEC / PRIMITIVO', en: 'Red Wine — Negroamaro, Malbec & Primitivo', img: IMG.dinner,
        menuImages: ['https://horizons-cdn.hostinger.com/a2bb8e3c-639e-4083-bf32-32f3f7f0cebb/92f588eea538d96a4fcd4497b0aa2d5f.jpg'] },
      { id: 'merlot-cabernet', label: 'MERLOT / CABERNET SAUVIGNON', en: 'Red Wine — Merlot & Cabernet Sauvignon', img: IMG.dinner,
        menuImages: ['https://horizons-cdn.hostinger.com/a2bb8e3c-639e-4083-bf32-32f3f7f0cebb/3fdfad2f41add4f997942db271a0850b.jpg'] },
      { id: 'montepulciano-blend', label: 'MONTEPULCIANO / RED WINE BLEND', en: 'Red Wine — Montepulciano & Red Wine Blend', img: IMG.dinner,
        menuImages: ['https://horizons-cdn.hostinger.com/a2bb8e3c-639e-4083-bf32-32f3f7f0cebb/9d1a3097dbeeae58d3e009c188a2cf6e.jpg'] },
    ],
  },
  do_uong: {
    id: 'do_uong', label: 'Đồ Uống', en: 'Beverages',
    categories: [
      { id: 'nuoc-ep-sinh-to', label: 'NƯỚC ÉP TRÁI CÂY TƯƠI & SINH TỐ', en: 'Fresh Fruit Juice & Smoothies', img: IMG.lotus, menuImages: ['https://horizons-cdn.hostinger.com/a2bb8e3c-639e-4083-bf32-32f3f7f0cebb/2a20ea944f3f58032e4407ee93000bf3.jpg'] },
      { id: 'sua-chua-healthy', label: 'SỮA CHUA TRÁI CÂY & THỨC UỐNG KHOẺ ĐẸP', en: 'Lassi & Healthy Juice', img: IMG.cozy, menuImages: ['https://horizons-cdn.hostinger.com/a2bb8e3c-639e-4083-bf32-32f3f7f0cebb/7a1018e4e3adbc61da510e118dcfc8ec.jpg'] },
      { id: 'tra-sua', label: 'TRÀ & SỮA', en: 'Tea & Milk', img: IMG.spread, menuImages: ['https://horizons-cdn.hostinger.com/a2bb8e3c-639e-4083-bf32-32f3f7f0cebb/1eae01e955ce762fe7e595c10b1d55fa.jpg'] },
      { id: 'ca-phe', label: 'CÀ PHÊ', en: 'Coffee', img: IMG.dinner, menuImages: ['https://horizons-cdn.hostinger.com/a2bb8e3c-639e-4083-bf32-32f3f7f0cebb/b75fb680d2529ce16a853101d3d30ace.jpg'] },
    ],
  },
};

/* ── small shared components ─────────────────────────────────────── */
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

const MENU_DROPDOWN = [
  { label: 'Thực Đơn Sáng', href: '/?group=sang' },
  { label: 'Thực Đơn Trưa & Tối', href: '/?group=trua_toi' },
  { label: 'Đồ Uống (không cồn)', href: '/?group=do_uong' },
  { label: 'Đồ Uống Có Cồn', href: '/?group=do_uong_co_con' },
  { label: 'Rượu Vang', href: '/menu?group=ruou_vang' },
];

function MenuHeader({ onReservation }) {
  const [scrolled, setScrolled] = useState(false);
  const [mobileOpen, setMobileOpen] = useState(false);
  const [dropOpen, setDropOpen] = useState(false);
  const [mobileDrop, setMobileDrop] = useState(false);
  const dropRef = useRef(null);
  const navigate = useNavigate();
  useEffect(() => {
    const fn = () => setScrolled(window.scrollY > 40);
    window.addEventListener('scroll', fn);
    return () => window.removeEventListener('scroll', fn);
  }, []);
  useEffect(() => {
    const fn = (e) => { if (dropRef.current && !dropRef.current.contains(e.target)) setDropOpen(false); };
    document.addEventListener('mousedown', fn);
    return () => document.removeEventListener('mousedown', fn);
  }, []);
  const lc = `text-[13px] font-medium tracking-wide transition-colors ${scrolled ? 'text-neutral-700 hover:text-[#5e4743]' : 'text-white/90 hover:text-white'}`;
  return (
    <header className={`fixed top-0 inset-x-0 z-50 transition-colors duration-300 ${scrolled ? 'bg-white/95 shadow-md backdrop-blur' : 'bg-black/60'}`}>
      <div className="max-w-[1200px] mx-auto px-5 h-20 flex items-center justify-between gap-4">
        <nav className="hidden lg:flex items-center gap-6 flex-1">
          <a href="/" className={lc}>TRANG CHỦ</a>
          <div className="relative" ref={dropRef}
            onMouseEnter={() => setDropOpen(true)}
            onMouseLeave={() => setDropOpen(false)}
          >
            <button onClick={() => setDropOpen(v => !v)} className={`flex items-center gap-1 text-[13px] font-medium tracking-wide transition-colors ${scrolled ? 'text-neutral-700 hover:text-[#5e4743]' : 'text-white/90 hover:text-white'}`}>
              THỰC ĐƠN <ChevronDown size={14} className={`transition-transform ${dropOpen ? 'rotate-180' : ''}`} />
            </button>
            {dropOpen && (
              <div className="absolute top-full left-0 mt-1 w-52 bg-white rounded-xl shadow-xl overflow-hidden" style={{ zIndex: 9999 }}>
                {MENU_DROPDOWN.map(m => (
                  <button key={m.label} onClick={() => { navigate(m.href); setDropOpen(false); }}
                    className="w-full text-left px-5 py-3 text-sm text-neutral-700 hover:text-[#5e4743] transition-colors font-typewriter" onMouseEnter={(e) => e.currentTarget.style.backgroundColor='#ffc95220'} onMouseLeave={(e) => e.currentTarget.style.backgroundColor=''}>{m.label}</button>
                ))}
              </div>
            )}
          </div>
        </nav>
        <div className="flex-shrink-0"><Logo dark={scrolled} /></div>
        <nav className="hidden lg:flex items-center gap-6 flex-1 justify-end">
          <a href="/#gallery" className={lc}>THƯ VIỆN</a>
          <a href="/#contact" className={lc}>LIÊN HỆ</a>
          <button onClick={onReservation}
            className="ml-2 px-5 py-2 rounded-full text-[13px] font-semibold tracking-wide uppercase transition-all"
            style={{ backgroundColor: GOLD, color: BROWN }}
            onMouseEnter={e => { e.currentTarget.style.backgroundColor = BROWN; e.currentTarget.style.color = GOLD; }}
            onMouseLeave={e => { e.currentTarget.style.backgroundColor = GOLD; e.currentTarget.style.color = BROWN; }}>
            Đặt Bàn
          </button>
        </nav>
        <button className="lg:hidden p-2" style={{ color: scrolled ? '#333' : '#fff' }} onClick={() => setMobileOpen(true)} aria-label="Menu"><MenuIcon size={26} /></button>
      </div>
      {mobileOpen && (
        <div className="fixed inset-0 z-50 bg-neutral-900/97 flex flex-col items-center justify-center gap-6 lg:hidden">
          <button className="absolute top-6 right-6 text-white" onClick={() => setMobileOpen(false)} aria-label="Close"><X size={30} /></button>
          <a href="/" onClick={() => setMobileOpen(false)} className="text-white text-lg tracking-wide font-typewriter">TRANG CHỦ</a>
          <div className="flex flex-col items-center gap-2">
            <button onClick={() => setMobileDrop(v => !v)} className="text-white text-lg tracking-wide font-typewriter flex items-center gap-2">
              THỰC ĐƠN <ChevronDown size={16} className={`transition-transform ${mobileDrop ? 'rotate-180' : ''}`} />
            </button>
            {mobileDrop && MENU_DROPDOWN.map(m => (
              <button key={m.label} onClick={() => { navigate(m.href); setMobileOpen(false); setMobileDrop(false); }}
                className="text-base tracking-wide" style={{ color: GOLD }}>{m.label}</button>
            ))}
          </div>
          <a href="/#gallery" onClick={() => setMobileOpen(false)} className="text-white text-lg tracking-wide font-typewriter">THƯ VIỆN</a>
          <a href="/#contact" onClick={() => setMobileOpen(false)} className="text-white text-lg tracking-wide font-typewriter">LIÊN HỆ</a>
          <button onClick={() => { onReservation(); setMobileOpen(false); }}
            className="px-8 py-3 rounded-full text-base font-semibold uppercase tracking-wide"
            style={{ backgroundColor: GOLD, color: BROWN }}>Đặt Bàn</button>
        </div>
      )}
    </header>
  );
}

/* ── the menu panel ──────────────────────────────────────────────── */
function MenuPanel({ initialGroup }) {
  const [lightbox, setLightbox] = useState(null);
  const [activeGroup, setActiveGroup] = useState(initialGroup || 'sang');
  const group = MENU_GROUPS[activeGroup] || MENU_GROUPS.sang;
  const visibleCats = group.categories.filter(c => !c.hidden);
  const [activeCat, setActiveCat] = useState(visibleCats[0]?.id || group.categories[0].id);
  const cat = visibleCats.find(c => c.id === activeCat) || visibleCats[0] || group.categories[0];

  const handleGroupChange = (gid) => {
    setActiveGroup(gid);
    const vc = MENU_GROUPS[gid].categories.filter(c => !c.hidden);
    setActiveCat(vc[0]?.id || MENU_GROUPS[gid].categories[0].id);
  };

  return (
    <section className="py-24" style={{ backgroundColor: BROWN_DARK }}>
      <div className="max-w-[1400px] mx-auto px-4 md:px-8">
        <div className="text-center mb-4">
          <h2 className="font-typewriter font-bold text-white text-3xl md:text-5xl">Thực Đơn</h2>
          <h3 className="font-typewriter font-bold text-white text-2xl md:text-4xl mt-1">Ngon Thị Hoa Restaurant</h3>
          <p className="text-white/60 text-sm mt-4">Lướt qua để xem thêm</p>
        </div>
        {/* group toggle */}
        <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-2 sm:gap-3 mt-8 mb-10 w-full max-w-3xl mx-auto">
          {Object.values(MENU_GROUPS).map(g => (
            <button key={g.id} onClick={() => handleGroupChange(g.id)}
              className={`px-8 py-3 rounded-full font-typewriter font-semibold text-base tracking-wide uppercase transition-all ${activeGroup === g.id ? 'text-neutral-900' : 'text-white/70 hover:text-white border border-white/20'}`}
              style={activeGroup === g.id ? { backgroundColor: GOLD } : {}}>
              {g.label}
            </button>
          ))}
        </div>
        <div className="grid md:grid-cols-[240px_minmax(0,1fr)] gap-6 lg:gap-8">
          <ul className="flex flex-col gap-1 min-w-0">
            {visibleCats.map(c => (
              <li key={c.id}>
                <button onClick={() => setActiveCat(c.id)}
                  className={`w-full text-left px-5 py-3 rounded-full text-sm leading-snug tracking-wide uppercase transition-colors ${activeCat === c.id ? 'text-white' : 'text-white/70 hover:text-white'}`}
                  style={activeCat === c.id ? { backgroundColor: BROWN } : {}}>
                  {c.label}
                </button>
              </li>
            ))}
          </ul>
          <motion.div key={activeGroup + '-' + cat.id}
            initial={{ opacity: 0, y: 20 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.5 }}
            className="relative rounded-2xl overflow-hidden min-w-0"
            style={{ border: '1px solid rgba(240,193,75,0.35)' }}>
            <div className="absolute inset-0">
              <img src={cat.img} alt={cat.label} className="w-full h-full object-cover" loading="lazy" decoding="async" />
              <div className="absolute inset-0" style={{ backgroundColor: 'rgba(19,38,26,0.82)' }} />
            </div>
            <div className="relative z-10 p-5 sm:p-8 lg:p-10">
              <div className="text-center border rounded-xl py-6 px-4 mb-8 max-w-md mx-auto" style={{ borderColor: 'rgba(240,193,75,0.5)' }}>
                <h4 className="font-script text-4xl" style={{ color: GOLD }}>{cat.label}</h4>
                <p className="text-white/70 tracking-widest text-xs uppercase mt-1">{cat.en}</p>
              </div>
              {cat.note && (
                <div className="text-center mb-6">
                  <span className="inline-block px-5 py-1.5 rounded-full text-sm font-semibold text-neutral-900" style={{ backgroundColor: GOLD }}>{cat.note}</span>
                </div>
              )}
              {cat.menuImages ? (
                <div className={`gap-6 ${cat.menuImages.length > 1 ? 'grid md:grid-cols-2' : 'flex flex-col items-center'}`}>
                  {cat.menuImages.map((imgUrl, idx) => (
                    <button
                      key={idx}
                      type="button"
                      onClick={() => setLightbox({ images: cat.menuImages, index: idx })}
                      className="group relative rounded-xl overflow-hidden shadow-2xl cursor-zoom-in block w-full"
                      style={{ border: '1px solid rgba(240,193,75,0.35)' }}
                      aria-label={`Phóng to ${cat.label} - trang ${idx + 1}`}
                    >
                      <img
                        src={imgUrl}
                        alt={`${cat.label} - trang ${idx + 1}`}
                        className="w-full h-auto object-contain transition-transform duration-500 group-hover:scale-[1.02]"
                        style={{ display: 'block', maxWidth: '100%' }}
                        loading="lazy"
                        decoding="async"
                        onLoad={e => e.currentTarget.classList.add('loaded')}
                      />
                      <span className="pointer-events-none absolute inset-0 flex items-center justify-center bg-black/0 group-hover:bg-black/25 transition-colors">
                        <span className="opacity-0 group-hover:opacity-100 transition-opacity rounded-full p-3" style={{ backgroundColor: GOLD, color: BROWN }}>
                          <ZoomIn size={22} />
                        </span>
                      </span>
                    </button>
                  ))}
                </div>
              ) : cat.subSections ? (
                <div className="space-y-10">
                  {cat.shotBottle && (
                    <div className="hidden sm:grid grid-cols-[1fr_auto_auto] gap-x-6 px-1 mb-1">
                      <span />
                      <span className="text-[#ffc952]/70 text-xs uppercase tracking-widest font-semibold text-right w-28">By Shot/40ml</span>
                      <span className="text-[#ffc952]/70 text-xs uppercase tracking-widest font-semibold text-right w-32">By Bottle</span>
                    </div>
                  )}
                  {cat.subSections.map(sec => (
                    <div key={sec.heading}>
                      <h5 className="font-typewriter font-bold text-[#ffc952] uppercase tracking-widest text-base mb-5 border-b border-[#ffc952]/30 pb-2">{sec.heading}</h5>
                      {cat.shotBottle ? (
                        <ul className="space-y-3">
                          {sec.dishes.map(d => (
                            <li key={d.name} className="border-b border-white/15 pb-3 min-w-0">
                              <div className="flex items-baseline gap-x-4">
                                <p className="font-typewriter font-semibold text-white text-base leading-snug break-words min-w-0 flex-1">{d.name}</p>
                                <p className="text-[#ffc952] font-semibold text-sm text-right shrink-0 w-28">{d.shotPrice}</p>
                                <p className="text-[#ffc952] font-semibold text-sm text-right shrink-0 w-32">{d.bottlePrice}</p>
                              </div>
                            </li>
                          ))}
                        </ul>
                      ) : (
                        <ul className="grid sm:grid-cols-2 gap-x-12 gap-y-5">
                          {sec.dishes.map(d => (
                            <li key={d.name} className="border-b border-white/15 pb-3 min-w-0">
                              <div className="flex justify-between items-baseline gap-x-4">
                                <p className="font-typewriter font-semibold text-white text-base leading-snug break-words min-w-0 flex-1">{d.name}</p>
                                {d.price && <p className="text-[#ffc952] font-semibold text-sm text-right whitespace-normal shrink-0 max-w-[45%]">{d.price}</p>}
                              </div>
                              <p className="text-white/60 text-sm mt-0.5 italic">{d.desc}</p>
                            </li>
                          ))}
                        </ul>
                      )}
                    </div>
                  ))}
                </div>
              ) : (
                <ul className="grid sm:grid-cols-2 gap-x-12 gap-y-6">
                  {cat.dishes.map(d => (
                    <li key={d.name} className="border-b border-white/15 pb-3 min-w-0">
                      <div className="flex justify-between items-baseline gap-x-4">
                        <p className="font-typewriter font-semibold text-white text-base leading-snug break-words min-w-0 flex-1">{d.name}</p>
                        {d.price && <p className="text-[#ffc952] font-semibold text-sm text-right whitespace-normal shrink-0 max-w-[45%]">{d.price}</p>}
                      </div>
                      <p className="text-white/60 text-sm mt-0.5 italic">{d.desc}</p>
                    </li>
                  ))}
                </ul>
              )}
              <p className="text-center text-white/50 text-xs mt-10">Giá chưa bao gồm VAT &nbsp;|&nbsp; Prices do not include VAT &nbsp;|&nbsp; 价格不含增值税</p>
            </div>
          </motion.div>
        </div>
      </div>
      {lightbox && (
        <MenuImageLightbox images={lightbox.images} initialIndex={lightbox.index} onClose={() => setLightbox(null)} />
      )}
    </section>
  );
}

function MenuFooter() {
  return (
    <footer className="text-neutral-300 pt-16 pb-8" style={{ backgroundColor: BROWN_DARK }}>
      <div className="max-w-[1140px] mx-auto px-5 grid md:grid-cols-3 gap-10">
        <div>
          <img src="https://horizons-cdn.hostinger.com/a2bb8e3c-639e-4083-bf32-32f3f7f0cebb/d5bf5030568dea1dbf0b975ffc8f98ce.png" alt="Ngon Thị Hoa – Tropical Garden" className="w-24 h-24 object-contain" style={{ filter: 'brightness(0) invert(1)' }} />
          <p className="text-sm leading-relaxed mt-3 text-neutral-400">
            Nhà hàng ẩm thực Việt Nam giữa khu vườn nhiệt đới, mang đến trải nghiệm hương vị và không gian đậm chất Đà Nẵng.
          </p>
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
          <h5 className="font-typewriter uppercase tracking-widest text-white mb-5">Liên hệ</h5>
          <ul className="space-y-3 text-sm text-neutral-400">
            <li className="flex gap-3"><MapPin size={18} className="shrink-0 mt-0.5" style={{ color: GOLD }} /> 100 Lê Quang Đạo, Ngũ Hành Sơn, Đà Nẵng</li>
            <li className="flex gap-3"><Phone size={18} className="shrink-0 mt-0.5" style={{ color: GOLD }} /> 02366 515 100 | 0967 220 100 | 098 481 88 80</li>
            <li className="flex gap-3"><span className="shrink-0 mt-0.5 text-sm" style={{ color: GOLD }}>@</span> info@ngonthihoarestaurant.com</li>
          </ul>
        </div>
        <div>
          <h5 className="font-typewriter uppercase tracking-widest text-white mb-5">Giờ mở cửa</h5>
          <ul className="space-y-2 text-sm text-neutral-400">
            <li className="flex justify-between border-b border-neutral-800 pb-2"><span>Hằng ngày / Daily</span><span>6:30 - 22:00</span></li>
          </ul>
        </div>
      </div>
      <div className="max-w-[1140px] mx-auto px-5 mt-10">
        <h5 className="font-typewriter uppercase tracking-widest text-white mb-4 text-sm">Vị trí nhà hàng</h5>
        <div className="rounded-xl overflow-hidden" style={{ border: '1px solid rgba(240,193,75,0.25)' }}>
          <iframe
            title="Ngon Thi Hoa Restaurant Location"
            width="100%"
            height="280"
            style={{ border: 0 }}
            loading="lazy"
            allowFullScreen
            src="https://maps.google.com/maps?q=100+Le+Quang+Dao+Ngu+Hanh+Son+Da+Nang+Vietnam&output=embed&hl=vi&z=16"
          />
        </div>
      </div>
      <div className="max-w-[1140px] mx-auto px-5 mt-8 flex justify-center">
        <img src="https://images.hostinger.com/bc1f6ef2-9cc1-4966-a917-06912b6c3c74.png" alt="" className="h-14 opacity-50" />
      </div>
      <div className="max-w-[1140px] mx-auto px-5 mt-6 pt-6 border-t text-center text-xs" style={{ borderColor: BROWN, color: '#ffc95299' }}>
        © {new Date().getFullYear()} Ngon Thị Hoa Restaurant · An Nam Phong Vị. All rights reserved.
      </div>
    </footer>
  );
}

export default function MenuPage() {
  const [params] = useSearchParams();
  const group = params.get('group') || 'sang';
  const [reservationOpen, setReservationOpen] = useState(false);

  useEffect(() => {
    window.scrollTo(0, 0);
  }, [group]);

  const groupLabel = MENU_GROUPS[group]?.label || 'Thực Đơn';

  return (
    <>
      <Helmet>
        <title>{groupLabel} - Ngon Thị Hoa Restaurant</title>
        <meta name="description" content={`Xem thực đơn ${groupLabel} của nhà hàng Ngon Thị Hoa tại Đà Nẵng.`} />
      </Helmet>
      <div className="bg-white">
        <MenuHeader onReservation={() => setReservationOpen(true)} />
        {/* hero banner */}
        <div className="relative h-56 md:h-72 flex items-center justify-center overflow-hidden" style={{ backgroundImage: `url(${IMG.heroGarden})`, backgroundSize: 'cover', backgroundPosition: 'center', backgroundAttachment: 'scroll' }}>
          <link rel="preload" as="image" href={IMG.heroGarden} />
          <div className="absolute inset-0 bg-black/55" />
          <div className="relative z-10 text-center">
            <span className="font-script text-3xl md:text-5xl block" style={{ color: GOLD }}>Khám phá</span>
            <h1 className="font-typewriter font-black text-white uppercase text-4xl md:text-6xl tracking-wide leading-none mt-1">Thực Đơn</h1>
          </div>
        </div>
        <MenuPanel initialGroup={group} />
        <MenuFooter />
      </div>
      {reservationOpen && <ReservationModal onClose={() => setReservationOpen(false)} />}
    </>
  );
}
