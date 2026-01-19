# Website Uptime Monitoring Application

A Laravel backend with Vue 3 frontend application for monitoring website uptime and sending email alerts.

## Features

- **Client Management**: Track clients with email addresses
- **Website Monitoring**: Monitor up to 10 websites per client
- **Automated Checks**: Checks website homepages every 15 minutes with 10-second timeout
- **Automatic Monitoring**: Websites are automatically checked when created (Observer pattern)
- **Email Alerts**: Sends email notifications when websites transition from UP to DOWN
- **Spam Prevention**: Emails only sent on UP→DOWN transition, not repeatedly
- **Web Interface**: Vue 3 SPA for viewing clients and their monitored websites with confirmation dialogs

## Requirements

- PHP 8.1+
- MySQL/MariaDB
- Node.js 16+
- Composer

**Note**: Redis is optional. The application can use database queues and file cache for development.

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

5. Configure your `.env` file with database and mail settings:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=uptime_monitor
DB_USERNAME=root
DB_PASSWORD=your_password

QUEUE_CONNECTION=sync  # or database for production

MAIL_MAILER=smtp  # or ses for production
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="do-not-reply@example.com"
MAIL_FROM_NAME="UptimeMonitor"
```

**For Gmail SMTP**: You need to create an App Password (not your regular password). See Gmail account settings → Security → App Passwords.

6. Run migrations:
```bash
php artisan migrate
```

7. Set up the task scheduler:

**Linux/Mac** (add to crontab):
```bash
* * * * * cd /path-to-project/backend && php artisan schedule:run >> /dev/null 2>&1
```

**Windows** (use Task Scheduler):
- Create a task that runs every minute
- Program: `C:\path\to\php.exe`
- Arguments: `artisan schedule:run`
- Start in: `C:\path\to\project\backend`
- Trigger: Repeat every 1 minute, indefinitely

Alternatively, you can manually run monitoring:
```bash
php artisan websites:monitor
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

The application automatically monitors all websites every 15 minutes via Laravel's task scheduler. Websites are also automatically checked when they are created.

**Key Features:**
- Checks websites every 15 minutes (scheduled)
- 10-second timeout for each check
- Automatically checks newly created websites immediately
- Updates `is_up` status and `last_checked_at` timestamp
- Detects HTTP errors (4xx, 5xx) and connection failures

**Manual Testing:**
```bash
php artisan websites:monitor
```

### Email Configuration

Email notifications are sent automatically when a website transitions from UP to DOWN.

**Email Details:**
- **From**: `do-not-reply@example.com`
- **Subject**: `{website URL} is down!`
- **Body**: `{website URL} is down!`
- **To**: Client's email address
- **Timing**: Sent immediately when UP→DOWN transition occurs (no spam)

**Configuration Options:**

- **Gmail SMTP** (for development):
  ```env
  MAIL_MAILER=smtp
  MAIL_HOST=smtp.gmail.com
  MAIL_PORT=587
  MAIL_USERNAME=your-email@gmail.com
  MAIL_PASSWORD=your-app-password
  MAIL_ENCRYPTION=tls
  ```

- **AWS SES** (for production - recommended):
  ```env
  MAIL_MAILER=ses
  AWS_ACCESS_KEY_ID=your-key
  AWS_SECRET_ACCESS_KEY=your-secret
  ```

**Note**: With Gmail SMTP, the From address may appear as your Gmail account due to Gmail's security restrictions. AWS SES supports custom From addresses fully.

## API Endpoints

- `GET /api/v1/clients` - Get all clients
- `GET /api/v1/clients/{id}/websites` - Get websites for a specific client

## Project Structure

```
.
├── backend/                  # Laravel backend
│   ├── app/
│   │   ├── Console/
│   │   │   ├── Commands/    # Artisan commands (monitoring, email testing)
│   │   │   └── Kernel.php   # Scheduler configuration
│   │   ├── Models/          # Eloquent models (Client, Website)
│   │   ├── Services/        # Business logic (WebsiteMonitorService)
│   │   ├── Notifications/   # Email notifications
│   │   ├── Observers/       # Model observers (auto-monitoring)
│   │   ├── Http/
│   │   │   └── Controllers/ # API controllers
│   │   └── Providers/       # Service providers
│   ├── database/
│   │   ├── migrations/      # Database schema
│   │   └── seeders/         # Sample data
│   └── config/              # Configuration files
├── resources/
│   └── js/                  # Vue 3 frontend
│       ├── app.js           # Application entry point
│       ├── App.vue          # Root component
│       └── pages/
│           └── Home.vue     # Main page with client selector
└── package.json             # Node.js dependencies
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

3. Ensure the scheduler cron is running (see Installation step 7)

4. Emails send immediately (no queue worker needed)

## Testing

**Test Website Monitoring:**
```bash
php artisan websites:monitor
```

**Test Email Notifications:**
```bash
php artisan test:email client@example.com "https://httpstat.us/500"
```

## Notes

- The application monitors websites every 15 minutes with a 10-second timeout
- Email alerts are sent **only** when a website goes from "up" to "down" status (prevents spam)
- Websites are automatically checked when created via Observer pattern
- Supports hundreds of clients with up to 10 websites each
- No authentication is included as the site won't be publicly accessible
- Frontend includes confirmation dialog before visiting websites