


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

6. **Konfigurasi Shiled untuk Super-admin:**

```bash
php artisan shield:super-admin
```

7. **Beri Permission Access untuk Super-Admin:**

* Pergi kehalaman Admin
* lakukan Edit pada role Super-admin
* kamu pilih Select All untuk memberi permission access keseluruh halaman admin
* kemudian simpan

8. **Aktifkan Shield policies admin:**

```bash
php artisan shield:generate --panel=admin --all
```

9. **Akses Dashboard Filament:**

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



## 📋 Log Aktivitas Admin

Aplikasi ini telah dilengkapi dengan fitur **Log Aktivitas** menggunakan plugin resmi Filament: [`rmsramos/filament-activitylog`](https://filamentphp.com/plugins/rmsramos-activitylog). Fitur ini memungkinkan admin untuk memantau semua perubahan data penting yang terjadi melalui dashboard.

### ✨ Aktivitas yang Dicatat

* Login dan logout admin
* Aksi **Create**, **Update**, dan **Delete** pada:

  * Produk
  * Kategori produk
  * Varian produk
  * Pemesanan
* Aktivitas umum yang dilakukan oleh admin dalam panel Filament

### 🗂️ Struktur Tabel `activity_log`

Plugin ini menggunakan tabel `activity_log` dari package `spatie/laravel-activitylog`. Beberapa kolom penting:

| Kolom         | Tipe      | Keterangan                             |
| ------------- | --------- | -------------------------------------- |
| id            | bigint    | Primary Key                            |
| log\_name     | string    | Nama log (opsional)                    |
| description   | string    | Deskripsi aktivitas                    |
| subject\_type | string    | Nama model terkait                     |
| subject\_id   | bigint    | ID dari model terkait                  |
| causer\_type  | string    | Tipe user yang melakukan aksi          |
| causer\_id    | bigint    | ID user yang melakukan aksi            |
| properties    | JSON      | Data tambahan (sebelum/sesudah update) |
| created\_at   | timestamp | Tanggal dan waktu aktivitas            |

---

## 📋 Log Aktivitas Admin
* Jika ingin menambahkan Log Activity bisa dilakukan pada bagian models
```bash
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Product extends Model
{
    use HasFactory, LogsActivity;
    protected $fillable = ['name'];


    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name'])
            ->setDescriptionForEvent(fn(string $eventName) => "Product has been {$eventName}");
    }
}

```
---
## 📍 Akses Log di Dashboard

Log aktivitas dapat dilihat langsung melalui menu **"Activity Logs"** di sidebar admin:

```
Admin Dashboard → Activity Logs
```

### Fitur Tampilan:

* Pencarian aktivitas
* Filter berdasarkan:

  * Tipe aksi (`created`, `updated`, `deleted`)
  * Nama model
  * Admin (causer)
* Tampilan detail per aktivitas

---

## ⚙️ Instalasi (Jika Belum Terpasang)

Jika kamu ingin menambahkan fitur ini ke proyek lain, berikut langkahnya:

```bash
composer require rmsramos/filament-activitylog
php artisan migrate
```


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
