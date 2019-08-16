<?php /** @noinspection PhpUnused */
declare(strict_types=1);

namespace UCRM\Common;

use DateTimeImmutable;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Exception\BadFormatException;
use Defuse\Crypto\Exception\EnvironmentIsBrokenException;
use Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException;
use Defuse\Crypto\Key;
use Exception;
use Monolog\Logger;
use MVQN\Common\Arrays;
use Nette\PhpGenerator\PhpNamespace;
use PDO;
use PDOException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Yaml\Yaml;
use ZipArchive;

/**
 * Class Plugin
 *
 * @package UCRM\Plugins
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 * @final
 */
final class Plugin
{

    #region CONSTANTS

    /*******************************************************************************************************************
     * NOTES:
     *   - By default the below will generate the settings in a file named "Settings.php" that lives in a folder named
     *     "App" in the "src" folder of including plugin's root folder.
     *     Example: <code>PLUGIN_ROOT/server/App/Settings.php</code>
     *
     *   - The PSR-4 class name will be \App\Settings
     ******************************************************************************************************************/

    /**
     * The default namespace for the Settings singleton.
     */
    private const _DEFAULT_SETTINGS_NAMESPACE = "App";

    /**
     * The default class name for the Settings singleton.
     */
    private const _DEFAULT_SETTINGS_CLASSNAME = "Settings";

    #endregion

    #region MODULES

    /**
     * NOTES:
     *   - The following are the currently supported "modules" for this Plugin SDK.
     *
     *   - Passing any of the following in $options["modules"] when invoking Plugin::initialize() will cause the SDK to
     *     perform some additional checks during initialization and configuration to ensure the UCRM system the plugin
     *     is being installed on has all of the necessary setup completed.
     */

    /**
     * The REST module enforces the following during initialization and configuration:
     *   - That the UCRM system has generated an "App Key" during plugin installation, which should ALWAYS be true!
     */
    public const MODULE_REST = "rest";

    /**
     * The DATA module enforces the following during initialization and configuration:
     *   - That the UCRM system has valid database settings, which should ALWAYS be true!
     */
    public const MODULE_DATA = "data";

    /**
     * The HTTP module enforces the following during initialization and configuration:
     *   - That a "Server domain name" has been configured in the UCRM's System->Settings->Application section.
     */
    public const MODULE_HTTP = "http";

    /**
     * The SMTP module enforces the following during initialization and configuration:
     *   - That the "SMTP Configuration" has been completed in the UCRM's System->Settings->Mailer section.
     */
    public const MODULE_SMTP = "smtp";

    /**
     * Gets a list of all required modules for this Plugin.
     *
     * @return array Returns an array of the currently required modules, or empty if none have been specified.
     */
    public static function getModules(): array
    {
        return array_key_exists("modules", self::$_options) ? self::$_options["modules"] : [];
    }

    /**
     * Checks to see if a given module is required.
     *
     * @param string $name The name of the module for which to check.
     *
     * @return bool Returns TRUE if the specified module is currently required, otherwise FALSE.
     */
    public static function hasModule(string $name): bool
    {
        return array_key_exists("modules", self::$_options) ? in_array($name, self::$_options["modules"]) : false;
    }
    #endregion

    #region INITIALIZATION

    /*******************************************************************************************************************
     * NOTES:
     *   - The following are the default options, but will be overridden by options passed to Plugin::initialize().
     *   - See individual options notes below.
     ******************************************************************************************************************/

    /**
     * @var array The Plugin's options.
     */
    private static $_options =
        [
            // A list of any required modules.
            "modules" => [
                // None required by default!
            ]
        ];

