<?php
namespace App\Core\Port;

/**
 * Marker interface for strategies whose input file
 * should be moved to processed/ on successful handling.
 */
interface MovableFileTypeStrategyInterface extends FileTypeStrategyInterface
{
}
