<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CleanOrphanedImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clean-orphaned-images';
    protected $description = 'Cleans up orphaned product images from storage that are no longer in the database';

    public function handle()
    {
        $this->info('Starting orphaned images cleanup...');

        // 1. Get all images currently on disk in the product_images directory
        $disk = \Illuminate\Support\Facades\Storage::disk('public');
        $filesOnDisk = $disk->files('product_images');

        // 2. Get all images currently in the database (raw paths)
        // ProductImage has an accessor, so we pluck directly from DB to get the raw path
        $filesInDb = \Illuminate\Support\Facades\DB::table('product_images')->pluck('image')->toArray();

        // 3. Find files on disk that are NOT in the database
        $orphanedFiles = array_diff($filesOnDisk, $filesInDb);

        if (empty($orphanedFiles)) {
            $this->info('No orphaned images found.');
            return;
        }

        // 4. Delete the orphaned files
        $disk->delete($orphanedFiles);

        $this->info('Successfully deleted ' . count($orphanedFiles) . ' orphaned images.');
    }
}