    /**
     * Initializes the Plugin singleton.
     *
     * This method MUST be called before any other method here, with the exception of Plugin::bundle(), provided a root
     * path is provided to that method.
     *
     * @param string|null $root The "root" path of this Plugin, if NULL, this method will attempt to guess it!
     * @param array|null $options An optional set of options for this Plugin.
     *
     * @throws Exceptions\RequiredDirectoryNotFoundException
     * @throws Exceptions\RequiredFileNotFoundException
     */
    public static function initialize( string $root = null, array $options = null )
    {

        // IF no "root" path has been provided...
        if($root === null)
        {
            // THEN, attempt to guess it using the most recent "calling" script.

            // Get the debug back-trace.
            $trace = debug_backtrace();

            // Set the "root" path the same as the directory of the top-most script.
            $root = dirname($trace[0]["file"]);

            // NOTE: Checks will be done below to determine whether this is a valid "root" path or not!
        }

        // IF any options are provided, merge them with the default options, preferring these!
        if($options !== null)
            self::$_options = array_merge(self::$_options, $options);

        #region REQUIRED: /

        // Get the absolute "root" path, in cases where a relative path is provided.
        $root = realpath($root);

        // IF the root path is invalid or does not exist...
        if(!$root || !file_exists($root) || !is_dir($root))
        {
            // THEN throw an Exception, as we cannot do anything else without this path!
            throw new Exceptions\RequiredDirectoryNotFoundException(
                "The provided root path does not exist!\n".
                "- Provided: '$root'\n");
        }

        // IF the root path is not a folder...
        if(!$root || !is_dir($root))
        {
            // THEN throw an Exception, as we cannot do anything else without this path!
            throw new Exceptions\RequiredDirectoryNotFoundException(
                "The provided root path is a file and should be a folder!\n".
                "- Provided: '$root'\n");
        }

        #endregion

        #region REQUIRED: /manifest.json

        // Get the absolute "manifest.json" path, relative to the "root" path.
        $manifest = realpath($root."/manifest.json");

        // IF the manifest.json path is invalid or does not exist...
        if(!$manifest || !file_exists($manifest) || !is_file($manifest))
        {
            // NOTE: This is a required Plugin file, so it should ALWAYS exist!
            // THEN throw an Exception, as we cannot do anything else without this file!
            throw new Exceptions\RequiredFileNotFoundException(
                "The provided root path '$root' does not contain a 'manifest.json' file!\n".
                "- Provided: '$root/manifest.json'\n");
        }

        #endregion

        #region REQUIRED: /ucrm.json

        // Get the absolute "ucrm.json" path, relative to the "root" path.
        $ucrm = realpath($root."/ucrm.json");

        // IF the ucrm.json path is invalid or does not exist...
        if(!$ucrm || !file_exists($ucrm) || !is_file($ucrm))
        {
            // NOTE: This is a required Plugin file, so it should ALWAYS exist!
            // THEN throw an Exception, as we cannot do anything else without this file!
            throw new Exceptions\RequiredFileNotFoundException(
                "The provided root path '$root' does not contain a 'ucrm.json' file!\n".
                "- Provided: '$root/ucrm.json'\n");
        }

        #endregion

        #region REQUIRED: /data/

        // Get the absolute "data" path, relative to the "root" path.
        $data = realpath($root."/data/");

        // IF the data path is invalid or does not exist...
        if(!$data || !file_exists($data))
        {
            // NOTE: By performing this check after the Plugin's required files, we can now simply create this folder!
            mkdir($root."/data/", 0775, TRUE);

            /*
            // THEN throw an Exception, as we cannot do anything else without this path!
            throw new Exceptions\RequiredDirectoryNotFoundException(
                "The provided root path '$root' does not contain a 'data' directory!\n".
                "- Provided: '$root/data/'\n");
            */
        }

        // TODO: Determine the need to handle a valid data path when a non-directory file exists?

        #endregion

        #region REQUIRED: /data/config.json

        // Get the absolute "config.json" path, relative to the "root" path.
        $config = realpath($root."/data/config.json");

        // IF the config.json path is invalid or does not exist...
        if(!$config || !file_exists($config) || !is_file($config))
        {
            // NOTE: By performing this check after the Plugin's required files, we can now simply create this file!
            file_put_contents($root."/data/config.json", "{}");

            /*
            // THEN throw an Exception, as we cannot do anything else without this file!
            throw new Exceptions\RequiredFileNotFoundException(
                "The provided root path '$root' does not contain a 'data/config.json' file!\n".
                "- Provided: '$root/data/config.json'\n");
            */
        }

        #endregion

        #region OPTIONAL: /data/plugin.log

        // Get the absolute "plugin.log" path, relative to the "root" path.
        $log = realpath($root."/data/plugin.log");

        // IF the plugin.log path is invalid or does not exist...
        if(!$log || !file_exists($log) || !is_file($log))
        {
            $trace = debug_backtrace()[0];

            // NOTE: By performing this check after the Plugin's required files, we can now simply create this file!
            $entry = new LogEntry([
                "timestamp" => (new DateTimeImmutable())->format(LogEntry::TIMESTAMP_FORMAT_DATETIME),
                "channel" => "UCRM",
                "level" => Logger::INFO,
                "levelName" => "INFO",
                "message" => "This plugin.log file has been automatically generated by Plugin::initialize().",
                "context" => [],
                "extra" => [
                    "file" => $trace["file"],
                    "line" => $trace["line"],
                    "class" => $trace["class"],
                    "function" => $trace["function"],
                    //"args" => $trace["args"],
                ],
            ]);

            file_put_contents($root."/data/plugin.log", $entry);

            /*
            // THEN throw an Exception, as we cannot do anything else without this file!
            throw new Exceptions\RequiredFileNotFoundException(
                "The provided root path '$root' does not contain a 'data/plugin.log' file!\n".
                "- Provided: '$root/data/plugin.log'\n");
            */
        }

        #endregion

        #region OPTIONAL: /server/

        // Get the absolute "source" path, relative to the "root" path.
        $src = realpath($root."/server/");

        // IF the source path is invalid or does not exist...
        if(!$src || !file_exists($src))
        {
            // NOTE: By performing this check after the Plugin's required files, we can now simply create this folder!
            mkdir($root."/server/", 0775, TRUE);

            /*
            // THEN throw an Exception, as we cannot do anything else without this path!
            throw new Exceptions\RequiredDirectoryNotFoundException(
                "The provided root path '$root' does not contain a 'source' directory!\n".
                "- Provided: '$root/src/'\n");
            */
        }

        // TODO: Determine the need to handle a valid data path when a non-directory file exists?

        #endregion

        // All required/optional checks have passed, so this must be a valid Plugin root path!
        self::$_rootPath = $root;

        // TODO: Add any further Plugin initialization code here!
        // ...
    }

    #endregion

    #region PATHS

    /**
     * @var string The root path of this Plugin, as configured by Plugin::initialize();
     */
    private static $_rootPath = "";

    /**
     * Gets the "root" path.
     *
     * @return string Returns the absolute ROOT path of this Plugin.
     * @throws Exceptions\PluginNotInitializedException
     */
    public static function getRootPath(): string
    {
        // IF the plugin is not initialized, THEN throw an Exception!
        if(self::$_rootPath === "")
            throw new Exceptions\PluginNotInitializedException(
                "The Plugin must be initialized using 'Plugin::initialize()' before calling any other methods!\n");

        // Finally, return the ROOT path!
        return self::$_rootPath;
    }

