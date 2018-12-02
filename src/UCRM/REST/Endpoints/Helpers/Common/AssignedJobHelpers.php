<?php
declare(strict_types=1);

namespace UCRM\REST\Endpoints\Helpers\Common;

use MVQN\Collections\Collection;
use UCRM\REST\Endpoints\Job;

/**
 * Trait AssignedJobHelpers
 *
 * @package UCRM\REST\Endpoints\Helpers\Common
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 */
trait AssignedJobHelpers
{
    // =================================================================================================================
    // HELPER METHODS
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return Collection
     * @throws \Exception
     */
    public function getAssignedJobs(): Collection
    {
        $collection = [];

        if(property_exists($this, "assignedJobIds") && $this->{"assignedJobIds"} !== null)
        {
            foreach($this->{"assignedJobIds"} as $jobId)
            $collection[] = Job::getById($jobId);
        }

        return new Collection(Job::class, $collection);
    }

    /**
     * @param Job $job
     * @return self Returns the appropriate Endpoint instance, for method chaining purposes.
     */
    /*
    public function addAssignedJob(Job $job): self
    {
        $this->{"jobId"} = $job->getId();

        return $this;
    }
    */

}