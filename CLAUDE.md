# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## MCP Integration

### Always use these MCP tools:
- **serena**: Semantic code search and editing across codebase
- **context7**: Up-to-date documentation for third-party libraries
- **laravel-boost**: Laravel-specific tools and documentation

### When to use each tool:
- Use **serena** when:
  - Finding code by intent: "where do we handle authentication?"
  - Locating related functions across different files
  - Understanding code relationships and structure

- Use **context7** when:
  - Need latest library documentation (Laravel, packages)
  - Implementing new third-party integrations
  - Verifying API methods and syntax

- Use **laravel-boost** when:
  - Querying Eloquent models and database schemas
  - Searching Laravel-specific documentation
  - Running artisan commands or tinker
  - Checking application routes and configurations
  - Analyzing error logs

## Project Overview

**Bengkel Sampah** is a Laravel 10.x waste management platform with:
- **Mobile REST API**: User-facing API for waste deposit, points, events, and content
- **Admin Dashboard**: Full-featured web interface for management and reporting
- **Dual notification system**: Firebase FCM + WhatsApp (TCast API)
- **Multi-format exports**: PDF receipts, Excel/CSV reports

## Development Environment

### Running Commands with Docker

This project runs PHP through Docker containers with shared volume mounts. All commands must use the appropriate PHP container:

```bash
# Using PHP 8.2 container (this project requires PHP 8.1+)
docker exec -i -w /var/www/html/bengkelsampah-laravel php8.2 php artisan <command>
docker exec -i -w /var/www/html/bengkelsampah-laravel php8.2 composer <command>

# Examples:
docker exec -i -w /var/www/html/bengkelsampah-laravel php8.2 php artisan migrate
docker exec -i -w /var/www/html/bengkelsampah-laravel php8.2 composer install
```

**Note**: File paths are identical between WSL and Docker containers due to shared volume mounts.

### Common Development Commands

#### Database
```bash
# Run migrations
docker exec -i -w /var/www/html/bengkelsampah-laravel php8.2 php artisan migrate

# Seed database
docker exec -i -w /var/www/html/bengkelsampah-laravel php8.2 php artisan db:seed

# Fresh migration with seed
docker exec -i -w /var/www/html/bengkelsampah-laravel php8.2 php artisan migrate:fresh --seed
```

#### Cache & Configuration
```bash
# Clear all caches
docker exec -i -w /var/www/html/bengkelsampah-laravel php8.2 php artisan optimize:clear

# Cache configuration (for production)
docker exec -i -w /var/www/html/bengkelsampah-laravel php8.2 php artisan config:cache
docker exec -i -w /var/www/html/bengkelsampah-laravel php8.2 php artisan route:cache
docker exec -i -w /var/www/html/bengkelsampah-laravel php8.2 php artisan view:cache
```

#### API Documentation
```bash
# Generate Swagger/OpenAPI documentation
docker exec -i -w /var/www/html/bengkelsampah-laravel php8.2 php artisan l5-swagger:generate
```

#### Testing & Code Quality
```bash
# Run tests
docker exec -i -w /var/www/html/bengkelsampah-laravel php8.2 php artisan test

# Run specific test
docker exec -i -w /var/www/html/bengkelsampah-laravel php8.2 php artisan test --filter=UserTest

# Code formatting with Laravel Pint
docker exec -i -w /var/www/html/bengkelsampah-laravel php8.2 ./vendor/bin/pint

# Check code without fixing
docker exec -i -w /var/www/html/bengkelsampah-laravel php8.2 ./vendor/bin/pint --test
```

#### Dependencies
```bash
# Install PHP dependencies
docker exec -i -w /var/www/html/bengkelsampah-laravel php8.2 composer install

# Update dependencies
docker exec -i -w /var/www/html/bengkelsampah-laravel php8.2 composer update

# Install IDE helper (for development)
docker exec -i -w /var/www/html/bengkelsampah-laravel php8.2 php artisan ide-helper:generate
docker exec -i -w /var/www/html/bengkelsampah-laravel php8.2 php artisan ide-helper:models
```

#### Frontend Assets
```bash
# Install Node dependencies
npm install

# Development build
npm run dev

# Production build
npm run build
```

## Architecture Overview

### API Response Pattern

All API responses use `ResponseHelper` (autoloaded globally):

```php
// Success response
return ResponseHelper::success('Message', $data);

// Error response
return ResponseHelper::error('Error message', 400, $optionalData);
```

Response format includes `success`, `message`, `data`, and `timestamp` fields.

