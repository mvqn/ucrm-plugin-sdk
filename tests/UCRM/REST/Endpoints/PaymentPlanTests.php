<?php
declare(strict_types=1);

namespace UCRM\REST\Endpoints;

use MVQN\REST\RestClient;

require_once __DIR__ . "/_TestFunctions.php";

class PaymentPlanTests extends \PHPUnit\Framework\TestCase
{
    // =================================================================================================================
    // INITIALIZATION
    // -----------------------------------------------------------------------------------------------------------------

    /** @var string Location of the .env file for development. */
    protected const DOTENV_PATH = __DIR__ . "/../../rest/";

    // -----------------------------------------------------------------------------------------------------------------

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
    // GETTERS & SETTERS
    // -----------------------------------------------------------------------------------------------------------------

    public function testAllGetters()
    {
        $paymentPlan = PaymentPlan::getById(1);

        $test = _TestFunctions::testAllGetters($paymentPlan);
        $this->assertTrue($test);
    }



    // =================================================================================================================
    // CREATE METHODS
    // -----------------------------------------------------------------------------------------------------------------

    public function testCreateMonthly()
    {
        $this->markTestSkipped("Skip test, as to not keep generating PaymentPlans!");

        /** @var Client $client */
        $client = Client::getById(14);

        $paymentPlan = PaymentPlan::createMonthly($client, new \DateTime("01/01/2019"), 20);

        $inserted = $paymentPlan->insert();

        echo $inserted."\n";
    }

    public function testInsert()
    {
        $this->markTestSkipped("Skip test, as to not keep generating PaymentPlans!");

        $client = Client::getById(1);

        $paymentPlan = (new PaymentPlan())
            ->setProvider(PaymentPlan::PROVIDER_IPPAY)
            ->setProviderPlanId("")
            ->setProviderSubscriptionId("")
            ->setClientId($client->getId())
            ->setCurrencyByCode("USD")
            ->setAmount(10)
            ->setPeriod(PaymentPlan::PERIOD_MONTHS_3)
            ->setStartDate(new \DateTime("01/01/2020"));

        echo ">>> PaymentPlan::insert()\n";
        print_r($paymentPlan);
        echo "\n";

        $inserted = $paymentPlan->insert();
        echo $inserted."\n";

        $this->assertTrue(true);
    }



    // =================================================================================================================
    // READ UPDATES
    // -----------------------------------------------------------------------------------------------------------------

    public function testGet()
    {
        $paymentPlans = PaymentPlan::get();
        $this->assertNotNull($paymentPlans);

        echo ">>> PaymentPlan::get()\n";
        echo $paymentPlans."\n";
        echo "\n";
    }

    public function testGetById()
    {
        $paymentPlan = PaymentPlan::getById(1);
        $this->assertEquals(1, $paymentPlan->getId());

        echo ">>> PaymentPlan::getById(1)\n";
        echo $paymentPlan."\n";
        echo "\n";
    }



    // =================================================================================================================
    // UPDATE METHODS
    // -----------------------------------------------------------------------------------------------------------------



    // =================================================================================================================
    // DELETE METHODS
    // -----------------------------------------------------------------------------------------------------------------



    // =================================================================================================================
    // EXTRA METHODS
    // -----------------------------------------------------------------------------------------------------------------

    public function testHelperMethods()
    {
        /** @var PaymentPlan $paymentPlan */
        $paymentPlan = PaymentPlan::getById(1);

        echo ">>> PaymentPlanTests::testHelperMethods()\n";

        $client = $paymentPlan->getClient();
        echo "> getClient() => ".$client."\n";
        echo "> setClient() => ".$paymentPlan->setClient($client)."\n";

        $currency = $paymentPlan->getCurrency();
        echo "> getCurrency() => ".$currency."\n";
        echo "> setCurrency() => ".$paymentPlan->setCurrency($currency)."\n";
        echo "> setCurrencyByCode('USD') => ".$paymentPlan->setCurrencyByCode("USD")."\n";

        echo "\n";

        $this->assertTrue(true);
    }

    // -----------------------------------------------------------------------------------------------------------------



}
