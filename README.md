# 🛠️ Laravel Patch Commands

[![Run Tests](https://github.com/jonaaix/laravel-patches/actions/workflows/run-tests.yml/badge.svg)](https://github.com/aaix/laravel-patches/actions/workflows/run-tests.yml)
[![Latest Stable Version](https://img.shields.io/packagist/v/aaix/laravel-patches.svg)](https://packagist.org/packages/aaix/laravel-patches)
[![Total Downloads](https://img.shields.io/packagist/dt/aaix/laravel-patches.svg)](https://packagist.org/packages/aaix/laravel-patches)


A simple, command-based patching system for Laravel. Patches are designed to be hidden, trackable, and disposable one-off commands, ideal for data migrations, one-time fixes, or complex deployments.

---

## ✨ Features

* 🧩 **Command-Based:** Every patch is a full-fledged Artisan command.
* 📋 **Trackable:** The system automatically logs which patches have been run in a database table to prevent re-execution.
* 🗑️ **Disposable:** Simply delete the patch file when it's no longer needed.
* 🧠 **User-Controlled Execution:** Unlike migrations, patches are not run automatically. You decide which patch to run and when.

---

## 📦 Installation

1. **Require the package via Composer:**

   ```bash
   composer require aaix/laravel-patches
   ```

2. **📂 Publish Assets:**

   This package provides a migration for the patch log table and an optional configuration file. You have full control over what to publish using standard Artisan commands.

   *To publish the migration file:*

   ```bash
   php artisan vendor:publish --tag=patches-migrations
   ```

   *To optionally publish the configuration file for customization:*

   ```bash
   php artisan vendor:publish --tag=patches-config
   ```

3. **🛠️ Run the Migration:**

   After publishing the migration, run it to create the `patch_logs` table in your database.

   ```bash
   php artisan migrate
   ```

---

## 🚀 Usage

### 1️⃣ Create a Patch

Use the provided `make:patch` command to create a new patch class. The file will be placed in the `app/Console/Patches` directory by default.

```bash
php artisan make:patch FixUserEmails
```

This will generate a file like `app/Console/Patches/Patch_2025_06_22_FixUserEmails.php`.

---

### 2️⃣ Implement the Patch Logic

Open the newly created file and add your logic to the `handle()` method.

```php
// app/Console/Patches/Patch_2025_06_22_FixUserEmails.php

public function handle(): int
{
    $this->info('Fixing user emails...');

    // Your database queries or other logic here.
    \App\Models\User::whereNull('email_verified_at')->update(['is_active' => false]);

    $this->info('User emails fixed successfully.');

    return self::SUCCESS;
}
```

---

### 3️⃣ Run a Patch

Execute the patch from your terminal using its unique signature.

```bash
php artisan patch:2025_06_22_fix-user-emails
```

The system will run the patch and log its execution. If you try to run the same command again, it will be skipped.

---

### 4️⃣ Check Patch Status

To see which patches have been run and which are pending, use the `patch:status` command.

```bash
php artisan patch:status
```

This will display two clear, sorted lists:

```
✅ Ran Patches
+------------------------------------------------+
| Patch                                          |
+------------------------------------------------+
| Patch_2025_06_20_SomeOldPatch.php              |
+------------------------------------------------+

❌ Pending Patches
+------------------------------------------------+
| Patch                                          |
+------------------------------------------------+
| Patch_2025_06_22_FixUserEmails.php             |
+------------------------------------------------+
```

Patches that have been run and then deleted from the filesystem will not be shown, as they are considered irrelevant.

---

## ⚙️ Configuration

If you need to customize the package, you can publish the configuration file and find it at `config/patches.php`.

* `table`: The name of the database table for logging executed patches.
* `path`: The directory where your patch files are stored.
