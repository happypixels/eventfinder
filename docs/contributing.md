# Contributing

## Getting started
1. Clone this repository.
2. Run `composer install` to install PHP dependencies.
3. Run `yarn && yarn run dev` to install NPM dependencies and compile assets.
4. Set up your .env file.
5. Run `php artisan migrate` to migrate your database.
6. Run `php artisan db:seed` to generate test data.

You should now be ready to visit your test domain for this project!

## Suggested development environment and tools
To make the process of contributing as smooth as possible we suggest using tools and services that run well with the Laravel backend, although none of these are required. Our suggested environment includes:
* Laravel Valet which serves as your local webserver ([https://laravel.com/docs/master/valet](https://laravel.com/docs/master/valet))
* Mailtrap which will safely catch any outgoing email: ([https://mailtrap.io](https://mailtrap.io))
* Sequel Pro for managing your database ([https://www.sequelpro.com](https://www.sequelpro.com))

## Coding style
We follow the PSR-2 coding standard, but don't worry if your styling isn't perfect. It will be fixed by [https://styleci.io/](StyleCI) when your pull request is merged.
