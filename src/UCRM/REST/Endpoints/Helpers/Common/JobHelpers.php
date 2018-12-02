<?php
declare(strict_types=1);

namespace UCRM\REST\Endpoints\Helpers\Common;

use UCRM\REST\Endpoints\Job;

/**
 * Trait JobHelpers
 *
 * @package UCRM\REST\Endpoints\Helpers\Common
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 */
trait JobHelpers
{
    // =================================================================================================================
    // HELPER METHODS
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return Job|null
     * @throws \Exception
     */
    public function getJob(): ?Job
    {
        if(property_exists($this, "jobId") && $this->{"jobId"} !== null)
            $job = Job::getById($this->{"jobId"});

        /** @var Job $job */
        return $job;
    }

    /**
     * @param Job $job
     * @return self Returns the appropriate Endpoint instance, for method chaining purposes.
     */
    public function setJob(Job $job): self
    {
        $this->{"jobId"} = $job->getId();

        /** @var self $this */
        return $this;
    }

}