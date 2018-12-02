<?php
declare(strict_types=1);

namespace UCRM\REST\Endpoints\Helpers\Common;

use UCRM\REST\Endpoints\User;

/**
 * Trait UserHelpers
 *
 * @package UCRM\REST\Endpoints\Helpers\Common
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 */
trait UserHelpers
{
    // =================================================================================================================
    // HELPER METHODS
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return User|null
     * @throws \Exception
     */
    public function getUser(): ?User
    {
        if(property_exists($this, "userId") && $this->{"userId"} !== null)
            $user = User::getById($this->{"userId"});

        /** @var User $user */
        return $user;
    }

    /**
     * @param User $user
     * @return self Returns the appropriate Endpoint instance, for method chaining purposes.
     */
    public function setUser(User $user): self
    {
        $this->{"userId"} = $user->getId();

        /** @var self $this */
        return $this;
    }

    /**
     * @param string $email
     * @return self Returns the appropriate Endpoint instance, for method chaining purposes.
     * @throws \Exception
     */
    public function setUserByEmail(string $email): self
    {
        /** @var User $user */
        $user = User::getByEmail($email);
        $this->{"userId"} = $user->getId();

        /** @var self $this */
        return $this;
    }

    /**
     * @param string $username
     * @return self Returns the appropriate Endpoint instance, for method chaining purposes.
     * @throws \Exception
     */
    public function setUserByUsername(string $username): self
    {
        /** @var User $user */
        $user = User::getByUsername($username);
        $this->{"userId"} = $user->getId();

        /** @var self $this */
        return $this;
    }

}