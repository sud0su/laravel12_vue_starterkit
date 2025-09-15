# Panduan: GenerateResourceCommand & MakeModelWithPermission (Langkah demi langkah)

Dokumen ini menjelaskan cara **menggunakan** dua command artisan kustom yang telah dibuat:

* `resource:generate {name}` — *GenerateResourceCommand* (membuat controller resource, policy, permission, route, dan CRUD views).
* `make:model-with-permission {name} [--migrate]` — *MakeModelWithPermission* (membuat model + migration opsional dan generate permission; mengisi `$fillable` dari struktur tabel bila tabel sudah ada).

---

# 1) `resource:generate {name}` — Ringkasan

**Apa yang dilakukan:**

* Membuat Controller resource (`php artisan make:controller {Name}Controller --resource`).
* Membuat Policy (`php artisan make:policy {Name}Policy --model={Name}`).
* Membuat daftar permission untuk model (memakai Spatie Permission).
* Menambahkan route resource ke `routes/web.php` dengan menyisipkan:

  ```php
  Route::resource('/{lowerPluralName}', App\Http\Controllers\{StudlyName}Controller::class);
  ```

  (disisipkan di bawah marker `// {{GEMINI_RESOURCE_ROUTES}}`)
* Membuat folder Vue/Inertia views: `resources/js/pages/{StudlyName}/Index.vue`, `Create.vue`, `Edit.vue` (stub dasar).

**Contoh pemakaian:**

```bash
php artisan resource:generate Post
```

**Lokasi file yang dihasilkan (default):**

* Controller: `app/Http/Controllers/PostController.php`
* Policy: `app/Policies/PostPolicy.php`
* Route: disisipkan di `routes/web.php` (di bawah marker)
* Views: `resources/js/pages/Post/Index.vue`, `Create.vue`, `Edit.vue`

**Daftar permission yang dibuat** (contoh untuk model `post`):

* view posts
* create posts
* edit posts
* delete posts
* approve posts
* publish posts
* archive posts
* restore posts
* export posts
* import posts
* manage posts
* assign posts

> NOTE: Permission dibuat dengan `Permission::firstOrCreate(...)` dan `guard_name` default `web`.

## Tips & Catatan

* Jika marker `// {{RAZINAL_RESOURCE_ROUTES}}` tidak ditemukan, command akan memberi peringatan. Tambahkan marker tersebut di `routes/web.php` untuk auto-insert.
* Setelah route ditambahkan, jalankan `php artisan route:list` jika ingin memverifikasi.
* Jika menggunakan Spatie, reset cache permission bila perlu:

  ```bash
  php artisan permission:cache-reset
  # atau di kode: app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
  ```
* Untuk menyesuaikan daftar permission, edit method `createPermissionsForModel()` di command atau gunakan pendekatan config (lihat bagian Best practices).

---

# 2) `make:model-with-permission {name} [--migrate]` — Ringkasan

**Tujuan:**

* Membuat model (opsional pembuatan migration) dan langsung membuat permission untuk model tersebut.
* Jika tabel sudah ada di database, command akan membaca daftar kolom (`Schema::getColumnListing`) dan \*\*meng-inject properti \*\***`$fillable`** ke file model otomatis (mengabaikan `id`, `created_at`, `updated_at`, `deleted_at`).

**Contoh pemakaian (workflow dua langkah):**

1. Buat model + migration file:

   ```bash
   php artisan make:model-with-permission Post --migrate
   ```

   -> akan membuat file `app/Models/Post.php` dan migration `create_posts_table.php`.

2. Jalankan migration (agar tabel ada di DB):

   ```bash
   php artisan migrate
   ```

3. Jalankan command lagi (tanpa `--migrate`) untuk menambahkan `$fillable` berdasarkan struktur tabel:

   ```bash
   php artisan make:model-with-permission Post
   ```

   -> command membaca struktur tabel `posts` dan menyisipkan array `$fillable` otomatis di dalam `app/Models/Post.php`.

**Behavior penting:**

* Jika tabel belum ada (belum di-migrate), command akan memperingatkan dan tidak menambahkan fillable.
* Permission yang di-generate mengikuti pola (contoh untuk `posts`): `view posts`, `create posts`, `edit posts`, `delete posts`, `approve posts`, `publish posts`, dll.

**File yang di-update/di-generate:**

* Model: `app/Models/Post.php` (fillable ditambahkan bila tabel ditemukan).
* Migration: `database/migrations/create_posts_table.php` (jika `--migrate` dipakai).
* Permissions: entri di tabel `permissions` (Spatie).

## Contoh output `$fillable` yang tersisip:

```php
protected $fillable = [
    'title',
    'content',
    'user_id',
    // ... kolom lain yang bukan id/timestamps
];
```

---

# Contoh Workflow Lengkap (Rekomendasi)

**Skenario A (bikin model + resource lengkap):**

1. `php artisan make:model-with-permission Post --migrate`
2. Edit migration `create_posts_table.php` untuk menambahkan kolom yang dibutuhkan (title, body, user\_id, dll.)
3. `php artisan migrate`
4. `php artisan make:model-with-permission Post` (untuk inject `$fillable`)
5. `php artisan resource:generate Post` (membuat Controller, Policy, Permissions tambahan, route, dan views)
6. `php artisan permission:cache-reset` (opsional)

**Skenario B (sudah punya tabel, langsung bikin resource):**

1. Pastikan tabel sudah ada di DB.
2. `php artisan make:model-with-permission Post` (akan langsung inject `$fillable` karena tabel ada)
3. `php artisan resource:generate Post`

---
