<?php
declare(strict_types=1);

namespace UCRM\REST\Endpoints;

use MVQN\Collections\Collection;

/**
 * Class JobAttachmentTests
 *
 * @package UCRM\REST\Endpoints
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 *
 */
class JobAttachmentTests extends EnpointTestCase
{
    // =================================================================================================================
    // JOB TESTS - GETTERS
    // =================================================================================================================

    public function testAllGetters()
    {
        /** @var JobAttachment $attachment */
        $attachment = JobAttachment::get()->last();
        $this->assertNotNull($attachment);

        $this->outputGetters($attachment, ["file"]);
    }

    // =================================================================================================================
    // JOB TESTS - CREATE
    // =================================================================================================================

    /**
     * @covers JobAttachment::insert()
     * @throws \Exception
     */
    public function testInsert()
    {
        /** @var Job $job */
        $job = Job::get()->last();

        $file = file_get_contents(__DIR__."/uploads/README.md");
        $fileSize = strlen($file);

        $attachment = (new JobAttachment())
            ->setJobId($job->getId())
            ->setFilename("README.md")
            ->setFile($file);

        /** @var JobAttachment $insertedAttachment */
        $insertedAttachment = $attachment->insert();
        $this->assertEquals($fileSize, $insertedAttachment->getSize());

        $this->outputResults($insertedAttachment);
    }

    // =================================================================================================================
    // JOB TESTS - READ
    // =================================================================================================================

    /**
     * @covers JobAttachment::get()
     * @throws \Exception
     */
    public function testGet()
    {
        /** @var Collection $attachments */
        $attachments = JobAttachment::get();
        $this->assertNotNull($attachments);

        $this->outputResults($attachments);
    }

    /**
     * @covers JobAttachment::getById([last])
     * @throws \Exception
     */
    public function testGetById()
    {
        /** @var JobAttachment $attachment */
        $attachment = JobAttachment::get()->last();
        $this->assertNotNull($attachment);

        /** @var JobAttachment $attachmentById */
        $attachmentById = JobAttachment::getById($attachment->getId());
        $this->assertEquals($attachment->getId(), $attachmentById->getId());

        $this->outputResults($attachmentById);
    }

    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @covers JobAttachment::getByJobId(1)
     * @throws \Exception
     */
    public function testGetByJobId()
    {
        /** @var Job $job */
        $job = Job::get()->last();

        /** @var JobAttachment $attachment */
        $attachment = JobAttachment::getByJobId($job->getId())->last();
        $this->assertNotNull($attachment);
        $this->assertEquals($job->getId(), $attachment->getJobId());

        $this->outputResults($attachment);
    }

    /**
     * @covers JobAttachment::getByJobId(1)
     * @throws \Exception
     */
    public function testGetByFilename()
    {
        /** @var Job $job */
        $job = Job::get()->last();

        /** @var JobAttachment $attachment */
        $attachment = JobAttachment::getByJobId($job->getId())->last();
        $this->assertNotNull($attachment);
        $this->assertEquals($job->getId(), $attachment->getJobId());

        $this->outputResults($attachment);
    }



    // =================================================================================================================
    // JOB TESTS - UPDATE
    // =================================================================================================================

    /**
     * @covers JobAttachment::update()
     * @throws \Exception
     */
    public function testUpdate()
    {
        /** @var Job $job */
        $job = Job::getByClientId(1)->last();

        /** @var JobAttachment $attachment */
        $attachment = $job->getAttachments()->where("filename", "README.md")->last();
        $this->assertEquals("README.md", $attachment->getFilename());

        $attachment
            ->setFilename("README.md.old")
            ->setFile(file_get_contents(__DIR__."/uploads/README.md"));

        $updatedAttachment = $attachment->update();

        $this->outputResults($updatedAttachment);
    }

    // =================================================================================================================
    // JOB TESTS - DELETE
    // =================================================================================================================

    /**
     * @covers JobAttachment::remove()
     * @throws \Exception
     */
    public function testJobRemove()
    {
        /** @var Job $job */
        $job = Job::getByClientId(1)->last();

        $job->uploadAttachment("README.md", file_get_contents(__DIR__."/uploads/README.md"));

        /** @var JobAttachment $attachment */
        $attachment = $job->getAttachments()->where("filename", "README.md")->last();
        $this->assertEquals("README.md", $attachment->getFilename());
        $this->outputResults($attachment);

        $this->assertTrue($attachment->remove());
    }

    // =================================================================================================================
    // JOB TESTS - HELPERS
    // =================================================================================================================

    /**
     * @covers JobAttachment::downloadUrl()
     */
    public function testDownloadUrl()
    {
        /** @var Job $job */
        $job = Job::get()->last();
        $this->assertNotNull($job);

        /** @var JobAttachment $attachment */
        $attachment = $job->getAttachments()->last();

        $downloadUrl = $attachment->downloadUrl();
        $this->assertNotNull($downloadUrl);

        $this->outputResults($downloadUrl);
    }

    /**
     * @covers JobAttachment::download()
     */
    public function testDownload()
    {
        /** @var Job $job */
        $job = Job::get()->last();
        $this->assertNotNull($job);

        /** @var JobAttachment $attachment */
        $attachment = $job->getAttachments()->last();

        $download = $attachment->download();

        file_put_contents(__DIR__."/downloads/README.md", $download);

        $this->assertFileExists(__DIR__."/downloads/README.md");
    }

    /**
     * @covers JobAttachment::downloadFile()
     */
    public function testDownloadFile()
    {
        /** @var Job $job */
        $job = Job::get()->last();
        $this->assertNotNull($job);

        /** @var JobAttachment $attachment */
        $attachment = $job->getAttachments()->last();
        $attachment->downloadFile(__DIR__."/downloads/README.md");

        $this->assertFileExists(__DIR__."/downloads/README.md");
    }



}
