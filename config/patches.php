<?php

return [
   /*
   |--------------------------------------------------------------------------
   | Patch Logs Table Name
   |--------------------------------------------------------------------------
   |
   | This is the name of the database table that will be used to track
   | which patches have already been run for your application.
   |
   */
   'table' => 'patch_logs',

   /*
   |--------------------------------------------------------------------------
   | Patch Storage Path
   |--------------------------------------------------------------------------
   |
   | This is the path where your generated patch command files will be stored.
   | By default, this is within the `app/Console` directory, which is
   | already covered by Laravel's default PSR-4 autoloader.
   |
   */
   'path' => 'app/Console/Patches',
];
