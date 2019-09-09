<?php
declare(strict_types=1);

use MVQN\Common\Directories;

require_once __DIR__."/vendor/autoload.php";

/**
 * @param string $directory
 * @param bool $recursive
 * @param string $ignore
 * @param callable|null $function
 * @return array
 */
function scanDirectory(string $directory, bool $recursive = true, string $ignore = "", callable $function = null): array
{
    $result = [];

    foreach(scandir($directory) as $filename)
    {
        if ($filename === '.' || $filename === '..')
            continue;

        if($ignore && preg_match($ignore, $directory))
            continue;

        $filePath = $directory . DIRECTORY_SEPARATOR . $filename;

        if (is_dir($filePath))
        {
            foreach (scanDirectory($filePath, $recursive, $ignore, $function) as $childFilename)
            {
                $result[] = $relative = $filename . DIRECTORY_SEPARATOR . $childFilename;

                if($function)
                    $function($relative, $directory . DIRECTORY_SEPARATOR . $relative);
            }
        }
        else
        {
            $result[] = $filename;

            if($function)
                $function($filename, $directory . DIRECTORY_SEPARATOR . $filename);
        }
    }

    return $result;
}

$count = 0;

scanDirectory(__DIR__, true, "/vendor/",
    function($path, $full) use (&$count)
    {
        $contents = file_get_contents($full);

        if(strpos($contents, "\r\n") === false)
            return;

        $contents = str_replace("\r\n", "\n", $contents);
        file_put_contents($full, $contents);
        $count++;
    }
);

echo "$count\n";