<?php
declare(strict_types=1);

namespace UCRM\REST\Endpoints\Lookups;

use MVQN\REST\Annotations\PostAnnotation as Post;
use MVQN\REST\Annotations\PatchAnnotation as Patch;

use MVQN\Collections\Collection;

/**
 * Class TicketActivityAssignment
 *
 * @package UCRM\REST\Endpoints
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 * @final
 *
 * @method int|null getAssignedUserId()
 */
final class TicketActivityAssignment extends Lookup
{
    // =================================================================================================================
    // PROPERTIES
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @var int
     */
    protected $assignedUserId;

}