    /**
     * Gets the "source" path.
     *
     * @return string Returns the absolute SOURCE path of this Plugin.
     * @throws Exceptions\PluginNotInitializedException
     */
    public static function getSourcePath(): string
    {
        // Get the ROOT path, which will also throw the PluginNotInitializedException if necessary.
        $root = self::getRootPath();

        // NOTE: This is now handled in Plugin::initialize().
        // IF the directory does not exist, THEN create it...
        //if(!file_exists("$root/src/"))
        //    mkdir("$root/src/");

        // Finally, return the SOURCE path!
        return realpath("$root/server/");
    }

    /**
     * Gets the "data" path.
     *
     * @return string Returns the absolute DATA path of this Plugin.
     * @throws Exceptions\PluginNotInitializedException
     */
    public static function getDataPath(): string
    {
        // Get the ROOT path, which will also throw the PluginNotInitializedException if necessary.
        $root = self::getRootPath();

        // NOTE: This is now handled in Plugin::initialize().
        // IF the directory does not exist, THEN create it...
        if(!file_exists("$root/data/"))
            mkdir("$root/data/");

        // Finally, return the DATA path!
        return realpath("$root/data/");
    }

    /**
     * Gets the "logs" path.
     *
     * @return string Returns the absolute LOGS path of this Plugin.
     * @throws Exceptions\PluginNotInitializedException
     */
    public static function getLogsPath(): string
    {
        // Get the ROOT path, which will also throw the PluginNotInitializedException if necessary.
        $data = self::getDataPath();

        // NOTE: This is now handled in Plugin::initialize().
        // IF the directory does not exist, THEN create it...
        if(!file_exists("$data/logs/"))
            mkdir("$data/logs/");

        // Finally, return the DATA path!
        return realpath("$data/logs/");
    }

    #endregion




    public static function manifest(string $path = "")
    {
        $manifest = json_decode(file_get_contents(self::getRootPath() . "/manifest.json"), true);

        if($path === null || $path === "")
            return $manifest;

        return Arrays::array_path($manifest, $path);
    }

    public static function ucrm(string $path = "")
    {
        $ucrm = json_decode(file_get_contents(self::getRootPath() . "/ucrm.json"), true);

        if($path === null || $path === "")
            return $ucrm;

        return Arrays::array_path($ucrm, $path);
    }



    #region PERMISSIONS

    /**
     * Scans the directory and builds an array of all directories and files (recursively).
     *
     * @param string $directory The directory (or file) with which to start scanning.
     * @return array Returns an array of absolute directories and files found.
     */
    private static function scandirRecursive(string $directory): array
    {
        // Initialize an empty array of results.
        $results = [];

        // IF the provided directory does not exist, THEN return an empty array!
        if(!file_exists($directory))
            return $results;

        // IF the provided directory is actually a file, THEN return an array with only this single file!
        if(!is_dir($directory))
        {
            $results[] = $directory;
            return $results;
        }

        // Loop over each item in the specified directory...
        foreach(scandir($directory) as $filename)
        {
            // IF the current item is one of the specials "." or "..", THEN simply skip this item!
            if ($filename[0] === "." || $filename[0] === "..")
                continue;

            // OTHERWISE, build the absolute path to the current item.
            $filePath = $directory . DIRECTORY_SEPARATOR . $filename;

            // IF the current item is a directory...
            if (is_dir($filePath))
            {
                // THEN, add this directory to the results.
                $results[] = $filename;

                // AND loop through the this directory (recursively)...
                foreach (self::scandirRecursive($filePath) as $childFilename)
                {
                    // Adding each set of recursive results to the top-level results.
                    $results[] = $filename . DIRECTORY_SEPARATOR . $childFilename;
                }
            }
            else
            {
                // OTHERWISE, simply add this file to the results.
                $results[] = $filename;
            }
        }

        // Finally, return the array of results that were found!
        return $results;
    }

    /**
     * Fixes all directory and file permissions for this plugin, recursively from the ROOT path.
     *
     * @param string $user The username in which to set ownership of all directories and files, default "nginx".
     * @return array Returns an array of all directories and files that had their ownership or permissions changed.
     * @throws Exceptions\PluginNotInitializedException
     */
    public static function fixPermissions(string $user = "nginx"): array
    {
        // Get the ROOT path, which will also throw the PluginNotInitializedException if necessary.
        $root = self::getRootPath();

        // Get the user's uid and gid from the POSIX.
        /** @noinspection PhpComposerExtensionStubsInspection - As this extension is NOT Available for Windows! */
        $owner = posix_getpwnam($user);
        $ownerId = $owner["uid"];
        $groupId = $owner["gid"];

        // Initialize an empty array of modified items.
        $modified = [];

        // Loop through all directories and files found using a recursive scan...
        foreach(self::scandirRecursive($root) as $filename)
        {
            // Set the current item's absolute path.
            $path = $root . DIRECTORY_SEPARATOR . $filename;

            // Get the current item's owner and group.
            $currentOwner = fileowner($path);
            $currentGroup = filegroup($path);

            // Convert the current permissions to their octal representation for later comparison.
            $currentPerms = intval(substr(sprintf('%o', fileperms($path)), -4), 8);

            // Prepare the access permissions, based on whether or not the current item is a directory or file.
            $permissions = is_dir($path) ? 0775 : 0664;

            // IF the current item's owner is not the same as the requested owner...
            if($currentOwner !== $ownerId)
            {
                // THEN append this item to the modified items array AND change the item's owner.
                $modified[$path]["owner"] = sprintf("%d -> %d", $currentOwner, $ownerId);
                chown($path, $ownerId);
            }

            // IF the current item's group is not the same as the requested group...
            if($currentGroup !== $groupId)
            {
                // THEN append this item to the modified items array AND change the item's group.
                $modified[$path]["group"] = sprintf("%d -> %d", $currentGroup, $groupId);
                chgrp($path, $groupId);
            }

            // IF the current item's access permissions are not the same as the requested permissions...
            if($currentPerms !== $permissions)
            {
                // THEN append this item to the modified items array AND change the item's permissions.
                $modified[$path]["perms"] = sprintf("%04o -> %04o", $currentPerms, $permissions);
                chmod($path, $permissions);
            }
        }

        /*
        $text = "";

        foreach($modified as $filename => $changes)
            $text .= "$filename : ".json_encode($changes)."\n";

        file_put_contents($root.DIRECTORY_SEPARATOR."fixed_permissions.txt", $text);
        */

        // Finally, return the array of modified items!
        return $modified;
    }

