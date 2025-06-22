<?php

namespace App\Console\Patches;

use Aaix\LaravelPatches\Commands\PatchCommand;
use Illuminate\Support\Facades\DB;

class MyTestPatch extends PatchCommand
{
   public $signature = 'patch:my-test-patch';
   public $description = 'A test patch for automated testing.';

   public function handle(): int
   {
      // We can write a value to the database to prove it ran.
      DB::table('migrations')->insert(['migration' => 'my-test-patch-was-here', 'batch' => 999]);
      $this->info('Test patch executed.');
      return self::SUCCESS;
   }
}
