# Blast/Vaitel CRM

Vaitel/Blast laravel test.

A demo application to illustrate how Inertia.js works.

![](https://raw.githubusercontent.com/inertiajs/blast-vaitel-crm/master/presentation.gif)

## Installation

Clone the repo locally:

```sh
git clone git@github.com:Whallas/vaitel-laravel-test.git
cd vaitel-laravel-test
```

Install PHP dependencies:

```sh
composer install
```

Install NPM dependencies:

```sh
npm ci
```

Build assets:

```sh
npm run dev
```

Setup configuration:

```sh
cp .env.example .env
```

Generate application key:

```sh
php artisan key:generate
```

Create an SQLite database. You can also use another database (MySQL, Postgres), simply update your configuration accordingly.

```sh
touch database/database.sqlite
```

Run database migrations and seeders:

```sh
php artisan migrate --seed
```

Run the dev server (the output will give the address):

```sh
php artisan serve
```

You're ready to go! Visit Blast/Vaitel CRM in your browser, and login with:

- **Username:** johndoe@example.com
- **Password:** secret

#
## Running tests

To run the Blast/Vaitel CRM tests, run:

```
php artisan test
```
