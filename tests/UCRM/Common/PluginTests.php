<?php
declare(strict_types=1);

namespace UCRM\Common;


use MVQN\Common\Directories;
use PHPUnit\Framework\TestCase;
use UCRM\Common\Exceptions\RequiredDirectoryNotFoundException;
use UCRM\Common\Exceptions\RequiredFileNotFoundException;

use PDO;
use PDOException;

/**
 * Class PluginTests
 *
 * @coversDefaultClass \UCRM\Common\Plugin
 *
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 */
class PluginTests extends TestCase
{
    protected const EXAMPLE_ROOT = __DIR__ . "/../../../examples/template/src";

    protected function setUp(): void
    {
        //Plugin::initialize(self:: EXAMPLE_ROOT);
    }

    protected function tearDown(): void
    {
        //if($database = realpath(self::EXAMPLE_ROOT . "/data/plugin.db"))
        //    Plugin::dbDelete();
    }

    public static function tearDownAfterClass(): void
    {
        if($database = realpath(self::EXAMPLE_ROOT . "/data/plugin.db"))
            Plugin::dbDelete();
    }

    #region INITIALIZATION

    /**
     * @covers ::initialize
     * @group Initialization
     */
    public function testInitialize()
    {
        Plugin::initialize(self::EXAMPLE_ROOT);
        $this->assertTrue(Plugin::isInitialized());
    }

    /**
     * @covers ::initialize
     * @group Initialization
     */
    public function testInitializeWithAutoDetect()
    {
        // Set the path for an auto-detection script.
        $path = self::EXAMPLE_ROOT . "/autodetect.php";

        // Set the script code to execute.
        $script =
            "<?php\n" .
            "require_once __DIR__. '/../../../vendor/autoload.php';\n" .
            "\UCRM\Common\Plugin::initialize();\n";

        // Save the script to a file in the correct location.
        file_put_contents($path, $script);

        /** @noinspection PhpIncludeInspection */
        include $path;

        // Assert that the auto-detection worked!
        $this->assertTrue(Plugin::isInitialized());

        // And then delete the auto-detection test script.
        unlink($path);
    }

    /**
     * @covers ::initialize
     * @group Initialization
     */
    public function testInitializeWithMissingRootDirectory()
    {
        $this->expectException(RequiredDirectoryNotFoundException::class);
        Plugin::initialize(self::EXAMPLE_ROOT . "/wrong");
        Plugin::initialize(self::EXAMPLE_ROOT . "/manifest.json");
    }

    /**
     * @covers ::initialize
     * @group Initialization
     */
    public function testInitializeWithMissingManifestFile()
    {
        $file = realpath(self::EXAMPLE_ROOT . DIRECTORY_SEPARATOR . "manifest.json");

        try
        {
            // BACKUP: Rename the file.
            rename($file, "$file.bak");

            // Initialize the Plugin, with missing file.
            Plugin::initialize(self::EXAMPLE_ROOT);
        }
        catch(RequiredFileNotFoundException $e)
        {
            // Verify the expected Exception was thrown.
            $this->assertEquals(RequiredFileNotFoundException::class, get_class($e));
            $this->assertStringContainsString("'manifest.json'", $e->getMessage());
        }
        finally
        {
            // RESTORE: Rename the backup file.
            rename("$file.bak", $file);
        }
    }

    /**
     * @covers ::initialize
     * @group Initialization
     */
    public function testInitializeWithMissingUcrmFile()
    {
        $file = realpath(self::EXAMPLE_ROOT . DIRECTORY_SEPARATOR . "ucrm.json");

        try
        {
            // BACKUP: Rename the file.
            rename($file, "$file.bak");

            // Initialize the Plugin, with missing file.
            Plugin::initialize(self::EXAMPLE_ROOT);
        }
        catch(RequiredFileNotFoundException $e)
        {
            // Verify the expected Exception was thrown.
            $this->assertEquals(RequiredFileNotFoundException::class, get_class($e));
            $this->assertStringContainsString("'ucrm.json'", $e->getMessage());
        }
        finally
        {
            // RESTORE: Rename the backup file.
            rename("$file.bak", $file);
        }
    }

    /**
     * @covers ::initialize
     * @group Initialization
     */
    public function testInitializeWithMissingDataDirectory()
    {
        $directory = realpath(self::EXAMPLE_ROOT . DIRECTORY_SEPARATOR . "data");

        // BACKUP: Rename the directory.
        rename($directory, "$directory.bak");

        // Initialize the Plugin, with missing directory.
        Plugin::initialize(self::EXAMPLE_ROOT);

        // Expect the directory to have been created.
        $this->assertDirectoryExists($directory);

        // Remove the newly created directory, including all files.
        Directories::rmdir($directory, true);

        // RESTORE: Rename the backup directory.
        rename("$directory.bak", $directory);
    }

