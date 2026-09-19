<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CleanOrphanedImagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_deletes_orphaned_images_and_keeps_valid_ones()
    {
        // Fake the public disk
        Storage::fake('public');

        // Create a valid image file that is IN the database
        $validImagePath = 'product_images/valid_image.jpg';
        Storage::disk('public')->put($validImagePath, 'valid image content');
        
        $product = Product::factory()->create();
        ProductImage::factory()->create([
            'product_id' => $product->id,
            'image' => $validImagePath, // Using raw path
        ]);

        // Create an orphaned image file that is NOT in the database
        $orphanedImagePath = 'product_images/orphaned_image.jpg';
        Storage::disk('public')->put($orphanedImagePath, 'orphaned image content');

        // Assert both files exist before running the command
        Storage::disk('public')->assertExists($validImagePath);
        Storage::disk('public')->assertExists($orphanedImagePath);

        // Run the console command
        $this->artisan('app:clean-orphaned-images')
            ->expectsOutput('Starting orphaned images cleanup...')
            ->expectsOutput('Successfully deleted 1 orphaned images.')
            ->assertExitCode(0);

        // Assert the orphaned image is deleted
        Storage::disk('public')->assertMissing($orphanedImagePath);

        // Assert the valid image is kept
        Storage::disk('public')->assertExists($validImagePath);
    }
}
