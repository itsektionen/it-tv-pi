# ITTV π

Version π of the ITTV system.

## Dev Environment

_Use WSL if on Windows_

Install:

- Docker
- PNPM
- Composer
- PHP

> [!TIP]
> If you are running on Windows and the repo is cloned on an NTFS drive not in WSL then the project will be SLOWWWWWW. Clone it in WSL instead.

```bash
$ git clone git@github.com:itsektionen/it-tv-pi.git
$ cd it-tv-pi
$ composer install && pnpm install
```

Fix the .env, use an LLM here and just tell it to setup the .env using the .env.example for Laravel Sail

```bash
$ ./vendor/bin/sail up
```

and then while sail is up

```bash
$ pnpm dev
```

## TODO

- MQQT Legacy Integration

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.
