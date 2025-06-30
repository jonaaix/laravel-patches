<?php

namespace App\Console\Commands\Patches;

use Aaix\LaravelPatches\Commands\PatchCommand;
use Illuminate\Support\Facades\DB;

class Patch_2025_01_01_MyTestPatch extends PatchCommand
{
   public $signature = 'patch:2025_01_01_my-test-patch';
   public $description = 'A test patch for automated testing.';

   public function handle(): int
   {
      DB::table('migrations')->insert(['migration' => 'my-test-patch-was-here', 'batch' => 999]);
      $this->info('Test patch executed.');
      return self::SUCCESS;
   }
}
