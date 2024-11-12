<?php

namespace App\Http\Traits;

use Illuminate\Contracts\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;



use Illuminate\Database\Eloquent\Model;

trait CanLoadRelationships
{



  public function loadRelationships(Model|QueryBuilder|eloquentBuilder $for, array $relations)
  {
    foreach ($relations as $relation) {
      $for->when(
        $this->shouldIncludeRelation($relation),
        fn($q) => $q->with($relation)
      );
    }
  }
  protected function shouldIncludeRelation(string $relation): bool
  {
    $include = request()->query('include');
    if (!$include) {
      return false;
    }
    $relations = array_map('trim', explode(',', $include));
    return in_array($relation, $relations);
  }
}
