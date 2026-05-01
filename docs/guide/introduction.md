# Introduction

**Laravel Patches** is a simple, command-based patching system for Laravel. Patches are designed to be hidden, trackable, and disposable one-off commands, ideal for data migrations, one-time fixes, or complex deployments.

## Why Patches?

Laravel migrations are perfect for evolving your database schema, but they aren't well suited for everything. Sometimes you need to:

- Backfill or correct data after a release.
- Apply a one-off fix in production without re-running it on every deploy.
- Coordinate a multi-step change that doesn't belong in a migration.

Patches fill that gap. They live as Artisan commands you can run on demand, and the package keeps a log so you can tell at a glance which patches have already been applied.

## Feature Overview

- **Command-Based** — Every patch is a full-fledged Artisan command.
- **Trackable** — Executed patches are recorded in a `patch_logs` table to prevent accidental re-execution.
- **Disposable** — Delete the patch file when it's no longer needed; the log entry remains for history.
- **User-Controlled Execution** — Patches never run automatically. You decide which patch to run and when.

## Next Steps

- [Installation](./installation) — install the package and run the migration.
- [Creating a Patch](./creating-a-patch) — scaffold and implement your first patch.
- [Running Patches](./running-patches) — interactive and non-interactive execution.
