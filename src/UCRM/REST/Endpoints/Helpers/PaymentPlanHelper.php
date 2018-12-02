<?php
declare(strict_types=1);

namespace UCRM\REST\Endpoints\Helpers;

//use MVQN\Collections\Collection;
use UCRM\REST\Endpoints\Client;
use UCRM\REST\Endpoints\PaymentPlan;


trait PaymentPlanHelper
{
    use Common\ClientHelpers;
    use Common\CurrencyHelpers;

    // =================================================================================================================
    // OBJECT METHODS
    // -----------------------------------------------------------------------------------------------------------------

    // =================================================================================================================
    // CREATE METHODS
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @param Client $client
     * @param \DateTime $startDate
     * @param float $amount
     * @return PaymentPlan
     * @throws \Exception
     */
    public static function createMonthly(Client $client, \DateTime $startDate, float $amount): PaymentPlan
    {
        $paymentPlan = (new PaymentPlan())
            ->setProvider(PaymentPlan::PROVIDER_IPPAY)
            ->setProviderPlanId("")
            ->setProviderSubscriptionId("")
            ->setClient($client)
            ->setCurrencyByCode("USD")
            ->setAmount($amount)
            ->setPeriod(PaymentPlan::PERIOD_MONTHS_1)
            ->setStartDate($startDate);

        return $paymentPlan;
    }

    // =================================================================================================================
    // READ METHODS
    // -----------------------------------------------------------------------------------------------------------------

    // =================================================================================================================
    // UPDATE METHODS
    // -----------------------------------------------------------------------------------------------------------------

    // =================================================================================================================
    // DELETE METHODS
    // -----------------------------------------------------------------------------------------------------------------

    // =================================================================================================================
    // EXTRA FUNCTIONS
    // -----------------------------------------------------------------------------------------------------------------
}