<?php
declare(strict_types=1);

namespace MVQN\UNMS\Data;

use Dotenv\Dotenv;
use Dotenv\Exception\ExceptionInterface;
use MVQN\Data\Database;
use MVQN\UNMS\Data\Models\Setting;
use MVQN\UNMS\Data\UnmsDB;
use UCRM\Common\Mailer;

class DatabaseTests extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        if(file_exists(__DIR__."/../../../.env") && (!defined("ENV_LOADED") || !ENV_LOADED ))
        {
            (new Dotenv(__DIR__ . "/../../../"))->load();
            define("ENV_LOADED", true);
        }

        UnmsDB::connect();
    }

    public function testQuery()
    {
        $results = UnmsDB::query("SELECT * FROM unms.setting");
        var_dump($results);

        $this->assertNotEmpty($results);
    }

    public function testSettingModel()
    {
        $results = Setting::where("name", "=", "smtp");
        echo $results;
    }

    public function testDatabaseQuery()
    {
        $results = Database::where("unms.setting", "name = 'aesKey'");//,  "name", "=", "smtp");
        var_dump($results);
    }



}