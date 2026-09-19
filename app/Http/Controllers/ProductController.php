<?php

namespace App\Http\Controllers;

use App\Filters\ProductFilter;
use App\Http\Requests\IndexProductRequest;
use App\Http\Requests\ProductIncludeRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Traits\HasIncludeRule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ProductController extends ApiController
{
    use HasIncludeRule;
    protected $key = 'product';
    public function index(IndexProductRequest $request)
    {
        return $this->runWithTransaction(function () use ($request) {

            $request->validated();

            if ($request->include ?? null) {
                $includes = $this->includeStrToArray($request->include);
            }
            $products = Product::with($includes ?? [])->latest();

            $products = (new ProductFilter())->apply($products, $request)
                ->paginate($request->per_page ?? 12);

            return [
                'products' => $products,
            ];
        },  __('messages.actions.retrieved_success', ['resource' => $this->resources($this->key)]));
    }

    public function indexTrashed(ProductIncludeRequest $request)
    {
        return $this->runWithTransaction(function () use ($request) {
            if ($request->include ?? null) {
                $includes = $this->includeStrToArray($request->include);
            }
            $products = Product::onlyTrashed()
                ->orderByDesc('deleted_at')
                ->with($includes ?? [])
                ->paginate($request->per_page ?? 12);

            return [
                'products' => $products,
            ];
        },  __('messages.actions.retrieved_success', ['resource' => $this->resources($this->key)]));
    }

    public function store(StoreProductRequest $request)
    {
        $uploadedPaths = [];
        return $this->runWithTransaction(function () use ($request, &$uploadedPaths) {
            if ($request->include ?? null) {
                $includes = $this->includeStrToArray($request->include);
            }
            $validated       = $request->validated();
            $position_counts = array_count_values($validated['positions']);
            foreach (['before', 'after'] as $position) {
                if (($position_counts[$position] ?? 0) !== 1) {
                    throw ValidationException::withMessages(
                        ['positions' => __('validation.only_one_position', ['attribute' => __('validation.attributes.' . $position)])]
                    );
                }
            }

            $product = Product::create(attributes: [
                'name'        => $validated['name'],
                'description' => $validated['description'] ?? null,
                'price'       => $validated['price'],
                'category_id' => $validated['category_id'],
            ]);

            foreach ($validated['images'] as $index => $image) {
                $image = $image->store('product_images', 'public');
                array_push($uploadedPaths, $image);

                $product->images()->create([
                    'image' => $image,
                    'position'   => $validated['positions'][$index],
                ]);
            }

            return [
                'product' => $product->load($includes ?? []),
            ];
        },  __('messages.actions.created_success', ['resource' => $this->resource($this->key)]), $uploadedPaths, successStatus: 201);
    }

    public function show(ProductIncludeRequest $request, Product $product)
    {
        return $this->runWithTransaction(function () use ($request, $product) {
            if ($request->include ?? null) {
                $includes = $this->includeStrToArray($request->include);
            }
            return [
                'product' => $product->load($includes ?? []),
            ];
        },  __('messages.actions.retrieved_success', ['resource' => $this->resource($this->key)]));
    }

    public function showTrashed(ProductIncludeRequest $request, int $id)
    {
        return $this->runWithTransaction(function () use ($request, $id) {
            if ($request->include ?? null) {
                $includes = $this->includeStrToArray($request->include);
            }
            $product  = Product::onlyTrashed()
                ->with($includes ?? [])
                ->findOrFail($id);

            return [
                'product' => $product,
            ];
        },  __('messages.actions.retrieved_success', ['resource' => $this->resource($this->key)]));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        return $this->runWithTransaction(function () use ($request, $product) {
            if ($request->include ?? null) {
                $includes = $this->includeStrToArray($request->include);
            }
            $product->update($request->validated());

            return [
                'product' => $product->refresh()->load($includes ?? []),
            ];
        },  __('messages.actions.updated_success', ['resource' => $this->resource($this->key)]));
    }

    public function destroy(ProductIncludeRequest $request, Product $product)
    {
        return $this->runWithTransaction(function () use ($request, $product) {
            if ($request->include ?? null) {
                $includes = $this->includeStrToArray($request->include);
            }
            $product->delete();
            return [
                'product' => $product->load($includes ?? [])
            ];
        },  __('messages.actions.deleted_success', ['resource' => $this->resource($this->key)]));
    }

    public function restore(ProductIncludeRequest $request, int $id)
    {
        return $this->runWithTransaction(function () use ($request, $id) {
            if ($request->include ?? null) {
                $includes = $this->includeStrToArray($request->include);
            }
            $product  = Product::onlyTrashed()
                ->with($includes ?? [])
                ->findOrFail($id);
            $product->restore();

            return [
                'product' => $product,
            ];
        },  __('messages.actions.restored_success', ['resource' => $this->resource($this->key)]));
    }

    public function forceDelete(ProductIncludeRequest $request, int $id)
    {
        return $this->runWithTransaction(function () use ($request, $id) {
            if ($request->include ?? null) {
                $includes = $this->includeStrToArray(include: $request->include);
            }

            $product   = Product::onlyTrashed()
                ->with('images')
                ->findOrFail($id);
            $imageUrls = $product->images->pluck('image')->toArray();

            Product::withTrashed()->findOrFail($id)->forceDelete();
            Storage::disk('public')->delete($imageUrls);

            return [
                'product' => $product->load($includes ?? []),
            ];
        },  __('messages.actions.force_deleted_success', ['resource' => $this->resource($this->key)]));
    }
}