    /**
     * @covers ::initialize
     * @group Initialization
     */
    public function testInitializeWithMissingConfigFile()
    {
        $path = self::EXAMPLE_ROOT . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "config.json";
        $real = realpath($path);

        // BACKUP: Rename the file.
        if($real)
            rename($real, "$real.bak");

        // Initialize the Plugin, with missing file.
        Plugin::initialize(self::EXAMPLE_ROOT);

        // Assert that the file exists now!
        $this->assertFileExists($path);

        // Delete the newly generated file.
        if(!$real)
            unlink($path);

        // RESTORE: Rename the backup file.
        if($real)
            rename("$real.bak", $real);
    }

    /**
     * @covers ::initialize
     * @group Initialization
     */
    public function testInitializeWithMissingLogFile()
    {
        $path = self::EXAMPLE_ROOT . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "plugin.log";
        $real = realpath($path);

        // BACKUP: Rename the file.
        if($real)
            rename($real, "$real.bak");

        // Initialize the Plugin, with missing file.
        Plugin::initialize(self::EXAMPLE_ROOT);

        // Assert that the file exists now!
        $this->assertFileExists($path);

        // Delete the newly generated file.
        if(!$real)
            unlink($path);

        // RESTORE: Rename the backup file.
        if($real)
            rename("$real.bak", $real);
    }

    /**
     * @covers ::initialize
     * @group Initialization
     */
    public function testInitializeWithMissingSourceDirectory()
    {
        $directory = realpath(self::EXAMPLE_ROOT . DIRECTORY_SEPARATOR . Plugin::PATH_SOURCE);

        // BACKUP: Rename the directory.
        rename($directory, "$directory.bak");

        // Initialize the Plugin, with missing directory.
        Plugin::initialize(self::EXAMPLE_ROOT);

        // Expect the directory to have been created.
        $this->assertDirectoryExists($directory);

        // Remove the newly created directory, including all files.
        Directories::rmdir($directory, true);

        // RESTORE: Rename the backup directory.
        rename("$directory.bak", $directory);
    }

    /**
     * @covers ::isInitialized
     * @group Initialization
     */
    public function testInitialized()
    {
        $this->assertTrue(Plugin::isInitialized());

    }

    #endregion

    #region PATHS

    /**
     * @covers ::getRootPath
     * @group Paths
     */
    public function testGetRootPath()
    {
        $this->assertTrue(Plugin::isInitialized());

        $this->assertEquals(
            Plugin::getRootPath(),
            realpath(self::EXAMPLE_ROOT)
        );
    }

    /**
     * @covers ::getSourcePath
     * @group Paths
     */
    public function testGetSourcePath()
    {
        $this->assertTrue(Plugin::isInitialized());

        $this->assertEquals(
            Plugin::getSourcePath(),
            realpath(self::EXAMPLE_ROOT . "/" . Plugin::PATH_SOURCE)
        );
    }

    /**
     * @covers ::getDataPath
     * @group Paths
     */
    public function testGetDataPath()
    {
        $this->assertTrue(Plugin::isInitialized());

        $this->assertEquals(
            Plugin::getDataPath(),
            realpath(self::EXAMPLE_ROOT . "/data/")
        );
    }

    /**
     * @covers ::getLogsPath
     * @group Paths
     */
    public function testGetLogsPath()
    {
        $this->assertTrue(Plugin::isInitialized());

        $this->assertEquals(
            Plugin::getLogsPath(),
            realpath(self::EXAMPLE_ROOT . "/data/logs/")
        );
    }

    #endregion

    #region METADATA

    /**
     * @covers ::manifest
     * @group Metadata
     */
    public function testManifest()
    {
        $this->assertTrue(Plugin::isInitialized());

        $manifest = Plugin::manifest();

        $this->assertNotNull($manifest);
    }

    /**
     * @covers ::manifest
     * @group Metadata
     */
    public function testManifestWithPath()
    {
        $this->assertTrue(Plugin::isInitialized());

        $manifest = Plugin::manifest("information/name");

        $this->assertEquals($manifest, "ucrm-plugin-template");
    }

    /**
     * @covers ::ucrm
     * @group Metadata
     */
    public function testUcrm()
    {
        $this->assertTrue(Plugin::isInitialized());

        $ucrm = Plugin::ucrm();

        $this->assertNotNull($ucrm);
    }

    /**
     * @covers ::ucrm
     * @group Metadata
     */
    public function testUcrmWithPath()
    {
        $this->assertTrue(Plugin::isInitialized());

        $ucrm = Plugin::ucrm("ucrmLocalUrl");

        $this->assertStringStartsWith("http://localhost/", $ucrm);
    }

    #endregion

    #region PERMISSIONS

    /**
     * @covers ::fixPermissions
     * @group Permissions
     */
    public function testFixPermissions()
    {
        $this->assertTrue(Plugin::isInitialized());

        // Nothing to do at the moment!
    }

