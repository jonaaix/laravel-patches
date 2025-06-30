<?php

namespace Aaix\LaravelPatches\Concerns;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

trait InteractsWithPatches
{
   protected function getRanPatches(): Collection
   {
      return DB::table(config('patches.table', 'patch_logs'))->pluck('patch_class');
   }

   protected function getAllLocalPatches(): Collection
   {
      $patchPath = config('patches.path', 'app/Console/Commands/Patches');
      $fullPath = base_path($patchPath);

      $filesystem = app(Filesystem::class);

      if (!$filesystem->isDirectory($fullPath)) {
         return collect();
      }

      $phpFiles = $filesystem->glob("{$fullPath}/*.php");
      $namespace = $this->getNamespaceForPath($patchPath);

      return collect($phpFiles)->map(function ($file) use ($namespace, $filesystem) {
         $className = $namespace . '\\' . $filesystem->name($file);
         return ['class' => $className, 'name' => $filesystem->name($file)];
      });
   }

   protected function getPendingPatches(): Collection
   {
      $ranPatches = $this->getRanPatches();
      $allLocalPatches = $this->getAllLocalPatches();

      return $allLocalPatches
         ->filter(fn($patch) => !$ranPatches->contains($patch['class']));
   }
}
