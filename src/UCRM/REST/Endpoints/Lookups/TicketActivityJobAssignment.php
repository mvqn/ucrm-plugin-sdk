<?php
declare(strict_types=1);

namespace UCRM\REST\Endpoints\Lookups;

use MVQN\REST\Annotations\PostAnnotation as Post;
use MVQN\REST\Annotations\PatchAnnotation as Patch;

use MVQN\Collections\Collection;

/**
 * Class TicketActivityJobAssignment
 *
 * @package UCRM\REST\Endpoints
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 * @final
 *
 * @method int|null getAssignedJobId()
 */
final class TicketActivityJobAssignment extends Lookup
{

    public const ASSIGNED_JOB_TYPE_ADD = "add";
    public const ASSIGNED_JOB_TYPE_REMOVE = "remove";

    // =================================================================================================================
    // PROPERTIES
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @var int
     */
    protected $assignedJobId;

    /**
     * @var string
     */
    protected $type;

}
