# Installation

## Requirements

- A working Laravel application.
- Composer.

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