    #endregion

    #region STATES

    /**
     * @covers ::isExecuting
     * @group States
     */
    public function testIsExecuting()
    {
        $this->assertTrue(Plugin::isInitialized());
        $this->assertFalse(Plugin::isExecuting());
    }

    /**
     * @covers ::isRunning
     * @group States
     */
    public function testIsRunning()
    {
        $this->assertTrue(Plugin::isInitialized());
        $this->assertFalse(Plugin::isRunning());
    }

    #endregion

    #region BUNDLING

    /**
     * @covers ::bundle
     * @group Bundling
     */
    public function testBundle()
    {
        Plugin::initialize(self::EXAMPLE_ROOT);
        $this->assertTrue(Plugin::isInitialized());

        Plugin::bundle(self::EXAMPLE_ROOT, "template", ".zipignore", self::EXAMPLE_ROOT . "/../");

        $path = self::EXAMPLE_ROOT . "/../template.zip";

        $this->assertFileExists($path);

        if(realpath($path))
            unlink($path);
    }

    #endregion

    #region SETTINGS

    /**
     * @covers ::createSettings
     * @group Settings
     */
    public function testCreateSettings()
    {
        Plugin::initialize(self::EXAMPLE_ROOT);
        $this->assertTrue(Plugin::isInitialized());

        Plugin::createSettings();

        $path = self::EXAMPLE_ROOT . "/server/App/Settings.php";

        $this->assertFileExists($path);

        if(realpath($path))
            unlink($path);
    }

    #endregion

    #region ENCRYPTION

    // TODO: Create encryption/decryption tests!

    #endregion

    #region ENVIRONMENT

    /**
     * @covers ::mode
     * @group Environment
     */
    public function testMode()
    {
        Plugin::initialize(self::EXAMPLE_ROOT);
        $this->assertTrue(Plugin::isInitialized());

        $mode = Plugin::mode();
        $this->assertEquals("development", $mode);
    }

    #endregion

    #region DATABASE

    /**
     * @covers ::dbConnect
     * @group Database
     */
    public function testDbConnect()
    {
        Plugin::initialize(self::EXAMPLE_ROOT);
        $this->assertTrue(Plugin::isInitialized());

        $pdo = Plugin::dbConnect();
        $this->assertNotNull($pdo);

        $path = self::EXAMPLE_ROOT . "/data/plugin.db";
        $this->assertFileExists($path);
    }

    /**
     * @covers ::dbPDO
     * @group Database
     * @depends testDbConnect
     */
    public function testDbPDO()
    {
        $this->assertTrue(Plugin::isInitialized());
        $this->assertNotNull(Plugin::dbPDO());
    }

    /**
     * @covers ::dbQuery
     * @group Database
     * @depends testDbConnect
     */
    public function testDbQuery()
    {
        //Plugin::initialize(self::EXAMPLE_ROOT);
        $this->assertTrue(Plugin::isInitialized());
        //$this->assertNotNull(Plugin::dbPDO());

        /** @noinspection SqlResolve */
        Plugin::dbQuery(
            "
            CREATE TABLE IF NOT EXISTS tests (
                timestamp DATETIME PRIMARY KEY DEFAULT CURRENT_TIMESTAMP,
                description TEXT
            );
            "
        );

        /** @noinspection SqlResolve */
        Plugin::dbQuery(
            "
            INSERT INTO tests (description) VALUES ('TESTING');
            "
        );

        $path = self::EXAMPLE_ROOT . "/data/plugin.db";
        $this->assertFileExists($path);

        /** @noinspection SqlResolve */
        $results = Plugin::dbQuery(
            "
            SELECT * FROM tests;
            "
        );

        $this->assertNotNull($results);
        $this->assertNotEmpty($results);
        $this->assertGreaterThanOrEqual(1, count($results));
        $this->assertEquals("TESTING", $results[0]["description"]);
    }


    /**
     * @covers ::dbClose
     * @group Database
     * @depends testDbConnect
     */
    public function testDbClose()
    {
        $this->assertTrue(Plugin::isInitialized());
        $this->assertNotNull(Plugin::dbPDO());

        Plugin::dbClose();
        $this->assertNull(Plugin::dbPDO());

        $path = self::EXAMPLE_ROOT . "/data/plugin.db";
        $this->assertFileExists($path);
    }

    /**
     * @covers ::dbDelete
     * @group Database
     * @depends testDbClose
     */
    public function testDbDelete()
    {
        $this->assertTrue(Plugin::isInitialized());
        $this->assertNull(Plugin::dbPDO());

        Plugin::dbDelete();

        $path = self::EXAMPLE_ROOT . "/data/plugin.db";
        $this->assertFileNotExists($path);
    }

    #endregion

}