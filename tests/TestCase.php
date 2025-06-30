<?php

namespace Aaix\LaravelPatches\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Aaix\LaravelPatches\PatchServiceProvider;
use Illuminate\Support\Facades\File;

abstract class TestCase extends OrchestraTestCase
{
   protected function setUp(): void
   {
      parent::setUp();
   }

   protected function setupTestPatches(): void
   {
      $destinationPath = app_path('Console/Commands/Patches');
      if (!File::isDirectory($destinationPath)) {
         File::makeDirectory($destinationPath, 0755, true);
      }

      $fixtureFiles = [
         'Patch_2025_01_01_MyTestPatch.php',
         'Patch_2025_01_02_MySecondTestPatch.php',
      ];

      foreach ($fixtureFiles as $fixtureFile) {
         $fixturePath = __DIR__ . '/fixtures/' . $fixtureFile;
         $destinationFile = $destinationPath . '/' . $fixtureFile;
         File::copy($fixturePath, $destinationFile);
         require_once $destinationFile;
      }
   }

   protected function getPackageProviders($app): array
   {
      return [PatchServiceProvider::class];
   }

   protected function getEnvironmentSetUp($app): void
   {
      $this->setupTestPatches();

      $app['config']->set('database.default', 'testing');
      $app['config']->set('database.connections.testing', [
         'driver' => 'sqlite',
         'database' => ':memory:',
         'prefix' => '',
      ]);

      $app['config']->set('patches.path', 'app/Console/Commands/Patches');
   }
}
