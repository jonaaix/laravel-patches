<?php

namespace Aaix\LaravelPatches\Commands;

use Aaix\LaravelPatches\Concerns\ResolvesPatchNamespace;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakePatchCommand extends Command
{
   use ResolvesPatchNamespace;

   protected $signature = 'make:patch {name : The name of the patch}';
   protected $description = 'Create a new hidden, command-based patch';

   protected Filesystem $files;

   public function __construct(Filesystem $files)
   {
      parent::__construct();
      $this->files = $files;
   }

   public function handle(): int
   {
      $name = Str::studly(trim($this->argument('name')));
      $date = now()->format('Y_m_d');
      $className = 'Patch_' . $date . '_' . $name;
      $signature = "patch:{$date}_" . Str::kebab($name);

      $path = $this->getPatchPath($className);

      if ($this->files->exists($path)) {
         $this->error("Patch {$className} already exists!");
         return self::FAILURE;
      }

      $this->makeDirectory(dirname($path));

      $stub = $this->getStub();

      $configPath = config('patches.path', 'app/Console/Commands/Patches');
      $namespace = $this->getNamespaceForPath($configPath);

      $stub = str_replace(
         ['{{ namespace }}', '{{ class }}', '{{ signature }}', '{{ description }}'],
         [$namespace, $className, $signature, "Patch for {$name}"],
         $stub,
      );

      $this->files->put($path, $stub);

      $this->info("Patch created successfully: <comment>[" . $configPath . "/{$className}.php]</comment>");
      $this->line("You can now run it using: <info>php artisan {$signature}</info>");

      return self::SUCCESS;
   }

   protected function getStub(): string
   {
      return $this->files->get(__DIR__ . '/../stubs/patch.stub');
   }

   protected function getPatchPath(string $className): string
   {
      $path = config('patches.path', 'app/Console/Commands/Patches');
      return base_path("{$path}/{$className}.php");
   }

   protected function makeDirectory(string $path): void
   {
      if (!$this->files->isDirectory($path)) {
         $this->files->makeDirectory($path, 0755, true, true);
      }
   }
}
