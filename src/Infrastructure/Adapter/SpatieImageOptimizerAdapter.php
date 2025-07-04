<?php

namespace App\Infrastructure\Adapter;

use App\Core\Port\ImageOptimizerPort;
use Spatie\ImageOptimizer\OptimizerChain;

final class SpatieImageOptimizerAdapter implements ImageOptimizerPort
{
    public function __construct(private readonly OptimizerChain $optimizer)
    {
    }

    /**
     * Optimize an image file (JPG, PNG, GIF, WebP, etc.) in place or to a target path.
     *
     * @param string $sourcePath Absolute path to the original image
     * @param string $targetPath Absolute path where the optimized image should be saved
     */
    public function optimize(string $sourcePath, string $targetPath): void
    {
        // If source and target differ, copy first
        if ($sourcePath !== $targetPath) {
            copy($sourcePath, $targetPath);
        }

        $this->optimizer->optimize($targetPath);
    }
}
