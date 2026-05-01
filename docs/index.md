---
layout: home

hero:
   name: Laravel Patches
   text: Trackable one-off commands for Laravel.
   tagline: A simple, command-based patching system — hidden, trackable, and disposable patches for data migrations, one-time fixes, and complex deployments.
   image:
      src: /logo.webp
      alt: Laravel Patches logo
   actions:
      - theme: brand
        text: Get Started
        link: /guide/introduction
      - theme: alt
        text: View on GitHub
        link: https://github.com/jonaaix/laravel-patches

features:
   - title: Command-Based
     details: Every patch is a full-fledged Artisan command — use the framework you already know.
   - title: Trackable
     details: The system automatically logs which patches have been run in a database table to prevent re-execution.
   - title: Disposable
     details: Simply delete the patch file when it's no longer needed. The log keeps the history.
   - title: User-Controlled
     details: Unlike migrations, patches are never run automatically. You decide which patch runs and when.
---
