# Aneris — Deployment Guide
Stack: Frontend (Vercel) · Backend (Render) · Database (Supabase) · Storage (Cloudinary) · Domain (aneris.my.id)

---

## URUTAN DEPLOY

1. Setup Supabase (database)
2. Setup Cloudinary (storage)
3. Install package Cloudinary di Laravel
4. Update config & env
5. Deploy Backend ke Render
6. Deploy Frontend ke Vercel
7. Setup domain aneris.my.id

---

## STEP 1 — SUPABASE

1. Buka https://supabase.com → New project
2. Catat:
   - Host: `db.xxxx.supabase.co`
   - Port: `5432`
   - Database: `postgres`
   - User: `postgres`
   - Password: (yang kamu set saat buat project)
3. Di Supabase dashboard → Settings → Database → Connection string → pilih **URI**
   Formatnya: `postgresql://postgres:[PASSWORD]@db.[REF].supabase.co:5432/postgres`

---

## STEP 2 — CLOUDINARY

1. Buka https://cloudinary.com → daftar/login
2. Dashboard → copy:
   - Cloud Name
   - API Key
   - API Secret
3. Tidak perlu install SDK berat — kita pakai package `cloudinary-labs/cloudinary-laravel`

---

## STEP 3 — INSTALL CLOUDINARY PACKAGE

Jalankan di local:

```bash
composer require cloudinary-labs/cloudinary-laravel
php artisan vendor:publish --provider="CloudinaryLabs\CloudinaryLaravel\CloudinaryServiceProvider"
```

---

## STEP 4 — FILE YANG PERLU DIUPDATE

Semua file ada di folder ini. Salin ke project:

- `.env.example` → update di local & set di Render
- `config/filesystems.php` → tambah disk cloudinary
- `config/cloudinary.php` → auto generate dari publish
- `app/Services/CloudinaryService.php` → helper upload/delete
- Update semua controller yang pakai `Storage::` → pakai CloudinaryService

---

## STEP 5 — RENDER (Backend Laravel)

1. Push project ke GitHub
2. Buka https://render.com → New → Web Service
3. Connect repo GitHub kamu
4. Settings:
   - **Environment**: `PHP`  
     (Render tidak native PHP — pakai Docker)
   - **Dockerfile**: gunakan `Dockerfile` yang disediakan di folder ini
   - **Build Command**: (sudah ada di Dockerfile)
   - **Start Command**: (sudah ada di Dockerfile)
5. Environment Variables → tambahkan semua dari `.env.example` yang sudah diisi
6. Set **Root Directory** kosong (root project)

---

## STEP 6 — VERCEL (Frontend / Asset)

Laravel bukan SPA — Vercel dipakai untuk serve **hasil build Vite** (CSS/JS assets) saja,
sementara Laravel tetap jalan di Render.

1. Buka https://vercel.com → New Project → import repo GitHub
2. **Framework Preset**: `Other`
3. **Build Command**: `npm run build`
4. **Output Directory**: `public/build`
5. **Install Command**: `npm install`
6. Set environment variable: `VITE_APP_URL=https://aneris.my.id`
7. Setelah deploy, Vercel akan memberikan URL seperti `aneris-assets.vercel.app`
8. Update `ASSET_URL` di Render env ke URL Vercel tersebut

---

## STEP 7 — DOMAIN aneris.my.id

### A. Untuk Backend (Render)
1. Render dashboard → Settings → Custom Domains → Add `aneris.my.id`
2. Render akan kasih CNAME record
3. Buka registrar domain kamu → DNS → tambah CNAME:
   - Name: `@` atau `aneris`
   - Value: (dari Render)

### B. Untuk Assets (Vercel)
1. Vercel → Settings → Domains → Add `assets.aneris.my.id`
2. Tambah CNAME di DNS:
   - Name: `assets`
   - Value: `cname.vercel-dns.com`

---

## CATATAN PENTING

- `BROADCAST_CONNECTION=pusher` → tetap pakai Pusher (sudah setup)
- `SESSION_DRIVER=database` → pastikan tabel sessions ada di Supabase setelah migrate
- `QUEUE_CONNECTION=database` → jalankan queue worker di Render (lihat Dockerfile)
- File upload lama (local storage) perlu dimigrasikan manual ke Cloudinary
