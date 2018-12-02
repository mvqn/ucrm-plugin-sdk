<?php
declare(strict_types=1);

namespace UCRM\REST\Endpoints\Helpers\Common;

use UCRM\REST\Endpoints\User;

/**
 * Trait AssignedUserHelpers
 *
 * @package UCRM\REST\Endpoints\Helpers\Common
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 */
trait AssignedUserHelpers
{
    // =================================================================================================================
    // HELPER METHODS
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return User|null
     * @throws \Exception
     */
    public function getAssignedUser(): ?User
    {
        if(property_exists($this, "assignedUserId") && $this->{"assignedUserId"} !== null)
            $user = User::getById($this->{"assignedUserId"});

        /** @var User $user */
        return $user ?? null;
    }

    /**
     * @param User $user
     * @return self Returns the appropriate Endpoint instance, for method chaining purposes.
     */
    public function setAssignedUser(User $user): self
    {
        $this->{"assignedUserId"} = $user->getId();

        /** @var self $this */
        return $this;
    }

    /**
     * @param string $email
     * @return self Returns the appropriate Endpoint instance, for method chaining purposes.
     * @throws \Exception
     */
    public function setAssignedUserByEmail(string $email): self
    {
        /** @var User $user */
        $user = User::getByEmail($email);
        $this->{"assignedUserId"} = $user->getId();

        /** @var self $this */
        return $this;
    }

    /**
     * @param string $username
     * @return self Returns the appropriate Endpoint instance, for method chaining purposes.
     * @throws \Exception
     */
    public function setAssignedUserByUsername(string $username): self
    {
        /** @var User $user */
        $user = User::getByUsername($username);
        $this->{"assignedUserId"} = $user->getId();

        /** @var self $this */
        return $this;
    }

}