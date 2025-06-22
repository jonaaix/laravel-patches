<?php

namespace Aaix\LaravelPatches\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Aaix\LaravelPatches\PatchServiceProvider;
use Illuminate\Support\Facades\File;

abstract class TestCase extends OrchestraTestCase
{
   /**
    * Setup the test environment.
    */
   protected function setUp(): void
   {
      parent::setUp();
   }

   /**
    * Copies a fixture patch into the test application's command directory.
    */
   protected function setupTestPatch(): void
   {
      $fixturePath = __DIR__ . '/fixtures/MyTestPatch.php';
      $destinationPath = app_path('Console/Patches');
      $destinationFile = $destinationPath . '/MyTestPatch.php';

      if (!File::isDirectory($destinationPath)) {
         File::makeDirectory($destinationPath, 0755, true);
      }

      File::copy($fixturePath, $destinationPath . '/MyTestPatch.php');

      // Manually load the class file so PHP knows about it,
      // bypassing the autoloader issue.
      require_once $destinationFile;
   }

   /**
    * Get package providers.
    *
    * @param  \Illuminate\Foundation\Application $app
    * @return array
    */
   protected function getPackageProviders($app): array
   {
      // We only return the provider class here. The setup happens elsewhere.
      return [PatchServiceProvider::class];
   }

   /**
    * Define environment setup.
    *
    * @param  \Illuminate\Foundation\Application  $app
    * @return void
    */
   protected function getEnvironmentSetUp($app): void
   {
      // 1. Set up the test patch file now that core services are available.
      $this->setupTestPatch();

      // 2. Set up the in-memory SQLite database.
      $app['config']->set('database.default', 'testing');
      $app['config']->set('database.connections.testing', [
         'driver' => 'sqlite',
         'database' => ':memory:',
         'prefix' => '',
      ]);

      // 3. Set the default patch path for our provider to find the test patch.
      $app['config']->set('patches.path', 'app/Console/Patches');
   }
}
