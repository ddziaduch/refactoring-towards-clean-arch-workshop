# Install

## Preferred installation via containers:
1. Install locally Docker Desktop/Rancher Desktop.
2. Clone the repository
3. Open the repository folder in your terminal
4. Run the command: `docker compose —-build`
5. Wait till it pulls & builds all the images
6. Run `docker compose run vendor/bin/phpunit` to verify whether all works as expected

## Host installation without containers:
1. Install PHP 8.3
2. Install PostgreSQL database (or any other compatible with Doctrine)
3. Clone the repository
4. Put .env.local, and set the correct DB host, user, password and database
5. Open the repository folder in your terminal
6. Run `vendor/bin/phpunit` to verify whether all works as expected
