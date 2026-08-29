# Patch Status

To see which patches have run and which are still pending, use the `patch:status` command:

```bash
php artisan patch:status
```

The output displays two clear, sorted lists:

```
✅ Ran Patches
+------------------------------------+----------------------------------------------+
| Patch Name                         | Description                                  |
+------------------------------------+----------------------------------------------+
| Patch_2025_06_20_SomeOldPatch      | Remove duplicate address records             |
+------------------------------------+----------------------------------------------+

❌ Pending Patches
+------------------------------------+----------------------------------------------+
| Patch Name                         | Description                                  |
+------------------------------------+----------------------------------------------+
| Patch_2025_06_30_FixUserEmails     | Strip whitespace from imported user emails   |
+------------------------------------+----------------------------------------------+
```

::: info
Patches that have been run and then deleted from the filesystem will not appear in either list — they are considered irrelevant once removed from the codebase.
:::
