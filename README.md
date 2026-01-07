# Install

## Preferred installation via containers:
Run `./install.sh`

## Host installation without containers:
1. Install PHP 8.3
2. Install PostgreSQL database (or any other compatible with Doctrine)
3. Clone the repository
4. Put .env.local, and set the correct DB host, user, password and database
5. Create test db and run migrations
6. Run `vendor/bin/phpunit` to verify whether all works as expected
