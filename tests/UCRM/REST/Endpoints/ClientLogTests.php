<?php
declare(strict_types=1);

namespace UCRM\REST\Endpoints;

use MVQN\Collections\Collection;

/**
 * Class ClientLogTests
 *
 * @package UCRM\REST\Endpoints
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 *
 */
class ClientLogTests extends EnpointTestCase
{
    // =================================================================================================================
    // CLIENT LOG TESTS - GETTERS
    // =================================================================================================================

    public function testAllGetters()
    {
        /** @var ClientLog $clientLog */
        $clientLog = ClientLog::get()->last();
        $this->assertNotNull($clientLog);

        $this->outputGetters($clientLog);
    }

    // =================================================================================================================
    // CLIENT LOG TESTS - CREATE
    // =================================================================================================================

    /**
     * @covers ClientLog::insert()
     * @throws \Exception
     */
    public function testClientLogInsert()
    {
        /** @var Client $client */
        $client = Client::getById(1);

        /** @var ClientLog $clientLog */
        $clientLog = (new ClientLog())
            //->setClientId(1)
            ->setClient($client)
            ->setMessage("This is a test from the API.")
            //->setUserId(1)
            ->setUser(User::getByEmail("rspaeth@mvqn.net"))
            ->setCreatedDate(new \DateTime("2018-01-01T00:00:00-0800"));

        /** @var ClientLog $insertedClientLog */
        $insertedClientLog = $clientLog->insert();
        $this->assertEquals("This is a test from the API.", $insertedClientLog->getMessage());

        $this->outputResults($insertedClientLog);
    }

    // =================================================================================================================
    // CLIENT LOG TESTS - READ
    // =================================================================================================================

    /**
     * @covers ClientLog::get()
     * @throws \Exception
     */
    public function testGet()
    {
        /** @var Collection $clientLogs */
        $clientLogs = ClientLog::get();
        $this->assertNotNull($clientLogs);

        $this->outputResults($clientLogs);
    }

    /**
     * @covers ClientLog::getById(1)
     * @throws \Exception
     */
    public function testGetById()
    {
        /** @var ClientLog $clientLog */
        $clientLog = ClientLog::get()->last();
        $this->assertNotNull($clientLog);

        /** @var ClientLog $clientLogById */
        $clientLogById = ClientLog::getById($clientLog->getId());
        $this->assertEquals($clientLog->getId(), $clientLogById->getId());

        $this->outputResults($clientLogById);
    }

    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @covers ClientLog::getByClientId(1)
     * @throws \Exception
     */
    public function testClientLogGetByClientId()
    {
        /** @var ClientLog $clientLog */
        $clientLog = ClientLog::getByClientId(1)->first();
        $this->assertNotNull($clientLog);
        $this->assertEquals(1, $clientLog->getClientId());

        $this->outputResults($clientLog);
    }

    /**
     * @covers ClientLog::getByCreatedDate(new \DateTime("2018-01-01T00:00:00-0800"))
     * @throws \Exception
     */
    public function testClientLogGetByCreatedDate()
    {
        /** @var ClientLog $clientLog */
        $clientLog = ClientLog::getByCreatedDate(new \DateTime("2018-01-01T00:00:00-0800"))->first();
        $this->assertNotNull($clientLog);
        $this->assertEquals("2018-01-01T00:00:00-0800", $clientLog->getCreatedDate());

        $this->outputResults($clientLog);
    }

    /**
     * @covers ClientLog::getByCreatedDate(new \DateTime("2017-12-31"), new \DateTime("2018-01-02"))
     * @throws \Exception
     */
    public function testClientLogGetByCreatedDateBetween()
    {
        /** @var ClientLog $clientLog */
        $clientLog = ClientLog::getByCreatedDateBetween(
            new \DateTime("2018-01-01"), new \DateTime("2018-01-02"))->first();
        $this->assertNotNull($clientLog);
        $this->assertEquals("2018-01-01T00:00:00-0800", $clientLog->getCreatedDate());

        $this->outputResults($clientLog);
    }

    // =================================================================================================================
    // CLIENT LOG TESTS - UPDATE
    // =================================================================================================================

    /**
     * @covers ClientLog::update()
     * @throws \Exception
     */
    public function testClientLogUpdate()
    {
        /** @var ClientLog $clientLog */
        $clientLog = ClientLog::getByClientId(1)->first();

        $clientLog
            ->setUserByEmail("rspaeth@mvqn.net");

        /** @var ClientLog $updatedClientLog */
        $updatedClientLog = $clientLog->update();
        $this->assertEquals($clientLog->getUserId(), $updatedClientLog->getUserId());

        $this->outputResults($updatedClientLog);
    }

    // =================================================================================================================
    // CLIENT LOG TESTS - DELETE
    // =================================================================================================================



    // =================================================================================================================
    // CLIENT LOG TESTS - HELPERS
    // =================================================================================================================

    /**
     * @covers ClientLog::getClient()
     * @throws \Exception
     */
    public function testClientLogGetClient()
    {
        /** @var ClientLog $clientLog */
        $clientLog = ClientLog::get()->last();
        $this->assertNotNull($clientLog);

        /** @var Client $client */
        $client = $clientLog->getClient();
        $this->assertNotNull($client);

        $this->outputResults($client);
    }

    /**
     * @covers ClientLog::getUser()
     * @throws \Exception
     */
    public function testClientLogGetUser()
    {
        /** @var ClientLog $clientLog */
        $clientLog = ClientLog::get()->last();
        $this->assertNotNull($clientLog);

        /** @var User $user */
        $user = $clientLog->getUser();
        $this->assertNotNull($user);

        $this->outputResults($user);
    }

}
