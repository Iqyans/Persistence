# `Dkid-Peristance.php`

Jika masih terjadi bug silahkan self-fix karena ini open  source 

**Sifat file:** Backdoor PHP (5.639 baris, Â±400 KB), dikendalikan lewat bot Telegram, bisa menyebar antar server, dengan 25 layer persistence yang saling memulihkan.

---

## 1. Proses Hidup Terus
- Daemon dan watchdog PHP tersembunyi (`.upd_*.cache`, `.tmp_*.cache`) dijalankan dengan `setsid`/`nohup`.
- Keduanya saling menghidupkan ulang, dengan file PID/lock/heartbeat bernama samar.

## 2. Penjadwalan dan Service
- **Cron:** `* * * * *` dan `@reboot`
- **systemd:** Service `php-session-<hash>` (user dan system) plus timer (3 menit setelah boot, lalu tiap 20 menit)
- **Lainnya:** `loginctl enable-linger`, `at` job, dan XDG autostart

## 3. Injeksi File Startup
- Shell startup: `.bashrc`, `.bash_profile`, `.bash_login`, `.profile`, `.zshrc`, `.zprofile`, `.bash_logout`
- SSH: `~/.ssh/rc` dan `~/.ssh/config` (`LocalCommand`)
- Lingkungan grafis & editor: `.xsessionrc`, `.xprofile`, `.lessfilter` (`LESSOPEN`), `.vimrc`, `.emacs`, `.tmux.conf`
- **Trap `DEBUG`:** Backdoor berjalan di setiap perintah yang diketik oleh pengguna.

## 4. Ghost Trap (3 Tingkat)
- Ghost `.gsh_*.sh`, blok Shadow di `.bashrc`, dan blok Base di `.bash_profile`/`.profile` saling menyalin ulang.
- `BASH_ENV` disuntikkan melalui `environment.d/99-*.conf` dan `systemd user.conf`.

## 5. Tooling Developer
- Hook pada file proyek/konfigurasi: `package.json`, `composer.json`, `.npmrc`
- **Git hooks:** `post-commit`, `post-merge`, `post-checkout`, `post-rewrite`
- **Python:** `sitecustomize.py`

## 6. Penggandaan dan Self-Heal
- Wrapper `.<md5>.bin`, backup terenkripsi AES-256 (`ENC1:`/`ENC2:`)
- Replika tersimpan di Â±10 folder tersembunyi (termasuk `~/.local/share/Trash`) beserta watcher `php-sess-*.cache`
- Pemeriksaan integritas (integrity check) dengan pemalsuan waktu modifikasi file (`mtime`)
- Fitur *self-heal* dipanggil otomatis pada setiap *request* web.

## 7. Modul Lanjutan
- **Ghost (Migrasi):** Menyalin diri ke lokasi acak dengan struktur kode berbeda, memalsukan `mtime`, lalu **menghapus file asli**. Interval eksekusi 30 menit hingga 4 jam.
- **Morph:** Menulis ulang kode watcher setiap 20 menit hingga 8 jam agar nilai hash file selalu berubah.
- **Phantom:** Mendeteksi tanda-tanda pemeriksaan (forensik, aktivitas jaringan, jam akses) lalu masuk ke mode senyap (*silent*) atau dorman.
- **Witness:** Menghitung skor probabilitas "akan ditemukan". Mengambil tindakan responsif: `watch`, `fortify`, `evacuate`, dan `last stand`.
  - **Evacuate:** Eksfiltrasi `bash_history`, `crontab`, **SSH private key**, serta file `.env/.conf/.json/.yml` ke `/tmp/.whist_*.enc`, ditambah penyiapan dead drop `/tmp/.wdd_*.enc`.
  - **Last Stand:** Memicu *ghost spawn* dan mengunci status integritas selama 24 jam.
- **Phantom Exfil:** Mengumpulkan isi `/etc/passwd`, *history*, SSH key, dan file konfigurasi ke format `.phe_*.json`.
- **Anima:** Lapisan simulasi "mood/persona" yang memengaruhi interval *morph* dan tingkat kewaspadaan.

## 8. Penyebaran Antar Server
- **Fleet (`?wn_fleet=`):** Eksekusi perintah `exec`, `install`, `deploy_module`, serta fitur *join* menggunakan HMAC.
- **Swarm & Mesh P2P:** Mengelola daftar *peer*, kunci bersama (*shared key*), dan mekanisme `mesh_distribute`.
- Perintah Telegram `fleet deploy` untuk mengirimkan *shell* ke seluruh *peer*.

## 9. Penyamaran dan Penghapusan Bukti
- Nama fungsi diobfuskasi (`\x..`), menyediakan *decoy* untuk mengelabui scanner, serta mendukung *hidden mode*.
- Otentikasi berbasis Cookie `PHPSESSID_xxxx`, OTP, dan sistem pemblokiran IP.
- **Dead-man Switch:** Fitur otomatisasi status (`warn` / `restore` / `wipe` menggunakan perintah `shred`).
