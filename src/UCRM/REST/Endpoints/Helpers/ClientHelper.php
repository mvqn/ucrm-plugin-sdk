<?php
declare(strict_types=1);

namespace UCRM\REST\Endpoints\Helpers;

use MVQN\Collections\Collection;
use MVQN\REST\RestClient;
use UCRM\REST\Endpoints\Client;
use UCRM\REST\Endpoints\ClientContact;
use UCRM\REST\Endpoints\Organization;

/**
 * Trait ClientHelper
 *
 * @package UCRM\REST\Endpoints\Helpers
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 */
trait ClientHelper
{
    use Common\AddressHelpers;
    use Common\ContactHelpers;
    use Common\CountryHelpers;
    use Common\InvoiceAddressHelpers;
    use Common\InvoiceCountryHelpers;
    use Common\InvoiceStateHelpers;
    use Common\OrganizationHelpers;
    use Common\StateHelpers;

    // =================================================================================================================
    // OBJECT METHODS
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Resets all Invoice Options for this Client.
     *
     * @return Client Returns the current Client, for method chaining purposes.
     */
    public function resetAllInvoiceOptions(): Client
    {
        $this->sendInvoiceByPost = null;
        $this->invoiceMaturityDays = null;
        $this->stopServiceDue = null;
        $this->stopServiceDueDays = null;
        // TODO: Add 'Late fee delay' to list of resets when made available!

        /** @var Client $this */
        return $this;
    }

    /**
     * Resets the 'Invoice by Post' option for this Client.
     *
     * @return Client Returns the current Client, for method chaining purposes.
     */
    public function resetSendInvoiceByPost(): Client
    {
        $this->sendInvoiceByPost = null;

        /** @var Client $this */
        return $this;
    }

    /**
     * Resets the 'Invoice Maturity Days' option for this Client.
     *
     * @return Client Returns the current Client, for method chaining purposes.
     */
    public function resetInvoiceMaturityDays(): Client
    {
        $this->invoiceMaturityDays = null;

        /** @var Client $this */
        return $this;
    }

    /**
     * Resets the 'Suspend Service if Payment is Overdue' option for this Client.
     *
     * @return Client Returns the current Client, for method chaining purposes.
     */
    public function resetStopServiceDue(): Client
    {
        $this->stopServiceDue = null;

        /** @var Client $this */
        return $this;
    }

    /**
     * Resets the 'Suspension Delay' option for this Client.
     *
     * @return Client Returns the current Client, for method chaining purposes.
     */
    public function resetStopServiceDueDays(): Client
    {
        $this->stopServiceDueDays = null;

        /** @var Client $this */
        return $this;
    }



    /**
     * @param ClientContact $contact The ClientContact for which to add to the Client's contacts list.
     * @return Client Returns the current Client, for method chaining purposes.
     * @throws \Exception Throws an Exception if an error occurs.
     */
    public function addContact(ClientContact $contact): Client
    {
        /** @var Client $client */
        $client = $this;

        if($contact->getId() === null)
        {
            $contact->setClientId($client->getId());
            $contact->insert();
        }

        return $client;
    }

    /**
     * @param int $index The index in the Client::contacts[] array for which to delete the ClientContact.
     * @return Client Returns the current Client, for method chaining purposes.
     * @throws \Exception Throws an Exception if an error occurs.
     */
    public function delContact(int $index): Client
    {
        /** @var Client $client */
        $client = $this;
        $contacts = $client->getContacts()->elements();

        if($index < count($contacts))
        {
            /** @var ClientContact $contact */
            $contact = $contacts[$index];
            $success = $contact->remove();

            if($success)
                unset($contacts[$index]);
        }

        $contacts = new Collection(ClientContact::class, array_values($contacts));
        $client->setContacts($contacts);

        return $client;
    }

    /**
     * @param int $contactId The ID of the ClientContact for which to delete.
     * @return Client Returns the current Client, for method chaining purposes.
     * @throws \Exception Throws an Exception if an error occurs.
     */
    public function delContactById(int $contactId): Client
    {
        /** @var Client $client */
        $client = $this;
        $contacts = $client->getContacts()->elements();

        foreach($contacts as $index => $contact)
        {
            /** @var ClientContact $contact */
            if($contact->getId() === $contactId)
                $this->delContact($index);
        }

        return $client;
    }



    // =================================================================================================================
    // CREATE METHODS
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Creates the minimal Residential Client to be used as a starting point for a new Client.
     *
     * @param string $firstName The Client's first name.
     * @param string $lastName The Client's last name.
     * @return Client Returns a partially generated Client for further use before insertion.
     *
     * @throws \Exception
     */
    public static function createResidential(string $firstName, string $lastName): Client
    {
        $organization = Organization::getByDefault();

        $client = new Client([
            "clientType" => Client::CLIENT_TYPE_RESIDENTIAL,
            "isLead" => false,
            "invoiceAddressSameAsContact" => true,
            "organizationId" => $organization->getId(),
            "registrationDate" => (new \DateTime())->format(Client::DATETIME_FORMAT),
            "firstName" => $firstName,
            "lastName" => $lastName
        ]);

        return $client;
    }

