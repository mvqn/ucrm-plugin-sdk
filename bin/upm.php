#!/usr/bin/env php
<?php
declare(strict_types=1);

// Should always be "PLUGIN_ROOT/src" during development.
$pluginPath = getcwd();

require_once $pluginPath."/vendor/autoload.php";

use UCRM\Common\Plugin;

/**
 * composer.php
 *
 * A shared script that handles composer script execution from the command line.
 *
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 */

if($argc === 1)
{
    $usage = "\n".
        "Usage:\n".
        "    composer.php [create|bundle]\n";

    die($usage);
}

$projectPath = realpath($pluginPath."/../");
$pluginName = baseName($projectPath);

if(file_exists($pluginPath."/../.env"))
    (new \Dotenv\Dotenv($pluginPath."/../"))->load();

Plugin::initialize($pluginPath);

// Handle the different command line arguments...
switch ($argv[1])
{
    // Perform initialization of the Plugin libraries and create the auto-generated Settings class.
    case "create":
        Plugin::createSettings("App", "Settings", $pluginPath."/src/");
        break;

    // Bundle the 'zip/' directory into a package ready for Plugin installation on the UCRM server.
    case "bundle":
        // Create the Settings, if needed!
        if(!file_exists("$pluginPath/src/App/Settings.php"))
            Plugin::createSettings("App", "Settings", $pluginPath."/src/");

        Plugin::bundle($pluginPath, \App\Settings::PLUGIN_NAME, ".zipignore", $projectPath);
        break;

    /*
    case "sync":
        $host = getenv("UCRM_SFTP_HOSTNAME");
        $port = 22;
        $user = getenv("UCRM_SFTP_USERNAME");
        $pass = getenv("UCRM_SFTP_PASSWORD");

        $sftp = new \MVQN\SFTP\SftpClient($host, $port);
        $sftp->login($user, $pass);
        $sftp->setRemoteBasePath("/home/ucrm/data/ucrm/ucrm/data/plugins/$pluginName/");
        $sftp->setLocalBasePath(__DIR__."/../");

        foreach([ "/ucrm.json", "/data/config.json" ] as $file)
            $data = $sftp->download($file);

        break;
    */

    // TODO: More commands to come!

    default:
        break;
}
