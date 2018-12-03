<?php
declare(strict_types=1);

namespace UCRM\Data\Models;

use MVQN\Data\Database;

/**
 * Class DatabaseTests
 *
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 */
class DatabaseTests extends \PHPUnit\Framework\TestCase
{
    protected function setUp()
    {
        $env = new \Dotenv\Dotenv(__DIR__ . "/../../../");
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

    public function testInsertPlugin()
    {
        $pluginData =
            [
                "name" => "test",
                "display_name" => "Test Plugin",
                "description" => "This is a test plugin.",
                "url" => "https://github.com/ucrm-plugins/test",
                "author" => "Ryan Spaeth <rspaeth@mvqn.net>",
                "version" => "1.0.0",
                "min_ucrm_version" => "2.14.1",
                "max_ucrm_version" => null,
                "enabled" => false,
                "execution_period" => null,
            ];

        $plugin = new Plugin($pluginData);
        $inserted = $plugin->insert();

        var_dump($inserted);

    }


    public function testInsertAppKey()
    {
        $appKey = (new AppKey())
            ->setName("test")
            ->setKey(base64_encode(random_bytes(48)))
            ->setType("TYPE_WRITE")
            ->setCreatedDate(new \DateTime())
            //->setLastUsedDate(null)
            ->setPluginId(77);
            //->setDeletedAt(null);

        /** @var AppKey $inserted */
        $inserted = $appKey->insert();

        var_dump($inserted);

        var_dump($inserted->getCreatedDate());

    }

    public function testUpdateAppKey()
    {
        /** @var AppKey $appKey */
        $appKey = AppKey::where("key_id", "=", 52)->first();

        $appKey->setType("TYPE_WRITE");


        /** @var AppKey $updated */
        $updated = $appKey->update();
        var_dump($updated);
    }



}