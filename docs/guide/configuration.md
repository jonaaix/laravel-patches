# Configuration

Publish the configuration file to customize the package:

```bash
php artisan vendor:publish --tag=patches-config
```

This creates `config/patches.php` with the following options:

| Key     | Description                                                       |
| ------- | ----------------------------------------------------------------- |
| `table` | The database table used for logging executed patches.             |
| `path`  | The directory where your patch files are stored.                  |

Adjusting these is rarely required — the defaults are designed to fit a standard Laravel application.
