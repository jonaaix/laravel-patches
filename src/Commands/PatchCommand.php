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
      // Use the dedicated service to validate the schema.
      if (!(new SchemaValidator())->validate($this)) {
         return self::FAILURE;
      }

      $tableName = config('patches.table', 'patch_logs');
      $patchClass = static::class;

      if (DB::table($tableName)->where('patch_class', $patchClass)->exists()) {
         $this->warn("Patch <info>{$patchClass}</info> has already been applied and will be skipped.");
         return self::SUCCESS;
      }

      // Call the parent execute method which runs the handle() method.
      // Note: We are overriding execute method from a parent that uses Symfony interfaces.
      // It's better to stick to the parent's signature for compatibility.
      $status = parent::execute($input, $output);

      if ($status === self::SUCCESS) {
         DB::table($tableName)->insert(['patch_class' => $patchClass, 'ran_at' => now()]);
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
