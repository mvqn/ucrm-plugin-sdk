<?php
declare(strict_types=1);

namespace UCRM\REST\Endpoints;

use MVQN\Collections\Collection;

/**
 * Class JobTests
 *
 * @package UCRM\REST\Endpoints
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 *
 */
class JobTests extends EnpointTestCase
{
    // =================================================================================================================
    // JOB TESTS - GETTERS
    // =================================================================================================================

    public function testAllGetters()
    {
        /** @var Job $job */
        $job = Job::get()->last();
        $this->assertNotNull($job);

        $this->outputGetters($job);
    }

    // =================================================================================================================
    // JOB TESTS - CREATE
    // =================================================================================================================

    /**
     * @covers Job::insert()
     * @throws \Exception
     */
    public function testJobInsert()
    {
        /** @var Job $job */
        $job = (new Job())
            ->setTitle("Test Job")
            ->setStatus(Job::STATUS_OPEN);

        /** @var Job $insertedJob */
        $insertedJob = $job->insert();
        $this->assertEquals("Test Job", $insertedJob->getTitle());

        $this->outputResults($insertedJob);
    }

    // =================================================================================================================
    // JOB TESTS - READ
    // =================================================================================================================

    /**
     * @covers Job::get()
     * @throws \Exception
     */
    public function testGet()
    {
        /** @var Collection $jobs */
        $jobs = Job::get();
        $this->assertNotNull($jobs);

        $this->outputResults($jobs);
    }

    /**
     * @covers Job::getById([last])
     * @throws \Exception
     */
    public function testGetById()
    {
        /** @var Job $job */
        $job = Job::get()->last();
        $this->assertNotNull($job);

        /** @var Job $jobById */
        $jobById = Job::getById($job->getId());
        $this->assertEquals($job->getId(), $jobById->getId());

        $this->outputResults($jobById);
    }

    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @covers Job::getByClientId(1)
     * @throws \Exception
     */
    public function testJobGetByClientId()
    {
        /** @var Job $job */
        $job = Job::getByClientId(1)->last();
        $this->assertNotNull($job);
        $this->assertEquals(1, $job->getClientId());

        $this->outputResults($job);
    }

    /**
     * @covers Job::getByAssignedUserId(1)
     * @throws \Exception
     */
    public function testJobGetByAssignedUserId()
    {
        /** @var Job $job */
        $job = Job::getByAssignedUserId(1)->last();
        $this->assertNotNull($job);
        $this->assertEquals(1, $job->getAssignedUserId());

        $this->outputResults($job);
    }

    /**
     * @covers Job::getByTicketId(21)
     * @throws \Exception
     */
    public function testJobGetByTicketId()
    {
        /** @var Job $job */
        $job = Job::getByTicketId(21)->last();
        $this->assertNotNull($job);
        $this->assertEquals(1, $job->getAssignedUserId());

        $this->outputResults($job);
    }

    /**
     * @covers Job::getByStatus(2, 0)
     * @throws \Exception
     */
    public function testJobGetByStatus()
    {
        /** @var Job $job */
        $job = Job::getByStatuses(Job::STATUS_CLOSED, Job::STATUS_OPEN)->first();
        $this->assertNotNull($job);
        $this->assertNotEquals($job->getStatus(), Job::STATUS_IN_PROGRESS);

        $this->outputResults($job);
    }

    /**
     * @covers Job::getByDate([today])
     * @throws \Exception
     */
    public function testJobGetByDate()
    {
        /** @var Job $job */
        $job = Job::getByDate(new \DateTime())->last();
        $this->assertNotNull($job);
        //$this->assertNotEquals($job->getStatus(), Job::STATUS_IN_PROGRESS);

        $this->outputResults($job);
    }

    /**
     * @covers Job::getByDateBetween([Jan 01, 2018], [today])
     * @throws \Exception
     */
    public function testJobGetByDateBetween()
    {
        /** @var Job $job */
        $job = Job::getByDateBetween(new \DateTime("2018-01-01"), new \DateTime())->last();
        $this->assertNotNull($job);
        //$this->assertNotEquals($job->getStatus(), Job::STATUS_IN_PROGRESS);

        $this->outputResults($job);
    }

