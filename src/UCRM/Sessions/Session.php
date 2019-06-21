<?php
declare(strict_types=1);

namespace UCRM\Sessions;

use MVQN\REST\RestClient;
use UCRM\Common\Config;
use UCRM\Sessions\SessionUser;
use UCRM\Sessions\SessionUser;


class Session
{
    private static $_curl;

    public static function getCurrentUser(): ?SessionUser
    {
        if(!isset($_COOKIE["PHPSESSID"]))
            return null;

        $sessionId = $_COOKIE["PHPSESSID"];
        $cookie = "PHPSESSID=" . preg_replace('~[^a-zA-Z0-9]~', '', $_COOKIE['PHPSESSID']);



        //$host = Config::getServerFQDN();
        $host = "localhost";

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
                $protocol = "http";
                $port = ":".Config::getServerPort();
                break;
        }

        $url = "$protocol://$host$port";

        //$url ="https://ucrm.dev.mvqn.net";


        $headers = [
            "Content-Type: application/json",
            "Cookie: PHPSESSID=" . preg_replace('~[^a-zA-Z0-9]~', "", $_COOKIE["PHPSESSID"] ?? ""),
        ];

        /*
        // Create a cURL session.
        self::$_curl = curl_init();


        // Set the options necessary for communicating with the UCRM Server.
        curl_setopt(self::$_curl, CURLOPT_URL, "https://ucrm.dev.mvqn.net/current-user");
        curl_setopt(self::$_curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(self::$_curl, CURLOPT_HEADER, false);
        curl_setopt(self::$_curl, CURLOPT_HTTPHEADER, [ "Cookie: PHPSESSID=f68eaede6caa83b9ba9661cf108b3b43" ]);
        curl_setopt(self::$_curl, CURLOPT_TIMEOUT, 1);

        //curl_setopt($curl, CURLOPT_FORBID_REUSE, true);

        curl_setopt(self::$_curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt(self::$_curl, CURLOPT_SSL_VERIFYPEER, 1);

        // Downloaded from: https://curl.haxx.se/docs/caextract.html
        curl_setopt(self::$_curl, CURLOPT_CAINFO, __DIR__ . "/../../../../rest/src/MVQN/REST/Certificates/cacert-2018-10-17.pem");
        curl_setopt(self::$_curl, CURLOPT_CAPATH, __DIR__ . "/../../../../rest/src/MVQN/REST/Certificates/cacert-2018-10-17.pem");

        $response = curl_exec(self::$_curl);

        echo curl_error(self::$_curl);
        echo "*".$response."*";
        curl_close(self::$_curl);

        echo "FINISHED";


        exit();
        */




        $oldUrl = RestClient::getBaseUrl();
        $oldHeaders = RestClient::getHeaders();

        RestClient::setBaseUrl($url);
        RestClient::setHeaders($headers);

        $results = RestClient::get("/current-user");

        RestClient::setBaseUrl($oldUrl);
        RestClient::setHeaders($oldHeaders);

        return $results !== null ? new SessionUser($results) : null;
    }


}
