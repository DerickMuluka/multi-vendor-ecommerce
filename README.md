# Multi-Vendor E-Commerce

A full-stack, production-shaped marketplace built with **PHP, MySQL, vanilla JavaScript and modern CSS**, where administrators oversee the platform, vendors manage their own stores, and customers shop across all vendors from a single cart and checkout flow.

![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.x-4479A1?logo=mysql&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-ES6+-F7DF1E?logo=javascript&logoColor=black)
![CSS](https://img.shields.io/badge/CSS-3-1572B6?logo=css3&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green)

---

## 📖 Overview

**Multi-Vendor E-Commerce** is a complete marketplace application that demonstrates end-to-end full-stack mastery: schema design, secure authentication, role-based dashboards, image uploads, transactional order placement, and a responsive front end that works flawlessly from a 360 px phone to a 4K monitor.

The system serves **three distinct user roles** — Administrator, Vendor and Customer — through a **single, unified login page** that detects the role automatically and routes the user to the correct dashboard.

---

## ✨ Features

### Authentication & Roles
- **Unified login page** — one form for admin, vendor and customer
- **Automatic role detection** by email/username lookup order (admin → vendor → user)
- **Role-based redirects** to the correct dashboard
- **Unified registration** with a Customer / Vendor tab switcher
- **Vendor approval workflow** — new vendors start as `pending` and require admin approval
- **Account status gating** — suspended vendors and blocked users are rejected at login
- **Passwords hashed** with PHP's `PASSWORD_DEFAULT` (bcrypt)

### Admin Panel
- KPI dashboard — vendors, users, products, orders, revenue
- Approve, suspend or delete vendors
- Block, unblock or delete users
- Browse and delete any product
- Create and delete categories with product counts
- View all orders and change status

### Vendor Panel
- Store-scoped dashboard with product, order and revenue KPIs
- Low-stock alerts
- Full product CRUD with image upload and replace
- Order management for own products
- Profile and password management

### Customer Panel
- Personal dashboard — total orders, active orders, total spent
- Shopping cart with quantity merge and live totals
- Checkout with address, phone, notes and Cash on Delivery
- Transactional order placement with stock deduction
- Order history with line items
- Profile and password management

### Catalog
- Product listing with category, search and sort filters
- Pagination (12 per page)
- Product detail page with related items
- Public vendor directory

### UI / UX
- Fully responsive (1024 / 768 / 480 breakpoints)
- Hero with background image and animated zoom
- Glassmorphism header with dropdown menus
- Trust strip, categories grid and seller CTA
- Flash messages that auto-dismiss
- Skeleton-friendly empty states
- Font Awesome 6 icons and Outfit typeface

### Security
- PDO prepared statements everywhere
- Output escaping through `sanitize()`
- Upload MIME and size validation
- Session-based authentication
- `.htaccess` blocks directory listing and protects `includes/`

---

## 🧱 Tech Stack

| Layer      | Technology                        |
|------------|-----------------------------------|
| Backend    | PHP 8+ (PDO, sessions)            |
| Database   | MySQL / MariaDB                   |
| Frontend   | HTML5, CSS3, vanilla JavaScript   |
| Icons      | Font Awesome 6 (CDN)              |
| Typeface   | Google Fonts — Outfit             |
| Local env  | XAMPP (Apache + MySQL)            |
| Hosting    | InfinityFree                      |

---

## 📁 Project Structure

```
multi-vendor-ecommerce/
├── admin/
│   ├── dashboard.php
│   ├── vendors.php
│   ├── products.php
│   ├── orders.php
│   ├── categories.php
│   └── users.php
├── vendor/
│   ├── dashboard.php
│   ├── products.php
│   ├── add-product.php
│   ├── edit-product.php
│   ├── orders.php
│   └── profile.php
├── user/
│   ├── dashboard.php
│   ├── cart.php
│   ├── checkout.php
│   ├── orders.php
│   └── profile.php
├── includes/
│   ├── config.php
│   ├── db.php
│   ├── functions.php
│   ├── auth.php
│   ├── header.php
│   └── footer.php
├── assets/
│   ├── css/style.css
│   └── js/main.js
├── uploads/products/
├── database.sql
├── setup.php                ← one-time password seeder (delete after use)
├── index.php                ← landing page
├── products.php             ← catalog
├── product.php              ← product detail
├── vendors.php              ← vendor directory
├── login.php                ← unified login
├── register.php             ← unified registration
├── logout.php               ← unified logout
├── .htaccess
└── README.md
```

---

## 🚀 Local Setup (XAMPP)

### 1. Prerequisites
- XAMPP with PHP 8+ and MySQL
- Any modern browser

### 2. Clone the repository
```bash
cd C:\xampp\htdocs
git clone https://github.com/<your-username>/multi-vendor-ecommerce.git
```

### 3. Start services
Open the **XAMPP Control Panel** and start **Apache** and **MySQL**.

### 4. Create the database
- Visit `http://localhost/phpmyadmin`
- Click **Import** → choose `database.sql` → **Go**

This creates the `multi_vendor_ecommerce` database and all tables.

### 5. Seed the demo passwords
- Visit `http://localhost/multi-vendor-ecommerce/setup.php`
- Confirm the success page
- **Delete `setup.php` immediately** — it is a security risk if left deployed

### 6. Open the app
```
http://localhost/multi-vendor-ecommerce
```

---

## 🔑 Demo Credentials

| Role     | Login                  | Password    |
|----------|------------------------|-------------|
| Admin    | `admin`                | `admin123`  |
| Vendor   | `vendor@example.com`   | `vendor123` |
| Customer | `user@example.com`     | `user123`   |

> ⚠️ **Change these immediately** after your first login in any non-local environment.

---

## 🌐 Deployment to InfinityFree

1. **Create an account** at [infinityfree.net](https://infinityfree.net) and add a hosting account.
2. **Note your MySQL credentials** from the control panel:
   - Host (e.g. `sqlXXX.infinityfree.com`)
   - Username (e.g. `if0_XXXXXXXX`)
   - Password
   - Database name (e.g. `if0_XXXXXXXX_multivendor`)
3. **Update `includes/config.php`**:
   ```php
   define('DB_HOST', 'sqlXXX.infinityfree.com');
   define('DB_USER', 'if0_XXXXXXXX');
   define('DB_PASS', 'your_password');
   define('DB_NAME', 'if0_XXXXXXXX_multivendor');
   define('SITE_URL', 'https://yoursubdomain.infinityfreeapp.com');
   ```
4. **Upload all files** to `htdocs/` via File Manager or FTP.
5. **Import `database.sql`** using the InfinityFree phpMyAdmin.
6. **Set permissions** on `uploads/products/` to `755`.
7. **Disable error display** by ensuring `display_errors` is `0` in `config.php`.
8. **Change all default passwords** after the first login.

> ⚠️ **Never leave `setup.php` on the server.** Run it locally, then delete it before uploading.

---

## 🗺️ How Authentication Works

The unified login flow is intentionally simple:

```
POST /login.php
      │
      ├──► Lookup in `admins`  (by username OR email)  ──► ✓ → /admin/dashboard.php
      │
      ├──► Lookup in `vendors` (by email)              ──► ✓ → /vendor/dashboard.php
      │
      └──► Lookup in `users`   (by email)              ──► ✓ → /user/dashboard.php
```

Each successful match sets a role-scoped session key (`admin_id`, `vendor_id` or `user_id`). Helper functions such as `isAdminLoggedIn()`, `requireVendor()` and `requireUser()` guard every protected page.

---

## 🔄 Order Lifecycle

```
pending ──► processing ──► shipped ──► delivered
   │            │             │
   └────────────┴─────────────┴──► cancelled
```

- Customer places the order → status `pending`
- Vendor advances to `processing` → `shipped`
- Delivery completes → `delivered`
- Any party can cancel before shipment → `cancelled`

Stock is deducted atomically inside a PDO transaction during checkout, and the cart is cleared only after the transaction commits.

---

## 🎨 Design Language

| Token         | Value                                              |
|---------------|----------------------------------------------------|
| Primary       | `#6366f1` (indigo) → `#4f46e5` gradient            |
| Accent        | `#f59e0b` (amber) for hero and CTA highlights      |
| Success       | `#10b981`                                          |
| Danger        | `#ef4444`                                          |
| Neutrals      | `#0f172a` → `#f8fafc` scale                        |
| Radius        | 6 / 12 / 16 / 24 px scale                          |
| Shadow        | 4 levels (`sm`, base, `md`, `lg`) with rgba tint   |
| Typeface      | Outfit (300 → 800 weights)                         |

All layouts use CSS Grid and Flexbox with fluid typography via `clamp()`.

---

## 🧪 Testing the Flow

A quick end-to-end smoke test:

1. **Register** a new vendor at `/register.php` (Vendor tab)
2. **Login** as `admin` → approve the vendor in **Vendors**
3. **Login** as the new vendor → **Add Product**
4. **Register** a new customer at `/register.php` (Customer tab)
5. **Login** as the customer → add product to **Cart** → **Checkout**
6. **Login** as the vendor → advance the order status
7. **Login** as admin → confirm the order and revenue in the dashboard

---

## 🐛 Troubleshooting

| Symptom                                       | Cause                                              | Fix |
|-----------------------------------------------|----------------------------------------------------|-----|
| `Call to undefined function setFlashMessage()`| `logout.php` required `config.php` only            | Require `includes/functions.php` instead |
| Login fails with valid credentials            | Seed hashes in `database.sql` are placeholders     | Run `setup.php` once, then delete it |
| `Database connection failed`                  | Wrong credentials in `config.php`                  | Verify DB_HOST, DB_USER, DB_PASS, DB_NAME |
| Images not showing                            | `uploads/products/` missing or not writable        | Create the folder, `chmod 755` |
| Blank page after login                        | Session started twice or output before headers     | Ensure `config.php` is the first include |
| 500 error on InfinityFree                     | `display_errors` on or wrong PHP version           | Set `display_errors=0`, use PHP 8+ |

---

## 🛣️ Roadmap

- [ ] Online payment integration (Stripe / M-Pesa)
- [ ] Product reviews and ratings
- [ ] Wishlist
- [ ] Email notifications on order status change
- [ ] Vendor payout ledger
- [ ] Multi-image product galleries
- [ ] Admin audit log
- [ ] REST API for a mobile client

---

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch: `git checkout -b feat/your-feature`
3. Commit using Conventional Commits: `git commit -m "feat(scope): description"`
4. Push: `git push origin feat/your-feature`
5. Open a Pull Request

---

## 📝 License

Released under the **MIT License**. See `LICENSE` for details.

---

## 👤 Author

**Your Name**
- GitHub: [@your-username](https://github.com/your-username)
- Email: you@example.com

---

## ⭐ Acknowledgements

- [Font Awesome](https://fontawesome.com/) for icons
- [Google Fonts](https://fonts.google.com/) for the Outfit typeface
- [Unsplash](https://unsplash.com/) for hero and CTA imagery
- The PHP and MySQL communities for exceptional documentation

---

<p align="center"><strong>Built with care — one commit at a time.</strong></p>