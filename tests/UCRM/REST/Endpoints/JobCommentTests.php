<?php
declare(strict_types=1);

namespace UCRM\REST\Endpoints;

use MVQN\Collections\Collection;

/**
 * Class JobCommentTests
 *
 * @package UCRM\REST\Endpoints
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 *
 */
class JobCommentTests extends EnpointTestCase
{
    // =================================================================================================================
    // JOB COMMENT TESTS - GETTERS
    // =================================================================================================================

    public function testAllGetters()
    {
        /** @var JobComment $comment */
        $comment = JobComment::get()->last();
        $this->assertNotNull($comment);

        $this->outputGetters($comment);
    }

    // =================================================================================================================
    // JOB COMMENT TESTS - CREATE
    // =================================================================================================================

    /**
     * @covers JobComment::insert()
     * @throws \Exception
     */
    public function testInsert()
    {
        /** @var Job $job */
        $job = Job::get()->last();

        /** @var JobComment $comment */
        $comment = (new JobComment())
            ->setJob($job)
            ->setMessage("Test Job Comment!")
            ->setCreatedDate();

        /** @var JobComment $insertedComment */
        $insertedComment = $comment->insert();
        $this->assertEquals("Test Job Comment!", $insertedComment->getMessage());

        $this->outputResults($insertedComment);
    }

    // =================================================================================================================
    // JOB COMMENT TESTS - READ
    // =================================================================================================================

    /**
     * @covers JobComment::get()
     * @throws \Exception
     */
    public function testGet()
    {
        /** @var Collection $comments */
        $comments = JobComment::get();
        $this->assertNotNull($comments);

        $this->outputResults($comments);
    }

    /**
     * @covers JobComment::getById([last])
     * @throws \Exception
     */
    public function testGetById()
    {
        /** @var JobComment $comment */
        $comment = JobComment::get()->last();
        $this->assertNotNull($comment);

        /** @var JobComment $commentById */
        $commentById = JobComment::getById($comment->getId());
        $this->assertEquals($comment->getId(), $commentById->getId());

        $this->outputResults($commentById);
    }

    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @covers JobComment::getByJobId([last])
     * @throws \Exception
     */
    public function testJobGetByJobId()
    {
        /** @var Job $job */
        $job = Job::getByClientId(1)->last();
        $this->assertNotNull($job);
        $this->assertEquals(1, $job->getClientId());

        /** @var JobComment $comment */
        $comment = JobComment::getByJobId($job->getId())->last();
        $this->assertEquals($comment->getJobId(), $job->getId());

        $this->outputResults($comment);
    }

    /**
     * @covers JobComment::getByUserId(1)
     * @throws \Exception
     */
    public function testJobGetByUserId()
    {
        /** @var User $user */
        $user = User::getByEmail("rspaeth@mvqn.net");

        /** @var JobComment $comment */
        $comment = JobComment::getByUserId($user->getId())->last();
        $this->assertNotNull($comment);
        $this->assertEquals($user->getId(), $comment->getUserId());

        $this->outputResults($comment);
    }

    /**
     * @covers JobComment::getByCreatedDate([today])
     * @throws \Exception
     */
    public function testGetByCreatedDate()
    {
        $comments = JobComment::getByCreatedDate(new \DateTime());
        $this->assertNotNull($comments);

        $this->outputResults($comments);
    }

    /**
     * @covers JobComment::getByCreatedDateBetween([Jan 01, 2018], [today])
     * @throws \Exception
     */
    public function testGetByCreatedDateBetween()
    {
        $comments = JobComment::getByCreatedDateBetween(new \DateTime("2018-01-01"), new \DateTime());
        $this->assertNotNull($comments);

        $this->outputResults($comments);
    }

    // =================================================================================================================
    // JOB COMMENT TESTS - UPDATE
    // =================================================================================================================

    /**
     * @covers JobComment::update()
     * @throws \Exception
     */
    public function testUpdate()
    {
        /** @var JobComment $comment */
        $comment = JobComment::get()->last();

        $date = new \DateTime();

        $newComment = clone $comment;
        $newComment
            ->setCreatedDate($date);

        /** @var JobComment $updatedJobComment */
        $updatedJobComment = $newComment->update();
        $this->assertEquals($date->format(JobComment::DATETIME_FORMAT), $updatedJobComment->getCreatedDate());

        $this->outputResults($updatedJobComment);
    }

    // =================================================================================================================
    // JOB COMMENT TESTS - DELETE
    // =================================================================================================================

    /**
     * @covers JobComment::remove()
     * @throws \Exception
     */
    public function testRemove()
    {
        /** @var Job $job */
        $job = Job::get()->last();

        $date = new \DateTime();

        /** @var JobComment $comment */
        $comment = (new JobComment())
            ->setJobId($job->getId())
            ->setMessage("Test Message")
            ->setCreatedDate($date);

        /** @var JobComment $insertedComment */
        $insertedComment = $comment->insert();
        $this->assertEquals($date->format(JobComment::DATETIME_FORMAT), $insertedComment->getCreatedDate());

        $this->outputResults($insertedComment);

        $this->assertTrue($insertedComment->remove());
    }

    // =================================================================================================================
    // JOB COMMENT TESTS - HELPERS
    // =================================================================================================================

    /**
     * @covers JobComment::setJob()
     * @throws \Exception
     */
    public function testSetJob()
    {
        /** @var Job $job */
        $job = Job::get()->last();
        $this->assertNotNull($job);

        /** @var JobComment $comment */
        $comment = JobComment::get()->whereNot("jobId", $job->getId())->last();
        $comment->setJob($job);

        /** @var Job $updatedJob */
        $updatedJob = $job->update();

        /** @var Client $client */
        $client = $updatedJob->getClient();
        $this->assertNotNull($client);

        $this->outputResults($client);
    }

    /**
     * @covers Job::getAssignedUser()
     * @throws \Exception
     */
    public function testGetJob()
    {
        /** @var JobComment $comment */
        $comment = JobComment::get()->last();
        $this->assertNotNull($comment);

        /** @var Job $job */
        $job = $comment->getJob();
        $this->assertEquals($comment->getJobId(), $job->getId());

        $this->outputResults($job);
    }



}