    #endregion

    #region STATES

    /**
     * Checks to determine if the Plugin is currently pending execution, via manual/scheduled execution.
     *
     * @return bool Returns TRUE if this Plugin is pending execution, otherwise FALSE.
     * @throws Exceptions\PluginNotInitializedException
     */
    public static function isExecuting(): bool
    {
        // Get the ROOT path, which will also throw the PluginNotInitializedException if necessary.
        $root = self::getRootPath();

        // Return TRUE if the UCRM specified file exists!
        return file_exists("$root/.ucrm-plugin-execution-requested");
    }

    // TODO: Upon feedback from UBNT, determine if it is possible to use something like Plugin::requestExecution()?

    /**
     * Checks to determine if the Plugin is currently executing, via manual/scheduled execution.
     *
     * @return bool Returns TRUE if this Plugin is currently executing, otherwise FALSE.
     * @throws Exceptions\PluginNotInitializedException
     */
    public static function isRunning(): bool
    {
        // Get the ROOT path, which will also throw the PluginNotInitializedException if necessary.
        $root = self::getRootPath();

        // Return TRUE if the UCRM specified file exists!
        return file_exists("$root/.ucrm-plugin-running");
    }

    #endregion

    #region BUNDLING (DEVELOPMENT ONLY)

    /*******************************************************************************************************************
     * NOTES:
     *   - The following are used to bundle the correct folders and files into a ZIP archive that can then be used by
     *     the UCRM systems to install/update the Plugin.
     *   - The methods are used only in the development environment, usually via composer script.
     *   - See individual options notes below.
     ******************************************************************************************************************/

    /**
     * @var string[]|null
     */
    private static $_ignoreCache = null;

    // -----------------------------------------------------------------------------------------------------------------
    /**
     * Builds a lookup cache from an optional .zipignore file.
     *
     * @param string $ignore An optional .zipignore file.
     * @return bool Returns TRUE when the file was parsed successfully, otherwise FALSE.
     * @throws Exceptions\PluginNotInitializedException
     */
    private static function buildIgnoreCache(string $ignore = ""): bool
    {
        // Generates the absolute path, given an optional ignore file or using the default.
        $ignore = $ignore ?: realpath(self::getRootPath()."/.zipignore");

        // IF an ignore file does not exist, THEN set the cache to empty and return FALSE!
        if (!$ignore || !file_exists($ignore))
        {
            // Set the cache to empty, but valid.
            self::$_ignoreCache = [];

            // Return failure!
            return false;
        }

        // OTHERWISE, load all the lines from the ignore file.
        $lines = explode("\n", file_get_contents($ignore));

        // Set a clean cache collection.
        $cache = [];

        // Loop through every line from the ignore file...
        foreach ($lines as $line) {

            // Trim any extra whitespace from the line.
            $line = trim($line);

            // IF the line is empty, THEN skip!
            if ($line === "")
                continue;

            // IF the line is a comment, THEN skip!
            if(substr($line, 0, 1) === "#")
                continue;

            // IF the line contains a trailing comment, THEN strip off the comment!
            if(strpos($line, "#") !== false)
            {
                $parts = explode("#", $line);
                $line = trim($parts[0]);
            }

            // This is a valid entry, so add it to the collection.
            $cache[] = $line;
        }

        // Set the cache to the newly build collection, even if it is completely empty.
        self::$_ignoreCache = $cache;

        // Return success!
        return true;
    }

    // -----------------------------------------------------------------------------------------------------------------
    /**
     * Checks an optional .zipignore file (or pre-built cache from the file) for inclusion of the specified string.
     *
     * @param string $path The relative path for which to search in the ignore file.
     * @param string $ignore The path to the optional ignore file, defaults to project root.
     *
     * @return bool Returns TRUE if the path is found in the file, otherwise FALSE.
     * @throws Exceptions\PluginNotInitializedException
     */
    private static function inIgnoreFile(string $path, string $ignore = ""): bool
    {
        if (!self::$_ignoreCache)
            self::buildIgnoreCache($ignore);

        // Identical match!
        if (array_search($path, self::$_ignoreCache, true) !== false)
            return true;

        // Partial match (at beginning only)!
        foreach (self::$_ignoreCache as $cacheItem)
        {
            if (strpos($path, $cacheItem) === 0)
                return true;
        }

        return false;
    }

