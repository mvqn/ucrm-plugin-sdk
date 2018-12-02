<?php
declare(strict_types=1);

namespace UCRM\REST\Endpoints\Helpers;

use MVQN\Collections\Collection;
use UCRM\REST\Endpoints\Country;
use UCRM\REST\Endpoints\State;

/**
 * Trait StateHelper
 *
 * @package UCRM\REST\Endpoints\Helpers
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 */
trait StateHelper
{
    use Common\CountryHelpers;

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
     * @param Country $country
     * @param string $name
     * @return State|null
     * @throws \Exception
     */
    public static function getByName(Country $country, string $name): ?State
    {
        if($country === null)
            throw new \Exception("Cannot call State->getByName() without providing a valid Country!");

        /** @var Collection $states */
        $states = $country->getStates();

        /** @var State $state */
        $state = $states->where("name", $name)->first();
        return $state;
    }

    /**
     * @param Country $country
     * @param string $code
     * @return State|null
     * @throws \Exception
     */
    public static function getByCode(Country $country, string $code): ?State
    {
        if($country === null)
            throw new \Exception("Cannot call State->getByName() without providing a valid Country!");

        /** @var Collection $states */
        $states = $country->getStates();

        /** @var State $state */
        $state = $states->where("code", $code)->first();
        return $state;
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