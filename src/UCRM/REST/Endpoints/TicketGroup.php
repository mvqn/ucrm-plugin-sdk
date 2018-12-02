<?php
declare(strict_types=1);

namespace UCRM\REST\Endpoints;

use MVQN\REST\Endpoints\EndpointObject;
use MVQN\REST\Annotations\EndpointAnnotation as Endpoint;

/**
 * Class TicketGroup
 *
 * @package UCRM\REST\Endpoints
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 * @final
 *
 * @Endpoint { "get": "/ticketing/ticket-groups", "getById": "/ticketing/ticket-groups/:id" }
 * @Endpoint { "post": "/ticketing/ticket-groups" }
 * @Endpoint { "patch": "/ticketing/ticket-groups/:id" }
 * @Endpoint { "delete": "/ticketing/ticket-groups/:id" }
 *
 * @method string|null getName()
 *
 */
final class TicketGroup extends EndpointObject
{
    // =================================================================================================================
    // PROPERTIES
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @var string
     * @PostRequired
     * @PatchRequired
     */
    protected $name;

}
