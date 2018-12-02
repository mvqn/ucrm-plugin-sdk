<?php
declare(strict_types=1);

namespace UCRM\REST\Endpoints\Helpers;

use MVQN\Collections\Collection;
use UCRM\REST\Endpoints\Tax;
use UCRM\REST\Endpoints\Ticket;
use UCRM\REST\Endpoints\TicketActivity;
use UCRM\REST\Endpoints\TicketComment;
use UCRM\REST\Endpoints\TicketCommentAttachment;

trait TicketHelper
{
    use Common\ClientHelpers;
    use Common\AssignedUserHelpers;
    use Common\AssignedJobHelpers;

    // =================================================================================================================
    // OBJECT METHODS
    // -----------------------------------------------------------------------------------------------------------------

    // =================================================================================================================
    // CREATE METHODS
    // -----------------------------------------------------------------------------------------------------------------

    public static function createNewWithComment(int $clientId, string $subject, string $comment, bool $public = false): Ticket
    {
        $ticket = new Ticket([
            "clientId" => $clientId,
            "subject" => $subject,
            "createdAt" => new \DateTime(),
            "status" => Ticket::STATUS_NEW,
            "public" => $public,
        ]);

        $ticket->addComment($comment);



        return $ticket;
    }









    // =================================================================================================================
    // READ METHODS
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @param int $clientId
     * @return Collection
     * @throws \Exception
     */
    public static function getByClientId(int $clientId): Collection
    {
        return Ticket::get("", [], [ "clientId" => $clientId ]);
    }

    /**
     * @param int $assignedUserId
     * @return Collection
     * @throws \Exception
     */
    public static function getByAssignedUserId(int $assignedUserId): Collection
    {
        return Ticket::get("", [], [ "assignedUserId" => $assignedUserId ]);
    }

    /**
     * @param int ...$statuses
     * @return Collection
     * @throws \Exception
     */
    public static function getByStatuses(int ...$statuses): Collection
    {
        return Ticket::get("", [], [ "statuses" => $statuses ]);
    }

    /**
     * Sends an HTTP GET Request using the calling class's annotated information, for objects, given the Creation Date.
     *
     * @param \DateTime $date The date for which to retrieve all Tickets.
     * @return Collection Returns a collection of Tickets, created on the specified date.
     *
     * @throws \Exception
     */
    public static function getByDate(\DateTime $date): Collection
    {
        return Ticket::get("", [], [ "dateFrom" => $date->format("Y-m-d"),
            "dateTo" => $date->format("Y-m-d") ]);
    }

    /**
     * Sends an HTTP GET Request using the calling class's annotated information, for objects, given the Creation Date.
     *
     * @param \DateTime $from The starting date for which to retrieve all Tickets.
     * @param \DateTime $to The ending date for which to retrieve all Tickets.
     * @return Collection Returns a collection of Tickets, created between the specified dates.
     * @throws \Exception
     */
    public static function getByDateBetween(\DateTime $from, \DateTime $to): Collection
    {
        return Ticket::get("", [], [ "dateFrom" => $from->format("Y-m-d"),
            "dateTo" => $to->format("Y-m-d") ]);
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


    public function addActivity(TicketActivity $activity): self
    {
        $this->activity[] = $activity;
        return $this;
    }

    public function addActivities(Collection $activities): self
    {
        $collection = new Collection(TicketActivity::class, $this->activity);
        $collection->pushMany($activities->elements());

        $this->activity[] = $collection->elements();
        return $this;
    }

    public function clearActivities(): self
    {
        $this->activity = [];
        return $this;
    }

    public function delActivity(int $index): self
    {
        if($this->activity === null)
            return $this;

        if($this->activity !== null && $index > 0 && $index < count($this->activity))
            unset($this->activity[$index]);

        $this->activity = array_values($this->activity);

        return $this;
    }

    public function delActivities(int ...$indices): self
    {
        if($this->activity === null)
            return $this;

        foreach($indices as $index)
            if($this->activity !== null && $index > 0 && $index < count($this->activity))
                unset($this->activity[$index]);

        $this->activity = array_values($this->activity);

        return $this;
    }







    public function addComment(string $message, bool $public = false): self
    {
        $comment = (new TicketComment())
            ->setCreatedAt()
            ->setTicketId($this->getId())
            ->setBody($message)
            ->setPublic($public);

        $insertedComment = $comment->insert();

        return $this;
    }

    public function addMimeAttachment(string $name, string $base64, string $message = "", bool $public = false): self
    {
        return $this->addMimeAttachments([ $name => $base64 ], $message, $public);
    }


    public function addMimeAttachments(array $files, string $message = "", bool $public = false): self
    {
        $comment = (new TicketComment())
            ->setCreatedAt()
            ->setTicketId($this->getId())
            ->setBody($message)
            ->setPublic($public);

        $attachments = [];

        foreach($files as $name => $data)
        {
            $attachments[] = new TicketCommentAttachment(
                [
                    "filename" => $name,
                    "file" => $data,
                ]
            );

            echo "";
        }

        $attachments = array_reverse($attachments);

        $comment->setAttachments($attachments);

        $insertedComment = $comment->insert();

        //echo $insertedComment;

        return $this;
    }




}