### Repository Pattern

The application implements the Repository Pattern for data access layer separation:

**Purpose**:
- Centralizes data access logic
- Enables query optimization and caching
- Improves testability by abstracting database queries

**Location**: `app/Repositories/`

**Available Repositories**:
- `AdminRepository.php` - Admin data access with eager loading
- `SetoranRepository.php` - Transaction queries with dashboard optimizations
- `UserRepository.php` - User queries with 10-minute caching
- `DashboardRepository.php` - Aggregated dashboard metrics

**Common Patterns**:
- Use eager loading to prevent N+1 queries
- Implement caching (10-minute TTL) for expensive queries
- Use selective column fetching to reduce memory usage
- Detailed PHPDoc blocks with array shape type definitions

**Example Usage**:
```php
// In a controller or service
public function __construct(private SetoranRepository $setoranRepository) {}

public function getDashboardData(array $filters): array
{
    return $this->setoranRepository->getForDashboard($filters);
}
```

### Service Layer Architecture

The application uses dedicated service classes for business logic and external integrations:

**External Integration Services**:
- `WhatsAppService` - TCast WhatsApp API integration for transaction notifications
- `FirebaseService` - Firebase Cloud Messaging for push notifications
- `NotificationService` - Orchestrates multi-channel notifications

**Business Logic Services**:
- `AdminService` (`app/Services/AdminService.php`) - Admin CRUD operations with business rules
  - Prevents deletion of the last admin (throws `AdminDeletionException`)
  - Handles password hashing and validation

**Dashboard Services** (`app/Services/Dashboard/`):
- `DashboardService` - Orchestrates dashboard data from multiple repositories
- `PeriodHelper` - Centralized period calculation logic
  - Supports: daily, weekly, monthly, six-monthly, yearly, custom date ranges
  - Eliminates duplicate period calculation code across controllers

**Pattern**: Services use constructor property promotion with dependency injection and implement business rules that don't belong in models or controllers.

### Form Request Validation Pattern

Admin-related operations use dedicated Form Request classes for validation:

**Location**: `app/Http/Requests/Admin/`

**Available Form Requests**:
- `DashboardFilterRequest.php` - Validates dashboard filter parameters (period, bank_sampah, status)
- `StoreAdminRequest.php` - Validates admin creation (name, username, email, password, role)
- `UpdateAdminRequest.php` - Validates admin updates (allows partial updates, password optional)

**Pattern**: Follow this approach for new admin features rather than inline controller validation.

**Example**:
```php
// Controller method signature
public function store(StoreAdminRequest $request)
{
    // $request->validated() contains validated data
    $admin = $this->adminService->createAdmin($request->validated());
    return ResponseHelper::success('Admin created', $admin);
}
```

### Authentication & Authorization

**Mobile API** (routes/api.php):
- Laravel Sanctum for stateless API authentication
- OTP verification via WhatsApp (TCast API)
- Middleware: `auth:sanctum` for protected routes

**Admin Dashboard** (routes/web.php):
- Session-based authentication via `AdminAuthController`
- Protected by `auth:admin` middleware
- Role-based access: admin vs branch-specific admins

### File Upload Architecture

Two upload systems exist due to storage requirements:

1. **Public Storage** (standard Laravel):
   - Article covers: `public/uploads/artikel_cover/`
   - Event covers: `public/uploads/event_cover/`
   - Event results: `public/uploads/event_results/`
   - Waste photos: `public/uploads/sampah/`

2. **External Storage** (for larger files):
   - Redeem proofs: `uploads/redeem/` (outside public directory)
   - Accessed via custom serving logic

### Notification System

**Dual WhatsApp Integration** via TCast:
- Environment variables: `TCAST_API_KEY`, `TCAST_CLIENT_ID`, `TCAST_SENDER_NUMBER`, `TCAST_META_TEMPLATE_ID`
- Phone number normalization: Converts `08xx`, `8xx`, `620xx` to `62xxx` format
- Template-based messages with dynamic parameters

**Firebase Cloud Messaging**:
- Service account configuration in `config/firebase.php`
- FCM tokens stored in `users.fcm_token` field
- Notification channel: `bengkelsampah_channel`

### Database Design Patterns

**Key Relationships**:
- Users → Addresses (one-to-many with default address)
- Users → Setorans (deposits/transactions)
- Setorans → BankSampah (waste bank where deposit is made)
- Users → Points (transaction history for both earning and redemption)
- Users → EventParticipants → Events

**Soft Deletes**:
Several models use soft deletes. Always use `forceDelete()` for permanent removal.