    // -----------------------------------------------------------------------------------------------------------------
    /**
     * Creates a zip archive for use when installing this Plugin.
     *
     * @param string $root An optional path to root of the bundle, defaults to the root of the project.
     * @param string $name An optional name of the bundle, defaults to the root's parent folder name.
     * @param string $ignore Path to an optional .zipignore file, default is a file named .zipignore in the root folder.
     * @param string $zipPath An optional location other than the root to which the archive should be saved.
     * @throws Exceptions\PluginNotInitializedException
     */
    public static function bundle(string $root = "", string $name = "", string $ignore = "", string $zipPath = ""): void
    {
        // IF the root path is not specified, THEN attempt to use the initialized Plugin's root path.
        if($root === "")
            $root = self::getRootPath();

        echo "Bundling...\n";

        // Fail if the root path does not exist!
        if(!file_exists($root))
            die("The provided root path does not exist!\n".
                "- Provided: '$root'");

        // Fail if the root path is not a directory!
        if(!is_dir($root))
            die("The provided root path is a file and should be a directory!\n".
                "- Provided: '$root'");

        // Fix-Up the root path to match all the remaining paths.
        $root = realpath($root);

        // Determine the absolute path, if any to the .zipignore file.
        $ignore = realpath($ignore ?: $root."/.zipignore");

        // Generate the archive name based on the project's folder name.
        $archive_name = $name ?: basename($root);

        $archive_path = realpath($root);
        echo "$archive_path => $archive_name.zip\n";

        // Instantiate a recursive directory iterator set to parse the files.
        $directory = new RecursiveDirectoryIterator($archive_path);
        $file_info = new RecursiveIteratorIterator($directory);

        // Create an empty collection of files to store the final set.
        $files = [];

        // Iterate through ALL of the files and folders starting at the root path...
        foreach ($file_info as $info)
        {
            $real_path = $info->getPathname();
            $file_name = $info->getFilename();

            // Skip /. and /..
            if($file_name === "." || $file_name === "..")
                continue;

            $path = str_replace($root, "", $real_path); // Remove base path from the path string.
            $path = str_replace("\\", "/", $path); // Match .zipignore format
            $path = substr($path, 1, strlen($path) - 1); // Remove the leading "/"

            // IF there is no .zipignore file OR the current file is NOT listed in the .zipignore...
            if (!$ignore || !self::inIgnoreFile($path, $ignore))
            {
                // THEN add this file to the collection of files.
                $files[] = $path;
                echo "ADDED  : $path\n";
            }
            else
            {
                // OTHERWISE, ignore this file.
                echo "IGNORED: $path\n";
            }
        }

        // Generate the new archive's file name.
        $file_name = ($zipPath !== "" ? $zipPath : $root)."/$archive_name.zip";

        // IF the file previously existed, THEN remove it to avoid inserting it into the new archive!
        if(file_exists($file_name))
            unlink($file_name);

        // Create a new archive.
        $zip = new ZipArchive();

        // IF the archive could not be created, THEN fail here!
        if ($zip->open($file_name, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true)
            die("Unable to create the new archive: '$file_name'!");

        // Save the current working directory and move to the root path for the next steps!
        $old_dir = getcwd();
        chdir($root);

        // Loop through each file in the list...
        foreach ($files as $file)
        {
            // Add the file to the archive using the same relative paths.
            $zip->addFile($file, $file);
        }

        // Report the total number of files archived.
        $total_files = $zip->numFiles;
        echo "FILES  : $total_files\n";

        // Report success or failure (including error messages).
        $status = $zip->status !== 0 ? $zip->getStatusString() : "SUCCESS!";
        echo "STATUS : $status\n";

        // Close the archive, we're all finished!
        $zip->close();

        // Return to the previous working directory.
        chdir($old_dir);
    }

    #endregion

    #region SETTINGS

    /**
     * @var string
     */
    private static $_settingsFile = "";

    /**
     * Generates a class with auto-implemented methods and then saves it to a PSR-4 compatible file.
     *
     * @param string $namespace An optional namespace to use for the settings file, defaults to "MVQN\UCRM\Plugins".
     * @param string $class An optional class name to use for the settings file, defaults to "Settings".
     * @param string|null $path
     *
     * @throws Exceptions\ManifestElementException
     * @throws Exceptions\PluginNotInitializedException
     */
    public static function createSettings(string $namespace = self::_DEFAULT_SETTINGS_NAMESPACE,
        string $class = self::_DEFAULT_SETTINGS_CLASSNAME, string $path = null): void
    {
        // Get the root path for this Plugin, throws an Exception if not already initialized.
        $root = self::getRootPath();

        // TODO: Test the need for DIRECTORY_SEPARATOR here...
        // Generate the source path based on namespace using PSR-4 standards for composer.
        $path = ($path === null ? self::getSourcePath() : $path)."/".str_replace("\\", "/", $namespace);

        // IF the path does not already exist, THEN create it, recursively (as needed)!
        if(!file_exists($path))
            mkdir($path, 0777, true);

        // Clean-Up the absolute path.
        $path = realpath($path);

        // Create the namespace.
        $_namespace = new PhpNamespace($namespace);

        // Add the necessary 'use' statements.
        $_namespace->addUse(SettingsBase::class);

        // Create and add the new Settings class.
        $_class = $_namespace->addClass($class);

        // Set the necessary parts of the class.
        $_class
            ->setFinal()
            ->setExtends(SettingsBase::class)
            ->addComment("@author Ryan Spaeth <rspaeth@mvqn.net>\n");

        #region Project

        $projectPath = dirname($root) === "/data/ucrm/data/plugins" ? $root : dirname($root);

        //$_class->addConstant("PROJECT_NAME", basename(realpath(Plugin::getRootPath()."/../")))
        $_class->addConstant("PROJECT_NAME", basename($projectPath))
            ->setVisibility("public")
            ->addComment("@const string The name of this Project, based on the root folder name.");

        //$_class->addConstant("PROJECT_ROOT_PATH", realpath(Plugin::getRootPath()."/../"))
        $_class->addConstant("PROJECT_ROOT_PATH", $projectPath)
            ->setVisibility("public")
            ->addComment("@const string The absolute path to this Project's root folder.");

        #endregion

        #region Plugin

        $_class->addConstant("PLUGIN_NAME", self::manifest("information/name"))
            ->setVisibility("public")
            ->addComment("@const string The name of this Plugin, based on the manifest information.");

        $_class->addConstant("PLUGIN_ROOT_PATH", self::getRootPath())
            ->setVisibility("public")
            ->addComment("@const string The absolute path to the root folder of this project.");

        $_class->addConstant("PLUGIN_DATA_PATH", self::getDataPath())
            ->setVisibility("public")
            ->addComment("@const string The absolute path to the data folder of this project.");

        $_class->addConstant("PLUGIN_LOGS_PATH", self::getLogsPath())
            ->setVisibility("public")
            ->addComment("@const string The absolute path to the logs folder of this project.");

        $_class->addConstant("PLUGIN_SOURCE_PATH", self::getSourcePath())
            ->setVisibility("public")
            ->addComment("@const string The absolute path to the source folder of this project.");

        #endregion

        #region ucrm.json

        //$ucrm = json_decode(file_get_contents($root."/ucrm.json"), true);
        $ucrm = self::ucrm();

        // IF the UCRM's public URL is not set...
        if($ucrm["ucrmPublicUrl"] === null)
        {
            // AND the HTTP module is required...
            if(self::hasModule(self::MODULE_HTTP))
            {
                // THEN display the requirement and exit!
                echo "
                    <p>This UCRM's public URL could not be determined and is required for this Plugin to function properly!</p>
                    <p>Some things to check:</p>
                    <ul>
                        <li>
                            <!--suppress HtmlUnknownTarget -->
                            <a href='/system/settings/application' target='_parent'>Server domain name</a> has not been set?
                        </li>
                    </ul>
                ";

                Log::info("This plugin uses the HTTP module, which requires that the 'Server domain name' be configured in System -> Settings.");
                exit();
            }

            // OTHERWISE, set the UCRM's public URL to that of the public facing IP address?

            /** @noinspection SpellCheckingInspection */
            // Get the current external IP address by lookup to "checkip.dyndns.com".
            //$externalContent = file_get_contents('http://checkip.dyndns.com/');
            //preg_match('/Current IP Address: \[?([:.0-9a-fA-F]+)]?/', $externalContent, $m);
            //$externalIp = $m[1];

            // Assume HTTP, as without a FQDN set in UCRM, there cannot be a valid certificate!
            //$ucrm["ucrmPublicUrl"] = "http://$externalIp/";

            //Log::info("This UCRM's public URL has been set dynamically to: {$ucrm['ucrmPublicUrl']}");

            // TODO: Determine if we should set this in the "ucrm.json" file to cache future lookups?
        }

        $_class
            ->addConstant("UCRM_PUBLIC_URL", rtrim($ucrm["ucrmPublicUrl"], "/"))
            ->setVisibility("public")
            ->addComment("@const string|null The public URL of this UCRM server, null if not configured.");

        $_class
            ->addConstant("UCRM_LOCAL_URL", $ucrm["ucrmLocalUrl"] !== null ? rtrim($ucrm["ucrmLocalUrl"], "/") : null)
            ->setVisibility("public")
            ->addComment("@const string|null The local URL of this UCRM server, null if not configured.");

        // IF this plugin is installed on UNMS 1.0.0-beta.1 or above, THEN this key should exist...
        if(array_key_exists("unmsLocalUrl", $ucrm))
        {
            $unmsLocalUrl = $ucrm["unmsLocalUrl"];

            $_class
                ->addConstant("UNMS_LOCAL_URL", $unmsLocalUrl !== null ? rtrim($unmsLocalUrl, "/") : null)
                ->setVisibility("public")
                ->addComment("@const string|null The local URL of this UNMS server, null if not configured.");
        }

        // IF this plugin's public URL is not set AND there is a "public.php" file present...
        if($ucrm["pluginPublicUrl"] === null && file_exists($root."/public.php"))
        {
            // AND the HTTP module is required...
            if(self::hasModule(self::MODULE_HTTP))
            {
                // THEN display the requirement and exit!
                echo "
                    <p>This plugin's public URL could not be determined and is required to function properly!</p>
                    <p>Some things to check:</p>
                    <ul>
                        <li>
                            <!--suppress HtmlUnknownTarget -->
                            <a href='/system/settings/application' target='_parent'>Server domain name</a> has not been set?
                        </li>
                    </ul>
                ";

                Log::info("This plugin uses the HTTP module, which requires that the 'Server domain name' be configured in System -> Settings.");
                exit();
            }

            // OTHERWISE, set the Plugin's public URL dynamically?
            //$ucrm["pluginPublicUrl"] =
            //    "{$ucrm['ucrmPublicUrl']}_plugins/{$manifest['information']['name']}/public.php";

            //Log::info("This Plugin's public URL has been set dynamically to: {$ucrm['pluginPublicUrl']}");
        }

        $_class->addConstant("PLUGIN_PUBLIC_URL", $ucrm["pluginPublicUrl"])
            ->setVisibility("public")
            ->addComment("@const string The public URL of this UCRM server, null if not configured.");

        // NOTE: This should NEVER really happen!
        // IF there is no App Key setup for this plugin...
        if($ucrm["pluginAppKey"] === null)
        {
            // AND the HTTP module is required...
            if(self::hasModule(self::MODULE_REST))
            {
                // THEN display the requirement and exit!
                echo "
                    <p>This plugin's App Key could not be determined and is required to function properly!</p>
                ";

                Log::info("This plugin uses the REST module, which requires that an 'App Key' was generated by the plugin installer.");
                exit();
            }
        }

        $_class->addConstant("PLUGIN_APP_KEY", $ucrm["pluginAppKey"])
            ->setVisibility("public")
            ->addComment("@const string An automatically generated UCRM API 'App Key' with read/write access.");

        $_class->addConstant("PLUGIN_ID", $ucrm["pluginId"])
            ->setVisibility("public")
            ->addComment("@const string An automatically generated UCRM Plugin ID.");

        #endregion

        #region version.yml

        if(file_exists("/usr/src/ucrm/app/config/version.yml"))
        {
            // THEN, parse the file and add the following constants to the Settings!
            $version = Yaml::parseFile("/usr/src/ucrm/app/config/version.yml")["parameters"];

            $_class->addConstant("UCRM_VERSION", $version["version"])
                ->setVisibility("public")
                ->addComment("@const string The UCRM server's current version.");

            $_class->addConstant("UCRM_VERSION_STABILITY", $version["version_stability"])
                ->setVisibility("public")
                ->addComment("@const string The UCRM server's current version stability.");
        }

        #endregion

        #region parameters.yml

        if(file_exists("/usr/src/ucrm/app/config/parameters.yml"))
        {
            $parameters = Yaml::parseFile("/usr/src/ucrm/app/config/parameters.yml")["parameters"];

            $_class->addConstant("UCRM_DB_DRIVER", $parameters["database_driver"])
                ->setVisibility("public")
                ->addComment("@const string The UCRM Database Driver.");

            // NOTE: This is "localhost" in UNMS 1.0.0-beta.1 and above, but used to be "postgresql".
            $_class->addConstant("UCRM_DB_HOST", $parameters["database_host"])
                ->setVisibility("public")
                ->addComment("@const string The UCRM Database Host.");

            $_class->addConstant("UCRM_DB_NAME", $parameters["database_name"])
                ->setVisibility("public")
                ->addComment("@const string The UCRM Database Name.");

            $_class->addConstant("UCRM_DB_PASSWORD", $parameters["database_password"])
                ->setVisibility("public")
                ->addComment("@const string The UCRM Database Password.");

            $_class->addConstant("UCRM_DB_PORT", $parameters["database_port"])
                ->setVisibility("public")
                ->addComment("@const string The UCRM Database Port.");

            $_class->addConstant("UCRM_DB_USER", $parameters["database_user"])
                ->setVisibility("public")
                ->addComment("@const string The UCRM Database User.");
        }

        #endregion

        #region Configuration

        // Loop through each key/value pair in the file...
        foreach(self::manifest("configuration") as $setting)
        {
            // Create a new Setting for each element, parsing the given values.
            $_setting = new Setting($setting);

            // Append the '|null' suffix to the type, if the value is NOT required.
            $type = $_setting->type.(!$_setting->required ? "|null" : "");

            // Add the property to the current Settings class.
            $_property = $_class->addProperty($_setting->key);

            // Set the necessary parts of the property.
            $_property
                ->setVisibility("protected")
                ->setStatic()
                ->addComment("{$_setting->label}")
                ->addComment("@var {$type} {$_setting->description}");

            // Generate the name of the AutoObject's getter method for this property.
            $getter = "get".ucfirst($_setting->key);

            // And then append it to the class comments for Annotation lookup and IDE auto-completion.
            $_class->addComment("@method static $type $getter()");
        }

        #endregion

        // Generate the code for the Settings file.
        $code =
            "<?php\n".
            "/** @noinspection PhpUnused */\n" .
            "/** @noinspection SpellCheckingInspection */\n".
            "declare(strict_types=1);\n".
            "\n".
            $_namespace;

        // Hack to add an extra line break between const declarations, as Nette\PhpGenerator does NOT!
        $code = str_replace(";\n\t/** @const", ";\n\n\t/** @const", $code);

        // Generate and set the Settings file absolute path.
        self::$_settingsFile = $path."/".$class.".php";

        // Save the code to the file location.
        file_put_contents(self::$_settingsFile, $code);

    }

    /**
     * @param string $name The name of the constant to append to this Settings class.
     * @param mixed $value The value of the constant to append to this Settings class.
     * @param string $comment An optional comment for this constant.
     * @return bool Returns TRUE if the constant was successfully appended, otherwise FALSE.
     * @throws Exception
     */
    public static function appendSettingsConstant(string $name, $value, string $comment = ""): bool
    {
        // IF the Settings file not been assigned or the file does not exist...
        if(self::$_settingsFile === "" || !file_exists(self::$_settingsFile))
            // Attempt to create the Settings now!
            self::createSettings();

        // Now load the Settings file contents.
        $code = file_get_contents(self::$_settingsFile);

        // Find all the occurrences of the constants using RegEx, getting the file positions as well.
        $constRegex = "/(\/\*\* @const (?:[\w\|\[\]]+).*\*\/)[\r|\n|\r\n]+(?:.*;[\r|\n|\r\n]+)([\r|\n|\r\n]+)/m";
        preg_match_all($constRegex, $code, $matches, PREG_OFFSET_CAPTURE);

        // IF there are no matches found OR the matches array does not contain the offsets part...
        if($matches === null || count($matches) !== 3)
            // THEN return failure!
            return false;

        // Get the position of the very last occurrence of the matches.
        $position = $matches[2][count($matches[2]) - 1][1];

        // Get the type of the "mixed" value as to set it correctly in the constant field...
        switch(gettype($value))
        {
            case "boolean":
                $typeString = "bool";
                $valueString = $value ? "true" : "false";
                break;
            case "integer":
                $typeString = "int";
                $valueString = "$value";
                break;
            case "double":
                $typeString = "float";
                $valueString = "$value";
                break;
            case "string":
                $typeString = "string";
                $valueString = "'$value'";
                break;
            case "array":
            case "object":
            case "resource":
            case "NULL":
                // NOT SUPPORTED!
                return false;

            case "unknown type":
            default:
                // Cannot determine key components, so return!
                return false;
        }

        // Generate the new constant code.
        $const = "\r\n".
            "\t/** @const $typeString".($comment ? " ".$comment : "")." */\r\n".
            "\tpublic const $name = $valueString;\r\n";

        // Append the new constant code after the last existing constant in the Settings file.
        $code = substr_replace($code, $const, $position, 0);

        // Save the contents over the existing file.
        file_put_contents(self::$_settingsFile, $code);

        // Finally, return success!
        return true;
    }

    #endregion

    #region ENCRYPTION / DECRYPTION

    /**
     * Gets the cryptographic key from the UCRM file system.
     *
     * @return Key
     * @throws Exceptions\CryptoKeyNotFoundException
     * @throws Exceptions\PluginNotInitializedException
     * @throws BadFormatException
     * @throws EnvironmentIsBrokenException
     */
    public static function getCryptoKey(): Key
    {
        // Set the path to the cryptographic key.
        $path = self::getRootPath() . "/../../encryption/crypto.key";

        // IF the file exists at the correct location, THEN return key, OTHERWISE return null!
        if(file_exists($path))
            return Key::loadFromAsciiSafeString(file_get_contents($path));

        // Handle DEV environment!
        if(getenv("CRYPTO_KEY") !== false)
            return Key::loadFromAsciiSafeString(getenv("CRYPTO_KEY"));

        throw new Exceptions\CryptoKeyNotFoundException("File not found at: '$path'!\n");
    }

    // -----------------------------------------------------------------------------------------------------------------
    /**
     * Decrypts a string using the provided cryptographic key.
     *
     * @param string $string The string to decrypt.
     * @param Key|null $key The key to use for decryption, or automatic detection if not provided.
     * @return string Returns the decrypted string.
     * @throws Exceptions\CryptoKeyNotFoundException
     * @throws Exceptions\PluginNotInitializedException
     * @throws BadFormatException
     * @throws EnvironmentIsBrokenException
     * @throws WrongKeyOrModifiedCiphertextException
     */
    public static function decrypt(string $string, Key $key = null): string
    {
        // Set the key specified; OR if not provided, get the key from the UCRM file system.
        $key = $key ?? self::getCryptoKey();

        // Decrypt and return the string!
        return Crypto::decrypt($string, $key);
    }

    // -----------------------------------------------------------------------------------------------------------------
    /**
     * Encrypts a string using the provided cryptographic key.
     *
     * @param string $string The string to encrypt.
     * @param Key|null $key The key to use for decryption, or automatic detection if not provided.
     * @return string Returns the encrypted string.
     * @throws Exceptions\CryptoKeyNotFoundException
     * @throws Exceptions\PluginNotInitializedException
     * @throws BadFormatException
     * @throws EnvironmentIsBrokenException
     */
    public static function encrypt(string $string, Key $key = null): ?string
    {
        // Set the key specified; OR if not provided, get the key from the UCRM file system.
        $key = $key ?? self::getCryptoKey();

        // Encrypt and return the string!
        return Crypto::encrypt($string, $key);
    }

    #endregion

    #region ENVIRONMENT

    /**
     * @return string
     * @throws Exceptions\PluginNotInitializedException
     */
    public static function environment(): string
    {
        return (file_exists(self::getRootPath() . "/.env")) ? "dev" : "prod";
    }

    #endregion

    #region DATABASE (plugin.db)

    /**
     * @var PDO|null
     */
    private static $_pdo = null;

    /**
     * @return PDO
     * @throws Exceptions\PluginNotInitializedException
     */
    public static function database(): PDO
    {
        $path = self::getDataPath() . DIRECTORY_SEPARATOR . "plugin.db";

        if(!self::$_pdo)
        {
            try
            {
                self::$_pdo = new PDO(
                    "sqlite:".$path,
                    null,
                    null,
                    [
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                    ]
                );
            }
            catch(PDOException $e)
            {
                http_response_code(400);
                die("The Plugin's Database could not be opened!\n$e");
            }
        }

        return self::$_pdo;
    }

    /**
     * @param string $statement
     *
     * @return array
     * @throws Exceptions\PluginNotInitializedException
     */
    public static function dbQuery(string $statement): array
    {
        $pdo = self::database();

        try
        {
            return $pdo->query($statement)->fetchAll();
        }
        catch(PDOException $e)
        {
            http_response_code(400);
            die("The Plugin's Database could not be accessed!\n$e");
        }
    }

    #endregion


}
