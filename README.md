# Epic Explore API

A Laravel-based API featuring a robust gamification system (XP, Levels, Quests) and exploration mechanics. This project provides a powerful backend for gamified user experiences, integrating modern PHP and architectural patterns.

## Features

- **User Accounts & Authentication**: Secure user management and registration.
- **Gamification Engine**: Earn XP, level up, and maintain streaks.
- **Quests & Rewards system**: Complete daily and epic quests for rewards.
- **AI-powered Recommendations**: Intelligent suggestions for user engagement.
- **Automated API Documentation**: Full documentation powered by Scribe.
- **Comprehensive API Tests**: Built-in test coverage for core features.

## Getting Started

Follow these steps to set up the project locally.

### Prerequisites

- PHP >= 8.2
- Composer
- MySQL or PostgreSQL database
- Git

### Installation

1. **Clone the repository:**

    ```bash
    git clone <repository-url>
    cd Epic-Explore-Api
    ```

2. **Install dependencies:**

    ```bash
    composer install
    ```

3. **Set up the environment file:**

    ```bash
    cp .env.example .env
    ```

    _Edit the `.env` file and configure your database (`DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`)._

4. **Generate the application key:**

    ```bash
    php artisan key:generate
    ```

5. **Run database migrations (and seeders if applicable):**

    ```bash
    php artisan migrate --seed
    ```

6. **Start the local development server:**
    ```bash
    php artisan serve
    ```
    The API will be available at `http://localhost:8000`.

## Postman Collection

For easy testing and exploration of the API endpoints, Postman collections are included in the root directory:

- `epic_explore_api_full.postman_collection.json` (Recommended: Includes all endpoints)

**How to import into Postman:**

1. Open Postman.
2. Click on **Import** (top left).
3. Drag and drop the `epic_explore_api_full.postman_collection.json` file, or select it from your file explorer.
4. Set up your environment variables in Postman (like the base URL and auth tokens) to start making requests.

## API Documentation

This project uses [Scribe](https://scribe.knuckles.wtf/laravel) to automatically generate API documentation.

You can view the documentation by running the project and navigating to the `/docs` route, or serving the `public/docs` directory.

To regenerate the documentation after updating routes/controllers:

```bash
php artisan scribe:generate
```

## Running Tests

To run the automated tests for this project:

```bash
php artisan test
```
