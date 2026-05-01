# Running Patches

Patches can be executed in three ways: interactively, by signature, or non-interactively for deployments.

## A) Interactively (recommended)

Run the main `patch` command to see an interactive list of all pending patches. You can select one or multiple patches to run.

```bash
php artisan patch
```

You'll see a prompt like:

```
? Which (pending) patches would you like to run?
  [ ] Patch_2025_06_30_FixUserEmails.php
  [ ] Patch_2025_07_01_AnotherFix.php
```

## B) Run a specific patch by signature

If you know the signature of a patch, you can run it directly. The signature is generated automatically when the patch is created.

```bash
php artisan patch:2025_06_30_fix-user-emails
```

The system runs the patch and logs its execution. If you try to run the same command again, you'll be asked to confirm.

## C) Non-interactively (for deployments)

For automated environments use the `--patch` or `--all` flags.

Run a specific patch by name:

```bash
php artisan patch --patch=Patch_2025_06_30_FixUserEmails
```

Run all pending patches:

```bash
php artisan patch --all
```

::: warning
The `--all` flag should be used with extreme caution. Patches are designed for controlled, deliberate execution. Running every pending patch at once — especially in production — can lead to unintended consequences if the order or combination of patches has not been thoroughly tested. Prefer running patches individually or in tested groups.
:::
