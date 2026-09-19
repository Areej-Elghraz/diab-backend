<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ProductFilter
{
  public function apply(Builder $query, Request $request)
  {
    if ($request->has('category_id')) {
      $query->where('category_id', $request->category_id);
    }
    if ($request->has('price')) {
      $query->where('price', $request->price);
    }
    if ($request->has('min_price')) {
      $query->where('price', '>=', $request->min_price);
    }
    if ($request->has('max_price')) {
      $query->where('price', '<=', $request->max_price);
    }
    return $query;
  }
}
