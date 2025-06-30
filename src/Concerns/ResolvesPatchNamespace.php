<?php

namespace Aaix\LaravelPatches\Concerns;

use Illuminate\Support\Str;

trait ResolvesPatchNamespace
{
   protected function getNamespaceForPath(string $path): string
   {
      $appNamespace = $this->getLaravel()->getNamespace();

      $path = ltrim(str_replace('app', '', $path), '/');
      $relativeNamespace = str_replace('/', '\\', Str::studly($path));

      return rtrim($appNamespace, '\\') . '\\' . trim($relativeNamespace, '\\');
   }
}
