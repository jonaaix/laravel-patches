<?php
namespace Aaix\LaravelPatches;

use Aaix\LaravelPatches\Commands\MakePatchCommand;
use Aaix\LaravelPatches\Commands\RunPatchesCommand;
use Aaix\LaravelPatches\Commands\StatusCommand;
use Aaix\LaravelPatches\Commands\SyncCommand;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class PatchServiceProvider extends ServiceProvider
{
   public function boot(): void
   {
      if ($this->app->runningInConsole()) {
         $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

         $this->commands([
            MakePatchCommand::class,
            StatusCommand::class,
            RunPatchesCommand::class,
            SyncCommand::class
         ]);

         $this->registerPatchCommands();

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

   protected function registerPatchCommands(): void
   {
      $patchPath = config('patches.path', 'app/Console/Commands/Patches');
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
