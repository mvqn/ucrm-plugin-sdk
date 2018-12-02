<?php
declare(strict_types=1);

namespace UCRM\REST\Endpoints;

use MVQN\REST\RestClient;

require_once __DIR__ . "/_TestFunctions.php";

class OrganizationTests extends \PHPUnit\Framework\TestCase
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
        $organization = Organization::getById(1);

        $test = _TestFunctions::testAllGetters($organization);
        $this->assertTrue($test);
    }

    // -----------------------------------------------------------------------------------------------------------------

    public function testGet()
    {
        $organizations = Organization::get();
        $this->assertNotNull($organizations);

        echo ">>> Organization::get()\n";
        echo $organizations."\n";
        echo "\n";
    }

    public function testGetById()
    {
        $organization = Organization::getById(1);
        $this->assertEquals(1, $organization->getId());

        echo ">>> Organization::getById(1)\n";
        echo $organization."\n";
        echo "\n";
    }

    // -----------------------------------------------------------------------------------------------------------------

    public function testGetByName()
    {
        $organizations = Organization::getByName("Mason Valley Quicknet");
        $this->assertNotNull($organizations);

        echo ">>> Organization::getByName('Mason Valley Quicknet')\n";
        echo $organizations."\n";
        echo "\n";
    }

    public function testGetDefault()
    {
        /** @var Organization $organization */
        $organization = Organization::getByDefault();
        $this->assertEquals("Mason Valley Quicknet", $organization->getName());

        echo ">>> Organization::getDefault()\n";
        echo $organization."\n";
        echo "\n";
    }



}
