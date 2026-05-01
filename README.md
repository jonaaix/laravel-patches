<p align="center">
  <a href="https://github.com/jonaaix/laravel-patches">
    <img src="https://raw.githubusercontent.com/jonaaix/laravel-patches/main/resources/laravel-patches.webp" alt="Laravel Patches Logo" width="200">
  </a>
</p>

<h1 align="center">Laravel Patches</h1>

<p align="center">
A simple, command-based patching system for Laravel — hidden, trackable, and disposable one-off commands for data migrations, one-time fixes, and complex deployments.
</p>

<p align="center">
  <a href="https://packagist.org/packages/aaix/laravel-patches"><img src="https://img.shields.io/packagist/v/aaix/laravel-patches.svg?style=flat-square" alt="Latest Version on Packagist"></a>
  <a href="https://packagist.org/packages/aaix/laravel-patches"><img src="https://img.shields.io/packagist/dt/aaix/laravel-patches.svg?style=flat-square" alt="Total Downloads"></a>
  <a href="https://github.com/jonaaix/laravel-patches/actions/workflows/run-tests.yml"><img src="https://img.shields.io/github/actions/workflow/status/jonaaix/laravel-patches/run-tests.yml?branch=main&label=tests&style=flat-square" alt="GitHub Actions"></a>
  <a href="https://github.com/jonaaix/laravel-patches/blob/main/LICENSE"><img src="https://img.shields.io/packagist/l/aaix/laravel-patches.svg?style=flat-square" alt="License"></a>
</p>

---

## Features

- **Command-Based** — every patch is a full-fledged Artisan command. 
- **Trackable** — executed patches are logged in a database table to prevent re-execution.
- **Disposable** — delete the patch file when it's no longer needed.
- **User-Controlled** — unlike migrations, patches never run automatically. You decide which patch runs and when.

## Quickstart

```bash
composer require aaix/laravel-patches
php artisan migrate
php artisan make:patch FixUserEmails
php artisan patch
```

## Documentation

Full guide, configuration reference and usage examples: **[laravel-patches docs](https://jonaaix.github.io/laravel-patches/)**.

## License

Released under the [MIT License](LICENSE).
