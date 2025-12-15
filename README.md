# 🎓 JPCS Malvar Chapter Website

<p align="center">
  <img src="assets/images/LOGO.png" alt="JPCS Logo" width="120">
</p>

<p align="center">
  <strong>🌟 Official Website & Membership Management System</strong><br>
  Junior Philippine Computer Society (JPCS) Malvar Chapter<br>
  Batangas State University TNEU - JPLPC Malvar
</p>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-7.4+-777BB4?style=for-the-badge&logo=php" alt="PHP">
  <img src="https://img.shields.io/badge/Database-XML-FF6B6B?style=for-the-badge&logo=xml" alt="XML">
  <img src="https://img.shields.io/badge/Auth-Google_OAuth_2.0-4285F4?style=for-the-badge&logo=google" alt="OAuth">
  <img src="https://img.shields.io/badge/Email-PHPMailer-FF6B35?style=for-the-badge&logo=gmail" alt="PHPMailer">
  <img src="https://img.shields.io/badge/Chat-Tawk.to-00D084?style=for-the-badge&logo=livechat" alt="Live Chat">
</p>

<p align="center">
  <img src="https://img.shields.io/badge/License-MIT-green?style=flat" alt="License">
  <img src="https://img.shields.io/badge/Status-Production_Ready-brightgreen?style=flat" alt="Status">
  <img src="https://img.shields.io/badge/Version-2.0.0-blue?style=flat" alt="Version">
</p>

---

## 📋 Table of Contents

