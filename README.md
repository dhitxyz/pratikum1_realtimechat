# Aplikasi Chat Real-Time dengan Laravel + Reverb

## Fitur

### ✅ Fitur Utama (Required)
1. **Pesan Real-Time**: Dua user dapat saling kirim pesan secara real-time tanpa refresh halaman
2. **Penyimpanan Database**: Semua pesan disimpan ke database
3. **Multiple Users**: Mendukung multiple users dengan login sederhana

### ✨ Bonus Features
1. **Presence Channel**: Menampilkan daftar user online menggunakan presence channel
2. **UI Modern**: Interface yang modern dan responsive
3. **Timestamp**: Setiap pesan menampilkan waktu pengiriman
4. **Auto-scroll**: Chat container otomatis scroll ke pesan terbaru

## Teknologi yang Digunakan

- **Laravel 11**: PHP Framework
- **Reverb**: WebSocket Server (Laravel Reverb)
- **Laravel Echo**: Broadcasting library
- **Pusher/Reverb**: Real-time communication
- **SQLite/MySQL**: Database
- **Vite**: Build tool
- **Blade**: Template engine

## Setup & Installation

### 1. Install Dependencies
```bash
composer install
npm install
```

### 2. Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```

### 3. Database Setup
```bash
php artisan migrate
php artisan db:seed
```

Database sudah berisi 2 user:
- **Andi** (andi@example.com)
- **Budi** (budi@example.com)

### 4. Configure Broadcasting
Pastikan `BROADCAST_CONNECTION=reverb` di .env

### 5. Start Development Server

**Terminal 1: Laravel Dev Server**
```bash
php artisan serve
```

**Terminal 2: Reverb WebSocket Server**
```bash
php artisan reverb:start
```

**Terminal 3: Vite Build (optional, untuk auto-reload assets)**
```bash
npm run dev
```

### 6. Akses Aplikasi
Buka `http://localhost:8000` di browser

## Cara Menggunakan

### Login dan Chat
1. Pilih user (Andi atau Budi) dari dropdown
2. Klik tombol "Login"
3. Interface chat akan muncul
4. Ketik pesan dan klik "Kirim" atau tekan Enter
5. Pesan akan langsung terlihat real-time untuk semua user tanpa refresh

### Presence Channel (Bonus)
- Bagian "User Online" menampilkan daftar user yang sedang aktif
- User akan otomatis ditambahkan ketika join channel
- User akan otomatis dihapus ketika leave channel

### Logout
- Klik tombol "Logout" di header chat
- Akan kembali ke halaman login

## Struktur File

### Backend
```
app/
├── Events/
│   └── ChatMessageSent.php          # Event untuk broadcast message
├── Http/Controllers/
│   └── ChatController.php           # Controller untuk chat logic
├── Models/
│   ├── User.php                     # User model dengan relasi messages
│   └── Message.php                  # Message model dengan relasi user
routes/
├── web.php                          # Web routes
└── channels.php                     # Broadcasting channels

database/
├── migrations/
│   └── create_messages_table.php    # Migration untuk table messages
└── seeders/
    └── DatabaseSeeder.php           # Seeder untuk 2 user
```

### Frontend
```
resources/
├── views/
│   └── chat.blade.php              # Chat UI template dengan JavaScript
├── js/
│   ├── app.js                      # Main JavaScript (minimal)
│   ├── bootstrap.js                # Bootstrap script
│   └── echo.js                     # Echo configuration
└── css/
    └── app.css                     # CSS styling
```

## API Endpoints

### GET `/`
- Halaman utama dengan login form dan chat interface

### POST `/login`
- Login dengan user_id
- Redirect ke chat page

### POST `/logout`
- Logout user
- Redirect ke halaman login

### GET `/messages`
- Get semua message yang sudah ada
- Response: JSON array of messages

### POST `/send-message`
- Send new message (require auth)
- Payload: `{ "body": "message content" }`
- Response: JSON message object
- Trigger broadcast event `ChatMessageSent`

## Broadcasting Channels

### Public Channel: `chat-channel`
- Channel untuk broadcast pesan ke semua user
- Event: `message.sent`

### Presence Channel: `chat-presence`
- Channel untuk tracking user online
- Mengirim user data: `{ id, name }`

## Database Schema

### Users Table
```
id          - Primary Key
name        - User name
email       - User email
password    - Hashed password
created_at  - Timestamp
updated_at  - Timestamp
```

### Messages Table
```
id          - Primary Key
user_id     - Foreign Key to users
body        - Message content
created_at  - Timestamp
updated_at  - Timestamp
```

## Troubleshooting

### Reverb tidak connect
- Pastikan Reverb server sudah running: `php artisan reverb:start`
- Check .env variable untuk REVERB_HOST, REVERB_PORT, REVERB_APP_KEY
- Clear cache: `php artisan cache:clear`

### Message tidak ter-broadcast
- Pastikan BROADCAST_CONNECTION di .env adalah `reverb`
- Check laravel logs: `storage/logs/laravel.log`
- Pastikan user sudah login (auth()->check())

### WebSocket tidak connect
- Check browser console untuk error
- Verify Reverb server running di port yang benar
- Check firewall settings

## Performance Notes

- Messages di-load saat page load dan via real-time channel
- Presence channel tracked automatically saat join/leave
- Database query optimized dengan eager loading (with('user'))

## Future Improvements

1. Typing indicator
2. Message editing & deletion
3. Group chat
4. Message search
5. Notification sounds
6. Message reactions/emoji
7. File upload
8. Encryption untuk privacy

---

**Created for:** Pemrograman Berbasis Web - Semester 4  
**Date:** May 8, 2026
