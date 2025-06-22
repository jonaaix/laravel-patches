<?php

namespace Aaix\LaravelPatches\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Aaix\LaravelPatches\Services\SchemaValidator;
use function Laravel\Prompts\select;

class StatusCommand extends Command
{
   protected $signature = 'patch:status';
   protected $description = 'Show the status of all patches';

   protected Filesystem $files;

   public function __construct(Filesystem $files)
   {
      parent::__construct();
      $this->files = $files;
   }

   public function handle(): int
   {

      // Use the dedicated service to validate the schema.
      if (!(new SchemaValidator())->validate($this)) {
         return self::FAILURE;
      }

      $ranPatches = $this->getRanPatches();
      $allLocalPatches = $this->getAllLocalPatches();

      if ($allLocalPatches->isEmpty()) {
         $this->info('No local patches found.');
         return self::SUCCESS;
      }

      $ranAndExistingPatches = $allLocalPatches
         ->filter(fn($patch) => $ranPatches->contains($patch['class']))
         ->sortBy('class')
         ->values();

      $pendingPatches = $allLocalPatches
         ->filter(fn($patch) => !$ranPatches->contains($patch['class']))
         ->sortBy('class')
         ->values();

      $this->displayPatches('✅ Ran Patches', $ranAndExistingPatches);
      $this->displayPatches('❌ Pending Patches', $pendingPatches);

      return self::SUCCESS;
   }

   protected function getRanPatches(): Collection
   {
      return DB::table(config('patches.table'))->pluck('patch_class');
   }

   protected function getAllLocalPatches(): Collection
   {
      $patchPath = config('patches.path', 'app/Console/Patches');
      $fullPath = base_path($patchPath);

      if (!$this->files->isDirectory($fullPath)) {
         return collect();
      }

      $files = $this->files->glob("{$fullPath}/*.php");
      $namespace = $this->getNamespaceForPath($patchPath);

      return collect($files)->map(function ($file) use ($namespace) {
         $className = $namespace . '\\' . $this->files->name($file);
         return ['class' => $className, 'name' => $this->files->name($file)];
      });
   }

   protected function getNamespaceForPath(string $path): string
   {
      $appNamespace = $this->laravel->getNamespace();

      // Strip leading slashes or “app” prefix
      $normalized = ltrim($path, '/\\');
      if (Str::startsWith(strtolower($normalized), 'app')) {
         $normalized = ltrim(substr($normalized, strlen('app')), '/\\');
      }

      // Split on / or \, studly-case each segment, rejoin with backslashes
      $segments = collect(preg_split('/[\/\\\\]+/', $normalized))
         ->map(fn($seg) => Str::studly($seg))
         ->filter()
         ->implode('\\');

      return trim($appNamespace . $segments, '\\');
   }

   protected function displayPatches(string $title, Collection $patches): void
   {
      $this->line('');
      $this->info($title);
      if ($patches->isEmpty()) {
         $this->line('  <fg=gray>None</>');
         return;
      }
      $this->table([' '], $patches->map(fn($patch) => ['Patch' => $patch['name']]));
   }
}