- [🎯 About](#-about)
- [⚡ Features](#-features)
- [🛠️ Technology Stack](#️-technology-stack)
- [📁 Project Structure](#-project-structure)
- [🚀 Installation Guide](#-installation-guide)
- [⚙️ Configuration](#️-configuration)
- [🔐 Security](#-security)
- [📧 Email System](#-email-system)
- [🗺️ Image Maps](#️-image-maps)
- [🎨 Live Chat](#-live-chat)
- [📱 Responsive Design](#-responsive-design)
 - [🛍️ Checkout & Orders](#-checkout--orders)
- [👤 User Roles](#-user-roles)
- [🗄️ Database Schema](#️-database-schema)
- [🤝 Contributing](#-contributing)

---

## 🔁 Local Testing & Tunneling

If you want to test the site from another device or make it temporarily accessible over the internet, you can use PHP's built-in server and an HTTP tunnel like `ngrok`.

Quick start (serve the app locally):

```sh
# from project root
composer serve
```

Expose to the internet with ngrok (separate terminal):

```sh
# requires ngrok installed and available in PATH
ngrok http 8000
```

For Windows PowerShell you can use the helper script:

```powershell
.
\scripts\start-ngrok.ps1 -Port 8000
```

Notes:
- Update `SITE_URL` in `config.php` to the forwarded ngrok URL (e.g., `https://abcd1234.ngrok.io`) when testing OAuth callbacks or links that use absolute URLs.
- If testing on a local network (LAN), use your machine's IPv4 address and open port 8000 in the firewall.
- Remember to revert `SITE_URL` after testing.

Want me to add a small script to start both the server and `ngrok` together? I can add a cross-platform helper that spawns both processes.

---

## 🎯 About

A comprehensive, modern PHP-based membership management system designed specifically for the JPCS Malvar Chapter. This system provides a complete digital infrastructure for student organization management, featuring member authentication, event coordination, announcements, merchandise management, and powerful administrative tools.

### 🌟 Key Highlights
- **📊 No SQL Database Required** - Uses XML flat-file databases for simplicity
- **🔐 Google SSO Integration** - OAuth 2.0 authentication with fallback
- **📧 Email Verification System** - PHPMailer with SMTP support
- **💬 Live Chat Support** - Integrated Tawk.to widget
- **🗺️ Interactive Image Maps** - Enhanced navigation experience
  - Note: The homepage uses a Leaflet (OpenStreetMap) interactive map for navigation; clicking a marker zooms in and opens a popup — click the popup's "Open" link to navigate to the relevant page.
- **📱 Mobile-First Design** - Responsive across all devices
- **🛍️ Checkout & Orders** - Built-in shopping cart and order management
- **🛡️ Role-Based Security** - Granular access control
- **🎨 Modern UI/UX** - JPCS-themed orange design system

---

## ⚡ Features

### 🌐 Public Features
| Feature | Description | Status |
|---------|-------------|--------|
| **🏠 Homepage** | Hero section, events preview, interactive navigation map | ✅ Complete |
| **ℹ️ About Page** | Dynamic officer profiles, organization history | ✅ Complete |
| **📅 Events Calendar** | Upcoming activities and workshops | ✅ Complete |
| **👥 Membership Info** | Benefits, requirements, application process | ✅ Complete |
| **📢 Announcements** | Latest news and updates | ✅ Complete |
| **🛒 JPCS.Mart** | Official merchandise store with categories | ✅ Complete |
| **🛍️ Checkout & Orders** | Add to cart, checkout, view orders | ✅ Complete |
| **🎧 Help Desk** | Contact forms, FAQs, inquiry system | ✅ Complete |
| **🖼️ Gallery** | Event photos with filtering and categories | ✅ Complete |
| **📝 Registration** | Online membership application with validation | ✅ Complete |
| **📧 Email Verification** | Secure account activation workflow | ✅ Complete |
| **💬 Live Chat** | Real-time support via Tawk.to | ✅ Complete |

### 👤 Member Dashboard
| Feature | Description | Status |
|---------|-------------|--------|
| **📊 Dashboard** | Membership status, event summary, announcements | ✅ Complete |
| **👤 Profile Management** | Edit personal info, upload photo, change password | ✅ Complete |
| **🎫 Event Registration** | Register for upcoming events and activities | ✅ Complete |
| **📬 My Announcements** | Personalized announcement feed | ✅ Complete |
| **🧾 My Orders** | View placed orders and status | ✅ Complete |
| **📱 Mobile Responsive** | Full mobile optimization | ✅ Complete |

### 🔧 Admin Panel
| Feature | Description | Status |
|---------|-------------|--------|
| **📈 Analytics Dashboard** | Member statistics, event metrics, system overview | ✅ Complete |
| **👥 Member Management** | Approve, edit, activate/deactivate members | ✅ Complete |
| **📅 Event Management** | Full CRUD operations for events | ✅ Complete |
| **📢 Announcement System** | Create, edit, delete announcements | ✅ Complete |
| **🖼️ Gallery Manager** | Upload photos, organize by categories | ✅ Complete |
| **🛒 Product Management** | Merchandise inventory, pricing, stock management | ✅ Complete |
| **👑 Officer Management** | Manage chapter officers and hierarchy | ✅ Complete |
| **📋 Registration Review** | Review and process membership applications | ✅ Complete |
| **🎧 Inquiry Management** | Handle help desk inquiries and feedback | ✅ Complete |
| **⚙️ System Settings** | Global configuration and maintenance | ✅ Complete |

### 🔐 Authentication & Security
| Feature | Description | Status |
|---------|-------------|--------|
| **🔐 Multi-Factor Login** | Email/password + Google OAuth 2.0 | ✅ Complete |
| **📧 Email Verification** | Token + 6-digit code verification | ✅ Complete |
| **🛡️ Role-Based Access** | Admin, Officer, Member permissions | ✅ Complete |
| **🔒 Session Security** | Secure session management | ✅ Complete |
| **🚫 Data Protection** | Sensitive file exclusion, bcrypt hashing | ✅ Complete |

---

## 🛠️ Technology Stack

### 📋 Backend
- **PHP 7.4+** - Server-side scripting
- **XML Databases** - Flat-file storage for simplicity
- **PHPMailer 7.0** - SMTP email delivery
- **Google OAuth 2.0** - Social login integration
- **bcrypt** - Password hashing security

### 🎨 Frontend
- **HTML5** - Semantic markup
- **CSS3** - Modern styling with custom properties
- **JavaScript ES6** - Interactive functionality
- **Responsive Design** - Mobile-first approach
- **Progressive Web App** - PWA features

### 🔧 Third-Party Integrations
- **Tawk.to** - Live chat support
- **Google OAuth** - Social authentication
- **SMTP Email** - Reliable email delivery
- **Interactive Image Maps** - Enhanced navigation

### 📦 Dependencies
```json
{
  "require": {
    "phpmailer/phpmailer": "^7.0"
  }
}
```

---

## 📁 Project Structure

```
JPCS/
├── 📁 admin/                    # Admin dashboard and management
│   ├── dashboard.php           # Admin analytics dashboard
│   ├── members.php            # Member management
│   ├── events.php             # Event management
│   ├── announcements.php      # Announcement management
│   ├── gallery.php            # Photo gallery management
│   ├── products.php           # Merchandise management
│   ├── orders.php             # Orders management
│   ├── handle_order.php      # Order actions (mark paid/completed)
│   ├── officers.php           # Officer management
│   ├── registrations.php      # Review applications
│   ├── inquiries.php          # Help desk management
│   ├── settings.php           # System configuration
│   └── includes/              # Admin components
├── 📁 member/                   # Member dashboard
│   ├── dashboard.php          # Member overview
│   ├── profile.php            # Profile management
│   ├── events.php             # Event registration
│   └── announcements.php      # Member announcements
├── 📁 pages/                    # Public pages
│   ├── about.php              # Organization info
│   ├── events.php             # Public events
│   ├── membership.php         # Membership info
│   ├── announcements.php      # Public announcements
│   ├── jpcsmart.php           # Merchandise store
│   ├── checkout.php           # Checkout page
│   ├── order_success.php      # Order confirmation
│   ├── my_orders.php          # Member orders listing
│   ├── helpdesk.php           # Support page
│   ├── gallery.php            # Photo gallery
│   └── registration.php       # Sign-up form
├── 📁 includes/                 # Core system files
│   ├── auth.php               # Authentication functions
│   ├── db_helper.php          # Database operations (1200+ lines)
│   ├── functions.php          # Utility functions
│   ├── email_verification.php # Email verification system
│   ├── mailer.php             # PHPMailer configuration
│   ├── image_map.php          # Interactive image maps
│   ├── tawk_chat.php          # Live chat widget
│   ├── google_oauth.php       # OAuth integration
│   └── env_loader.php         # Environment variables
├── 📁 handlers/                 # Form processing
│   ├── register.php           # Registration handler
│   ├── logout.php             # Logout handler
│   ├── checkout.php           # Checkout handler
│   ├── gcash_webhook.php      # GCash webhook handler
│   ├── event_registration.php # Event signup
│   └── sso_callback.php       # OAuth callback
├── 📁 database/                 # XML data storage
│   ├── users.xml              # User accounts (excluded from git)
│   ├── members.xml            # Member details (excluded from git)
│   ├── events.xml             # Events data
│   ├── announcements.xml      # Announcements
│   ├── products.xml           # Merchandise
│   ├── gallery.xml            # Photo gallery
│   ├── orders.xml             # Orders and transactions
│   ├── officers.xml           # Chapter officers
│   ├── registrations.xml      # Applications
│   ├── inquiries.xml          # Help desk
│   ├── newsletter.xml         # Email subscribers
│   ├── event_registrations.xml # Event signups
│   ├── verifications.xml      # Email verification tokens
│   └── *.xml.example         # Database templates
├── 📁 css/                      # Stylesheets (13 files)
│   ├── style.css              # Global styles
│   ├── index.css              # Homepage styles
│   ├── admin.css              # Admin dashboard styles
│   ├── member.css             # Member dashboard styles
│   ├── login.css              # Login page styles
│   ├── checkout.css           # Checkout page styles
│   └── [page].css             # Page-specific styles
├── 📁 assets/                   # Static resources
│   ├── images/                # Logos, photos, icons
│   └── uploads/               # User-uploaded content
├── 📁 vendor/                   # Composer dependencies
├── 📄 config.php                # Core configuration
├── 📄 index.php                 # Homepage
├── 📄 login.php                 # Authentication page
├── 📄 verify.php                # Email verification page
├── 📄 .env                      # Environment variables (excluded)
├── 📄 .env.example              # Environment template
├── 📄 .gitignore                # Git exclusions
├── 📄 composer.json             # PHP dependencies
└── 📄 README.md                 # This documentation
```

---

## 🚀 Installation Guide

### 📋 Prerequisites

- **PHP 7.4+** with extensions:
  - `xml` (for XML parsing)
  - `curl` (for OAuth and email)
  - `openssl` (for encryption)
  - `fileinfo` (for file uploads)
- **Web Server** (Apache/Nginx with URL rewriting)
- **Composer** (for PHPMailer dependency)
- **SMTP Email Account** (Gmail recommended)

### 1️⃣ Clone Repository

```bash
# Clone the repository
git clone https://github.com/your-username/JPCS.git

# Navigate to project directory
cd JPCS

# Set proper permissions
chmod 755 database/ uploads/ assets/uploads/
chmod 644 database/*.xml.example
```

### 2️⃣ Install Dependencies

```bash
# Install PHPMailer via Composer
composer install

# Or if Composer not available, download PHPMailer manually
```

### 3️⃣ Database Setup

```bash
# Copy XML templates to create actual database files
cp database/users.xml.example database/users.xml
cp database/members.xml.example database/members.xml
cp database/verifications.xml.example database/verifications.xml
cp database/orders.xml.example database/orders.xml

# Set write permissions
chmod 666 database/*.xml
```

### 4️⃣ Environment Configuration

```bash
# Copy environment template
cp .env.example .env

# Edit .env with your configuration
nano .env
```

### 5️⃣ Web Server Setup

#### Apache (.htaccess)
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^([^/]+)/?$ pages/$1.php [L,QSA]

# Security headers
Header always set X-Content-Type-Options nosniff
Header always set X-Frame-Options DENY
Header always set X-XSS-Protection "1; mode=block"
```

#### Nginx
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}

location ~ \.php$ {
    fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
    fastcgi_index index.php;
    include fastcgi_params;
}
```

---

## ⚙️ Configuration

### 📧 Email Setup (Gmail)

1. **Enable 2-Step Verification** in your Google account
2. **Generate App Password**:
   - Go to https://myaccount.google.com/apppasswords
   - Select "Mail" and "Computer"
   - Copy the 16-character password

3. **Update .env file**:
```env
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=xxxx-xxxx-xxxx-xxxx
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME=JPCS Malvar Chapter
```

### 🔐 Google OAuth Setup

1. **Create Google Cloud Project**:
   - Go to https://console.cloud.google.com/
   - Create new project or select existing
   - Enable Google+ API

2. **Configure OAuth Consent Screen**:
   - Add your domain to authorized domains
   - Set application logo and privacy policy

3. **Create OAuth 2.0 Credentials**:
   - Go to Credentials > Create Credentials > OAuth 2.0
   - Add authorized redirect URIs:
     - `http://localhost/JPCS/sso_callback.php` (development)
     - `https://yourdomain.com/sso_callback.php` (production)

4. **Update .env file**:
```env
GOOGLE_CLIENT_ID=your-client-id.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=your-client-secret
GOOGLE_REDIRECT_URI=http://localhost/JPCS/sso_callback.php
```

### 🧾 Payment/Webhook Configuration

If you plan to accept online payments (e.g., GCash), configure webhook signing to secure incoming payment notifications.

1. Configure a webhook secret in `.env`:
```env
GCASH_WEBHOOK_SECRET=your-webhook-secret
``` 
2. Register your webhook URL with the payment provider pointing to `https://yourdomain.com/handlers/gcash_webhook.php`.

#### Testing GCash Webhook (local / staging)
If you want to test the webhook locally or from a staging environment, you can simulate a webhook request with a generated HMAC signature. Example using `openssl`:

```bash
payload='{"order_id":"ord_test_123","status":"paid"}'
secret='your-webhook-secret'
signature=$(printf "%s" "$payload" | openssl dgst -sha256 -hmac "$secret" -binary | xxd -p -c 256)
curl -X POST -H "Content-Type: application/json" -H "X-GCASH-SIGNATURE: $signature" -d "$payload" https://yourdomain.com/handlers/gcash_webhook.php
```

This will help you validate that the webhook handler correctly verifies signature and updates the order. Check `admin/orders.php` to validate the payment status change.

### 💬 Live Chat Setup (Tawk.to)

1. **Create Tawk.to Account**: https://www.tawk.to/
2. **Get Widget Code**: Administration → Channels → Chat Widget
3. **Update chat widget**: Edit `includes/tawk_chat.php` with your widget ID

### 🧪 Testing Configuration

Visit `http://localhost/JPCS/test_email.php` to verify:
- ✅ Email configuration
- ✅ SMTP connection
- ✅ PHPMailer setup

**⚠️ Important**: Delete `test_email.php` in production!

---

## 🛍️ Checkout & Orders

This project now includes a functioning shopping cart and order management flow for JPCS.Mart. Below is a quick overview of what was added and how it works.

- Cart: Client-side `localStorage`-based cart (key `jpcs_cart`). Products can be added from `pages/jpcsmart.php`.
- Checkout: `pages/checkout.php` is the user-facing checkout page. It supports GCash payment (receipt upload) and Onsite payment for manual verification.
- Order Storage: `database/orders.xml` (constant `DB_ORDERS`) stores orders. CRUD helpers (`getAllOrders`, `getOrderById`, `createOrder`, `updateOrder`) are available in `includes/db_helper.php`.
- Order Status: Newly created orders have `payment_status = pending` and `status = processing`. Stock decrementation occurs only when payment is confirmed (admin marks as paid or webhook marks as paid).
- Admin Controls: Admins can view and manage orders within `admin/orders.php` and change status using `admin/handle_order.php`.
- GCash Integration: There's a webhook handler `handlers/gcash_webhook.php` that validates HMAC-signed payloads and marks orders as paid when verified. Configure `GCASH_WEBHOOK_SECRET` in your `.env` for secure signature verification.

### Files Added/Updated
- New pages: `pages/checkout.php`, `pages/order_success.php`, `pages/my_orders.php`
- New handlers: `handlers/checkout.php`, `handlers/gcash_webhook.php`
- Administration: `admin/orders.php`, `admin/handle_order.php` (for marking invoices paid/completed)
- New CSS: `css/checkout.css` for checkout styles; `css/login.css` (login CSS was moved to a dedicated file and made responsive)
- Database: `database/orders.xml` (orders) — the system will create this file automatically during initialization if it does not exist; you may also create it from a template if you prefer.

### Notes for Deployment
- GCash webhook requires a public HTTPS endpoint for the webhook URL and a configured `GCASH_WEBHOOK_SECRET` to validate the payload signature.
- For Onsite payments, staff will verify and mark the order as paid from the admin interface.

### Testing Checkout (Local)
1. Create a product using `admin/products.php` with a stock > 0.
2. Visit `pages/jpcsmart.php`, add the product to the cart, then go to `pages/checkout.php`.
3. Choose `Onsite` (or upload a GCash receipt) and place the order.
4. Verify the order appears in `admin/orders.php` (or in `pages/my_orders.php` for a member).
5. If using GCash webhook, simulate a webhook using the `curl` example above.

---
| Registration Approvals | Review and approve applications |
| Inquiry Management | View and respond to help desk inquiries |
| Settings | System configuration |

---

## 🛠 Tech Stack

| Component | Technology |
|-----------|------------|
| **Backend** | PHP 7.4+ |
| **Database** | XML (Flat-file, 11 database files) |
| **Frontend** | HTML5, CSS3, JavaScript |
| **Authentication** | Session-based + Google OAuth 2.0 |
| **Password Hashing** | bcrypt (PASSWORD_BCRYPT) |
| **Icons** | Lucide Icons |
| **Fonts** | Poppins (Google Fonts) |
| **Server** | Apache (XAMPP recommended) |

---

## 📁 Project Structure

```
JPCS/
├── index.php                    # Home page
├── login.php                    # Login with SSO support
├── config.php                   # Core configuration
├── sso_login.php               # Google OAuth initiation
├── sso_callback.php            # Google OAuth callback
├── .env                        # Environment variables (NOT in repo)
├── .env.example                # Environment template
├── .gitignore                  # Git ignore rules
│
├── admin/                      # Admin Dashboard (10 files)
│   ├── dashboard.php           # Admin overview with stats
│   ├── members.php             # Member management
│   ├── events.php              # Event CRUD
│   ├── announcements.php       # Announcement management
│   ├── gallery.php             # Gallery uploads
│   ├── products.php            # Product/merchandise management
│   ├── officers.php            # Officer management
│   ├── registrations.php       # Registration approvals
│   ├── inquiries.php           # Help desk inquiries
│   └── settings.php            # System settings
│
├── member/                     # Member Dashboard (5 files)
│   ├── dashboard.php           # Member overview
│   ├── profile.php             # Profile management
│   ├── events.php              # Event registration
│   ├── announcements.php       # View announcements
│   └── includes/               # Member includes
│
├── pages/                      # Public Pages (8 files)
│   ├── about.php               # About with dynamic officers
│   ├── events.php              # Events listing
│   ├── announcements.php       # Announcements listing
│   ├── membership.php          # Membership info
│   ├── jpcsmart.php            # Merchandise store
│   ├── gallery.php             # Photo gallery
│   ├── helpdesk.php            # Contact & FAQs
│   └── registration.php        # Membership application
│
├── includes/                   # PHP Includes (5 files)
│   ├── functions.php           # Utility functions (310 lines)
│   ├── db_helper.php           # XML database operations (1200+ lines)
│   ├── auth.php                # Authentication functions
│   ├── google_oauth.php        # Google OAuth class
│   └── env_loader.php          # Environment variable loader
│
├── handlers/                   # Form Processors (4 files)
│   ├── register.php            # Registration handler
│   ├── event_registration.php  # Event registration handler
│   ├── logout.php              # Logout handler
│   └── sso_callback.php        # SSO callback handler
│
├── database/                   # XML Databases (11 files)
│   ├── users.xml               # User accounts
│   ├── members.xml             # Member profiles
│   ├── events.xml              # Events data
│   ├── announcements.xml       # Announcements
│   ├── products.xml            # Products/merchandise
│   ├── gallery.xml             # Gallery items
│   ├── officers.xml            # Officers data (39 entries)
│   ├── registrations.xml       # Pending registrations
│   ├── inquiries.xml           # Help desk inquiries
│   ├── newsletter.xml          # Newsletter subscribers
│   └── event_registrations.xml # Event registrations
│
├── css/                        # Stylesheets (13 files)
│   ├── style.css               # Main stylesheet (320 lines)
│   ├── pages.css               # Shared page styles (352 lines)
│   ├── index.css               # Homepage modern styles
│   ├── admin.css               # Admin dashboard styles
│   ├── member.css              # Member dashboard styles
│   └── [page-specific].css     # Individual page styles
│
├── js/                         # JavaScript
│   └── script.js               # Main JavaScript file
│
└── assets/                     # Static Assets
    ├── images/                 # Site images (LOGO.png, JPCS.gif)
    ├── profiles/               # Officer profile photos (39 images)
    └── uploads/                # User uploads
        ├── products/           # Product images
        └── gallery/            # Gallery images
```

---

## 🚀 Installation

### Prerequisites
- PHP 7.4 or higher
- Apache web server (XAMPP recommended)
- cURL extension enabled
- XML extension enabled

### Steps

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/jpcs-malvar.git
   ```

2. **Move to web server directory**
   ```powershell
   # For XAMPP on Windows
   Move-Item jpcs-malvar C:\xampp\htdocs\JPCS
   ```

3. **Create environment file**
   ```powershell
   Copy-Item .env.example .env
   ```

4. **Configure environment variables** (edit `.env` file)

5. **Create database files**
   ```powershell
   Copy-Item database\users.xml.example database\users.xml
   Copy-Item database\members.xml.example database\members.xml
   ```

6. **Access the website**
   ```
   http://localhost/JPCS/
   ```

### Default Admin Account
| Field | Value |
|-------|-------|
| Email | admin@jpcs-malvar.edu.ph |
| Password | Admin@2025 |

> ⚠️ **IMPORTANT:** Change the default admin password immediately!

---

## ⚙️ Configuration

### Environment Variables (.env)

```env
# Google OAuth Configuration
GOOGLE_CLIENT_ID=your_client_id.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=your_client_secret
GOOGLE_REDIRECT_URI=http://localhost/JPCS/sso_callback.php

# Site Configuration
SITE_NAME=JPCS Malvar Chapter
SITE_URL=http://localhost/JPCS/
DB_PATH=database/
```

### Google OAuth Setup

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select existing
3. Enable **Google+ API** and **Google Identity API**
4. Go to **Credentials** → **Create Credentials** → **OAuth 2.0 Client IDs**
5. Add authorized redirect URI: `http://localhost/JPCS/sso_callback.php`
6. Copy Client ID and Client Secret to `.env`

### Config.php Settings

| Setting | Description | Default |
|---------|-------------|---------|
| `SITE_NAME` | Website name | JPCS Malvar Chapter |
| `SITE_URL` | Base URL | http://localhost/JPCS |
| `SITE_EMAIL` | Contact email | jpcs.malvar@g.batstate-u.edu.ph |
| `MEMBERSHIP_FEE` | Registration fee | 500 |
| `MAX_UPLOAD_SIZE` | Max file upload | 5MB (5242880 bytes) |
| `ITEMS_PER_PAGE` | Pagination limit | 10 |
| `SESSION_TIMEOUT` | Session duration | 4 hours |

---

## 🔒 Security Implementation

### Security Features

| Feature | Implementation |
|---------|---------------|
| Password Hashing | bcrypt with PASSWORD_BCRYPT |
| Input Sanitization | `htmlspecialchars()` + `strip_tags()` |
| Session Security | HttpOnly cookies, custom session name |
| Role-Based Access | `requireAdmin()`, `requireLogin()` functions |
| Email Validation | `filter_var()` with FILTER_VALIDATE_EMAIL |
| Phone Validation | Philippine format regex validation |
| XSS Prevention | Output escaping on all user data |
| File Upload Validation | MIME type and size checking |

### Session Configuration (in config.php)
```php
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Set to 1 for HTTPS
session_name('JPCS_SESSION');
```

### Production Security Checklist

- [ ] Change default admin password
- [ ] Set `error_reporting(0)` and `display_errors = 0`
- [ ] Enable HTTPS and set `session.cookie_secure = 1`
- [ ] Update Google OAuth redirect URI to production URL
- [ ] Review `.env` with production values
- [ ] Set proper file permissions (755 folders, 644 files)
- [ ] Implement rate limiting for login attempts
- [ ] Set up regular database backups
- [ ] Remove any test accounts

---

## 📡 API Reference

### Authentication Endpoints

| Endpoint | Method | Description |
|----------|--------|-------------|
| `/login.php` | GET/POST | Login page and handler |
| `/sso_login.php` | GET | Initiates Google OAuth flow |
| `/sso_callback.php` | GET | Handles OAuth callback |
| `/handlers/logout.php` | GET | Destroys session and logout |

### Form Handlers

| Endpoint | Method | Description | Response |
|----------|--------|-------------|----------|
| `/handlers/register.php` | POST | Member registration | JSON |
| `/handlers/event_registration.php` | POST | Event registration | JSON |

### Admin AJAX Operations

| Action | File | Description |
|--------|------|-------------|
| Approve/Reject Member | `admin/members.php` | Update membership_status |
| CRUD Events | `admin/events.php` | Create, Read, Update, Delete |
| CRUD Announcements | `admin/announcements.php` | Manage announcements |
| CRUD Products | `admin/products.php` | Manage merchandise |
| Upload Gallery | `admin/gallery.php` | Image upload with category |
| Manage Officers | `admin/officers.php` | Update officer data |

---

## 👥 User Roles

### Role Permissions Matrix

| Permission | Admin | Officer | Member | Guest |
|------------|:-----:|:-------:|:------:|:-----:|
| View public pages | ✅ | ✅ | ✅ | ✅ |
| Login/Register | ✅ | ✅ | ✅ | ✅ |
| Member dashboard | ✅ | ✅ | ✅ | ❌ |
| Register for events | ✅ | ✅ | ✅ | ❌ |
| Admin dashboard | ✅ | ✅ | ❌ | ❌ |
| Manage members | ✅ | ❌ | ❌ | ❌ |
| Manage events | ✅ | ✅ | ❌ | ❌ |
| Manage announcements | ✅ | ✅ | ❌ | ❌ |
| Manage products | ✅ | ❌ | ❌ | ❌ |
| Manage officers | ✅ | ❌ | ❌ | ❌ |
| Manage gallery | ✅ | ✅ | ❌ | ❌ |
| System settings | ✅ | ❌ | ❌ | ❌ |

---

## 🗄 Database Schema

### XML Database Files (12 Total)

#### users.xml
```xml
<user>
    <id>user_abc123def456</id>
    <username>johndoe</username>
    <email>john@example.com</email>
    <password>$2y$10$...</password>  <!-- bcrypt hash -->
    <first_name>John</first_name>
    <last_name>Doe</last_name>
    <name>John Doe</name>
    <role>member|officer|admin</role>
    <status>active|inactive|pending</status>
    <google_id>optional_google_id</google_id>
    <profile_photo>photo.jpg</profile_photo>
    <created_at>2024-01-01 00:00:00</created_at>
</user>
```

#### members.xml
```xml
<member>
    <id>mem_abc123</id>
    <user_id>user_abc123</user_id>
    <member_id>JPCS-2024-0001</member_id>
    <first_name>John</first_name>
    <middle_name>Smith</middle_name>
    <last_name>Doe</last_name>
    <email>john@example.com</email>
    <phone>09123456789</phone>
    <birthdate>2000-01-01</birthdate>
    <gender>male|female</gender>
    <address>123 Main St</address>
    <city>Malvar</city>
    <province>Batangas</province>
    <zip_code>4233</zip_code>
    <school>BatStateU JPLPC</school>
    <course>BSIT</course>
    <year_level>3</year_level>
    <student_id>20-12345</student_id>
    <skills>PHP, JavaScript, Python</skills>
    <motivation>To learn and grow</motivation>
    <membership_status>active|pending|expired</membership_status>
    <joined_date>2024-01-01</joined_date>
    <expiry_date>2025-01-01</expiry_date>
    <profile_photo>profile.jpg</profile_photo>
</member>
```

#### orders.xml
```xml
<order>
  <id>ord_abc123</id>
  <user_id>user_abc123</user_id>
  <total>599.00</total>
  <payment_method>gcash|onsite</payment_method>
  <payment_status>pending|paid</payment_status>
  <status>processing|on-hold|completed</status>
  <payment_info>uploads/payments/receipt.png</payment_info>
  <created_at>2025-12-13 12:34:56</created_at>
  <items>
    <item>
      <product_id>prd_xyz</product_id>
      <name>JPCS Shirt</name>
      <price>299.00</price>
      <quantity>2</quantity>
    </item>
  </items>
</order>
```

#### officers.xml
```xml
<officer>
    <id>off_abc123</id>
    <name>Jaynellan Almary O. Magpantay</name>
    <position>President</position>
    <category>Executive|Governor|Director|Member</category>
    <image>MAGPANTAY.jpg</image>
    <order>1</order>
    <status>active</status>
</officer>
```

#### products.xml
```xml
<product>
    <id>prod_abc123</id>
    <name>JPCS T-Shirt</name>
    <description>Official chapter shirt</description>
    <price>350.00</price>
    <stock>100</stock>
    <category>apparel</category>
    <image>tshirt.jpg</image>
    <status>active|inactive</status>
    <created_at>2024-01-01</created_at>
</product>
```

#### gallery.xml
```xml
<item>
    <id>gal_abc123</id>
    <title>Tech Workshop 2024</title>
    <image>workshop.jpg</image>
    <category>Events|Activities|General</category>
    <description>Annual tech workshop</description>
    <uploaded_at>2024-01-01 00:00:00</uploaded_at>
</item>
```

---

## 🎨 Theme & Styling

### Color Palette

| Color | Hex | CSS Variable | Usage |
|-------|-----|--------------|-------|
| Primary Orange | `#ff6a00` | `--primary` | Buttons, accents |
| Primary Dark | `#e05e00` | `--primary-dark` | Hover states |
| Primary Light | `#ff8c42` | `--primary-light` | Secondary elements |
| Dark Background | `#1a1a2e` | `--dark` | Dark sections |
| Text Dark | `#333333` | `--text-dark` | Primary text |
| Text Light | `#666666` | `--text-light` | Secondary text |
| Background | `#f5f5f5` | `--bg-light` | Page background |

---

## 👥 Current Leadership (2025-2026)

| Position | Name |
|----------|------|
| **Chapter Adviser** | Mr. Joseph Rizalde E. Guillo |
| **President** | Jaynellan Almary O. Magpantay |
| **VP Internal Affairs** | Ralph Gabriel T. Loleng |
| **VP External Affairs** | Mard Jonas Prato |
| **General Secretary** | Ernest Vincent Aidan L. Mabilangan |
| **Finance Officer** | Hazel Anne Malitig |
| **Auditor** | Kisha Rain Enaje |
| **Public Information Officer** | Axle Dave Navata |
| **Director for Technical** | John Rick Bantog |

---

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

### Development Guidelines
- Follow PSR-12 coding standards for PHP
- Use meaningful commit messages
- Test all features before submitting PR
- Update documentation for new features
- Never commit sensitive data

---

## 📄 License

This project is licensed under the MIT License.

---

## 📞 Contact

**JPCS Malvar Chapter**
- **Email:** jpcs.malvar@g.batstate-u.edu.ph
- **Location:** Batangas State University TNEU - JPLPC Malvar

---

<p align="center">
  Made with ❤️ by JPCS Malvar Chapter<br>
  © 2024-2025 Junior Philippine Computer Society - Malvar Chapter
</p>
