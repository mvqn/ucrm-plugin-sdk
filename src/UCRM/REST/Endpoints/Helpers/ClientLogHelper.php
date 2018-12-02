<?php
declare(strict_types=1);

namespace UCRM\REST\Endpoints\Helpers;

use MVQN\Collections\Collection;
use UCRM\REST\Endpoints\ClientLog;

/**
 * Trait ClientLogHelper
 *
 * @package UCRM\REST\Endpoints\Helpers
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 */
trait ClientLogHelper
{
    use Common\ClientHelpers;
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

    /**
     * Sends an HTTP GET Request using the calling class's annotated information, for objects, given the Client ID.
     *
     * @param int $clientId The Client ID for which to filter the Client Logs.
     * @return Collection Returns a collection of Client Logs, belonging to the specified Client ID.
     * @throws \Exception
     */
    public static function getByClientId(int $clientId): Collection
    {
        return ClientLog::get("", [], [ "clientId" => $clientId ]);
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
        return ClientLog::get("", [], [ "createdDateFrom" => $date->format("Y-m-d"),
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
        return ClientLog::get("", [], [ "createdDateFrom" => $from->format("Y-m-d"),
            "createdDateTo" => $to->format("Y-m-d") ]);
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

}