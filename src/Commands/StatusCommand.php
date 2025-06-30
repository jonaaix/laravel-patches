<?php

namespace Aaix\LaravelPatches\Commands;

use Aaix\LaravelPatches\Concerns\InteractsWithPatches;
use Aaix\LaravelPatches\Concerns\ResolvesPatchNamespace;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Aaix\LaravelPatches\Services\SchemaValidator;

class StatusCommand extends Command
{
   use ResolvesPatchNamespace;
   use InteractsWithPatches;

   protected $signature = 'patch:status';
   protected $description = 'Show the status of all patches';

   public function handle(): int
   {
      if (!(new SchemaValidator())->validate($this)) {
         return self::FAILURE;
      }

      $allLocalPatches = $this->getAllLocalPatches();

      if ($allLocalPatches->isEmpty()) {
         $this->info('No local patches found.');
         return self::SUCCESS;
      }

      $pendingPatches = $this->getPendingPatches();

      $ranAndExistingPatches = $allLocalPatches
         ->diffKeys($pendingPatches)
         ->sortBy('name')
         ->values();

      $this->displayPatches('✅ Ran Patches', $ranAndExistingPatches);
      $this->displayPatches('❌ Pending Patches', $pendingPatches->sortBy('name')->values());

      return self::SUCCESS;
   }

   protected function displayPatches(string $title, Collection $patches): void
   {
      $this->line('');
      $this->info($title);
      if ($patches->isEmpty()) {
         $this->line('  <fg=gray>None</>');
         return;
      }
      $this->table(['Patch Name'], $patches->map(fn($patch) => ['Patch' => $patch['name']]));
   }
}
