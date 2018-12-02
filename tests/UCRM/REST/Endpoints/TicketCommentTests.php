<?php
declare(strict_types=1);

namespace UCRM\REST\Endpoints;

use MVQN\REST\RestClient;
use UCRM\REST\Endpoints\Lookups\TicketActivityComment;
use UCRM\REST\Endpoints\Lookups\TicketActivityCommentAttachment;

require_once __DIR__ . "/_TestFunctions.php";

class TicketCommentTests extends \PHPUnit\Framework\TestCase
{

    // =================================================================================================================
    // INITIALIZATION
    // -----------------------------------------------------------------------------------------------------------------

    /** @var string Location of the .env file for development. */
    protected const DOTENV_PATH = __DIR__ . "/../../rest/";

    protected function setUp()
    {
        // Load ENV variables from a file during development.
        if(file_exists(self::DOTENV_PATH))
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



    // =================================================================================================================
    // GETTERS & SETTERS
    // -----------------------------------------------------------------------------------------------------------------

    public function testAllGetters()
    {
        /** @var Ticket $first */
        $first = TicketComment::get()->first();

        $test = _TestFunctions::testAllGetters($first);
        $this->assertTrue($test);
    }

    // =================================================================================================================
    // CREATE
    // -----------------------------------------------------------------------------------------------------------------

    public function testInsert()
    {
        $user = User::getById(1);

        $comment = (new TicketComment())
            ->setUserId($user->getId())
            ->setPublic(false)
            ->setCreatedAt(new \DateTime())
            ->setTicketId(1)
            ->setBody("This is a test body!");

        if($comment->validate("post", $missing, $ignored))
        {
            echo "MISSING: ";
            print_r($missing);
            echo "\n";

            echo "IGNORED: ";
            print_r($ignored);
            echo "\n";
        }

        $test = $comment->toJSON("post");

        /** @var Ticket $inserted */
        $inserted = $comment->insert();
        //$this->assertEquals($name, $inserted->getName());

        echo $inserted."\n";
    }

    // =================================================================================================================
    // READ
    // -----------------------------------------------------------------------------------------------------------------

    public function testGet()
    {
        $activity = TicketComment::get();
        $this->assertNotNull($activity);

        echo ">>> TicketComment::get()\n";
        echo $activity."\n";
        echo "\n";
    }

    // -----------------------------------------------------------------------------------------------------------------

    public function testGetById()
    {
        /** @var Ticket $first */
        $first = Ticket::get()->first();
        $id = $first->getId();

        /** @var Ticket $ticket */
        $ticket = TicketComment::getById(11);
        //$this->assertEquals($id, $ticket->getId());

        echo ">>> TicketComment::getById(11)\n";
        echo $ticket."\n";
        echo "\n";
    }


    public function testDownload()
    {
        //$data = TicketCommentAttachment::download(2);
        //file_put_contents(__DIR__."/downloads/plugin-notifier.zip", $data);
    }


    // =================================================================================================================
    // UPDATE
    // -----------------------------------------------------------------------------------------------------------------

    // NO PATCH ENDPOINT

    // =================================================================================================================
    // DELETE
    // -----------------------------------------------------------------------------------------------------------------

    // NO DELETE ENDPOINT



    // =================================================================================================================
    // OTHER TESTS
    // -----------------------------------------------------------------------------------------------------------------

    // ...
}
