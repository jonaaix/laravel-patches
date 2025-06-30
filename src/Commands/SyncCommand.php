<?php

namespace Aaix\LaravelPatches\Commands;

use Aaix\LaravelPatches\Concerns\InteractsWithPatches;
use Aaix\LaravelPatches\Concerns\ResolvesPatchNamespace;
use Aaix\LaravelPatches\Services\SchemaValidator;
use Illuminate\Console\Command;
use function Laravel\Prompts\info;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\warning;

class SyncCommand extends Command
{
   use InteractsWithPatches;
   use ResolvesPatchNamespace;

   protected $signature = 'patch:sync';
   protected $description = 'Register existing patches in the database without running them.';

   public function handle(): int
   {
      if (!(new SchemaValidator())->validate($this)) {
         return self::FAILURE;
      }

      $pendingPatches = $this->getPendingPatches()->sortBy('name');

      if ($pendingPatches->isEmpty()) {
         info('All filesystem patches are already synced with the database.');
         return self::SUCCESS;
      }

      $options = $pendingPatches->pluck('name')->all();

      $selectedPatchNames = multiselect(
         label: 'Which patches should be marked as "run" without executing them?',
         options: $options,
         scroll: 15,
         required: false,
         hint: 'These patches will only be added to the log table.'
      );

      if (empty($selectedPatchNames)) {
         warning('No patches selected. Aborting sync.');
         return self::SUCCESS;
      }

      $patchesToSync = $pendingPatches->whereIn('name', $selectedPatchNames);

      $dataToInsert = $patchesToSync->map(fn ($patch) => [
         'patch_class' => $patch['class'],
         'ran_at' => now(),
         'run_count' => 1,
      ])->all();

      app('db')->table(config('patches.table'))->insert($dataToInsert);

      info(count($dataToInsert) . ' patch(es) successfully synced to the database.');

      return self::SUCCESS;
   }
}
