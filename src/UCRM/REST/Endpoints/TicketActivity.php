<?php
declare(strict_types=1);

namespace UCRM\REST\Endpoints;

use MVQN\REST\Endpoints\EndpointObject;
use MVQN\REST\Annotations\EndpointAnnotation as Endpoint;
use MVQN\REST\Annotations\PostAnnotation as Post;
use MVQN\REST\Annotations\PostRequiredAnnotation as PostRequired;
use MVQN\REST\Annotations\PatchAnnotation as Patch;
use MVQN\REST\Annotations\PatchRequiredAnnotation as PatchRequired;
use UCRM\REST\Endpoints\Lookups\TicketActivityAssignment;
use UCRM\REST\Endpoints\Lookups\TicketActivityClientAssignment;
use UCRM\REST\Endpoints\Lookups\TicketActivityComment;
use UCRM\REST\Endpoints\Lookups\TicketActivityCommentAttachment;
use UCRM\REST\Endpoints\Lookups\TicketActivityJobAssignment;
use UCRM\REST\Endpoints\Lookups\TicketActivityStatusChange;

//use UCRM\REST\Endpoints\Helpers\PaymentHelper;
//use UCRM\REST\Endpoints\Lookups\PaymentCover;

/**
 * Class TicketActivity
 *
 * @package UCRM\REST\Endpoints
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 * @final
 *
 * @Endpoint { "get": "/ticketing/tickets/activities", "getById": "/ticketing/tickets/activities/:id" }
 * @Endpoint { "post": "/ticketing/tickets/activities" }
 * @Endpoint { "patch": "/ticketing/tickets/activities/:id" }
 * @Endpoint { "delete": "/ticketing/tickets/activities/:id" }
 *
 * @method int|null getUserId()
 * @method TicketActivity setUserId(int $userId)
 * @method string|null getCreatedAt()
 * @see    TicketActivity setCreatedAt(\DateTimeImmutable $date)
 * @method bool|null getPublic()
 * @method TicketActivity setPublic(bool $isPublic)
 * @method int|null getTicketId()
 * @method TicketActivity setTicketId(int $ticketId)
 * @see    TicketActivityComment getComment()
 * @see    TicketActivity setComment(TicketActivityComment $comment)
 * @see    TicketActivityAssignment getAssignment()
 * @see    TicketActivity setAssignment(TicketActivityAssignment $assignment)
 * @see    TicketActivityClientAssignment getClientAssignment()
 * @see    TicketActivity setClientAssignment(TicketActivityClientAssignment $clientAssignment)
 * @see    TicketActivityStatusChange getStatusChange()
 * @see    TicketActivity setStatusChange(TicketActivityStatusChange $statusChange)
 * @see    TicketActivityJobAssignment getJobAssignment()
 * @see    TicketActivity setJobAssignment(TicketActivityJobAssignment $jobAssignment)
 * @method string|null getType()
 * @method TicketActivity setType(string $type)
 */
final class TicketActivity extends EndpointObject
{
    public const DATETIME_FORMAT = "Y-m-d\TH:i:sO";

    // =================================================================================================================
    // ENUMS
    // -----------------------------------------------------------------------------------------------------------------

    public const TYPE_COMMENT           = "comment";
    public const TYPE_ASSIGNMENT        = "assignment";
    public const TYPE_ASSIGNMENT_CLIENT = "assignment_client";
    public const TYPE_ASSIGNMENT_JOB    = "assignment_job";
    public const TYPE_STATUS_CHANGE     = "status_change";

    // =================================================================================================================
    // PROPERTIES
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @var int
     * @Post
     * @Patch
     */
    protected $userId;

    /**
     * @var string
     * @Post
     * @Patch
     */
    protected $createdAt;

    /**
     * @param \DateTimeInterface $date
     * @return TicketActivity
     */
    public function setCreatedAt(\DateTimeInterface $date): TicketActivity
    {
        $this->createdAt = $date->format(self::DATETIME_FORMAT);
        return $this;
    }

