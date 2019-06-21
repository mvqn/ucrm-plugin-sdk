<?php
declare(strict_types=1);

namespace UCRM\Sessions;

use Exception;

use MVQN\REST\RestClient;
use UCRM\Common\Config;

/**
 * Class Session
 *
 * @package UCRM\Sessions
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 */
class Session
{

    /**
     * @return SessionUser|null
     * @throws Exception
     */
    public static function getCurrentUser(): ?SessionUser
    {
        // IF the PHPSESSID cookie is not currently set, THEN no user is currently logged in, so return NULL!
        if(!isset($_COOKIE["PHPSESSID"]))
            return null;

        // In the case of /current-user, ALWAYS use localhost for security!
        $host = "localhost";

        // Check to determine which scheme and port to use for the lookup...
        switch(Config::getServerPort())
        {
            case 80:
                $protocol = "http";
                $port = "";
                break;
            case 443:
                $protocol = "https";
                $port = "";
                break;
            default:
                $protocol = "http"; // TODO: Check to determine whether we can get HTTP/HTTPS from the database also!
                $port = ":".Config::getServerPort(); // Non-standard port is being used here!
                break;
        }

        // Combine the pieces to make up the request URL.
        $url = "$protocol://$host$port";

        // Generate the necessary headers, passing along the PHP Session ID.
        $headers = [
            "Content-Type: application/json",
            "Cookie: PHPSESSID=" . preg_replace('~[^a-zA-Z0-9]~', "", $_COOKIE["PHPSESSID"] ?? ""),
        ];

        // Store the current cURL client's base URL and headers, to restore when complete.
        $oldUrl = RestClient::getBaseUrl();
        $oldHeaders = RestClient::getHeaders();

        // Set the current cURL client's base URL and headers for immediate use.
        RestClient::setBaseUrl($url);
        RestClient::setHeaders($headers);

        // Make a request to the UCRM server to get the currently logged in user.
        $results = RestClient::get("/current-user");

        // Restore the old cURL client's base URL and headers.
        RestClient::setBaseUrl($oldUrl);
        RestClient::setHeaders($oldHeaders);

        // Return either NULL or a SessionUser object parsed from the resulting data!
        return $results !== null ? new SessionUser($results) : null;
    }


}
