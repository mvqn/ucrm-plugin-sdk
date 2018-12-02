<?php
declare(strict_types=1);

namespace UCRM\Data\Models;

use MVQN\Data\Database;

/**
 * Class GeneralTests
 *
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 */
class GeneralTests extends \PHPUnit\Framework\TestCase
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

    public function testGeneralSelect()
    {
        $generals = General::select();
        echo $generals."\n";
        $this->assertGreaterThan(0, count($generals));

        $generals = $generals->where("code", "crm_installed_version");
        echo $generals."\n";
        $this->assertNotNull($generals);

        $generals = $generals->where("code", "crm_installed_version");
        echo $generals."\n";
        $this->assertNotNull($generals);
    }

    public function testGeneralWhere()
    {
        $generals = General::where("code", "=", "crm_installed_version");
        echo $generals."\n";
        $this->assertCount(1, $generals);

        $generals = General::where("general_id", "=", 1);
        echo $generals."\n";
        $this->assertCount(1, $generals);
    }



}