<?php
declare(strict_types=1);

namespace UCRM\Common;

/**
 * Class PluginTests
 *
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 */
class PluginTests extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        Plugin::initialize(__DIR__ . "/../../../examples/template/src/");

        /*
        $env = new \Dotenv\Dotenv(__DIR__ . "/../../../../");
        $env->load();

        Database::connect(
            getenv("POSTGRES_HOST"),
            (int)getenv("POSTGRES_PORT"),
            getenv("POSTGRES_DB"),
            getenv("POSTGRES_USER"),
            getenv("POSTGRES_PASSWORD")
        );
        */
    }

    public function testInitialize()
    {
        echo Plugin::mode();
        //$this->assertCount(1, $options);

    }



}