<?php

namespace Aaix\LaravelPatches\Services;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class SchemaValidator
{
   /**
    * Validate that the required patch log table exists.
    */
   public function validate(Command $command): bool
   {
      $tableName = config('patches.table', 'patch_logs');

      if (!Schema::hasTable($tableName)) {
         $command->error("The patch log table '{$tableName}' does not exist.");
         $command->warn(
            'Please publish and run the package migrations: `php artisan vendor:publish --tag=patches-migrations` and then `php artisan migrate`.',
         );
         return false;
      }

      return true;
   }
}
