<?php
namespace Aaix\LaravelPatches;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Aaix\LaravelPatches\Commands\MakePatchCommand;
use Aaix\LaravelPatches\Commands\StatusCommand;

class PatchServiceProvider extends ServiceProvider
{
   public function boot(): void
   {
      if ($this->app->runningInConsole()) {
         // 1) Load our patch_logs migration so tests (and apps) see the table
         $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

         // 2) Register the make:patch and patch:status commands
         $this->commands([MakePatchCommand::class, StatusCommand::class]);

         // 3) Auto-discover any user-defined patch commands
         $this->registerPatchCommands();

         // 4) Publish config & migration stubs as before
         $this->publishes(
            [
               __DIR__ . '/../config/patches.php' => config_path('patches.php'),
            ],
            'patches-config',
         );
      }
   }

   public function register(): void
   {
      $this->mergeConfigFrom(__DIR__ . '/../config/patches.php', 'patches');
   }

   /**
    * Scan the configured patches path and register each class as an Artisan command.
    */
   protected function registerPatchCommands(): void
   {
      $patchPath = config('patches.path', 'app/Console/Patches');
      $fullPath = base_path($patchPath);

      if (!$this->app['files']->isDirectory($fullPath)) {
         return;
      }

      $appNamespace = $this->app->getNamespace();
      $path = ltrim(str_replace('app', '', $patchPath), '/');
      $relativeNamespace = str_replace('/', '\\', Str::studly($path));
      $namespace = rtrim($appNamespace, '\\') . '\\' . trim($relativeNamespace, '\\');

      $files = $this->app['files']->files($fullPath);
      $commands = [];

      foreach ($files as $file) {
         $className = pathinfo($file->getFilename(), PATHINFO_FILENAME);

         $fqcn = $namespace . '\\' . $className;

         if (class_exists($fqcn)) {
            $commands[] = $fqcn;
         }
      }

      if (!empty($commands)) {
         $this->commands($commands);
      }
   }
}
