<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryIncludeRequest;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use App\Traits\HasIncludeRule;
use Illuminate\Support\Facades\Storage;

class CategoryController extends ApiController
{
    use HasIncludeRule;
    protected $key = 'category';
    public function index(CategoryIncludeRequest $request)
    {
        return $this->runWithTransaction(function () use ($request) {
            $request->validated();

            /** @var \App\Models\User $user */
            $user = auth('sanctum')->user();
            if ($request->include ?? null) {
                $includes = $this->includeStrToArray($request->include);
            }
            $perPage  = $request->per_page ?? null;
            $categoriesQuery = Category::with($includes ?? [])
                ->withCount('products')
                ->orderByDesc('products_count');

            if ($perPage != null && $perPage > 0) {
                $categories = $categoriesQuery->paginate($perPage);
            } else {
                $categories = $categoriesQuery->get();
            }

            return [
                'categories' => $categories ?? null,
            ];
        },  __('messages.actions.retrieved_success', ['resource' => $this->resources($this->key)]));
    }

    public function store(StoreCategoryRequest $request)
    {
        return $this->runWithTransaction(function () use ($request) {

            if ($request->include ?? null) {
                $includes = $this->includeStrToArray($request->include);
            }

            return [
                'category' => Category::create($request->validated())->load($includes ?? [])
            ];
        },  __('messages.actions.created_success', ['resource' => $this->resource($this->key)]), successStatus: 201);
    }

    public function show(CategoryIncludeRequest $request, Category $category)
    {
        return $this->runWithTransaction(function () use ($request, $category) {
            if ($request->include ?? null) {
                $includes = $this->includeStrToArray($request->include);
            }
            return [
                'category' => $category->load($includes ?? [])
            ];
        },  __('messages.actions.retrieved_success', ['resource' => $this->resource($this->key)]));
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        return $this->runWithTransaction(function () use ($request, $category) {

            if ($request->include ?? null) {
                $includes = $this->includeStrToArray($request->include);
            }

            $category->update($request->validated());

            return [
                'category' => $category->refresh()->load($includes ?? [])
            ];
        },  __('messages.actions.updated_success', ['resource' => $this->resource($this->key)]));
    }

    public function destroy(CategoryIncludeRequest $request, Category $category)
    {
        return $this->runWithTransaction(function () use ($request, $category) {

            if ($request->include ?? null) {
                $includes = $this->includeStrToArray($request->include);
            }

            $category->products->load('images');
            $imageUrls = $category->products->pluck('images.*.image')->flatten()->toArray();

            $category->delete();
            Storage::disk('public')->delete($imageUrls);

            return [
                'category' => $category->load($includes ?? [])
            ];
        },  __('messages.actions.deleted_success', ['resource' => $this->resource($this->key)]));
    }
}
