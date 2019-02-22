<?php
declare(strict_types=1);

namespace UCRM\REST\Endpoints;

require_once __DIR__."/../../../../vendor/autoload.php";

use MVQN\Annotations\AnnotationReader;
use MVQN\Collections\Collectible;
use MVQN\REST\Endpoints\EndpointObject;
use MVQN\REST\RestClient;


class EndpointTestCase extends \PHPUnit\Framework\TestCase
{
    // =================================================================================================================
    // INITIALIZATION
    // -----------------------------------------------------------------------------------------------------------------

    /** @var string Location of the .env file for development. */
    const DOTENV_PATH = __DIR__ . "/../../../../";

    protected function setUp()
    {
        // Load ENV variables from a file during development.
        if(file_exists(self::DOTENV_PATH.".env"))
        {
            $dotenv = new \Dotenv\Dotenv(self::DOTENV_PATH);
            $dotenv->load();
        }

        //RestClient::cacheDir(__DIR__);

        RestClient::setBaseUrl(getenv("REST_URL"));
        RestClient::setHeaders([
            "Content-Type: application/json",
            "X-Auth-App-Key: ".getenv("REST_KEY")
        ]);
    }


    public function outputResults($results)
    {
        $debug = debug_backtrace()[1];
        $class = $debug["class"];
        $method = $debug["function"];

        $reader = new AnnotationReader($class);
        $covers = $reader->getMethodAnnotations($method)["covers"];

        if($covers !== null)
        {
            echo ">>> ";
            print_r($covers);
            echo "\n";
        }

        if (is_string($results) ||
            is_a($results, \JsonSerializable::class) ||
            method_exists($results, "__toString"))
        {
            echo $results;
        }
        else
        {
            print_r($results);
        }




    }



    // =================================================================================================================
    // GLOBAL TESTS
    // -----------------------------------------------------------------------------------------------------------------

    public static function outputGetters(EndpointObject $endpoint, array $ignores = []): bool
    {
        $class = get_class($endpoint);

        $reflection = new \ReflectionClass($endpoint);
        $properties = $reflection->getProperties(\ReflectionProperty::IS_PROTECTED);

        echo ">>> {$class}Tests->outputAllGetters()\n";

        foreach ($properties as $property)
        {
            if(in_array($property->getName(), $ignores))
                continue;

            $name = $property->getName();
            $func = "get" . ucfirst($name);

            $value = $endpoint->$func();

            $funcName = $reflection->getShortName()."::get".ucfirst($name)."()";

            echo ">   $funcName => ".(is_array($value) ? json_encode($value, JSON_UNESCAPED_SLASHES) : $value)."\n";
        }

        echo "\n";

        return true;
    }
}