    /**
     * @covers Job::getByTitle("Test Job")
     * @throws \Exception
     */
    public function testJobGetByTitle()
    {
        /** @var Job $job */
        $job = Job::getByTitle("Test Job")->last();
        $this->assertNotNull($job);
        $this->assertEquals("Test Job", $job->getTitle());

        $this->outputResults($job);
    }

    // =================================================================================================================
    // JOB TESTS - UPDATE
    // =================================================================================================================

    /**
     * @covers Job::update()
     * @throws \Exception
     */
    public function testJobUpdate()
    {
        /** @var Job $job */
        $job = Job::getByClientId(1)->last();

        $date = new \DateTime();

        $newJob = clone $job;
        $newJob
            ->setAssignedUserByEmail("rspaeth@mvqn.net")
            ->setDate(new \DateTime());

        /** @var Job $updatedJob */
        $updatedJob = $newJob->update();
        $this->assertEquals($date->format(Job::DATETIME_FORMAT), $updatedJob->getDate());

        $this->outputResults($updatedJob);
    }

    // =================================================================================================================
    // JOB TESTS - DELETE
    // =================================================================================================================

    /**
     * @covers Job::remove()
     * @throws \Exception
     */
    public function testJobRemove()
    {
        /** @var Job $job */
        $job = (new Job())
            ->setTitle("Test Job")
            ->setStatus(Job::STATUS_OPEN);

        /** @var Job $insertedJob */
        $insertedJob = $job->insert();
        $this->assertEquals("Test Job", $insertedJob->getTitle());

        $this->outputResults($insertedJob);

        $this->assertTrue($insertedJob->remove());
    }

    // =================================================================================================================
    // JOB TESTS - HELPERS
    // =================================================================================================================

    /**
     * @covers Job::setClient()
     * @throws \Exception
     */
    public function testJobSetClient()
    {
        /** @var Job $job */
        $job = Job::get()->last();
        $this->assertNotNull($job);
        $job->setClientId(1);

        /** @var Job $updatedJob */
        $updatedJob = $job->update();

        /** @var Client $client */
        $client = $updatedJob->getClient();
        $this->assertNotNull($client);

        $this->outputResults($client);
    }

    /**
     * @covers Job::getClient()
     * @throws \Exception
     */
    public function testJobGetClient()
    {
        /** @var Job $job */
        $job = Job::get()->last();
        $this->assertNotNull($job);

        /** @var Client $client */
        $client = $job->getClient();
        $this->assertNotNull($client);

        $this->outputResults($client);
    }

    /**
     * @covers Job::setAssignedUserByEmail()
     * @throws \Exception
     */
    public function testJobSetUser()
    {
        /** @var Job $job */
        $job = Job::get()->last();
        $this->assertNotNull($job);
        $job
            ->setDate(new \DateTime())
            ->setAssignedUserByEmail("rspaeth@mvqn.net");

        /** @var Job $updatedJob */
        $updatedJob = $job->update();

        /** @var User $user */
        $user = $updatedJob->getAssignedUser();
        $this->assertNotNull($user);

        $this->outputResults($user);
    }

    /**
     * @covers Job::getAssignedUser()
     * @throws \Exception
     */
    public function testJobGetUser()
    {
        /** @var Job $job */
        $job = Job::get()->last();
        $this->assertNotNull($job);

        /** @var User $user */
        $user = $job->getAssignedUser();
        $this->assertNotNull($user);

        $this->outputResults($user);
    }





    /**
     * @covers Job::uploadAttachment()
     * @throws \Exception
     */
    public function testUploadAttachment()
    {
        /** @var Job $job */
        $job = Job::get()->last();
        $this->assertNotNull($job);

        $job->uploadAttachment("README.md", file_get_contents(__DIR__."/uploads/README.md"));

        /** @var JobAttachment $attachment */
        $attachment = $job->getAttachments()->last();

        $this->outputResults($attachment);
    }


}
