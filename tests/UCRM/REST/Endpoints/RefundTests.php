<?php
declare(strict_types=1);

namespace UCRM\REST\Endpoints;

use MVQN\REST\RestClient;

require_once __DIR__ . "/_TestFunctions.php";

class RefundTests extends \PHPUnit\Framework\TestCase
{
    // =================================================================================================================
    // INITIALIZATION
    // -----------------------------------------------------------------------------------------------------------------

    /** @var string Location of the .env file for development. */
    protected const DOTENV_PATH = __DIR__ . "/../../tests/";

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
        $refund = Refund::getById(1);

        $test = _TestFunctions::testAllGetters($refund);
        $this->assertTrue($test);
    }

    // -----------------------------------------------------------------------------------------------------------------

    public function testGet()
    {
        $refunds = Refund::get();
        $this->assertNotNull($refunds);

        echo ">>> Refund::get()\n";
        echo $refunds."\n";
        echo "\n";
    }

    public function testGetById()
    {
        $refund = Refund::getById(1);
        $this->assertEquals(1, $refund->getId());

        echo ">>> Refund::getById(1)\n";
        echo $refund."\n";
        echo "\n";
    }

    // -----------------------------------------------------------------------------------------------------------------

    public function testRefundInsert()
    {
        //$client = Client::getById(1);

        $refund = (new Refund())
            ->setMethod(Payment::METHOD_CUSTOM)
            ->setCreatedDate(new \DateTime())
            ->setAmount(10)
            ->setNote("Partial refund from API.")
            ->setClientId(1)
            ->setCurrencyCode("USD");

        echo ">>> Refund::insert()\n";
        print_r($refund);
        echo "\n";

        //$refund->insert();

        $this->assertTrue(true);
    }

}