    /**
     * @var bool
     * @Post
     * @Patch
     */
    protected $public;

    /**
     * @var int
     * @Post
     * @Patch
     */
    protected $ticketId;

    /**
     * @var array
     * @Post
     * @Patch
     */
    protected $comment;

    /**
     * @return TicketActivityComment|null
     * @todo Fix nested Lookups issue with RestObject.
     */
    public function getComment(): ?TicketActivityComment
    {
        return $this->comment != null ? new TicketActivityComment($this->comment) : null;
    }

    /**
     * @param TicketComment $comment
     * @return TicketActivity
     * @throws \Exception
     * @todo Fix nested Lookups issue with RestObject.
     */
    public function setComment(TicketComment $comment): TicketActivity
    {
        $this->comment = $comment->toArray("post");
        return $this;
    }

    /**
     * @var array
     * @Post
     * @Patch
     */
    protected $assignment;

    /**
     * @return TicketActivityAssignment|null
     * @todo Fix nested Lookups issue with RestObject.
     */
    public function getAssignment(): ?TicketActivityAssignment
    {
        return $this->assignment !== null ? new TicketActivityAssignment($this->assignment) : null;
    }

    /**
     * @param TicketActivityAssignment $assignment
     * @return TicketActivity
     * @throws \Exception
     * @todo Fix nested Lookups issue with RestObject.
     */
    public function setAssignment(TicketActivityAssignment $assignment): TicketActivity
    {
        $this->assignment = $assignment->toArray("post");
        return $this;
    }

    /**
     * @var array
     * @Post
     * @Patch
     */
    protected $clientAssignment;

    /**
     * @return TicketActivityClientAssignment|null
     * @todo Fix nested Lookups issue with RestObject.
     */
    public function getClientAssignment(): ?TicketActivityClientAssignment
    {
        return $this->clientAssignment !== null ? new TicketActivityClientAssignment($this->clientAssignment) : null;
    }

    /**
     * @param TicketActivityClientAssignment $clientAssignment
     * @return TicketActivity
     * @throws \Exception
     * @todo Fix nested Lookups issue with RestObject.
     */
    public function setClientAssignment(TicketActivityClientAssignment $clientAssignment): TicketActivity
    {
        $this->clientAssignment = $clientAssignment->toArray("post");
        return $this;
    }

    /**
     * @var array
     * @Post
     * @Patch
     */
    protected $statusChange;

    /**
     * @return TicketActivityStatusChange|null
     * @todo Fix nested Lookups issue with RestObject.
     */
    public function getStatusChange(): ?TicketActivityStatusChange
    {
        return $this->statusChange !== null ? new TicketActivityStatusChange($this->statusChange) : null;
    }

    /**
     * @param TicketActivityStatusChange $statusChange
     * @return TicketActivity
     * @throws \Exception
     * @todo Fix nested Lookups issue with RestObject.
     */
    public function setStatusChange(TicketActivityStatusChange $statusChange): TicketActivity
    {
        $this->statusChange = $statusChange->toArray("post");
        return $this;
    }

    /**
     * @var array
     * @Post
     * @Patch
     */
    protected $jobAssignment;

    /**
     * @return TicketActivityJobAssignment|null
     * @todo Fix nested Lookups issue with RestObject.
     */
    public function getJobAssignment(): ?TicketActivityJobAssignment
    {
        return $this->jobAssignment !== null ? new TicketActivityJobAssignment($this->jobAssignment) : null;
    }

    /**
     * @param TicketActivityJobAssignment $jobAssignment
     * @return TicketActivity
     * @throws \Exception
     * @todo Fix nested Lookups issue with RestObject.
     */
    public function setJobAssignment(TicketActivityJobAssignment $jobAssignment): TicketActivity
    {
        $this->jobAssignment = $jobAssignment->toArray("post");
        return $this;
    }

    /**
     * @var string
     * @Post
     * @Patch
     */
    protected $type;

}
