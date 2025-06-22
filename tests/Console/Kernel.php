<?php

namespace Aaix\LaravelPatches\Tests\Console;

use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
   /**
    * Define the application's command schedule.
    *
    * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
    * @return void
    */
   protected function schedule($schedule)
   {
      // Not needed for tests.
   }

   /**
    * Register the commands for the application.
    *
    * @return void
    */
   protected function commands()
   {
      // This is the crucial part.
      // We explicitly tell Laravel to scan the directory where our test patches are located.
      // This ensures they are always discovered during tests.
      $this->load(app_path('Console/Patches'));
      $this->load(app_path('Console/Commands'));
   }
}
