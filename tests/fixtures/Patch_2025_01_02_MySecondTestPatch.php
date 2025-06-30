<?php

namespace App\Console\Commands\Patches;

use Aaix\LaravelPatches\Commands\PatchCommand;
use Illuminate\Support\Facades\DB;

class Patch_2025_01_02_MySecondTestPatch extends PatchCommand
{
   public $signature = 'patch:2025_01_02_my-second-test-patch';
   public $description = 'A second test patch for automated testing.';

   public function handle(): int
   {
      DB::table('migrations')->insert(['migration' => 'my-second-test-patch-was-here', 'batch' => 999]);
      $this->info('Second test patch executed.');
      return self::SUCCESS;
   }
}
