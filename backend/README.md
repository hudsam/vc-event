# Maxy Event Admin Portal (Vite React)

Sistem admin panel untuk pengelolaan Event platform berbasis React, Vite, dan Tailwind CSS.

## Fitur Utama

- **Dashboard Admin**: Ringkasan total event dan daftar event terbaru.
- **Manajemen Event (CRUD)**:
  - Membuat, membaca, memperbarui, dan menghapus event.
  - Pengelolaan status event (*Draft*, *Published*, *Archived*).
  - Integrasi thumbnail & banner menggunakan gambar dari Unsplash.

## Persyaratan Sistem

- Node.js >= 18
- npm atau yarn

## Panduan Instalasi & Pengembangan

1. **Instalasi Dependency**:
   ```bash
   npm install
   ```

2. **Jalankan Server Development**:
   ```bash
   npm run dev
   ```
   Aplikasi akan berjalan di `http://localhost:3000`.

3. **Build untuk Production**:
   ```bash
   npm run build
   ```

## Struktur Direktori Utama

- `src/` - Seluruh kode React (komponen, halaman, layanan API).
- `public/` - Aset statis seperti logo, favicon, dll.
- `index.html` - Entry point HTML.
- `vite.config.js` - Konfigurasi build Vite.
- `vercel.json` - Konfigurasi deployment Vercel.
