# 🛠️ Laravel Patch Commands

[![Run Tests](https://github.com/jonaaix/laravel-patches/actions/workflows/run-tests.yml/badge.svg)](https://github.com/aaix/laravel-patches/actions/workflows/run-tests.yml)
[![Latest Stable Version](https://img.shields.io/packagist/v/aaix/laravel-patches.svg)](https://packagist.org/packages/aaix/laravel-patches)
[![Total Downloads](https://img.shields.io/packagist/dt/aaix/laravel-patches.svg)](https://packagist.org/packages/aaix/laravel-patches)

A simple, command-based patching system for Laravel. Patches are designed to be hidden, trackable, and disposable one-off
commands, ideal for data migrations, one-time fixes, or complex deployments.

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

2. **📂 Configuration (Optional):**

   If you want to customize the configuration, you can optionally publish the config file:

   ```bash
   php artisan vendor:publish --tag=patches-config
   ```

3. **🛠️ Run the Migration:**

   Simply run the migration command to create the patch_logs table:

   ```bash
   php artisan migrate
   ```

If you update this package in the future and additional migrations are added, they will be automatically detected the next time
you run php artisan migrate.

---

## 🚀 Usage

### 1️⃣ Create a Patch

Use the provided `make:patch` command to create a new patch class. The file will be placed in the `app/Console/Commands/Patches`
directory by default.

```bash
php artisan make:patch FixUserEmails
```

This will generate a file like `app/Console/Commands/Patches/Patch_2025_06_30_FixUserEmails.php`.

---

### 2️⃣ Implement the Patch Logic

Open the newly created file and add your logic to the `handle()` method.

```php
// app/Console/Commands/Patches/Patch_2025_06_30_FixUserEmails.php

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

### 3️⃣ Run Patches

**A) Interactively (Recommended)**

Run the main `patch` command to get an interactive list of all pending patches. You can select one or multiple patches to run.

```bash
php artisan patch
```

This will present a prompt like this:

```bash
? Which (pending) patches would you like to run?
  [ ] Patch_2025_06_30_FixUserEmails.php
  [ ] Patch_2025_07_01_AnotherFix.php
```

**B) Run a Specific Patch by Signature**
If you know the exact signature of a patch, you can run it directly. The signature is generated automatically when you create the
patch.

```bash
php artisan patch:2025_06_30_fix-user-emails
```

The system will run the patch and log its execution. If you try to run the same command again, you will be asked for confirmation.

**C) Non-Interactively (for deployments)**
You can run patches non-interactively using the `--all` or `--patch` flags.

- Run a specific patch by name:
   ```bash
   php artisan patch --patch=Patch_2025_06_30_FixUserEmails
   ```
- Run all pending patches:
   ```bash
  php artisan patch --all
   ```
  ⚠️ Warning: The --all flag should be used with extreme caution. Patches are designed for controlled, deliberate execution.
  Running all pending patches at once, especially in a production environment, can lead to unintended consequences if the order or
  combination of patches has not been thoroughly tested. It is highly recommended to run patches individually or in tested groups.

---

### 4️⃣ Check Patch Status

To see which patches have been run and which are pending, use the patch:status command.

```bash
php artisan patch:status
```

This will display two clear, sorted lists:

```
✅ Ran Patches
+------------------------------------------+
| Patch Name                               |
+------------------------------------------+
| Patch_2025_06_20_SomeOldPatch.php        |
+------------------------------------------+

❌ Pending Patches
+------------------------------------------+
| Patch Name                               |
+------------------------------------------+
| Patch_2025_06_30_FixUserEmails.php       |
+------------------------------------------+
```

Patches that have been run and then deleted from the filesystem will not be shown, as they are considered irrelevant.

---

### 5️⃣ Syncing Existing Patches

If you are integrating this package into a project that already has numerous patch files, you might want to log them in the database without re-running their code. The `patch:sync` command allows you to do this.

It will find all patch files that are not in the `patch_logs` table and let you interactively select which ones to mark as "run".

```bash
php artisan patch:sync
```

---

## ⚙️ Configuration

If you need to customize the package, you can publish the configuration file and find it at `config/patches.php`.

* `table`: The name of the database table for logging executed patches.
* `path`: The directory where your patch files are stored.
