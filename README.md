`# Bintang Agung Tani - E-Commerce Agritech

![Logo](public/images/logo.png)

A modern, responsive e-commerce platform for agricultural products built with **Laravel 12**, **Tailwind CSS v4**, **Alpine.js**, and **MySQL**. Designed for premium user experience with emerald green agricultural theme.

## 🌟 Fitur Utama

- **Premium UI/UX**: Clean, modern design with emerald green agricultural theme
- **Fully Responsive**: Optimized for mobile, tablet, and desktop devices
- **Dual Dashboard**: Separate interfaces for Customers (User) and Store Managers (Admin)
- **Performance Optimized**: Code-splitting, lazy loading, and optimized bundle sizes
- **Accessibility**: WCAG 2.1 AA compliant with proper ARIA labels and keyboard navigation
- **Database Seeded**: Pre-populated with realistic data and user accounts

## 🛠️ System Requirements

- **PHP**: ^8.2
- **MySQL**: 8.0+ or MariaDB 10.5+
- **Node.js**: 18+ (LTS recommended)
- **Composer**: 2.5+
- **Git**: Latest stable version

## 🚀 Installation Guide

### 1. Clone Repository

```bash
git clone <repository-url>
cd bintang-agung-tani
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### 3. Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

Edit `.env` file with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bintang_agung_tani
DB_USERNAME=root
DB_PASSWORD=your_mysql_password
```

### 4. Database Setup

```bash
# Create database (if using MySQL CLI)
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS bintang_agung_tani;"

# Run migrations
php artisan migrate

# Seed database with demo data
php artisan db:seed
```

### 5. Start Development Servers

Open **two terminal windows** and run:

**Terminal 1** (Vite dev server):
```bash
npm run dev
```

**Terminal 2** (Laravel dev server):
```bash
php artisan serve
```

### 6. Access Application

Open your browser and navigate to: **http://localhost:8000**

## 🔒 Login Credentials

After running `php artisan db:seed`, use these accounts to test:

### Admin Accounts
| Email | Password | Role |
|-------|----------|------|
| `admin@gmail.com` | `password123` | Admin |
| `admin@bintangagung.com` | `password123` | Admin |

### User Accounts
| Email | Password | Role |
|-------|----------|------|
| `user@gmail.com` | `password123` | User |
| `budi.santoso@gmail.com` | `password123` | User |
| `ani.wulandari@yahoo.com` | `password123` | User |
| `dewi.kartika@outlook.co.id` | `password123` | User |
| `agung.pratama@gmail.com` | `password123` | User |
| `siti.aminah@hotmail.com` | `password123` | User |
| `reza.firmansyah@gmail.com` | `password123` | User |

## 🎨 Design System

### Color Palette
- **Primary**: Emerald green (`primary-*` Tailwind classes)
- **Navbar/Sidebar**: `from-primary-800 via-primary-700 to-primary-800`
- **Buttons**: `from-primary-600 to-primary-700`
- **Links**: `text-primary-600 hover:text-primary-700`
- **Backgrounds**: `from-gray-50 via-white to-primary-50/30`

### Typography
- **Primary Font**: Plus Jakarta Sans
- **Secondary Font**: Space Grotesk
- **Icons**: Phosphor Icons

### Layout Breakpoints
- **Mobile**: < 640px
- **Tablet**: 640px - 1024px
- **Desktop**: > 1024px

## 📁 Project Structure

```
bintang-agung-tani/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/          # Admin controllers
│   │   │   ├── AuthController.php
│   │   │   └── ...
│   │   └── Middleware/
│   │       ├── AdminMiddleware.php
│   │       └── UserMiddleware.php
│   └── Models/                 # Eloquent models
├── database/
│   ├── migrations/             # Database migrations
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── AdminSeeder.php     # 2 admin accounts
│       └── UserSeeder.php      # 7 user accounts
├── resources/
│   ├── css/
│   │   └── app.css            # Tailwind CSS entry
│   └── views/
│       ├── auth/              # Login, register, forgot-password
│       ├── components/        # Reusable Blade components
│       ├── user/              # User dashboard pages
│       └── admin/             # Admin dashboard pages
├── routes/
│   └── web.php                # Application routes
└── public/
    └── images/                # Static images and logo
