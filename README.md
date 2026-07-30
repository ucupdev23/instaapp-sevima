# InstaApp 📸

Aplikasi berbagi foto sederhana mirip Instagram, dibuat dengan PHP Native dan MySQL.

## Fitur
- ✅ Register & Login (dengan session)
- ✅ Upload foto dengan caption
- ✅ Like & unlike post
- ✅ Komentar pada post
- ✅ Autentikasi pengguna (session-based)
- ✅ Hak akses — hanya pemilik yang bisa hapus post/komentar sendiri
- ✅ Halaman profil user
- ✅ Frontend dan Backend (API) terpisah

## Tech Stack
- **Backend**: PHP 8 (Native, tanpa framework)
- **Frontend**: HTML, CSS, JavaScript (Vanilla)
- **Database**: MySQL (RDBMS)
- **Server**: Apache (Laragon)

## Cara Menjalankan

### 1. Clone Repository
```bash
git clone https://github.com/ucupdev23/instaapp-sevima.git
```

### 2. Setup Database
- Buka phpMyAdmin (`http://localhost/phpmyadmin`)
- Import file `database/schema.sql`
- Atau jalankan query berikut di MySQL CLI:
```bash
mysql -u root < database/schema.sql
```

### 3. Konfigurasi Database
Edit file `api/config/database.php` jika perlu:
```php
$host = 'localhost';
$dbname = 'instaapp';
$username = 'root';
$password = '';  // Default Laragon/XAMPP
```

### 4. Jalankan
- Letakkan folder project di `htdocs` (XAMPP) atau `www` (Laragon)
- Buka `http://localhost/instaapp-sevima/`
- Register akun baru, lalu login dan mulai posting!

## Struktur Folder
```
instaapp-sevima/
├── api/                    ← Backend REST API
│   ├── config/             ← Konfigurasi database
│   ├── controllers/        ← Logic bisnis (Auth, Post, Like, Comment)
│   ├── middleware/          ← Autentikasi session
│   ├── models/             ← Database queries
│   └── index.php           ← API Router
├── public/                 ← Frontend
│   ├── assets/css/         ← Stylesheet
│   ├── assets/js/          ← JavaScript
│   ├── uploads/            ← Uploaded images
│   └── *.html              ← Halaman-halaman UI
├── database/
│   └── schema.sql          ← SQL schema untuk import
└── README.md
```

## API Endpoints

| Method | Endpoint | Deskripsi | Auth |
|--------|----------|-----------|------|
| POST | `/api/auth/register` | Daftar akun baru | ❌ |
| POST | `/api/auth/login` | Login | ❌ |
| POST | `/api/auth/logout` | Logout | ✅ |
| GET | `/api/auth/me` | Data user yang login | ✅ |
| GET | `/api/posts` | Semua post (feed) | ❌ |
| POST | `/api/posts` | Buat post baru | ✅ |
| DELETE | `/api/posts/{id}` | Hapus post (owner only) | ✅ |
| GET | `/api/posts/user/{id}` | Post milik user | ❌ |
| POST | `/api/posts/{id}/like` | Like/unlike post | ✅ |
| GET | `/api/posts/{id}/comments` | Komentar pada post | ❌ |
| POST | `/api/posts/{id}/comments` | Tambah komentar | ✅ |
| DELETE | `/api/comments/{id}` | Hapus komentar (owner only) | ✅ |
| GET | `/api/users/{id}` | Data profil user | ❌ |

## Screenshot

> Screenshot/demo video akan ditambahkan.
