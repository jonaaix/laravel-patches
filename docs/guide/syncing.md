# Syncing Existing Patches

If you're integrating this package into a project that already has numerous patch files, you probably don't want to re-run them — you just want them recorded as "already executed".

The `patch:sync` command does exactly that. It finds patch files that aren't in the `patch_logs` table and lets you interactively select which ones to mark as run.

```bash
php artisan patch:sync
```

This is a safe way to onboard an existing codebase without accidentally executing legacy patch logic.
