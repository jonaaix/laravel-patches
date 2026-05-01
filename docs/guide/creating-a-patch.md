# Creating a Patch

## Scaffold a new patch

Use the provided `make:patch` command. By default, files are placed in `app/Console/Commands/Patches`.

```bash
php artisan make:patch FixUserEmails
```

This generates a file like:

```
app/Console/Commands/Patches/Patch_2025_06_30_FixUserEmails.php
```

The date prefix and signature are generated automatically so patches stay sortable and uniquely identifiable.

## Implement the patch logic

Open the new file and add your logic to the `handle()` method:

```php
// app/Console/Commands/Patches/Patch_2025_06_30_FixUserEmails.php

public function handle(): int
{
    $this->info('Fixing user emails...');

    \App\Models\User::whereNull('email_verified_at')->update(['is_active' => false]);

    $this->info('User emails fixed successfully.');

    return self::SUCCESS;
}
```

A patch is just an Artisan command, so anything you can do in a command — dependency injection, prompts, progress bars, transactions — is available here.

## What's next?

- [Run your patch](./running-patches) interactively or in a deployment.
- [Check patch status](./patch-status) to see what has been run.
