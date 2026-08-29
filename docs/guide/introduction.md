# Introduction

**Laravel Patches** is a simple, command-based patching system for Laravel. Patches are hidden, trackable Artisan commands that repair or clean up data at a specific point in your project's history.

## Why Patches?

Laravel migrations are perfect for evolving your database schema, but they aren't well suited for everything. Sometimes you need to:

- Correct values that a since-fixed bug corrupted.
- Normalise inconsistent data from a legacy or manual import.
- Apply a fix in production without re-running it on every deploy.

Patches fill that gap. They live as Artisan commands you can run on demand, and the package keeps a log so you can tell at a glance which patches have already been applied.

## What belongs elsewhere

A schema change and the data move it implies belong together in **one migration**: backfilling a new column before making it `NOT NULL`, copying rows into a restructured table, splitting or merging columns. Those steps must run in every environment for the release to be correct, so they are part of the release — not a patch.

And anything still needed once every environment has applied it — a recurring cleanup, a scheduled normalisation — is a regular Artisan command, not a patch.

Re-running a patch is fine, though: if the same damage reappears before the root cause was fixed, run it again. The log tracks how often it ran.

## Feature Overview

- **Command-Based** — Every patch is a full-fledged Artisan command.
- **Trackable** — Executed patches are recorded in a `patch_logs` table to prevent accidental re-execution.
- **Disposable** — Delete the patch file when it's no longer needed; the log entry remains for history.
- **User-Controlled Execution** — Patches never run automatically. You decide which patch to run and when.

## Next Steps

- [Installation](./installation) — install the package and run the migration.
- [Creating a Patch](./creating-a-patch) — scaffold and implement your first patch.
- [Running Patches](./running-patches) — interactive and non-interactive execution.
