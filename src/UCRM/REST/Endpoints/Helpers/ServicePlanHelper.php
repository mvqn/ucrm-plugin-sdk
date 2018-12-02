<?php
declare(strict_types=1);

namespace UCRM\REST\Endpoints\Helpers;

use MVQN\Collections\Collection;
use UCRM\REST\Endpoints\Helpers\Common\OrganizationHelpers;
use UCRM\REST\Endpoints\ServicePlan;

/**
 * Trait ServicePlanHelper
 *
 * @package UCRM\REST\Endpoints\Helpers
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 */
trait ServicePlanHelper
{
    use OrganizationHelpers;

    // =================================================================================================================
    // HELPER METHODS
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @param string $name
     * @return Collection
     * @throws \Exception
     */
    public static function getByName(string $name): Collection
    {
        return ServicePlan::get()->where("name", $name);
    }






}