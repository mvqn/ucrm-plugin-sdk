<?php
declare(strict_types=1);

namespace UCRM\REST\Endpoints\Helpers;

use MVQN\REST\RestClient;
use UCRM\REST\Endpoints\User;

/**
 * Trait UserHelper
 *
 * @package UCRM\REST\Endpoints\Helpers
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 */
trait UserHelper
{
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
     * @param string $email
     * @return User|null
     * @throws \Exception
     */
    public static function getByEmail(string $email): ?User
    {
        $users = User::get();

        /** @var User $user */
        $user = $users->where("email", $email)->first();
        return $user;
    }

    /**
     * @param string $first
     * @param string $last
     * @return User|null
     * @throws \Exception
     */
    public static function getByName(string $first, string $last): ?User
    {
        $users = User::get();

        /** @var User $user */
        $user = $users->whereAll([ "firstName" => $first, "lastName" => $last ])->first();
        return $user;
    }

    /**
     * @param string $username
     * @return User|null
     * @throws \Exception
     */
    public static function getByUsername(string $username): ?User
    {
        $users = User::get();

        /** @var User $user */
        $user = $users->where("username", $username)->first();
        return $user;
    }

    private static function getCurrentlyAuthenticated(): ?array
    {
        if(!isset($_COOKIE["PHPSESSID"]))
            return null;

        $sessionId = $_COOKIE["PHPSESSID"];
        $cookie = "PHPSESSID=" . preg_replace('~[^a-zA-Z0-9]~', '', $_COOKIE['PHPSESSID']);

        RestClient::pushHeader("Cookie: ".$cookie);

        $restUrl = RestClient::getBaseUrl();
        $tempUrl = (getenv("UCRM_REST_URL_DEV") ?: "http://localhost");

        RestClient::setBaseUrl($tempUrl);

        $current = RestClient::get("/current-user");

        RestClient::setBaseUrl($restUrl);

        RestClient::popHeader();

        return $current;
    }

    public static function getByAuthenticated(?array &$authenticated = null): ?User
    {
        $authenticated = self::getCurrentlyAuthenticated();

        if($authenticated === null || $authenticated["isClient"] || $authenticated["userId"] === null)
            return null;

        /** @var User $user */
        $user = User::getById($authenticated["userId"]);

        return $user;
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