<?php

namespace Aaix\LaravelPatches\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Aaix\LaravelPatches\Services\SchemaValidator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class PatchCommand extends Command
{
   /**
    * Hide the command from the artisan list.
    */
   public function isHidden(): bool
   {
      return true;
   }

   /**
    * Execute the console command, wrapping it with schema and execution checks.
    * We use the Symfony interfaces here to align with the parent method signature.
    */
   protected function execute(InputInterface $input, OutputInterface $output): int
   {
      if (!(new SchemaValidator())->validate($this)) {
         return self::FAILURE;
      }

      $tableName = config('patches.table', 'patch_logs');
      $patchClass = static::class;

      $existing = DB::table($tableName)->where('patch_class', $patchClass)->first();

      if ($existing) {
         if ($this->confirm("Patch <info>{$patchClass}</info> has already been applied {$existing->run_count} time(s). Do you want to run it again?")) {
            $this->info("Re-running patch <info>{$patchClass}</info>...");
         } else {
            $this->warn("Skipping patch <info>{$patchClass}</info>.");
            return self::SUCCESS;
         }
      }

      $status = parent::execute($input, $output);

      if ($status === self::SUCCESS) {
         if ($existing) {
            // Increment run_count
            DB::table($tableName)->where('patch_class', $patchClass)->increment('run_count');
         } else {
            DB::table($tableName)->insert([
               'patch_class' => $patchClass,
               'ran_at' => now(),
               'run_count' => 1,
            ]);
         }

         $this->info("Successfully ran and logged <info>{$patchClass}</info>.");
      } else {
         $this->error("Patch <info>{$patchClass}</info> failed to execute.");
      }

      return $status;
   }


   /**
    * The patch logic that must be implemented by child classes.
    */
   abstract public function handle(): int;
}
