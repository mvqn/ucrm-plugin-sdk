<?php
declare(strict_types=1);

namespace UCRM\REST\Endpoints\Helpers;

use MVQN\REST\Endpoints\EndpointObject;
use UCRM\REST\Endpoints\Client;
use UCRM\REST\Endpoints\Service;

trait ServiceHelper
{
    use Common\ClientHelpers;
    use Common\OrganizationHelpers;
    use Common\CountryHelpers;
    use Common\StateHelpers;
    use Common\InvoiceCountryHelpers;
    use Common\InvoiceStateHelpers;

    // =================================================================================================================
    // HELPER METHODS
    // -----------------------------------------------------------------------------------------------------------------


    public function create(): Service
    {
        /** @var EndpointObject $data */
        $data = $this;

        /** @var Service $service */
        $service = Service::post($data, ["clientId" => $this->clientId]);

        return $service;
    }








    public function sendInvitationEmail(): Client
    {
        /** @var Client $client */
        $client = Client::patch($this->id, null, "/send-invitation");
        return $client;
    }

}