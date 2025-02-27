# Yandex Music Parser

This Laravel application parses artist and track data from Yandex Music and stores it in a MySQL database.

## Requirements
- PHP 7.4+
- Laravel 8+
- MySQL Database

## Setup

1. Clone the repository and run `composer install` to install dependencies.
2. Configure `.env` for database connection.
3. Run `php artisan migrate` to create necessary tables.
4. Use the endpoint `/parse-artist/{artistId}` to parse and save artist and track data.

## Usage
To parse and save artist data, simply navigate to the URL:
http://localhost/parse-artist/{artistId}

Where `{artistId}` is the ID of the artist you want to parse.