<?php
declare(strict_types=1);

namespace UCRM\Data\Models;

use MVQN\Data\Database;

/**
 * Class OptionTests
 *
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 */
class OptionTests extends \PHPUnit\Framework\TestCase
{
    protected function setUp()
    {
        $env = new \Dotenv\Dotenv(__DIR__ . "/../../../../");
        $env->load();

        Database::connect(
            getenv("POSTGRES_HOST"),
            (int)getenv("POSTGRES_PORT"),
            getenv("POSTGRES_DB"),
            getenv("POSTGRES_USER"),
            getenv("POSTGRES_PASSWORD")
        );

    }

    // =================================================================================================================
    // DATABASE
    // -----------------------------------------------------------------------------------------------------------------

    public function testOptionSelect()
    {
        $options = Option::select();
        echo $options."\n";
        $this->assertGreaterThan(0, count($options));

        $options = $options->where("code", "MAILER_SENDER_ADDRESS");
        echo $options."\n";
        $this->assertCount(1, $options);
    }

    public function testOptionWhere()
    {
        $options = Option::where("code", "=", "MAILER_SENDER_ADDRESS");
        echo $options."\n";
        $this->assertCount(1, $options);

        $options = Option::where("optionId", "=", 19);
        echo $options."\n";
        $this->assertCount(1, $options);

    }



}