**Performance Indexes**:
The application uses composite indexes for common query patterns:
- Transaction status + date filtering
- Bank sampah + status combinations
- User transaction history
- Dashboard aggregation queries

When adding new queries, consider if composite indexes would improve performance for common filter combinations.

### Performance Optimization Strategy

Recent optimizations (commit eabbb37) significantly improved dashboard performance:

**Database Indexing** (migration: `2026_01_07_223657_add_dashboard_performance_indexes.php`):
- Added 8+ composite indexes for common filter combinations
- Indexes on: `status`, `bank_sampah_id`, `created_at`, `user_id`, and combinations
- Covering indexes where applicable

**Query Optimization Results**:
- Dashboard queries reduced: ~150 queries → ~15 queries
- Status-based queries: 10x faster
- Date range queries: 5x faster
- Bank sampah filtering: 8x faster

**Caching Strategy**:
- Repository-level caching with 10-minute TTL for expensive queries
- Use `Cache::remember()` for aggregated data
- Example: User statistics, dashboard metrics

**Best Practices**:
- Use `SetoranRepository` for transaction queries (optimized with indexes)
- Leverage eager loading in repositories to prevent N+1 queries
- Use selective column fetching (`select()`) when full models aren't needed
- Add composite indexes when filtering on multiple columns together

## Key Development Patterns

**Architectural Patterns in Use**:
- **Repository Pattern**: Data access layer in `app/Repositories/`
- **Service Layer**: Business logic in `app/Services/`
- **Form Requests**: Validation in `app/Http/Requests/`
- **Strict Types**: All new classes use `declare(strict_types=1)`
- **Constructor Promotion**: PHP 8 constructor property promotion for DI
- **PHPDoc Annotations**: Comprehensive type hints and array shapes

### Pilahku Integration

The system includes a waste validation system (`PilahkuCheckController`):
- Validates waste data and pricing against bank_sampah rules
- Real-time price checking for waste deposits
- Endpoint: `POST /api/pilahku/check`

### Dashboard Architecture

The admin dashboard uses a specialized architecture for performance:

**Components**:
1. `DashboardService` - Orchestrates data from multiple repositories
2. `PeriodHelper` - Centralized date range calculations
3. `DashboardRepository` - Aggregated metrics queries
4. `SetoranRepository` - Transaction data with optimizations
5. `DashboardFilterRequest` - Validates filter parameters

**Supported Periods**:
- `harian` (daily) - Today's data
- `mingguan` (weekly) - Last 7 days
- `bulanan` (monthly) - Current month
- `6bulanan` (six-monthly) - Last 6 months
- `tahunan` (yearly) - Current year
- Custom date range - Specify `tgl_awal` and `tgl_akhir`

**Usage Pattern**:
```php
// Controller
public function index(DashboardFilterRequest $request)
{
    $filters = $request->validated();
    $data = $this->dashboardService->getDashboardData($filters);
    return view('admin.dashboard', $data);
}
```

**Performance**: Uses composite indexes and caching to minimize database load.

### Transaction Lifecycle (Setoran)

Waste deposits follow this status flow:
1. `konfirmasi` - Initial submission
2. `diproses` - Bank staff processing
3. `selesai` - Completed (points awarded)
4. `batal` - Cancelled (user or admin initiated)

Points and XP are calculated automatically on completion based on transaction type (jual/donasi/tabung).

### Points System Logic

- **Earning**: Points awarded on completed transactions
- **Redemption**: Users request redemption with proof upload
- **Processing**: Admin reviews and approves/rejects redemptions
- Points table tracks both `setor` and `redeem` transaction types

### Export Functionality

The admin dashboard supports comprehensive exports:
- **PDF Reports**: DomPDF-based with custom templates
- **Excel Export**: PhpSpreadsheet with formatting
- **CSV Export**: Simple data dumps for analysis
- **Receipt Generation**: Transaction receipts for completed deposits

## Configuration Requirements

### Essential Environment Variables

```env
# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bengkelsampah
DB_USERNAME=root
DB_PASSWORD=

# Firebase FCM
FIREBASE_PROJECT_ID=your_project_id
FIREBASE_PRIVATE_KEY=your_private_key
FIREBASE_CLIENT_EMAIL=your_client_email
FIREBASE_SERVER_KEY=your_server_key

# TCast WhatsApp API
TCAST_API_KEY=your_api_key
TCAST_CLIENT_ID=your_client_id
TCAST_SENDER_NUMBER=your_sender_number
TCAST_META_TEMPLATE_ID=your_template_id
```

