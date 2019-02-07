<?php
declare(strict_types=1);

namespace UCRM\REST\Endpoints;

use MVQN\Collections\Collection;

/**
 * Class ClientTests
 *
 * @package UCRM\REST\Endpoints
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 *
 * @coversDefaultClass \UCRM\REST\Endpoints\Client
 *
 */
class ClientTests extends EnpointTestCase
{
    // =================================================================================================================
    // CLIENT TESTS - GETTERS
    // -----------------------------------------------------------------------------------------------------------------

    public function testAllGetters()
    {
        /** @var Client $client */
        $client = Client::getById(1);
        $this->assertNotNull($client);

        $this->outputGetters($client);
    }

    // =================================================================================================================
    // CREATE TESTS - CREATE
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @covers Client->insert()
     * @throws \Exception
     */
    public function testInsert()
    {
        //$this->markTestSkipped("Skip test, as to not keep generating Clients!");

        /** @var Organization $organization */
        $organization = Organization::getByDefault();

        $client = new Client();
        $client
            // REQUIRED: Organization (NOT AVAILABLE ON EDIT SCREEN)
            ->setOrganizationId($organization->getId())

            // --- GENERAL ---------------------------------------------------------------------------------------------
            // REQUIRED (FOR COMMERCIAL): Company Name
            ->setCompanyName("United States Government")
            // Contact Person
            ->setCompanyContactFirstName("Donald")
            ->setCompanyContactLastName("Trump")
            // REQUIRED (FOR RESIDENTIAL): First Name
            //->setFirstName("Donald")
            // REQUIRED (FOR RESIDENTIAL): Last Name
            //->setLastName("Trump")
            // REQUIRED: Client Lead
            ->setIsLead(true)
            // REQUIRED: Company?
            ->setClientType(Client::CLIENT_TYPE_COMMERCIAL)
            // Registration Number
            ->setCompanyRegistrationNumber("12345")
            // Tax ID
            ->setCompanyTaxId("12-3456789")
            // Website
            ->setCompanyWebsite("https://www.usa.gov/")
            // Tags
            // TODO: Add Tag support ???
            // Note
            ->setNote("President of the United States of America!")

            // --- CONTACT ADDRESS -------------------------------------------------------------------------------------
            ->setStreet1("1600 Pennsylvania Avenue NW")
            ->setStreet2("")
            ->setCity("Washington")
            ->setCountryByName("United States")
            ->setStateByCode("DC")
            ->setZipCode("20500")

            // --- INVOICE ADDRESS -------------------------------------------------------------------------------------
            // REQUIRED: Invoice address is the same as contact address
            ->setInvoiceAddressSameAsContact(true)
            // REQUIRED (WHEN NOT THE SAME)
            //->setInvoiceStreet1("")
            //->setInvoiceStreet2("") // NOT REQUIRED!
            //->setInvoiceCity("")
            //->setInvoiceCountryByName("")
            //->setInvoiceStateByCode("")
            //->setInvoiceZipCode("")

            // --- CONTACT DETAILS -------------------------------------------------------------------------------------
            // Primary Contact
            ->setContacts([
                ((new ClientContact())
                    ->setEmail("potus@usa.gov.notreal")
                    ->setName("Donald Trump")
                    ->setPhone("(202) 555-1234")
                    ->setIsContact(true)
                ),
                ((new ClientContact())
                    ->setEmail("accountsreceivable@usa.gov.notreal")
                    ->setName("Steven Mnuchin")
                    ->setPhone("(202) 555-5678")
                    ->setIsBilling(true)
                )
            ])

            // UNIQUE: Username
            //->setUsername("potus1@usa.gov.notreal")

            // --- INVOICE OPTIONS -------------------------------------------------------------------------------------
            // NOTE: Setting any of the below options overrides the defaults.
            // Invoice by Post
            ->setSendInvoiceByPost(true)
            // Invoice maturity days
            ->setInvoiceMaturityDays(30)
            // Suspend services if payment is overdue
            ->setStopServiceDue(true)
            // Suspension delay
            ->setStopServiceDueDays(10)
            // MISSING: Late fee delay (NO API???)

            // --- TAXES -----------------------------------------------------------------------------------------------
            // Tax 1
            //->setTax1Id()
            // Tax 2
            //->setTax2Id()
            // Tax 3
            //->setTax3Id()
            // TODO: Test Taxes Later!

            // --- OTHER -----------------------------------------------------------------------------------------------
            // UNIQUE: Custom ID
            ->setUserIdent("")
            // Previous ISP
            ->setPreviousIsp("ARPANET")
            // REQUIRED: Registration date
            ->setRegistrationDate(new \DateTime());

        // --- CUSTOM ATTRIBUTES -----------------------------------------------------------------------------------
        // TODO: Test Attributes Later!

        //print_r($client);

        //$this->markTestSkipped("Skip so we do not keep creating Clients in the UCRM!");

        $inserted = $client->insert();
        print_r($inserted);
    }


