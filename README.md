# Website Uptime Monitoring Application

A Laravel backend with Vue 3 frontend application for monitoring website uptime and sending email alerts.

## Features

- **Client Management**: Track clients with email addresses
- **Website Monitoring**: Monitor up to 10 websites per client
- **Automated Checks**: Checks website homepages every 15 minutes
- **Email Alerts**: Sends email notifications when websites go down
- **Web Interface**: Vue 3 SPA for viewing clients and their monitored websites

## Requirements

- PHP 8.1+
- MySQL/MariaDB
- Redis
- Node.js 16+
- Composer

## Installation

### Backend Setup

1. Navigate to the backend directory:
```bash
cd backend
```

2. Install PHP dependencies:
```bash
composer install
```

3. Copy the environment file:
```bash
cp .env.example .env
```

4. Generate application key:
```bash
php artisan key:generate
```

5. Configure your `.env` file with database and Redis settings:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=uptime_monitor
DB_USERNAME=root
DB_PASSWORD=

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp  # or ses for production
MAIL_FROM_ADDRESS="do-not-reply@example.com"
MAIL_FROM_NAME="UptimeMonitor"
```

6. Run migrations:
```bash
php artisan migrate
```

7. Set up the task scheduler in your cron (add to crontab):
```bash
* * * * * cd /path-to-project/backend && php artisan schedule:run >> /dev/null 2>&1
```

### Frontend Setup

1. Install Node.js dependencies (from project root):
```bash
npm install
```

2. Build the frontend for development:
```bash
npm run dev
```

3. Build for production:
```bash
npm run build
```

## Usage

### Adding Clients and Websites

Clients and websites should be manually entered into the database during deployment. You can use Laravel Tinker:

```bash
php artisan tinker
```

```php
// Create a client
$client = App\Models\Client::create(['email' => 'client@example.com']);

// Add websites for the client
$client->websites()->create(['url' => 'https://example.com']);
$client->websites()->create(['url' => 'https://another-site.com']);
```

Or use SQL directly:

```sql
INSERT INTO clients (email, created_at, updated_at) 
VALUES ('client@example.com', NOW(), NOW());

INSERT INTO websites (client_id, url, is_up, created_at, updated_at) 
VALUES (1, 'https://example.com', 1, NOW(), NOW());
```

### Monitoring

The application automatically monitors all websites every 15 minutes via Laravel's task scheduler. Make sure the scheduler cron job is set up (see Installation step 7).

### Email Configuration

For production, configure your mail settings in `.env`:

- **SES (Recommended)**: Set `MAIL_MAILER=ses` and configure AWS credentials
- **SMTP**: Configure SMTP settings if using a different provider

## API Endpoints

- `GET /api/v1/clients` - Get all clients
- `GET /api/v1/clients/{id}/websites` - Get websites for a specific client

## Project Structure

```
.
├── backend/              # Laravel backend
│   ├── app/
│   │   ├── Models/      # Eloquent models
│   │   ├── Services/    # Business logic (monitoring service)
│   │   ├── Notifications/  # Email notifications
│   │   └── Http/        # Controllers, middleware
│   ├── database/        # Migrations, seeders
│   └── config/          # Configuration files
├── resources/
│   └── js/              # Vue 3 frontend
│       ├── app.js       # Application entry point
│       ├── App.vue      # Root component
│       └── pages/       # Page components
└── package.json         # Node.js dependencies
```

## Development

Start the development server for Vue:
```bash
npm run dev
```

Start Laravel development server:
```bash
cd backend
php artisan serve
```

## Production Deployment

1. Build the frontend:
```bash
npm run build
```

2. Optimize Laravel:
```bash
cd backend
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

3. Ensure the scheduler cron is running

4. Configure queue worker (if using queues):
```bash
php artisan queue:work redis
```

## Notes

- The application monitors websites every 15 minutes with a 10-second timeout
- Email alerts are sent when a website goes from "up" to "down" status
- Supports hundreds of clients with up to 10 websites each
- No authentication is included as the site won't be publicly accessible
