<?php
declare(strict_types=1);

namespace UCRM\REST;

class EndpointGenerator
{
    /**
     *
     */
    private const GENERATED_PATH_PREFIX = __DIR__ . "/../../../.cache";

    /**
     * @var string
     */
    public $path = "";



    public function EndpointGenerator(string $endpoint)
    {
        if(!file_exists(self::GENERATED_PATH_PREFIX))
            mkdir(self::GENERATED_PATH_PREFIX);

        $path = self::GENERATED_PATH_PREFIX . "/$endpoint";

        if(!file_exists($path))
            file_put_contents($path, "");

        $this->path = realpath($path);
    }



    public static function generate()
    {

    }




}