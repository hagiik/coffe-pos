


# 🍹 Aplikasi Kasir Minuman – Laravel + Filament

Sistem POS (Point of Sale) sederhana untuk kasir minuman, dibangun menggunakan Laravel 12 dan Filament 3. Aplikasi ini mendukung pengelolaan produk dengan varian (ukuran dan suhu), pemesanan multi-item, dan sistem pelacakan pembayaran.

---

## 🚀 Fitur Utama

- CRUD Produk:
  - Nama dan gambar produk (multi upload)
  - Kategori produk
  - Varian produk (ukuran dan suhu)
- Manajemen Kategori Produk
- Pemesanan multi-produk dengan varian
- Status pemesanan dan metode pembayaran
- Tipe pemesanan (ditempat atau pulang)
- Dashboard admin menggunakan **Filament**
- Tampilan detail pesanan dalam bentuk tabel interaktif

---

## 🧱 Struktur Database

### 🛒 `products`
| Kolom         | Tipe        | Keterangan               |
|---------------|-------------|--------------------------|
| id            | bigint      | Primary Key              |
| name          | string      | Nama produk              |
| slug          | string      | Slug unik                |
| images        | JSON        | Array gambar produk      |
| category_id   | foreign key | Relasi ke kategori       |
| timestamps    |             |                          |

### 🔄 `product_variants`
| Kolom        | Tipe   | Keterangan                       |
|--------------|--------|----------------------------------|
| id           | bigint | Primary Key                      |
| product_id   | FK     | Relasi ke tabel `products`       |
| size         | enum   | Kecil / Sedang / Besar           |
| temperature  | enum   | Dingin / Hangat                  |
| price        | decimal| Harga per varian                 |
| timestamps   |        |                                  |

### 📦 `orders`
| Kolom          | Tipe    | Keterangan                                 |
|----------------|---------|--------------------------------------------|
| id             | bigint  | Primary Key                                |
| customer_name  | string  | Nama pelanggan (opsional)                  |
| order_type     | enum    | `ditempat` / `pulang`                      |
| payment_method | enum    | `Cash` / `Qris`                            |
| status         | enum    | `Menunggu`, `Diproses`, `Selesai`         |
| pembayaran     | enum    | `Menunggu`, `Sudah Dibayar`               |
| total_price    | decimal | Total harga seluruh pesanan               |
| timestamps     |         |                                            |

### 🧾 `order_items`
| Kolom            | Tipe    | Keterangan                                |
|------------------|---------|-------------------------------------------|
| id               | bigint  | Primary Key                               |
| order_id         | FK      | Relasi ke `orders`                        |
| product_variant_id | FK    | Relasi ke `product_variants`             |
| quantity         | integer | Jumlah produk                             |
| unit_price       | decimal | Harga satuan saat dipesan                |
| subtotal         | decimal | unit_price × quantity                     |
| timestamps       |         |                                           |
---


## ⚙️ Instalasi

1. **Clone repo:**
```bash
git clone https://github.com/hagiik/coffe-pos.git
cd coffe-pos
````

2. **Install dependensi:**

```bash
composer install
npm install && npm run build
```

3. **Setup `.env` dan database:**

```bash
cp .env.example .env
php artisan key:generate
```

4. **Migrasi dan seed data:**

```bash
php artisan migrate --seed
```

5. **Buat admin Filament:**

```bash
php artisan make:filament-user
```

6. **Akses Dashboard Filament:**

```
http://localhost:8000/admin
```

---


## 📦 Teknologi

* Laravel 12
* Filament 3
* Livewire
* TailwindCSS
* MySQL
* PHP 8+

---

## 📝 Catatan

* Gambar produk disimpan di direktori `public/storage/product-images`
* Tidak tersedia fitur pembuatan pesanan di dashboard (hanya view)
* Pemesanan dilakukan melalui kasir langsung (POS)

---

## 👨‍💻 Kontribusi

Pull request sangat diterima!
Silakan fork repo ini dan ajukan PR jika ingin menambah fitur atau memperbaiki bug.

---

## 📄 Lisensi

Proyek ini menggunakan lisensi [MIT](LICENSE).

---
