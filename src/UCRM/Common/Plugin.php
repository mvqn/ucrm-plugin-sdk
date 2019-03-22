<?php
declare(strict_types=1);

namespace UCRM\Common;

use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;
use Nette\PhpGenerator\PhpNamespace;

/**
 * Class Plugin
 *
 * @package UCRM\Plugins
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 * @final
 */
final class Plugin
{
    // =================================================================================================================
    // CONSTANTS
    // =================================================================================================================

    // The default class name for the Settings singleton.
    private const DEFAULT_SETTINGS_CLASSNAME = "Settings";

    // The default namespace for the Settings singleton.
    private const DEFAULT_SETTINGS_NAMESPACE = "Plugin";

    // =================================================================================================================
    // INITIALIZATION
    // =================================================================================================================

    /**
     * Initializes the Plugin singleton. This method should ALWAYS be called before any other method here, with the
     * exception of Plugin::bundle(), provided a root path is given to that method.
     *
     * @param string $root The root of this Plugin.
     * @throws Exceptions\RequiredDirectoryNotFoundException
     * @throws Exceptions\RequiredFileNotFoundException
     */
    public static function initialize(string $root)
    {
        $root = realpath($root);

        // Fail if the root path does not exist!
        if(!$root || !file_exists($root))
            die("The provided root path does not exist!\n".
                "- Provided: '$root'\n");

        // Fail if the root path is not a directory!
        if(!$root || !is_dir($root))
            die("The provided root path is a file and should be a directory!\n".
                "- Provided: '$root'\n");

        // Fail if the data path does not exist!
        $data = realpath($root."/data/");
        if(!$data || !file_exists($data))
            throw new Exceptions\RequiredDirectoryNotFoundException(
                "The provided root path '$root' does not contain a 'data' directory!\n");

        // Fail if the config file does not exist!
        $config = realpath($root."/data/config.json");
        if(!$config || !file_exists($config))
            throw new Exceptions\RequiredFileNotFoundException(
                "The provided root path '$root' does not contain a 'data/config.json' file!\n");

        // Fail if the manifest file does not exist!
        $manifest = realpath($root."/manifest.json");
        if(!$manifest || !file_exists($manifest))
            throw new Exceptions\RequiredFileNotFoundException(
                "The provided root path '$root' does not contain a 'manifest.json' file!\n");

        // Fail if the ucrm file does not exist!
        $ucrm = realpath($root."/ucrm.json");
        if(!$ucrm || !file_exists($ucrm))
            throw new Exceptions\RequiredFileNotFoundException(
                "The provided root path '$root' does not contain a 'ucrm.json' file!\n");

        self::$_rootPath = $root;
    }

    // =================================================================================================================
    // PATHS
    // =================================================================================================================

    /**
     * @var string The root path of this Plugin.
     */
    private static $_rootPath = "";

    // -----------------------------------------------------------------------------------------------------------------
    /**
     * @return string Returns the absolute ROOT path of this Plugin.
     * @throws Exceptions\PluginNotInitializedException
     */
    public static function getRootPath(): string
    {
        // IF the plugin is not initialized, THEN throw an Exception!
        if(self::$_rootPath === "")
            throw new Exceptions\PluginNotInitializedException(
                "The plugin must be initialized using 'Plugin::initialize()' before calling any other methods!\n");

        // Finally, return the ROOT path!
        return self::$_rootPath;
    }

    // -----------------------------------------------------------------------------------------------------------------
    /**
     * @return string Returns the absolute SOURCE path of this Plugin.
     * @throws Exceptions\PluginNotInitializedException
     */
    public static function getSourcePath(): string
    {
        // Get the ROOT path, which will also throw the PluginNotInitializedException if necessary.
        $root = self::getRootPath();

        // IF the directory does not exist, THEN create it...
        if(!file_exists("$root/src/"))
            mkdir("$root/src/");

        // Finally, return the SOURCE path!
        return realpath("$root/src/");
    }

    // -----------------------------------------------------------------------------------------------------------------
    /**
     * @return string Returns the absolute DATA path of this Plugin.
     * @throws Exceptions\PluginNotInitializedException
     */
    public static function getDataPath(): string
    {
        // Get the ROOT path, which will also throw the PluginNotInitializedException if necessary.
        $root = self::getRootPath();

        // IF the directory does not exist, THEN create it...
        if(!file_exists("$root/data/"))
            mkdir("$root/data/");

        // Finally, return the DATA path!
        return realpath("$root/data/");
    }

    // -----------------------------------------------------------------------------------------------------------------
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

    // -----------------------------------------------------------------------------------------------------------------
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

    // =================================================================================================================
    // STATES
    // =================================================================================================================

    /**
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

    // -----------------------------------------------------------------------------------------------------------------
    /**
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

    // =================================================================================================================
    // BUNDLING
    // =================================================================================================================

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

        return array_search($path, self::$_ignoreCache, true) !== false;
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
        $directory = new \RecursiveDirectoryIterator($archive_path);
        $file_info = new \RecursiveIteratorIterator($directory);

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
        $zip = new \ZipArchive();

        // IF the archive could not be created, THEN fail here!
        if ($zip->open($file_name, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true)
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

    // =================================================================================================================
    // SETTINGS
    // =================================================================================================================

    /**
     * @var string
     */
    private static $_settingsFile = "";