    public function testCreateResidential()
    {
        $this->markTestSkipped("Skip test, as to not keep generating Clients!");

        $lastName = "Doe";
        $firstName = "John".rand(1, 9);

        $client = Client::createResidential($firstName, $lastName);

        $client->setAddress(
            "422 Silver Star Court\n".
            "c/o Michelle Spaeth",
            "Yerington", "NV", "US", "89447"
        );
        //$client->setInvoiceAddress("422 Silver Star Court\nc/o Ryan Spaeth", "Yerington", "NV", "US", "89447");
        $client->setInvoiceAddressSameAsContact(true);

        if(!$client->validate("post", $missing, $ignored))
        {
            echo "MISSING: ";
            print_r($missing);
            echo "\n";
        }

        if($ignored)
        {
            echo "IGNORED: ";
            print_r($ignored);
            echo "\n";
        }

        /** @var Client $inserted */
        $inserted = $client->insert();
        $this->assertEquals($lastName, $inserted->getLastName());

        echo $inserted."\n";
    }

    public function testCreateCommercial()
    {
        $this->markTestSkipped("Skip test, as to not keep generating Clients!");

        //$lastName = "Doe";
        //$firstName = "John".rand(1, 9);
        $companyName = "ACME Rockets, Inc.";

        /** @var Client $inserted */
        $inserted = Client::createCommercial($companyName)->insert();
        $this->assertEquals($companyName, $inserted->getCompanyName());

        echo $inserted."\n";
    }

    public function testCreate()
    {
        $this->markTestSkipped("Skip test, as to not keep generating Clients!");

        $created = Client::createResidentialLead("John", "Doe");

        $created->insert();

        echo $created."\n";
    }

    // -----------------------------------------------------------------------------------------------------------------





    // =================================================================================================================
    // READ METHODS
    // -----------------------------------------------------------------------------------------------------------------

    public function testGet()
    {
        $clients = Client::get();
        $this->assertNotNull($clients);

        echo ">>> Client::get()\n";
        echo $clients."\n";
        echo "\n";
    }

    public function testGetById()
    {
        /** @var Client $first */
        $first = Client::get()->last();
        $id = $first->getId();

        /** @var Client $client */
        $client = Client::getById($id);
        $this->assertEquals($id, $client->getId());

        echo ">>> Client::getById($id)\n";
        echo $client."\n";
        echo "\n";
    }

    public function testGetClientsOnly()
    {
        /** @var Collection $clients */
        $clients = Client::getClientsOnly();

        echo ">>> Client::getClientsOnly()\n";
        echo $clients."\n";
        echo "\n";

        //$this->assertCount(8, $clients);
        $this->assertNotEmpty($clients);
    }


    public function testGetLeadsOnly()
    {
        /** @var Collection $leads */
        $leads = Client::getLeadsOnly();

        echo ">>> Client::getLeadsOnly()\n";
        echo $leads."\n";
        echo "\n";

        //$this->assertCount(35, $leads);
        $this->assertNotEmpty($leads);
    }



    public function testGetByUserIdent()
    {
        $client = Client::getByUserIdent("123");
        $this->assertNotNull($client);

        echo ">>> Client::getByUserIdent('123')\n";
        echo $client."\n";
        echo "\n";
    }

    public function testGetByCustomAttribute()
    {
        $clients = Client::getByCustomAttribute("age", "60");
        $this->assertNotNull($clients);

        echo ">>> Client::getByCustomAttribute('Age', '60')\n";
        echo $clients."\n";
        echo "\n";
    }



    // =================================================================================================================
    // UPDATE METHODS
    // -----------------------------------------------------------------------------------------------------------------

    public function testUpdate()
    {
        /** @var Client $client */
        $client = Client::getById(1);

        // Update any setting here...
        $name = "Worthen".rand(0, 9);
        $client->setLastName($name);

        // Use the built-in reset commands to change back to system defaults.
        //$client->setSendInvoiceByPost(true);
        $client->resetSendInvoiceByPost();

        // Validate the information...
        if($client->validate("patch", $missing, $ignored))
        {
            echo "IGNORED: ";
            print_r($ignored);
            echo "\n";

            /** @var Client $updated */
            $updated = $client->update();
            $this->assertEquals($name ,$updated->getLastName());
            echo $updated."\n";
        }
        else
        {
            echo "MISSING: ";
            print_r($missing);
            echo "\n";
        }
    }



    // =================================================================================================================
    // DELETE METHODS
    // -----------------------------------------------------------------------------------------------------------------

    // NO DELETE ENDPOINTS

    public function testDelContact()
    {
        /** @var Client $client */
        $client = Client::getById(1);

        $contacts = $client->getContacts();

        /** @var ClientContact $contact */
        $contact = $contacts->elements()[2];

        //$results = $client->delContact(2);
        $client->delContactById($contact->getId());


        echo "";
    }



    // =================================================================================================================
    // EXTRA METHODS
    // -----------------------------------------------------------------------------------------------------------------

    public function testSendInvitation()
    {
        $this->markTestSkipped("Skip so we do not keep attempting to send emails to Clients in the UCRM!");

        /** @var Client $client */
        $client = Client::getById(1);
        $client->sendInvitationEmail();
        $this->assertNotNull($client);

        echo ">>> Client::getById(1)->sendInvitationEmail()\n";
        echo $client."\n";
        echo "\n";
    }

}
