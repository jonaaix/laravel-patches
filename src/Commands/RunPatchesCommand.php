<?php

namespace Aaix\LaravelPatches\Commands;

use Aaix\LaravelPatches\Concerns\InteractsWithPatches;
use Aaix\LaravelPatches\Concerns\ResolvesPatchNamespace;
use Aaix\LaravelPatches\Services\SchemaValidator;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use function Laravel\Prompts\info;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\warning;

class RunPatchesCommand extends Command
{
   use InteractsWithPatches;
   use ResolvesPatchNamespace;

   protected $signature = 'patch
                            {--patch=* : The name(s) of specific patches to run}
                            {--all : Run all pending patches non-interactively}';

   protected $description = 'Run pending patches interactively or via options';

   public function handle(): int
   {
      if (!(new SchemaValidator())->validate($this)) {
         return self::FAILURE;
      }

      $pendingPatches = $this->getPendingPatches();

      if ($this->option('all')) {
         return $this->runPatches($pendingPatches, 'all pending patches');
      }

      if ($this->option('patch')) {
         $selectedNames = $this->option('patch');
         $selectedPatches = $pendingPatches->filter(
            fn($patch) => in_array($patch['name'], $selectedNames)
         );
         return $this->runPatches($selectedPatches, 'the specified patches');
      }

      return $this->runInteractive($pendingPatches->sortBy('name'));
   }

   private function runInteractive(Collection $pendingPatches): int
   {
      if ($pendingPatches->isEmpty()) {
         info('No pending patches to run.');
         return self::SUCCESS;
      }

      $options = $pendingPatches->pluck('name')->all();

      $selectedPatchNames = multiselect(
         label: 'Which (pending) patches would you like to run?',
         options: $options,
         scroll: 15,
         required: false
      );

      if (empty($selectedPatchNames)) {
         warning('No patches were selected. Aborting.');
         return self::SUCCESS;
      }

      $selectedPatches = $pendingPatches->filter(
         fn($patch) => in_array($patch['name'], $selectedPatchNames)
      );

      return $this->runPatches($selectedPatches, 'the selected patches');
   }

   private function runPatches(Collection $patches, string $context): int
   {
      if ($patches->isEmpty()) {
         warning("No pending patches found for {$context}.");
         return self::SUCCESS;
      }

      info("Running {$context}...");
      $this->line('');

      foreach ($patches->sortBy('name') as $patch) {
         $signature = $this->getSignatureFromPatchName($patch['name']);
         $this->call($signature);
         $this->line('');
      }

      info('All specified patches have been executed.');
      return self::SUCCESS;
   }

   private function getSignatureFromPatchName(string $patchName): string
   {
      preg_match('/^Patch_(\d{4}_\d{2}_\d{2})_(.*)$/', $patchName, $matches);

      $date = $matches[1];
      $namePart = $matches[2];

      return 'patch:' . $date . '_' . Str::kebab($namePart);
   }
}
