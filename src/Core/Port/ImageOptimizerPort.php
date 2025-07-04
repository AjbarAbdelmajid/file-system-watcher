<?php
namespace App\Core\Port;

interface ImageOptimizerPort
{
    /**
     * Optimize an image file for web.
     *
     * @param string $sourcePath Absolute path to the original image
     * @param string $targetPath Absolute path where the optimized image should be saved
     */
    public function optimize(string $sourcePath, string $targetPath): void;
}