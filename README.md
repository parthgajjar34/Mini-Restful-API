<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About this Project

This is a mini RestFul API project. This application has following abilities:

- Register and Login with two different user Roles i.e Customer and Lender.
- Apply Loan Application.
- Approve Loan
- Repayment

### Prerequisites:
    - Apache2 / Nginx webserver running PHP 8.1, MYSQL >= 5.7.x 
    - Composer >= 1.10
    - Laravel Passport for authentication

### Install and Setup Steps:
    1. Run: "composer install"
    2. Copy ".env.example to .env"
    3. Run: "php artisan migrate"
    4. Run: "php artisan passport:install"
    5. To Run tests suits, Run command: "/vendor/bin/phpunit tests/"
    6. Create below directories to the storage folder and give it the correct folder permission in the case of linux/unit os:
    "framework/cache/", "framework/sessions/", "framework/views/"

### To run a application:
    Run: "php artisan serve" and open "http://127.0.0.1:8000" to your browser or API request client

### Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

### Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

### Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

### License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