### Storage Setup

After installation, create the storage symlink:

```bash
docker exec -i -w /var/www/html/bengkelsampah-laravel php8.2 php artisan storage:link
```

## API Documentation

Interactive API documentation is available via L5-Swagger:
- **URL**: `/api/documentation`
- **Regenerate**: `php artisan l5-swagger:generate`
- **Config**: `config/l5-swagger.php`

## Testing Approach

The project uses PHPUnit for testing:
- **Unit tests**: `tests/Unit/`
- **Feature tests**: `tests/Feature/`
- Test environment automatically uses array cache/sessions
- Database connection for tests defined in `phpunit.xml`

## Common Troubleshooting

### Migration Issues
If migrations fail, check:
1. Database connection in `.env`
2. User permissions on database
3. Existing table conflicts (use `migrate:fresh` cautiously)

### File Upload Errors
Ensure:
1. Storage symlink exists: `php artisan storage:link`
2. Directory permissions: `chmod -R 775 storage public/uploads uploads`
3. Upload paths in config match actual directory structure

### WhatsApp Notification Failures
Verify:
1. TCast API credentials in `.env`
2. Phone number format conversion logic
3. Template ID matches TCast account
4. Check logs: `storage/logs/laravel.log`

### Firebase FCM Issues
Check:
1. Service account credentials are correct
2. FCM tokens are being stored in `users.fcm_token`
3. Firebase project settings allow FCM API v1

## IDE Helper

For better IDE autocomplete and type hints:

```bash
docker exec -i -w /var/www/html/bengkelsampah-laravel php8.2 php artisan ide-helper:generate
docker exec -i -w /var/www/html/bengkelsampah-laravel php8.2 php artisan ide-helper:models
```

This generates `.phpstorm.meta.php` and `_ide_helper.php` files for improved development experience.

===

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to enhance the user's satisfaction building Laravel applications.

## Foundational Context
This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.2.16
- laravel/framework (LARAVEL) - v10
- laravel/prompts (PROMPTS) - v0
- laravel/sanctum (SANCTUM) - v3
- laravel/mcp (MCP) - v0
- laravel/pint (PINT) - v1
- laravel/sail (SAIL) - v1
- phpunit/phpunit (PHPUNIT) - v10

## Conventions
- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts
- Do not create verification scripts or tinker when tests cover that functionality and prove it works. Unit and feature tests are more important.

## Application Structure & Architecture
- Stick to existing directory structure - don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling
- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Replies
- Be concise in your explanations - focus on what's important rather than explaining obvious details.

## Documentation Files
- You must only create documentation files if explicitly requested by the user.


=== boost rules ===

## Laravel Boost
- Laravel Boost is an MCP server that comes with powerful tools designed specifically for this application. Use them.

## Artisan
- Use the `list-artisan-commands` tool when you need to call an Artisan command to double check the available parameters.

## URLs
- Whenever you share a project URL with the user you should use the `get-absolute-url` tool to ensure you're using the correct scheme, domain / IP, and port.

## Tinker / Debugging
- You should use the `tinker` tool when you need to execute PHP to debug code or query Eloquent models directly.
- Use the `database-query` tool when you only need to read from the database.

## Reading Browser Logs With the `browser-logs` Tool
- You can read browser logs, errors, and exceptions using the `browser-logs` tool from Boost.
- Only recent browser logs will be useful - ignore old logs.

## Searching Documentation (Critically Important)
- Boost comes with a powerful `search-docs` tool you should use before any other approaches. This tool automatically passes a list of installed packages and their versions to the remote Boost API, so it returns only version-specific documentation specific for the user's circumstance. You should pass an array of packages to filter on if you know you need docs for particular packages.
- The 'search-docs' tool is perfect for all Laravel related packages, including Laravel, Inertia, Livewire, Filament, Tailwind, Pest, Nova, Nightwatch, etc.
- You must use this tool to search for Laravel-ecosystem documentation before falling back to other approaches.
- Search the documentation before making code changes to ensure we are taking the correct approach.
- Use multiple, broad, simple, topic based queries to start. For example: `['rate limiting', 'routing rate limiting', 'routing']`.
- Do not add package names to queries - package information is already shared. For example, use `test resource table`, not `filament 4 test resource table`.

### Available Search Syntax
- You can and should pass multiple queries at once. The most relevant results will be returned first.

