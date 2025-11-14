# Mini Wallet

Mini Wallet is a Laravel 12 + Vue 3 single-page application for sending money between users with real-time updates powered by Laravel Echo and Pusher. Balances are protected with concurrency-safe transfers, commissions are applied automatically, and the frontend refreshes instantly when new transactions land.

## Features

- **Real-time transfers:** Transactions broadcast to sender and receiver channels via Pusher, updating balances and history without a page refresh.
- **Concurrency-safe ledger:** Atomic DB transactions with row locking ensure balances stay accurate under high throughput.
- **Commission support:** A configurable 1.5% commission is debited from the sender on every successful transfer.
- **SPA experience:** Vue 3 + Inertia.js wallet dashboard with live validation, success/error feedback, and paginated history.
- **Dashboard insights:** Snapshot card for today’s balance and transfer volume, plus quick navigation and roadmap sections.
- **Secure authentication:** Sanctum SPA authentication with CSRF protection and cookie-based sessions.

## Requirements

- PHP ^8.2
- Composer
- Node.js ^18 & npm
- PostgreSQL or MySQL (or another database supported by Laravel)
- Redis or database queue driver for broadcasting (optional but recommended)
- Pusher Channels account (or compatible WebSocket broadcaster)

## Getting started

1. **Clone the repository**

   ```bash
   git clone https://github.com/thomsontochi/miniwalet.git
   cd miniwalet
   ```

2. **Install PHP dependencies**

   ```bash
   composer install
   ```

3. **Install frontend dependencies**

   ```bash
   npm install
   ```

4. **Bootstrap environment**

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

   Update `.env` with database credentials, Sanctum domains, and Pusher keys:

   ```dotenv
   APP_URL=http://miniwalet.test
   SANCTUM_STATEFUL_DOMAINS=miniwalet.test
   SESSION_DOMAIN=miniwalet.test

   BROADCAST_DRIVER=pusher
   PUSHER_APP_ID=your-app-id
   PUSHER_APP_KEY=your-app-key
   PUSHER_APP_SECRET=your-app-secret
   PUSHER_APP_CLUSTER=mt1
   ```

5. **Run migrations & seeders**

   ```bash
   php artisan migrate --seed
   ```

6. **Start the development servers**

   ```bash
   npm run dev
   php artisan serve
   ```

   Visit `http://miniwalet.test` (or the URL defined in your `.env`).

## Running tests

```bash
php artisan test
```

Pest is preconfigured for backend tests, and you can add feature/unit suites under the `tests/` directory.

## Broadcast setup

Mini Wallet ships with Laravel Echo and Pusher integration out of the box. To run broadcasting locally:

1. Create a Pusher Channels app and copy your credentials into `.env`.
2. Ensure the queue worker is running to dispatch broadcast jobs:

   ```bash
   php artisan queue:listen
   ```

3. If you prefer Laravel WebSockets or Ably, adjust `config/broadcasting.php` and `resources/js/app.ts` accordingly.

## Deployment checklist

- Cache configuration: `php artisan config:cache`
- Compile assets: `npm run build`
- Run database migrations: `php artisan migrate --force`
- Ensure queue/broadcast workers are supervised (e.g., Supervisor, Horizon)

## Project structure highlights

- `app/Services/WalletTransferService.php` – Atomic money transfer logic with commission handling.
- `app/Events/TransferCompleted.php` – Broadcast event for real-time updates.
- `resources/js/pages/wallet/Index.vue` – Wallet UI with live validation and Echo listeners.
- `resources/js/pages/Dashboard.vue` – Dashboard snapshot, quick links, and roadmap.
- `app/Http/Controllers/Api` – REST endpoints for transactions and dashboard stats.

## Credits

Built and maintained by [Austin Opia](https://github.com/thomsontochi). If you ship something cool with Mini Wallet, please share it and give proper attribution.

## License

This project is open-sourced under the [MIT license](LICENSE).