    /**
     * Creates the minimal Residential Client (Lead) to be used as a starting point for a new Client.
     *
     * @param string $firstName The Client's first name.
     * @param string $lastName The Client's last name.
     * @return Client Returns a partially generated Client for further use before insertion.
     *
     * @throws \Exception
     */
    public static function createResidentialLead(string $firstName, string $lastName): Client
    {
        $organization = Organization::getByDefault();

        $client = new Client([
            "clientType" => Client::CLIENT_TYPE_RESIDENTIAL,
            "isLead" => true,
            "invoiceAddressSameAsContact" => true,
            "organizationId" => $organization->getId(),
            "registrationDate" => (new \DateTime())->format(Client::DATETIME_FORMAT),
            "firstName" => $firstName,
            "lastName" => $lastName
        ]);

        return $client;
    }

    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Creates the minimal Commercial Client to be used as a starting point for a new Client.
     *
     * @param string $companyName The company name of this Commercial Client.
     * @return Client Returns a partially generated Client for further use before insertion.
     *
     * @throws \Exception
     */
    public static function createCommercial(string $companyName): Client
    {
        $organization = Organization::getByDefault();

        $client = new Client([
            "clientType" => Client::CLIENT_TYPE_COMMERCIAL,
            "isLead" => false,
            "invoiceAddressSameAsContact" => true,
            "organizationId" => $organization->getId(),
            "registrationDate" => (new \DateTime())->format(Client::DATETIME_FORMAT),
            "companyName" => $companyName
        ]);

        return $client;
    }

    /**
     * Creates the minimal Commercial Client (Lead) to be used as a starting point for a new Client.
     *
     * @param string $companyName The company name of this Commercial Client.
     * @return Client Returns a partially generated Client for further use before insertion.
     *
     * @throws \Exception
     */
    public static function createCommercialLead(string $companyName): Client
    {
        $organization = Organization::getByDefault();

        $client = new Client([
            "clientType" => Client::CLIENT_TYPE_COMMERCIAL,
            "isLead" => true,
            "invoiceAddressSameAsContact" => true,
            "organizationId" => $organization->getId(),
            "registrationDate" => (new \DateTime())->format(Client::DATETIME_FORMAT),
            "companyName" => $companyName
        ]);

        return $client;
    }

    // =================================================================================================================
    // READ METHODS
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return Collection
     * @throws \Exception
     */
    public static function getClientsOnly(): Collection
    {
        return Client::get("", [], [ "lead" => "0" ]);

    }

    /**
     * @return Collection
     * @throws \Exception
     */
    public static function getLeadsOnly(): Collection
    {
        return Client::get("", [], [ "lead" => "1" ]);

    }

    /**
     * Sends an HTTP GET Request using the calling class's annotated information, for an object, given the Custom ID.
     *
     * @param string $userIdent The Custom ID of the Client for which to retrieve.
     * @return Client|null Returns the matching Client or NULL, if none was found.
     *
     * @throws \Exception
     */
    public static function getByUserIdent(string $userIdent): ?Client
    {
        /** @var Client $client */
        $client = Client::get("", [], [ "userIdent" => $userIdent ])->first();

        // Custom ID is Unique, so return the only result or null!
        return $client;
    }

    /**
     * Sends an HTTP GET Request using the calling class's annotated information, for objects, given an Attribute pair.
     *
     * @param string $key The Custom Attribute Key for which to search, will be converted to camel case as needed.
     * @param string $value The Custom Attribute Value for which to search.
     * @return Collection Returns a collection of Clients matching the given criteria.
     *
     * @throws \Exception
     */
    public static function getByCustomAttribute(string $key, string $value): Collection
    {
        // TODO: Determine if this is ALWAYS the case!
        $key = lcfirst($key);
        return Client::get("", [], [ "customAttributeKey" => $key, "customAttributeValue" => $value ]);
    }


    private static function getCurrentlyAuthenticated(): ?array
    {
        if(!isset($_COOKIE["PHPSESSID"]))
            return null;

        $sessionId = $_COOKIE["PHPSESSID"];
        $cookie = "PHPSESSID=" . preg_replace('~[^a-zA-Z0-9]~', '', $_COOKIE['PHPSESSID']);

        RestClient::pushHeader("Cookie: ".$cookie);

        $restUrl = RestClient::getBaseUrl();
        $tempUrl = (getenv("UCRM_REST_URL_DEV") ?: "http://localhost");

        RestClient::setBaseUrl($tempUrl);

        $current = RestClient::get("/current-user");

        RestClient::setBaseUrl($restUrl);

        RestClient::popHeader();

        return $current;
    }

    public static function getByAuthenticated(?array &$authenticated = null): ?Client
    {
        $authenticated = self::getCurrentlyAuthenticated();

        if($authenticated === null || !$authenticated["isClient"] || $authenticated["clientId"] === null)
            return null;

        /** @var Client $client */
        $client = Client::getById($authenticated["clientId"]);

        return $client;
    }




    // =================================================================================================================
    // UPDATE METHODS
    // -----------------------------------------------------------------------------------------------------------------

    // =================================================================================================================
    // DELETE METHODS
    // -----------------------------------------------------------------------------------------------------------------

    // =================================================================================================================
    // EXTRA FUNCTIONS
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Sends an invitation email to this Client.
     *
     * @return Client Returns the Client for which the invitation email was just sent.
     *
     * @throws \Exception
     */
    public function sendInvitationEmail(): Client
    {
        /** @var Client $client */
        $client = Client::patch(null, [ "id" => $this->getId() ], "/send-invitation");
        return $client;
    }

}