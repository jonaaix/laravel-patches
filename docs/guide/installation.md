# Installation

## Requirements

- Laravel 12 or 13.
- PHP 8.2 or higher.
- Composer.

::: info
Support for Laravel 10 and 11 was dropped in 1.1.0 — both are past their security-fix window. Stay on 1.0.x if you need them.
:::

## 1. Require the package

```bash
composer require aaix/laravel-patches
```

## 2. Publish the configuration (optional)

If you'd like to customize the package, publish the config file:

```bash
php artisan vendor:publish --tag=patches-config
```

This creates `config/patches.php`. See [Configuration](./configuration) for the available options.

## 3. Run the migration

The package ships with a migration that creates the `patch_logs` table:

```bash
php artisan migrate
```

::: tip
If a future package update adds new migrations, they will be picked up automatically the next time you run `php artisan migrate`.
:::

You're ready to [create your first patch](./creating-a-patch).