    // -----------------------------------------------------------------------------------------------------------------
    /**
     * Generates a class with auto-implemented methods and then saves it to a PSR-4 compatible file.
     * @param string $namespace An optional namespace to use for the settings file, defaults to "MVQN\UCRM\Plugins".
     * @param string $class An optional class name to use for the settings file, defaults to "Settings".
     * @throws Exceptions\ManifestElementException
     * @throws Exceptions\PluginNotInitializedException
     */
    public static function createSettings(string $namespace = self::DEFAULT_SETTINGS_NAMESPACE,
        string $class = self::DEFAULT_SETTINGS_CLASSNAME, string $path = null): void
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

        // Set any desired constants to be included in the Settings file by default...

        $_class->addConstant("PROJECT_NAME", basename(realpath(Plugin::getRootPath()."/../")))
            ->setVisibility("public")
            ->addComment("@const string The name of this Project, based on the root folder name.");

        $_class->addConstant("PROJECT_ROOT_PATH", realpath(Plugin::getRootPath()."/../"))
            ->setVisibility("public")
            ->addComment("@const string The absolute path to this Project's root folder.");

        $manifest = json_decode(file_get_contents(Plugin::getRootPath()."/manifest.json"), true);

        $_class->addConstant("PLUGIN_NAME", $manifest["information"]["name"])
            ->setVisibility("public")
            ->addComment("@const string The name of this Project, based on the root folder name.");

        $_class->addConstant("PLUGIN_ROOT_PATH", Plugin::getRootPath())
            ->setVisibility("public")
            ->addComment("@const string The absolute path to the root path of this project.");

        $_class->addConstant("PLUGIN_DATA_PATH", Plugin::getDataPath())
            ->setVisibility("public")
            ->addComment("@const string The absolute path to the data path of this project.");

        $_class->addConstant("PLUGIN_SOURCE_PATH", Plugin::getSourcePath())
            ->setVisibility("public")
            ->addComment("@const string The absolute path to the source path of this project.");

        // IF a ucrm.json file exists...
        if(file_exists($root."/ucrm.json"))
        {
            // THEN, load the values from the file.
            $ucrm = json_decode(file_get_contents($root."/ucrm.json"), true);

            // Set each value from the file as a constant, as these should NEVER change after the Plugin is installed...

            $_class->addConstant("UCRM_PUBLIC_URL", $ucrm["ucrmPublicUrl"])
                ->setVisibility("public")
                ->addComment("@const string The publicly accessible URL of this UCRM, null if not configured in UCRM.");

            if(array_key_exists("ucrmLocalUrl", $ucrm))
                $_class->addConstant("UCRM_LOCAL_URL", $ucrm["ucrmLocalUrl"])
                    ->setVisibility("public")
                    ->addComment("@const string The locally accessible URL of this UCRM, null if not configured in UCRM.");

            $_class->addConstant("PLUGIN_PUBLIC_URL", $ucrm["pluginPublicUrl"])
                ->setVisibility("public")
                ->addComment("@const string The publicly accessible URL assigned to this Plugin by the UCRM.");

            $_class->addConstant("PLUGIN_APP_KEY", $ucrm["pluginAppKey"])
                ->setVisibility("public")
                ->addComment("@const string An automatically generated UCRM API 'App Key' with read/write access.");
        }

        // Load the contents of the manifest.json file.
        $data = json_decode(file_get_contents($root."/manifest.json"), true);

        // Get the configuration section, specifically.
        $data = array_key_exists("configuration", $data) ? $data["configuration"] : [];

        // Loop through each key/value pair in the file...
        foreach($data as $setting)
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

        // Generate the code for the Settings file.
        $code =
            "<?php /** @noinspection SpellCheckingInspection */\n".
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

    // -----------------------------------------------------------------------------------------------------------------
    /**
     * @param string $name The name of the constant to append to this Settings class.
     * @param mixed $value The value of the constant to append to this Settings class.
     * @param string $comment An optional comment for this constant.
     * @return bool Returns TRUE if the constant was successfully appended, otherwise FALSE.
     * @throws \Exception
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

    // =================================================================================================================
    // ENCRYPTION / DECRYPTION
    // =================================================================================================================

    /**
     * Gets the cryptographic key from the UCRM file system.
     *
     * @return Key
     * @throws Exceptions\CryptoKeyNotFoundException
     * @throws Exceptions\PluginNotInitializedException
     * @throws \Defuse\Crypto\Exception\BadFormatException
     * @throws \Defuse\Crypto\Exception\EnvironmentIsBrokenException
     */
    public static function getCryptoKey(): Key
    {
        // Set the path to the cryptographic key.
        $path = Plugin::getRootPath()."/../../encryption/crypto.key";

        // IF the file exists at the correct location, THEN return key, OTHERWISE return null!
        if(file_exists($path))
            return Key::loadFromAsciiSafeString(file_get_contents($path));

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
     * @throws \Defuse\Crypto\Exception\BadFormatException
     * @throws \Defuse\Crypto\Exception\EnvironmentIsBrokenException
     * @throws \Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException
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
     * @throws \Defuse\Crypto\Exception\BadFormatException
     * @throws \Defuse\Crypto\Exception\EnvironmentIsBrokenException
     */
    public static function encrypt(string $string, Key $key = null): ?string
    {
        // Set the key specified; OR if not provided, get the key from the UCRM file system.
        $key = $key ?? self::getCryptoKey();

        // Encrypt and return the string!
        return Crypto::encrypt($string, $key);
    }



    /**
     * @return string
     * @throws Exceptions\PluginNotInitializedException
     */
    public static function environment(): string
    {
        return (file_exists(self::getRootPath()."/../.env")) ? "dev" : "prod";
    }




}
