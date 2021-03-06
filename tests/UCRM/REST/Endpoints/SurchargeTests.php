<?php
declare(strict_types=1);

namespace UCRM\REST\Endpoints;

use MVQN\REST\RestClient;

require_once __DIR__ . "/_TestFunctions.php";

class SurchargeTests extends \PHPUnit\Framework\TestCase
{
    // =================================================================================================================
    // INITIALIZATION
    // -----------------------------------------------------------------------------------------------------------------

    /** @var string Location of the .env file for development. */
    protected const DOTENV_PATH = __DIR__ . "/../../rest/";

    protected function setUp()
    {
        // Load ENV variables from a file during development.
        if(file_exists(self::DOTENV_PATH))
        {
            $dotenv = new \Dotenv\Dotenv(self::DOTENV_PATH);
            $dotenv->load();
        }

        //RestClient::cacheDir(__DIR__);

        RestClient::setBaseUrl(getenv("REST_URL"));
        RestClient::setHeaders([
            "Content-Type: application/json",
            "X-Auth-App-Key: ".getenv("REST_KEY")
        ]);
    }

    // =================================================================================================================
    // TESTS
    // -----------------------------------------------------------------------------------------------------------------

    public function testAllGetters()
    {
        $surcharge = Surcharge::getById(1);

        $test = _TestFunctions::testAllGetters($surcharge);
        $this->assertTrue($test);
    }

    // -----------------------------------------------------------------------------------------------------------------

    public function testGet()
    {
        $surcharge = Surcharge::get();
        $this->assertNotNull($surcharge);

        echo ">>> Surcharge::get()\n";
        echo $surcharge."\n";
        echo "\n";
    }

    public function testGetById()
    {
        /** @var Surcharge $surcharge */
        $surcharge = Surcharge::getById(1);
        $this->assertEquals(1, $surcharge->getId());

        echo ">>> Surcharge::getById(1)\n";
        echo $surcharge."\n";
        echo "\n";
    }

    // -----------------------------------------------------------------------------------------------------------------

    public function testGetByName()
    {
        $surcharges = Surcharge::getByName("Test Surcharge");
        $this->assertGreaterThanOrEqual(1, $surcharges->count());

        echo ">>> Surcharge::getById(1)\n";
        echo $surcharges."\n";
        echo "\n";
    }


}
