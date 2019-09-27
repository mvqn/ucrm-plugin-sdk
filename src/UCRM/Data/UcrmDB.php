<?php /** @noinspection PhpUnusedLocalVariableInspection, DuplicatedCode */
declare(strict_types=1);

namespace UCRM\Data;

use PDO;
use PDOException;
use Symfony\Component\Yaml\Yaml;
use UCRM\Common\Plugin;

final class UcrmDB
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

            $dsn = "$driver:host=$host;port=$port;dbname=$name";

            try
            {
                self::$pdo = new PDO($dsn, $user, $password);
                self::$pdo->exec("SET search_path TO $ucrmSchema");
                return self::$pdo;
            }
            catch (PDOException $e)
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