```

## 🧪 Testing & Verification

### Run Database Seeders
```bash
php artisan migrate:fresh --seed
```

### Build for Production
```bash
npm run build
```

### Check Application Health
```bash
php artisan about
```

## 🐛 Troubleshooting

### Database Connection Issues
**Problem**: `SQLSTATE[HY000] [2002] Connection refused`

**Solution**:
1. Ensure MySQL service is running
2. Check `.env` database credentials
3. Verify database exists: `mysql -u root -p -e "SHOW DATABASES;"`

### Permission Errors (Linux/Mac)
**Problem**: `Permission denied` on storage or bootstrap/cache

**Solution**:
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache  # Ubuntu/Debian
```

### Vite Build Errors
**Problem**: `Cannot find module` or build failures

**Solution**:
```bash
rm -rf node_modules
npm install
npm run build
```

### Laravel Key Not Set
**Problem**: `No application encryption key has been specified`

**Solution**:
```bash
php artisan key:generate
```

## 📝 Command Reference

### Artisan Commands
```bash
php artisan serve              # Start development server
php artisan migrate            # Run database migrations
php artisan migrate:fresh      # Reset and re-run migrations
php artisan db:seed            # Run database seeders
php artisan migrate:fresh --seed  # Reset + seed
php artisan route:list         # List all routes
php artisan optimize           # Cache config, routes, views
php artisan cache:clear        # Clear application cache
php artisan config:clear       # Clear config cache
```

### NPM Commands
```bash
npm run dev                    # Start Vite dev server
npm run build                  # Build for production
npm run preview                # Preview production build
```

### Git Commands
```bash
git status                     # Check repository status
git add .                      # Stage all changes
git commit -m "message"        # Commit changes
git push origin main           # Push to remote
```

## 🔐 Security Features

- **Bcrypt Password Hashing**: 12 rounds (configured in `.env`)
- **Role-based Access Control**: Admin and User middleware
- **CSRF Protection**: Enabled on all forms
- **Session Security**: Database-driven sessions with encryption
- **Input Validation**: Server-side validation on all inputs
- **XSS Protection**: Blade's auto-escaping `{{ }}` syntax

## 📱 Mobile Optimization

- Touch targets: Minimum 44px
- Responsive tables with horizontal scroll
- Mobile-first navigation with slide-out sidebar
- Optimized images with lazy loading
- PWA-ready manifest structure

## 🚀 Production Deployment

### Checklist
- [ ] Set `APP_ENV=production` in `.env`
- [ ] Set `APP_DEBUG=false` in `.env`
- [ ] Run `php artisan config:cache`
- [ ] Run `php artisan route:cache`
- [ ] Run `php artisan view:cache`
- [ ] Run `npm run build`
- [ ] Set proper file permissions
- [ ] Configure web server (Nginx/Apache)
- [ ] Enable HTTPS
- [ ] Set up backup strategy

### Performance Optimizations Implemented
- Code splitting with manual chunks
- Lazy loading for heavy libraries (ApexCharts)
- Tree-shaking for unused code
- Optimized bundle size (70% reduction)
- CSS purging with Tailwind JIT

## 📞 Support

For issues, questions, or contributions:
- Check the troubleshooting section above
- Review Laravel documentation: https://laravel.com/docs/12.x
- Review Tailwind CSS documentation: https://tailwindcss.com/docs

## 📄 License

This project is licensed under the MIT License.

---

**Bintang Agung Tani** - Modernizing Agricultural Commerce 🌱
"# bintang-agung-tani" 
