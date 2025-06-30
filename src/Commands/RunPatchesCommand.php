<?php

namespace Aaix\LaravelPatches\Commands;

use Aaix\LaravelPatches\Concerns\InteractsWithPatches;
use Aaix\LaravelPatches\Concerns\ResolvesPatchNamespace;
use Aaix\LaravelPatches\Services\SchemaValidator;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use function Laravel\Prompts\info;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\warning;

class RunPatchesCommand extends Command
{
   use InteractsWithPatches;
   use ResolvesPatchNamespace;

   protected $signature = 'patch';
   protected $description = 'Select and run pending patches interactively';

   public function handle(): int
   {
      if (!(new SchemaValidator())->validate($this)) {
         return self::FAILURE;
      }

      $pendingPatches = $this->getPendingPatches()->sortBy('name');

      if ($pendingPatches->isEmpty()) {
         info('No pending patches to run.');
         return self::SUCCESS;
      }

      $options = $pendingPatches->pluck('name', 'name')->all();

      $selectedPatches = multiselect(
         label: 'Which (pending) patches would you like to run?',
         options: $options,
         scroll: 15,
         required: false
      );

      if (empty($selectedPatches)) {
         warning('No patches were selected. Aborting.');
         return self::SUCCESS;
      }

      info('Running selected patches...');
      $this->line('');

      foreach ($selectedPatches as $patchName) {
         $signature = $this->getSignatureFromPatchName($patchName);
         $this->call($signature);
         $this->line('');
      }

      info('All selected patches have been executed.');

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
