<?php
declare(strict_types=1);

namespace UCRM\REST\Endpoints\Helpers;

use MVQN\Collections\Collection;
use UCRM\REST\Endpoints\Job;
use UCRM\REST\Endpoints\JobAttachment;
use UCRM\REST\Endpoints\Ticket;

/**
 * Trait JobHelper
 *
 * @package UCRM\REST\Endpoints\Helpers
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 */
trait JobHelper
{
    use Common\ClientHelpers;
    use Common\AssignedUserHelpers;

    // =================================================================================================================
    // OBJECT METHODS
    // -----------------------------------------------------------------------------------------------------------------

    // =================================================================================================================
    // CREATE METHODS
    // -----------------------------------------------------------------------------------------------------------------

    // =================================================================================================================
    // READ METHODS
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Sends an HTTP GET Request using the calling class's annotated information, for objects, given the Client ID.
     *
     * @param int $clientId The Client ID for which to filter the Client Logs.
     * @return Collection Returns a collection of Client Logs, belonging to the specified Client ID.
     * @throws \Exception
     */
    public static function getByClientId(int $clientId): Collection
    {
        return Job::get("", [], [ "clientId" => $clientId ]);
    }

    public static function getByAssignedUserId(int $userId): Collection
    {
        return Job::get("", [], [ "assignedUserId" => $userId ]);
    }

    public static function getByTicketId(int $ticketId): Collection
    {
        return Job::get("", [], [ "ticketId" => $ticketId ]);
    }

    public static function getByStatuses(int ...$statuses): Collection
    {
        return Job::get("", [], [ "statuses" => $statuses ]);
    }

    /**
     * Sends an HTTP GET Request using the calling class's annotated information, for objects, given the Creation Date.
     *
     * @param \DateTime $date The date for which to retrieve all Client Logs.
     * @return Collection Returns a collection of Client Logs, created on the specified date.
     *
     * @throws \Exception
     */
    public static function getByDate(\DateTime $date): Collection
    {
        return Job::get("", [], [ "dateFrom" => $date->format("Y-m-d"),
            "dateTo" => $date->format("Y-m-d") ]);
    }

    /**
     * Sends an HTTP GET Request using the calling class's annotated information, for objects, given the Creation Date.
     *
     * @param \DateTime $from The starting date for which to retrieve all Client Logs.
     * @param \DateTime $to The ending date for which to retrieve all Client Logs.
     * @return Collection Returns a collection of Client Logs, created between the specified dates.
     * @throws \Exception
     */
    public static function getByDateBetween(\DateTime $from, \DateTime $to): Collection
    {
        return Job::get("", [], [ "dateFrom" => $from->format("Y-m-d"),
            "dateTo" => $to->format("Y-m-d") ]);
    }


    public static function getByTitle(string $title): Collection
    {
        return Job::get()->where("title", $title);
    }



    public function getTickets(): Collection
    {
        $tickets = Ticket::get();

        $matched = [];

        /** @var Ticket $ticket */
        foreach($tickets as $ticket)
        {
            $jobIds = $ticket->getAssignedJobIds();

            if(in_array($this->getId(), $jobIds))
                $matched[] = $ticket;
        }

        return new Collection(Ticket::class, $matched);
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


    public function uploadAttachment(string $filename, string $data): self
    {
        $attachment = (new JobAttachment())
            ->setJobId($this->{"id"})
            ->setFilename($filename)
            ->setFile($data);

        /** @var JobAttachment $addedAttachment */
        $addedAttachment = $attachment->insert();

        return $this;
    }






}