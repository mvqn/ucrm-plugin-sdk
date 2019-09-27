<?php /** @noinspection PhpUnusedLocalVariableInspection, DuplicatedCode */
declare(strict_types=1);

namespace MVQN\UNMS\Data;

use MVQN\Data\Database;
use MVQN\Data\Exceptions\DatabaseConnectionException;
use UCRM\Common\Plugin;

use PDO;
use PDOException;



final class UnmsDB
{
    /** @var PDO|null */
    private static $pdo = null;

    public static function connect(): ?PDO
    {
        if (self::$pdo)
            return self::$pdo;

        if($parameters = Plugin::parameters())
        {
            $driver  /* =   $parameters["database_driver"]; */      =   "pgsql";
            $host       =   $parameters["database_host"]            ?:  "unms-postgres";
            $port       =   $parameters["database_port"]            ?:  "5432";
            $name       =   $parameters["database_name"]            ?:  "unms";
            $user       =   $parameters["database_user"]            ?:  "ucrm";
            $password   =   $parameters["database_password"];
            $ucrmSchema =   $parameters["database_schema_ucrm"]     ?:  "ucrm";
            $unmsSchema =   $parameters["database_schema_unms"]     ?:  "unms";

            /*
            $dsn = "$driver:host=$host;port=$port;dbname=$name";

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            */

            try
            {
                //self::$pdo = new PDO($dsn, $user, $password, $options);
                self::$pdo = Database::connect($host, (int)$port, $name, $user, $password);
                self::$pdo->exec("SET search_path TO $unmsSchema");

                return self::$pdo;
            }
            catch (DatabaseConnectionException $e)
            {
                //self::$pdo = null;
                //return null;
            }
        }

        self::$pdo = null;
        return null;
    }


    public static function query(string $query): ?array
    {
        if(!self::connect())
            return null;

        return self::$pdo->query($query)->fetchAll();
    }







}