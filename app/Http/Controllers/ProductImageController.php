<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductImageIncludeRequest;
use App\Http\Requests\StoreProductImageRequest;
use App\Http\Requests\UpdateProductImageRequest;
use App\Models\Product;
use App\Models\ProductImage;
use App\Traits\HasIncludeRule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ProductImageController extends ApiController
{
    use HasIncludeRule;
    protected $key = 'productimage';
    protected $all_key = 'all_images';
    public function index(ProductImageIncludeRequest $request, Product $product)
    {
        return $this->runWithTransaction(function () use ($request, $product) {
            if ($request->include ?? null) {
                $includes = $this->includeStrToArray($request->include);
            }
            return [
                'images' => $product->images->load($includes ?? []),
            ];
        },  __('messages.actions.retrieved_success', ['resource' => $this->resources($this->key)]));
    }

    public function store(StoreProductImageRequest $request, Product $product)
    {
        $uploadedPaths = [];
        return $this->runWithTransaction(function () use ($request, $product, &$uploadedPaths) {

            if ($request->include ?? null) {
                $includes = $this->includeStrToArray($request->include);
            }

            $validated = $request->validated();
            $position_counts = array_count_values($validated['positions']);

            foreach (['before', 'after'] as $position) {
                if (($position_counts[$position] ?? 0) > 1) {
                    throw ValidationException::withMessages(['positions' => __('validation.only_one_position', ['attribute' => __('validation.attributes.' . $position)])]);
                }
            }

            foreach ($validated['images'] as $index => $image) {
                if (in_array($validated['positions'][$index], ['before', 'after'])) {
                    $product->images()
                        ->where('position', $validated['positions'][$index])
                        ->update(['position' => 'gallery']);
                }

                $image = $image->store('product_images', 'public');
                array_push($uploadedPaths, $image);

                $product->images()->create([
                    'image' => $image,
                    'position'   => $validated['positions'][$index],
                ]);
            }

            return [
                'images' => $product->refresh()->images->load($includes ?? []),
            ];
        },  __('messages.actions.created_success', ['resource' => $this->resources($this->key)]), uploadedPaths: $uploadedPaths, successStatus: 201);
    }

    public function show(ProductImageIncludeRequest $request, Product $product, ProductImage $productImage)
    {
        return $this->runWithTransaction(function () use ($request, $product, $productImage) {

            if ($request->include ?? null) {
                $includes = $this->includeStrToArray($request->include);
            }

            if ($productImage->product?->id !== $product->id) {
                throw new \Exception(__('messages.image_not_found_in_product'), 404);
            }
            return [
                'image' => $productImage->load($includes ?? []),
            ];
        },  __('messages.actions.retrieved_success', ['resource' => $this->resource($this->key)]));
    }

    public function update(UpdateProductImageRequest $request, Product $product, ProductImage $productImage)
    {
        $uploadedPaths = [];
        return $this->runWithTransaction(function () use ($request, $product, $productImage, &$uploadedPaths) {

            if ($request->include ?? null) {
                $includes = $this->includeStrToArray($request->include);
            }

            $validated   = $request->validated();
            $newPath     = null;
            $newPosition = null;

            if ($productImage->product_id !== $product->id) {
                throw new \Exception(__('messages.image_not_found_in_product'), 404);
            }

            if (($validated['position'] ?? null) !== null) {
                foreach (['before', 'after'] as $position) {
                    $checkMethod = 'is' . ucfirst($position) . 'Image';
                    if ($productImage->$checkMethod()) {
                        throw ValidationException::withMessages(['position' => __('validation.cannot_update_position', ['attribute' => __('validation.attributes.' . $position)])]);
                    }
                }
                if (in_array($validated['position'], ['before', 'after'])) {
                    $product->images()
                        ->where('position', $validated['position'])
                        ->update(['position' => 'gallery']);
                }
                $newPosition = $validated['position'];
            }

            if ($validated['image'] ?? null) {
                Storage::disk('public')->delete($productImage->image);
                $newPath = $validated['image']->store('product_images', 'public');
                array_push($uploadedPaths, $newPath);
            }

            if ($newPath) {
                $validated['image'] = $newPath;
            }
            $validated['position'] = $newPosition ?? $productImage->position;
            $productImage->update($validated);

            return [
                'image' => $productImage->refresh()->load($includes ?? []),
            ];
        },  __('messages.actions.updated_success', ['resource' => $this->resource($this->key)]), uploadedPaths: $uploadedPaths);
    }

    public function destroy(ProductImageIncludeRequest $request, Product $product, ProductImage $productImage)
    {
        return $this->runWithTransaction(function () use ($request, $product, $productImage) {

            if ($request->include ?? null) {
                $includes = $this->includeStrToArray($request->include);
            }

            if ($productImage->product_id !== $product->id) {
                throw new \Exception(__('messages.image_not_found_in_product'), 404);
            }

            foreach (['before', 'after'] as $position) {
                $checkMethod = 'is' . ucfirst($position) . 'Image';
                if ($productImage->$checkMethod()) {
                    throw new \Exception(__('messages.cannot_delete_position', ['attribute' => __('validation.attributes.' . $position)]), 403);
                }
            }

            $image = $productImage->image;

            $productImage->delete();
            Storage::disk('public')->delete($image);

            return [
                'image' => $productImage->load($includes ?? []),
            ];
        },  __('messages.actions.deleted_success', ['resource' => $this->resource($this->key)]));
    }

    public function destroyAll(ProductImageIncludeRequest $request, Product $product)
    {
        return $this->runWithTransaction(function () use ($request, $product) {

            if ($request->include ?? null) {
                $includes = $this->includeStrToArray($request->include);
            }

            $images = $product->gallery;
            if ($images->isEmpty()) {
                throw new \Exception(__('messages.product_has_no_images'), 404);
            }
            $images = $images->pluck('image')->toArray();

            $product->gallery()->delete();
            Storage::disk('public')->delete($images);

            return [
                'images' => $product->gallery->load($includes ?? []),
            ];
        }, __('messages.actions.deleted_success', ['resource' => $this->resource($this->all_key)]));
    }
}
