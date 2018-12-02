<?php
declare(strict_types=1);

namespace UCRM\REST\Endpoints\Helpers;

use MVQN\Collections\Collection;
use UCRM\REST\Endpoints\Job;
use UCRM\REST\Endpoints\JobAttachment;
use UCRM\REST\Endpoints\JobComment;

/**
 * Trait JobCommentHelper
 *
 * @package UCRM\REST\Endpoints\Helpers
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 */
trait JobCommentHelper
{
    use Common\JobHelpers;
    use Common\UserHelpers;


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
        return JobComment::get("", [], [ "jobId" => $jobId ]);
    }

    public static function getByUserId(int $userId): Collection
    {
        return JobComment::get("", [], [ "userId" => $userId ]);
    }

    /**
     * Sends an HTTP GET Request using the calling class's annotated information, for objects, given the Creation Date.
     *
     * @param \DateTime $date The date for which to retrieve all Client Logs.
     * @return Collection Returns a collection of Client Logs, created on the specified date.
     *
     * @throws \Exception
     */
    public static function getByCreatedDate(\DateTime $date): Collection
    {
        return JobComment::get("", [], [ "createdDateFrom" => $date->format("Y-m-d"),
            "createdDateTo" => $date->format("Y-m-d") ]);
    }

    /**
     * Sends an HTTP GET Request using the calling class's annotated information, for objects, given the Creation Date.
     *
     * @param \DateTime $from The starting date for which to retrieve all Client Logs.
     * @param \DateTime $to The ending date for which to retrieve all Client Logs.
     * @return Collection Returns a collection of Client Logs, created between the specified dates.
     * @throws \Exception
     */
    public static function getByCreatedDateBetween(\DateTime $from, \DateTime $to): Collection
    {
        return JobComment::get("", [], [ "createdDateFrom" => $from->format("Y-m-d"),
            "createdDateTo" => $to->format("Y-m-d") ]);
    }


    // =================================================================================================================
    // UPDATE METHODS
    // -----------------------------------------------------------------------------------------------------------------

    // =================================================================================================================
    // DELETE METHODS
    // -----------------------------------------------------------------------------------------------------------------

    // =================================================================================================================
    // HELPER METHODS
    // -----------------------------------------------------------------------------------------------------------------





}