1. Simple Word Searches with auto-stemming - query=authentication - finds 'authenticate' and 'auth'
2. Multiple Words (AND Logic) - query=rate limit - finds knowledge containing both "rate" AND "limit"
3. Quoted Phrases (Exact Position) - query="infinite scroll" - Words must be adjacent and in that order
4. Mixed Queries - query=middleware "rate limit" - "middleware" AND exact phrase "rate limit"
5. Multiple Queries - queries=["authentication", "middleware"] - ANY of these terms


=== php rules ===

## PHP

- Always use curly braces for control structures, even if it has one line.

### Constructors
- Use PHP 8 constructor property promotion in `__construct()`.
    - <code-snippet>public function __construct(public GitHub $github) { }</code-snippet>
- Do not allow empty `__construct()` methods with zero parameters.

### Type Declarations
- Always use explicit return type declarations for methods and functions.
- Use appropriate PHP type hints for method parameters.

<code-snippet name="Explicit Return Types and Method Params" lang="php">
protected function isAccessible(User $user, ?string $path = null): bool
{
    ...
}
</code-snippet>

## Comments
- Prefer PHPDoc blocks over comments. Never use comments within the code itself unless there is something _very_ complex going on.

## PHPDoc Blocks
- Add useful array shape type definitions for arrays when appropriate.

## Enums
- Typically, keys in an Enum should be TitleCase. For example: `FavoritePerson`, `BestLake`, `Monthly`.


=== laravel/core rules ===

## Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using the `list-artisan-commands` tool.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Database
- Always use proper Eloquent relationship methods with return type hints. Prefer relationship methods over raw queries or manual joins.
- Use Eloquent models and relationships before suggesting raw database queries
- Avoid `DB::`; prefer `Model::query()`. Generate code that leverages Laravel's ORM capabilities rather than bypassing them.
- Generate code that prevents N+1 query problems by using eager loading.
- Use Laravel's query builder for very complex database operations.

### Model Creation
- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `list-artisan-commands` to check the available options to `php artisan make:model`.

### APIs & Eloquent Resources
- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

### Controllers & Validation
- Always create Form Request classes for validation rather than inline validation in controllers. Include both validation rules and custom error messages.
- Check sibling Form Requests to see if the application uses array or string based validation rules.

### Queues
- Use queued jobs for time-consuming operations with the `ShouldQueue` interface.

### Authentication & Authorization
- Use Laravel's built-in authentication and authorization features (gates, policies, Sanctum, etc.).

### URL Generation
- When generating links to other pages, prefer named routes and the `route()` function.

### Configuration
- Use environment variables only in configuration files - never use the `env()` function directly outside of config files. Always use `config('app.name')`, not `env('APP_NAME')`.

### Testing
- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

### Vite Error
- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.


=== laravel/v10 rules ===

## Laravel 10

- Use the `search-docs` tool to get version specific documentation.
- Middleware typically live in `app/Http/Middleware/` and service providers in `app/Providers/`.
- Laravel 10 has a `bootstrap/app.php` file that creates the application instance and binds kernel contracts, but does not use it for application configuration like Laravel 11:
    - Middleware registration is in `app/Http/Kernel.php`
    - Exception handling is in `app/Exceptions/Handler.php`
    - Console commands and schedule registration is in `app/Console/Kernel.php`
    - Rate limits likely exist in `RouteServiceProvider` or `app/Http/Kernel.php`
- When using Eloquent model casts, you must use `protected $casts = [];` and not the `casts()` method. The `casts()` method isn't available on models in Laravel 10.


=== pint/core rules ===

## Laravel Pint Code Formatter

- You must run `vendor/bin/pint --dirty` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test`, simply run `vendor/bin/pint` to fix any formatting issues.


=== phpunit/core rules ===

## PHPUnit Core

- This application uses PHPUnit for testing. All tests must be written as PHPUnit classes. Use `php artisan make:test --phpunit {name}` to create a new test.
- If you see a test using "Pest", convert it to PHPUnit.
- Every time a test has been updated, run that singular test.
- When the tests relating to your feature are passing, ask the user if they would like to also run the entire test suite to make sure everything is still passing.
- Tests should test all of the happy paths, failure paths, and weird paths.
- You must not remove any tests or test files from the tests directory without approval. These are not temporary or helper files, these are core to the application.

### Running Tests
- Run the minimal number of tests, using an appropriate filter, before finalizing.
- To run all tests: `php artisan test`.
- To run all tests in a file: `php artisan test tests/Feature/ExampleTest.php`.
- To filter on a particular test name: `php artisan test --filter=testName` (recommended after making a change to a related file).
</laravel-boost-guidelines>
