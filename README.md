# Technical Assessment – Laravel + Quasar + Docker

This project is a full-stack application built using:

- **Laravel 12 (PHP 8.4)** – Backend (API, JWT auth, PDF generator)
- **Quasar (Vue 3)** – Frontend SPA
- **MySQL 8** – Database
- **Docker & Docker Compose** – Fully containerized environment
- **Dompdf** – PDF generation
- **Simple QrCode** – QR code rendering
- **MySQL triggers** – Automatic audit logging

---

# 🚀 Features

- Registration & Login (JWT)
- Protected API endpoints
- PDF generator with embedded QR code
- Audit log using DB triggers
- Automatic MySQL user tracking via session variable `@user_id`
- Clean configuration (all constants in `.env` → `config/technical.php`)
- SPA frontend (Quasar)
- Fully dockerized — runs anywhere

---

# 🐳 1. Requirements

- Docker 25+
- Docker Compose v2+
- Node.js 18+
- npm 9+

No PHP / MySQL required — they run in Docker.

---

# 🐳 2. Project Setup (from zero)

Clone the repository:

```bash
git clone https://github.com/<your-repo>/technical-assessment.git
cd technical-assessment
