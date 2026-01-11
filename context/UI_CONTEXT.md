# Tailwind UI Context – Warkop QR
Theme: Modern Minimal

## Tujuan
Membuat tampilan web yang:
- Bersih dan modern
- Konsisten di semua halaman
- Nyaman dipakai di HP (mobile-first)
- Cocok untuk sistem pemesanan & kasir

---

## Teknologi
- HTML statis
- Tailwind CSS (via CDN)
- PHP native (logic menyusul, tidak dibahas di sini)

---

## Prinsip Desain (WAJIB)
1. Simple > dekoratif
2. Konsisten > kreatif
3. Mudah dibaca > estetika
4. Mobile-first

---

## Tema Visual
- Style: Modern Minimal / POS
- Mood: Clean, profesional, netral
- Tidak menggunakan elemen dekoratif berlebihan

---

## Warna (LOCKED)
Gunakan warna berikut saja:

- Primary: indigo-600
- Primary hover: indigo-700
- Background: gray-100
- Card: white
- Text utama: gray-900
- Text secondary: gray-600
- Border: gray-200

Tidak menggunakan warna lain di luar daftar ini.

---

## Layout Global (WAJIB)
Semua halaman HARUS menggunakan struktur berikut:

```html
<div class="min-h-screen bg-gray-100">
  <header class="bg-white shadow px-4 py-3">
    <h1 class="text-lg font-semibold">Warkop QR</h1>
  </header>

  <main class="max-w-md mx-auto p-4 space-y-4">
    <!-- konten halaman -->
  </main>
</div>
