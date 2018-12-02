<?php
declare(strict_types=1);

namespace UCRM\REST\Endpoints\Helpers;

use MVQN\Collections\Collection;
use MVQN\REST\RestClient;
use UCRM\REST\Endpoints\Job;
use UCRM\REST\Endpoints\JobAttachment;

/**
 * Trait JobAttachmentHelper
 *
 * @package UCRM\REST\Endpoints\Helpers
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 */
trait JobAttachmentHelper
{
    //use Common\ClientHelpers;
    //use Common\AssignedUserHelpers;

    // =================================================================================================================
    // OBJECT METHODS
    // -----------------------------------------------------------------------------------------------------------------

    // =================================================================================================================
    // CREATE METHODS
    // -----------------------------------------------------------------------------------------------------------------

    // =================================================================================================================
    // READ METHODS
    // -----------------------------------------------------------------------------------------------------------------

    public static function getByJobId(int $jobId): Collection
    {
        return JobAttachment::get("", [], [ "jobId" => $jobId ]);
    }


    public static function getByFilename(string $filename): Collection
    {
        return JobAttachment::get()->where("filename", $filename);
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


    public function downloadUrl(): string
    {
        return "/scheduling/jobs/attachments/{$this->getId()}/file";
    }

    public function download(): string
    {
        $data = RestClient::download($this->downloadUrl());

        $data = base64_decode($data);

        return $data;
    }

    public function downloadFile(string $path): self
    {
        $data = $this->download();

        file_put_contents($path, $data);

        return $this;
    }



}