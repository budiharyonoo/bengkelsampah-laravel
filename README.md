# Bengkel Sampah

## Todo List

[x] Di dashboard ada filter bank sampah kan, nah minta ga dropdown gitu, tapi tipe search
[x] Di transaksi juga dibuat sama
[x] Di menu user perlu tambahain jenis nasabahnya di table, lalu di action read dan editnya juga (bisa edit jenis nasabah)
[ ] Di transaksi struct PDF nya corrupt nampaknya, minta di fix sama dipastiin sizenya aman buat di thermal print
[x] Minta ditambahkan export data user
[ ] Dampak Lingkungan ganti dengan standar KLHK -> (Dokumennya dari user)
[ ] Penambahan form url di event, api mobile juga minta ditambah return form url nya
[x] Laporan event sekarang gabisa di download PDF nya

## Tech Stack

[![Laravel](https://img.shields.io/badge/Laravel-10.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.1+-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

## 📋 Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Technology Stack](#technology-stack)
- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Configuration](#configuration)
- [Database Setup](#database-setup)
- [API Documentation](#api-documentation)
- [Project Structure](#project-structure)
- [Key Features Explained](#key-features-explained)
- [Contributing](#contributing)
- [License](#license)

## 🎯 Overview

**Bengkel Sampah** is a comprehensive Laravel-based waste management platform that includes both a REST API for mobile applications and a full-featured admin dashboard for managing the entire waste management ecosystem. The platform facilitates the connection between users, waste banks (Bank Sampah), and environmental events, promoting sustainable waste management practices through a digital platform.

### 🌐 **Platform Components:**

**📱 Mobile API:**
- REST API for mobile applications
- User authentication and management
- Waste deposit and tracking system
- Points and rewards system
- Event participation
- Educational content access

**🖥️ Admin Dashboard:**
- Complete web-based administration panel
- User management and analytics
- Transaction monitoring and processing
- Content management (articles, events)
- Waste bank management
- Points and redemption system
- Comprehensive reporting and exports

**📚 User Documentation:**
- Docusaurus-based user guide
- Complete feature documentation
- Tutorials and troubleshooting guides

The system enables users to:
- Deposit waste at registered waste banks
- Earn points and rewards for recycling activities
- Participate in environmental events
- Access educational content about waste management
- Track their environmental impact

## ✨ Features

### 🔐 Authentication & User Management
- **Multi-factor Authentication**: OTP-based verification system via WhatsApp/Email
- **User Registration & Login**: Secure authentication with Laravel Sanctum
- **Profile Management**: Complete user profile with statistics tracking
- **Account Deletion**: Secure account removal with request system
- **Address Management**: Multiple address support with default location
- **App Version Management**: Automatic update checking for mobile apps
- **Privacy Policy**: Comprehensive privacy policy and data protection
- **GDPR Compliance**: User data protection and deletion requests

### 🏦 Waste Bank Management
- **Bank Sampah Directory**: Comprehensive database of waste banks
- **Service Types**: Support for pickup, drop-off, and hybrid services
- **Bank Profiles**: Detailed information including photos and contact details
- **Geographic Integration**: Address management with default locations

### 📦 Waste Deposit System
- **Multiple Deposit Types**: Sell, donate, or save waste options
- **Real-time Tracking**: Status updates throughout the deposit process
- **Photo Documentation**: Waste photo uploads for verification
- **Scheduling**: Pickup scheduling with date and time selection
- **Cancellation System**: User-initiated deposit cancellations
- **Transaction Receipts**: PDF receipt generation for completed transactions
- **Status Management**: Complete lifecycle from confirmation to completion
- **Point Calculation**: Automatic point and XP calculation based on transaction type

### 🎯 Points & Rewards System
- **Point Accumulation**: Earn points for recycling activities
- **Level System**: User progression based on activity
- **Statistics Tracking**: Comprehensive user activity metrics
- **Reward Redemption**: Point-based reward system with proof upload
- **Redeem Management**: Admin-controlled redemption processing
- **Point History**: Complete transaction history tracking
- **Balance Monitoring**: Real-time point balance tracking

### 📚 Content Management
- **Educational Articles**: Categorized content about waste management
- **Event Management**: Environmental events with participant tracking
- **Event Results**: Post-event reporting with photo documentation
- **Content Categories**: Organized content structure
- **Waste Catalog**: Comprehensive waste type database with pricing
- **Pilahku Check System**: Real-time validation of waste data and pricing

### 🔔 Notification System
- **Push Notifications**: Firebase Cloud Messaging integration
- **Dual Zenziva Integration**:
  - **OTP Account**: For user verification (WhatsApp Official API)
  - **Setor Account**: For transaction notifications (WhatsApp Regular API)
- **Real-time Updates**: Instant status change notifications
- **Multi-channel Delivery**: Push, WhatsApp, and email notifications
- **Balance Monitoring**: Real-time Zenziva account balance tracking

### 🖥️ Admin Dashboard
- **Complete Web Interface**: Full-featured web-based administration panel
- **Comprehensive Management**: Full CRUD operations for all entities
- **Multi-format Export**: PDF, Excel, and CSV export capabilities
- **User Management**: Complete user administration with detailed profiles
- **Content Management**: Article and event administration
- **Transaction Management**: Complete setoran lifecycle management
- **Points & Redeem System**: Point management and redemption processing
- **Analytics**: System-wide statistics and reporting
- **Role-based Access**: Admin and branch-level permissions
- **Real-time Monitoring**: Live dashboard with key metrics
- **Professional UI**: Modern, responsive design with intuitive navigation

## 🛠 Technology Stack

### Backend
- **Framework**: Laravel 10.x
- **PHP Version**: 8.1+
- **Database**: MySQL/PostgreSQL
- **Authentication**: Laravel Sanctum + JWT
- **API Documentation**: L5-Swagger (OpenAPI)

### External Services
- **Push Notifications**: Firebase Cloud Messaging
- **Dual Zenziva Integration**:
  - OTP Account (WhatsApp Official API)
  - Setor Account (WhatsApp Regular API)
- **File Storage**: Local filesystem with upload management
- **PDF Generation**: DomPDF for reports and receipts
- **Excel Export**: PhpSpreadsheet for data export
- **User Guide**: Docusaurus-based documentation system
- **Company Website**: Professional landing page with company profile

### Development Tools
- **Testing**: PHPUnit
- **Code Quality**: Laravel Pint
- **Package Management**: Composer

## 📋 Prerequisites

Before installing this project, ensure you have the following installed:

- **PHP** 8.1 or higher
- **Composer** 2.0 or higher
- **MySQL** 8.0 or higher / **PostgreSQL** 13 or higher
- **Node.js** 16 or higher (for frontend assets)
- **Git**

### PHP Extensions Required
```bash
php-bcmath
php-curl
php-dom
php-gd
php-mbstring
php-mysql
php-xml
php-zip
```

## 🚀 Installation

### 1. Clone the Repository
```bash
git clone https://github.com/yourusername/bengkelsampah-api.git
cd bengkelsampah-api
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Environment Configuration
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure Environment Variables
Edit the `.env` file with your configuration:

```env
APP_NAME="Bengkel Sampah API"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bengkelsampah
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Firebase Configuration
FIREBASE_PROJECT_ID=your_firebase_project_id

# Zenziva Configuration
# OTP Account (WhatsApp Official API)
ZENZIVA_USERKEY=your_zenziva_otp_userkey
ZENZIVA_PASSKEY=your_zenziva_otp_passkey
ZENZIVA_BRAND=your_zenziva_brand

# Setor Account (WhatsApp Regular API)
ZENZIVA_USERKEY_SETOR=your_zenziva_setor_userkey
ZENZIVA_PASSKEY_SETOR=your_zenziva_setor_passkey
```

### 5. Database Setup
```bash
php artisan migrate
php artisan db:seed
```

### 6. Storage Setup
```bash
php artisan storage:link
```

### 7. Generate API Documentation
```bash
php artisan l5-swagger:generate
```

### 8. Build User Guide (Optional)
```bash
cd user-guide
npm install
npm run build
```

### 9. Start the Development Server
```bash
php artisan serve
```

The API will be available at `http://localhost:8000`

## ⚙️ Configuration

### Firebase Setup
1. Create a Firebase project at [Firebase Console](https://console.firebase.google.com/)
2. Download your service account JSON file
3. Place it in `storage/app/firebase-service-account.json`
4. Update your `.env` file with the Firebase project ID

### Zenziva Setup
1. Register at [Zenziva](https://zenziva.net/)
2. Create two separate accounts:
   - **OTP Account**: For user verification (WhatsApp Official API)
   - **Setor Account**: For transaction notifications (WhatsApp Regular API)
3. Get userkey, passkey, and brand for each account
4. Update the `.env` file with your credentials

### File Upload Configuration
The application supports file uploads for:
- Article covers (`public/uploads/artikel_cover/`)
- Event covers (`public/uploads/event_cover/`)
- Event results (`public/uploads/event_results/`)
- Waste photos (`public/uploads/sampah/`)
- User redeem proofs (`uploads/redeem/`)

### Report Generation
The system supports comprehensive reporting:
- **PDF Reports**: Transaction, user, bank sampah, event, and article reports
- **Excel Export**: Data export in Excel format with formatting
- **CSV Export**: Simple data export for analysis
- **Receipt Generation**: PDF receipts for completed transactions
- **Custom Templates**: Branded report templates

### User Guide Configuration
The user guide is built with Docusaurus and includes:
- **Documentation**: Comprehensive feature guides
- **Blog**: Updates and announcements
- **Custom Styling**: Branded appearance
- **Search Functionality**: Easy content discovery
- **Responsive Design**: Mobile-friendly interface

## 🗄️ Database Setup

### Key Tables
- **users**: User accounts and profiles
- **bank_sampah**: Waste bank information
- **setorans**: Waste deposit transactions
- **events**: Environmental events
- **artikels**: Educational articles
- **points**: User point transactions (setor/redeem)
- **notifications**: Push notification records
- **addresses**: User address management
- **categories**: Waste categories
- **sampah**: Waste type definitions
- **prices**: Waste pricing per bank
- **levels**: User level system
- **otps**: OTP verification records
- **delete_account_requests**: Account deletion requests
- **app_versions**: Mobile app version management
- **admins**: Admin user accounts with role-based access
- **event_participants**: Event participation tracking
- **event_results**: Event result documentation

### Seeding Data
```bash
# Seed all data
php artisan db:seed

# Seed specific data
php artisan db:seed --class=BankSampahSeeder
php artisan db:seed --class=CategorySeeder
php artisan db:seed --class=ArtikelSeeder
```

## 📱 Mobile API Documentation

### Base URL
```
http://localhost:8000/api
```

### Authentication
The API uses Laravel Sanctum for authentication. Include the bearer token in the Authorization header:
```
Authorization: Bearer {your_token}
```

### Key Endpoints

#### Authentication
- `POST /api/login` - User login
- `POST /api/register` - User registration
- `POST /api/send-otp` - Send OTP verification
- `POST /api/forgot` - Password reset
- `GET /api/home` - Get home data with app version info

#### User Management
- `GET /api/profile` - Get user profile
- `GET /api/detail-profile` - Get detailed profile with addresses
- `PUT /api/edit-profile` - Update user profile
- `POST /api/delete-account` - Request account deletion

#### Waste Banks
- `GET /api/bank-sampah` - List all waste banks
- `GET /api/bank-sampah/{id}` - Get specific waste bank details

#### Waste Deposits
- `GET /api/setorans` - List user deposits
- `POST /api/setorans` - Create new deposit
- `GET /api/setorans/{id}` - Get deposit details
- `POST /api/setorans/{id}/cancel` - Cancel deposit
- `PUT /api/setorans/{id}/status` - Update deposit status

#### Points & Redeem
- `GET /api/point` - Get user points and history

#### Events
- `GET /api/events` - List all events
- `GET /api/events/{id}` - Get event details
- `POST /api/events/{id}/toggle-join` - Join/leave event

#### Content
- `GET /api/artikels` - List articles
- `GET /api/artikels/{id}` - Get article details
- `GET /api/katalog` - Get waste catalog
- `GET /api/katalog/{id}` - Get specific waste details
- `POST /api/pilahku/check` - Validate waste data and pricing

#### Notifications
- `GET /api/notifications` - List notifications
- `POST /api/notifications/mark-as-read` - Mark as read
- `POST /api/fcm-token` - Update FCM token

### Complete API Documentation
Access the interactive API documentation at:
```
http://localhost:8000/api/documentation
```

### 🖥️ Admin Dashboard Access
Access the admin dashboard at:
```
http://localhost:8000/admin
```

Dashboard Features:
- **User Management**: Complete user administration
- **Transaction Management**: Setoran processing and monitoring
- **Content Management**: Articles and events administration
- **Points & Redeem**: Point management and redemption processing
- **Reporting**: PDF, Excel, and CSV exports
- **Analytics**: Real-time statistics and metrics

### 📚 User Guide Documentation
Access the comprehensive user guide built with Docusaurus at:
```
http://localhost:8000/user-guide
```

The user guide includes:
- **Dashboard Overview**: Complete admin dashboard guide
- **Feature Documentation**: Detailed guides for all features
  - User Management
  - Bank Sampah Management
  - Artikel Management
  - Event Management
  - Waste Management
  - Transaction Management
  - Points System
  - Category Management
  - Reporting System
- **Tutorials**: Step-by-step tutorials
- **FAQ**: Frequently asked questions
- **Troubleshooting**: Common issues and solutions

### Company Website
Access the professional company landing page at:
```
http://localhost:8000
```

Features:
- **Company Profile**: Professional presentation of Bengkel Sampah
- **Service Overview**: Detailed service descriptions
- **Contact Information**: Company contact details
- **Privacy Policy**: Comprehensive privacy policy
- **Account Deletion**: GDPR-compliant account deletion request form

## 📁 Project Structure

```
bengkelsampah/
├── app/
│   ├── Console/Commands/          # Artisan commands
│   ├── Exceptions/                # Exception handlers
│   ├── Helpers/                   # Helper functions
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/              # Mobile API controllers
│   │   │   └── Auth/             # Authentication controllers
│   │   └── Middleware/            # Custom middleware
│   ├── Models/                    # Eloquent models
│   ├── Providers/                 # Service providers
│   └── Services/                  # Business logic services
├── config/                        # Configuration files
├── database/
│   ├── factories/                 # Model factories
│   ├── migrations/                # Database migrations
│   └── seeders/                   # Database seeders
├── public/                        # Public assets
│   ├── uploads/                   # File uploads
│   └── user-guide/                # Docusaurus documentation
├── resources/
│   ├── views/                     # Admin dashboard views (Blade)
│   └── js/                        # Frontend assets
├── routes/
│   ├── api.php                    # Mobile API routes
│   └── web.php                    # Admin dashboard routes
├── storage/                       # File storage
├── user-guide/                    # Docusaurus source files
│   ├── docs/                      # Documentation content
│   ├── blog/                      # Blog posts
│   └── src/                       # Docusaurus components
└── uploads/                       # Additional uploads
```

## 🔑 Key Features Explained

### Waste Deposit Workflow
1. **User Registration**: Users register with phone number verification
2. **Bank Selection**: Users browse and select nearby waste banks
3. **Waste Catalog**: Users select waste types from comprehensive catalog
4. **Pricing Validation**: Real-time pricing validation via Pilahku check
5. **Deposit Creation**: Users create deposits with waste details and photos
6. **Scheduling**: Users schedule pickup or drop-off times
7. **Processing**: Bank staff process deposits and update status
8. **Completion**: Points awarded and transaction completed

### Address Management System
- **Multiple Addresses**: Users can save multiple delivery addresses
- **Default Address**: Set primary address for quick access
- **Geographic Data**: Complete address structure (province, city, district, postal code)
- **Address Labels**: Custom labels for easy identification

### Notification System
- **Real-time Updates**: Firebase Cloud Messaging for instant notifications
- **Dual WhatsApp Integration**:
  - OTP notifications via WhatsApp Official API
  - Transaction notifications via WhatsApp Regular API
- **Multi-channel**: Support for push, email, and WhatsApp notifications
- **Customizable**: Configurable notification templates
- **Balance Monitoring**: Real-time Zenziva account balance tracking

### Points System
- **Earning**: Points earned based on waste type and quantity
- **Leveling**: User levels based on total points accumulated
- **Redemption**: Points can be redeemed for rewards with proof upload
- **Tracking**: Complete history of point transactions
- **Admin Management**: Admin-controlled redemption processing
- **Balance Monitoring**: Real-time point balance tracking

### Waste Catalog & Pricing
- **Comprehensive Database**: Extensive waste type definitions
- **Dynamic Pricing**: Bank-specific pricing for each waste type
- **Real-time Validation**: Pilahku check system for data integrity
- **Category Management**: Organized waste categorization

### App Version Management
- **Platform Support**: Android and iOS version tracking
- **Update Notifications**: Automatic update checking
- **Force Updates**: Required update enforcement
- **Store Integration**: Direct links to app stores

### Admin Role Management
- **Role-based Access**: Admin and branch-level permissions
- **Branch Management**: Bank sampah-specific admin access
- **Multi-level Administration**: Central and local admin control

### Event Management
- **Event Creation**: Admin can create environmental events
- **Participant Management**: Users can join/leave events
- **Result Tracking**: Post-event results with photo documentation
- **Statistics**: Event participation and impact metrics
- **Event Lifecycle**: Complete event management from creation to completion
- **Photo Documentation**: Multiple photo uploads for event results

## 🤝 Contributing

We welcome contributions to improve Bengkel Sampah API! Please follow these steps:

1. **Fork** the repository
2. **Create** a feature branch (`git checkout -b feature/amazing-feature`)
3. **Commit** your changes (`git commit -m 'Add amazing feature'`)
4. **Push** to the branch (`git push origin feature/amazing-feature`)
5. **Open** a Pull Request

### Development Guidelines
- Follow PSR-12 coding standards
- Write tests for new features
- Update documentation for API changes
- Use conventional commit messages

### Testing
```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter=UserTest
```

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 📞 Support

For support and questions:
- **Email**: support@bengkelsampah.com
- **Documentation**: [API Documentation](http://localhost:8000/api/documentation)
- **Issues**: [GitHub Issues](https://github.com/yourusername/bengkelsampah-api/issues)

## 🙏 Acknowledgments

- **Laravel Team** for the amazing framework
- **Firebase** for push notification services
- **Zenziva** for WhatsApp integration
- **Open Source Community** for various packages and tools

---

**Made with ❤️ for a sustainable future**
