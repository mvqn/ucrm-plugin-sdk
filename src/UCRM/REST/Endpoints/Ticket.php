<?php
declare(strict_types=1);

namespace UCRM\REST\Endpoints;

use MVQN\Collections\Collection;
use MVQN\REST\Endpoints\EndpointObject;
use MVQN\REST\Annotations\EndpointAnnotation as Endpoint;
use MVQN\REST\Annotations\PostAnnotation as Post;
use MVQN\REST\Annotations\PostRequiredAnnotation as PostRequired;
use MVQN\REST\Annotations\PatchAnnotation as Patch;
use MVQN\REST\Annotations\PatchRequiredAnnotation as PatchRequired;
use UCRM\REST\Endpoints\Lookups\TicketActivityComment;

//use UCRM\REST\Endpoints\Helpers\PaymentHelper;
//use UCRM\REST\Endpoints\Lookups\PaymentCover;

/**
 * Class Ticket
 *
 * @package UCRM\REST\Endpoints
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 * @final
 *
 * @Endpoint { "get": "/ticketing/tickets", "getById": "/ticketing/tickets/:id" }
 * @Endpoint { "post": "/ticketing/tickets" }
 * @Endpoint { "patch": "/ticketing/tickets/:id" }
 * @Endpoint { "delete": "/ticketing/tickets/:id" }
 *
 * @method string|null  getSubject()
 * @method Ticket       setSubject(string $subject)
 * @method int|null     getClientId()
 * @method Ticket       setClientId(int $clientId)
 * @method string|null  getEmailFromAddress()
 * @method Ticket       setEmailFromAddress(string $email)
 * @method string|null  getEmailFromName()
 * @method Ticket       setEmailFromName(string $name)
 * @method int|null     getAssignedGroupId()
 * @method Ticket       setAssignedGroupId(int $groupId)
 * @method int|null     getAssignedUserId()
 * @method Ticket       setAssignedUserId(int $userId)
 * @method string|null  getCreatedAt()
 * @see    Ticket       setCreatedAt(\DateTimeImmutable|null $date)
 * @method int|null     getStatus()
 * @method Ticket       setStatus(int $status)
 * @method bool|null    getPublic()
 * @method Ticket       setPublic(bool $public)
 * @method int[]|null   getAssignedJobIds()
 * @method Ticket       setAssignedJobIds(int[] $subject)
 * @method string|null  getLastActivity()
 * @method string|null  getLastCommentAt()
 * @method string|null  getIsLastActivityByClient()
 */
final class Ticket extends EndpointObject
{
    public const DATETIME_FORMAT = "Y-m-d\TH:i:sO";

    use Helpers\TicketHelper;

    // =================================================================================================================
    // ENUMS
    // -----------------------------------------------------------------------------------------------------------------

    public const STATUS_NEW     = 0;
    public const STATUS_OPEN    = 1;
    public const STATUS_PENDING = 2;
    public const STATUS_SOLVED  = 3;

    // =================================================================================================================
    // PROPERTIES
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @var string
     * @PostRequired
     * @PatchRequired
     */
    protected $subject;

    /**
     * @var int
     * @PostRequired
     * @PatchRequired
     */
    protected $clientId;

    /**
     * @var string
     */
    protected $emailFromAddress;

    /**
     * @var string
     */
    protected $emailFromName;

    /**
     * @var int
     */
    protected $assignedGroupId;

    /**
     * @var int
     */
    protected $assignedUserId;

    /**
     * @var string
     */
    protected $createdAt;

    /**
     * @param \DateTimeInterface|null $date
     * @return Ticket
     * @throws \Exception
     */
    public function setCreatedAt(?\DateTimeInterface $date = null): Ticket
    {
        if($date === null)
            $date = new \DateTime();

        $this->createdAt = $date->format(self::DATETIME_FORMAT);
        return $this;
    }

    /**
     * @var int
     */
    protected $status;

    /**
     * @var bool
     */
    protected $public;

    /**
     * @var array[]
     */
    protected $assignedJobIds;

    /**
     * @var string
     */
    protected $lastActivity;

    /**
     * @var string
     */
    protected $lastCommentAt;

    /**
     * @var string
     */
    protected $isLastActivityByClient;

    /**
     * NOTE: This can only be a single element array during a POST

     * @var TicketActivity[]|null
     * @PostRequired
     * @PatchRequired
     *
     * @todo Add an annotation for single element only POST.
     */
    protected $activity;


    public function getActivity(): Collection
    {
        return new Collection(TicketActivity::class, $this->activity);
    }

    public function setActivity(array $activity): Ticket
    {
        $this->activity = $activity;
        return $this;
    }





}
