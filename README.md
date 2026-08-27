# Marketplace Project

A modern marketplace platform that allows users to browse products, manage listings, place orders, and interact with sellers through a secure and user-friendly interface.

## Features

* User registration and authentication
* User profiles
* Product listings
* Product categories
* Product search and filtering
* Product details and images
* Shopping cart
* Checkout and order management
* Seller/product management
* Order history
* Real-time notifications
* Real-time features using Laravel Reverb
* Responsive design
* Secure API and backend architecture

## Tech Stack

### Backend

* Laravel
* PHP
* MySQL
* Laravel Reverb
* Laravel Sanctum / Authentication

### Frontend

* Blade / Vue / React
* JavaScript
* Tailwind CSS

> Update the frontend technologies above according to the technologies used in the project.

## Requirements

Before running the project, make sure you have:

* PHP 8.x or higher
* Composer
* MySQL
* Node.js and npm
* Laravel
* A configured `.env` file

## Installation

### 1. Clone the repository

```bash
git clone <repository-url>
cd <project-directory>
```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Install frontend dependencies

```bash
npm install
```

### 4. Configure environment

Copy the example environment file:

```bash
cp .env.example .env
```

Generate the application key:

```bash
php artisan key:generate
```

Update your `.env` file with your database and application configuration.

Example:

```env
APP_NAME=Marketplace
APP_ENV=local
APP_DEBUG=true

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=marketplace
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Run migrations

```bash
php artisan migrate
```

If the project includes seeders:

```bash
php artisan db:seed
```

Or run both together:

```bash
php artisan migrate --seed
```

### 6. Create storage link

```bash
php artisan storage:link
```

### 7. Build frontend assets

For development:

```bash
npm run dev
```

For production:

```bash
npm run build
```

## Running the Project

Start the Laravel development server:

```bash
php artisan serve
```

The application will normally be available at:

```text
http://127.0.0.1:8000
```

### Running Laravel Reverb

If the project uses Laravel Reverb for real-time functionality, start the Reverb server:

```bash
php artisan reverb:start
```

For debugging:

```bash
php artisan reverb:start --debug
```

You may need to run the frontend development server and Reverb in separate terminals:

```bash
npm run dev
```

```bash
php artisan reverb:start
```

## Queue Worker

If the project uses queued jobs, run the queue worker:

```bash
php artisan queue:work
```

For local development, you may need three terminals:

**Terminal 1 — Laravel**

```bash
php artisan serve
```

**Terminal 2 — Frontend**

```bash
npm run dev
```

**Terminal 3 — Reverb**

```bash
php artisan reverb:start
```

If queues are required:

**Terminal 4 — Queue**

```bash
php artisan queue:work
```

## Database

The marketplace database contains the core entities required for managing the platform, such as:

* Users
* Products
* Categories
* Sellers
* Orders
* Order Items
* Cart Items
* Payments
* Notifications

The exact database structure depends on the current implementation.

## Environment Configuration

Important environment variables may include:

```env
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=marketplace
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_CONNECTION=reverb

REVERB_APP_ID=
REVERB_APP_KEY=
REVERB_APP_SECRET=
REVERB_HOST=127.0.0.1
REVERB_PORT=8080
REVERB_SCHEME=http
```

Never commit sensitive credentials or production secrets to the repository.

## Testing

Run the application's test suite with:

```bash
php artisan test
```

You can also run:

```bash
./vendor/bin/phpunit
```

## Code Formatting

Before committing changes, make sure the code follows the project's coding standards.

For Laravel projects using Laravel Pint:

```bash
./vendor/bin/pint
```

## Useful Artisan Commands

Clear application caches:

```bash
php artisan optimize:clear
```

Run migrations:

```bash
php artisan migrate
```

Rollback migrations:

```bash
php artisan migrate:rollback
```

Create a new migration:

```bash
php artisan make:migration create_example_table
```

Create a model:

```bash
php artisan make:model Example -m
```

List routes:

```bash
php artisan route:list
```

## Project Structure

```text
marketplace/
├── app/
│   ├── Http/
│   ├── Models/
│   ├── Events/
│   └── ...
├── bootstrap/
├── config/
├── database/
│   ├── migrations/
│   └── seeders/
├── public/
├── resources/
│   ├── css/
│   ├── js/
│   └── views/
├── routes/
│   ├── web.php
│   ├── api.php
│   └── channels.php
├── storage/
├── tests/
├── .env.example
├── composer.json
├── package.json
└── README.md
```

## Git Workflow

Create a feature branch before making changes:

```bash
git checkout -b feature/your-feature-name
```

After making your changes:

```bash
git add .
git commit -m "Add your feature"
git push origin feature/your-feature-name
```

Create a pull request for review.

## Troubleshooting

### Clear Laravel cache

```bash
php artisan optimize:clear
```

### Rebuild frontend dependencies

```bash
rm -rf node_modules
npm install
npm run dev
```

### Recreate the database

> **Warning:** This deletes existing database data.

```bash
php artisan migrate:fresh --seed
```

### Reverb is not connecting

Make sure Reverb is running:

```bash
php artisan reverb:start
```

Check that the Reverb variables in `.env` match the frontend configuration and restart your development servers after changing environment variables.

## Security

If you discover a security vulnerability, please report it privately to the project maintainers rather than opening a public issue.

Do not commit:

* `.env` files
* API keys
* Passwords
* Database credentials
* Private certificates
* Production secrets

## License

This project is proprietary unless otherwise specified by the project owner.

## Contributors

Developed and maintained by the Marketplace Project Team.
