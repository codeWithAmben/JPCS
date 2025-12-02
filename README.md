# JPCS Malvar Chapter Website

<p align="center">
  <img src="assets/images/LOGO.png" alt="JPCS Logo" width="120">
</p>

<p align="center">
  <strong>Official website for the Junior Philippine Computer Society (JPCS) Malvar Chapter</strong><br>
  Batangas State University TNEU - JPLPC Malvar
</p>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-7.4+-777BB4?style=flat&logo=php" alt="PHP Version">
  <img src="https://img.shields.io/badge/Database-XML-orange?style=flat" alt="Database">
  <img src="https://img.shields.io/badge/Auth-Google_OAuth_2.0-4285F4?style=flat&logo=google" alt="OAuth">
  <img src="https://img.shields.io/badge/License-MIT-green?style=flat" alt="License">
</p>

---

## 📋 Table of Contents

- [About](#-about)
- [Features](#-features)
- [Tech Stack](#-tech-stack)
- [Project Structure](#-project-structure)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [Security](#-security)
- [API Reference](#-api-reference)
- [User Roles](#-user-roles)
- [Database Schema](#-database-schema)
- [Contributing](#-contributing)

---

## 📖 About

A full-featured PHP-based membership management system for the JPCS Malvar Chapter. It provides member authentication, event management, announcements, merchandise store, and comprehensive administrative tools.

### Key Highlights
- **No SQL Database Required** - Uses XML flat-file databases
- **Google SSO Integration** - OAuth 2.0 authentication
- **Responsive Design** - Mobile-first approach
- **Role-Based Access Control** - Admin, Officer, and Member roles
- **Modern UI** - Clean, professional design with JPCS orange (#ff6a00) theme

---

## 🔒 Security Notice

**IMPORTANT:** Before publishing to GitHub, ensure sensitive files are NOT committed:

### Files that should NEVER be committed:
| File | Contains |
|------|----------|
| `.env` | API keys, OAuth secrets |
| `database/users.xml` | User credentials (bcrypt hashes) |
| `database/members.xml` | Member personal data |
| `database/registrations.xml` | Registration data |
| `database/inquiries.xml` | User inquiries |
| `database/newsletter.xml` | Email addresses |
| `database/event_registrations.xml` | Event registration data |
| `assets/uploads/` | User uploaded files |

---

## 🌟 Features

### 🌐 Public Pages
| Page | Description |
|------|-------------|
| **Home** | Hero section, featured events, quick links, products, gallery preview |
| **About** | Organization history, mission/vision, officer profiles (dynamic from database) |
| **Events** | Calendar of upcoming activities, workshops, and seminars |
| **Membership** | Information about joining and membership benefits |
| **Announcements** | Latest news and chapter updates |
| **JPCS.Mart** | Official merchandise store with product catalog |
| **Help Desk** | Contact information, FAQs, and inquiry form |
| **Gallery** | Photo gallery of past events (synced with admin) |
| **Registration** | Online membership application form |

### 👤 Member Features
| Feature | Description |
|---------|-------------|
| Dashboard | Personalized dashboard with membership status |
| Profile Management | Update personal information and photo |
| Event Registration | Register for upcoming events |
| Announcements | View latest chapter updates |

### 🔐 Admin Features
| Feature | Description |
|---------|-------------|
| Dashboard | Statistics overview (members, events, officers) |
| Member Management | Approve, edit, activate/deactivate members |
| Event Management | Full CRUD for events |
| Announcement Management | Post and manage announcements |
| Gallery Management | Upload and organize event photos with categories |
| Product Management | Manage merchandise inventory and pricing |
| Officer Management | Manage chapter officers and positions |
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

### XML Database Files (11 Total)

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
| Primary Orange | `#ff6a00` | `--primary-color` | Buttons, accents |
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
