<?php

declare(strict_types=1);

namespace App\Utils;

use \RuntimeException;
use \RecursiveDirectoryIterator;
use \RecursiveIteratorIterator;

/**
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class FilesystemStaticUtilities
{
    public static function recursiveDirectoryRemoval(string $directory): bool
    {
        if (!is_dir($directory)) {
            return false;
        }

        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $file) {
            $callable = ($file->isDir() ? 'App\Utils\FilesystemStaticUtilities::recursiveDirectoryRemoval' : 'unlink');
            $callable($file->getRealPath());
        }

        return rmdir($directory);
    }

    public static function forceFileCopying(string $source, string $directoryDestination, string $fileName): void
    {
        if (!is_dir($directoryDestination) && !mkdir($directoryDestination, 0755) && !is_dir($directoryDestination)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created.', $directoryDestination));
        }

        if (!copy($source, $directoryDestination.$fileName)) {
            throw new RuntimeException(sprintf('"%s" can\'t be copied.', $source));
        }
    }
}
