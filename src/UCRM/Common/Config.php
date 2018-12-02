<?php
declare(strict_types=1);

namespace UCRM\Common;

use MVQN\Dynamics\AutoObject;

use MVQN\Data\Database;
use UCRM\Data\Models\Option;

/**
 * Class Config
 * @package MVQN\UCRM\Plugins
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 *
 *
 * @method static string|null getLanguage()
 * @method static string|null getSmtpTransport()
 * @method static string|null getSmtpUsername()
 * @method static string|null getSmtpPassword()
 * @method static string|null getSmtpHost()
 * @method static string|null getSmtpPort()
 * @method static string|null getSmtpEncryption()
 * @method static string|null getSmtpAuthentication()
 * @method static bool|null   getSmtpVerifySslCertificate()
 * @method static string|null getSmtpSenderEmail()
 * @method static string|null getGoogleApiKey()
 * @method static string|null getTimezone()
 * @method static string|null getServerIP()
 * @method static string|null getServerFQDN()
 * @method static int|null    getServerPort()
 */
final class Config extends AutoObject
{
    /** @var string */
    protected static $language;

    /** @var string */
    protected static $smtpTransport;

    /** @var string */
    protected static $smtpUsername;

    /** @var string */
    protected static $smtpPassword;

    /** @var string */
    protected static $smtpHost;

    /** @var string */
    protected static $smtpPort;

    /** @var string */
    protected static $smtpEncryption;

    /** @var string */
    protected static $smtpAuthentication;

    /** @var bool */
    protected static $smtpVerifySslCertificate;

    /** @var string */
    protected static $smtpSenderEmail;

    /** @var string */
    protected static $googleApiKey;

    /** @var string */
    protected static $timezone;

    /** @var string */
    protected static $serverIP;

    /** @var string */
    protected static $serverFQDN;

    /** @var int */
    protected static $serverPort;

    /**
     * Executes prior to the very fist static __call() method and used to initialize the properties of this class.
     *
     * @return bool
     * @throws Exceptions\PluginNotInitializedException
     * @throws \Defuse\Crypto\Exception\BadFormatException
     * @throws \Defuse\Crypto\Exception\EnvironmentIsBrokenException
     * @throws \MVQN\Data\Exceptions\DatabaseConnectionException
     * @throws \MVQN\Data\Exceptions\ModelClassException
     * @throws \ReflectionException
     */
    public static function __beforeFirstStaticCall(): bool
    {
        // =============================================================================================================
        // ENVIRONMENT
        // =============================================================================================================

        // Get the path to an optional .env file for development.
        //$envPath = realpath(__DIR__ . "/../../../../");
        $envPath = realpath(Plugin::getRootPath()."/../");

        // IF an .env file exists, THEN initialize environment variables from the .env file!
        if (file_exists($envPath . "/.env"))
            (new \Dotenv\Dotenv($envPath))->load();

        // =============================================================================================================
        // SETTINGS
        // =============================================================================================================

        // Initialize the Plugin libraries using the current directory as the root.
        //Plugin::initialize(__DIR__ . "/../../../");

        // Regenerate the Settings class, in case anything has changed in the manifest.json file.
        //Plugin::createSettings();

        // Create a database connection using environment variables, from either an .env file or the actual environment.
        // NOTE: These variables all exist on the production servers!
        Database::connect(
            getenv("POSTGRES_HOST"),
            (int)getenv("POSTGRES_PORT"),
            getenv("POSTGRES_DB"),
            getenv("POSTGRES_USER"),
            getenv("POSTGRES_PASSWORD")
        );

        // Generate the Cryptographic Key used by the Crypto library from the has already created by the UCRM server.
        // NOTE: The '../../encryption/crypto.key' file will not exist in development environments and the crypto hash
        // will need to be included in an .env file for decryption to work in development!
        $cryptoKey = Plugin::getCryptoKey() ?? \Defuse\Crypto\Key::loadFromAsciiSafeString(getenv("CRYPTO_KEY"));

        // Get a collection of all rows of the option table from the database!
        $options = Option::select();

        /** @var Option $option */

        // LANGUAGE/LOCALE
        $option = $options->where("code", "APP_LOCALE")->first();
        self::$language = $option->getValue();


        // SMTP TRANSPORT
        $option = $options->where("code", "MAILER_TRANSPORT")->first();
        self::$smtpTransport = $option->getValue();

        // SMTP USERNAME
        $option = $options->where("code", "MAILER_USERNAME")->first();
        self::$smtpUsername = $option->getValue();

        // SMTP PASSWORD
        $option = $options->where("code", "MAILER_PASSWORD")->first();
        self::$smtpPassword = $option->getValue() !== "" ? Plugin::decrypt($option->getValue(), $cryptoKey) : null;

        if (self::$smtpPassword === null || self::$smtpPassword === "")
            Log::error("SMTP Password could not be determined by UCRM Settings!", \Exception::class);

        // SMTP HOST
        $option = $options->where("code", "MAILER_HOST")->first();
        self::$smtpHost = self::$smtpTransport === "gmail" ? "smtp.gmail.com" : $option->getValue();

        // SMTP PORT
        $option = $options->where("code", "MAILER_PORT")->first();
        self::$smtpPort = self::$smtpTransport === "gmail" ? "587" : $option->getValue();

        // SMTP ENCRYPTION
        $option = $options->where("code", "MAILER_ENCRYPTION")->first();
        // None = "", SSL = "ssl", TLS = "tls"
        self::$smtpEncryption = self::$smtpTransport === "gmail" ? "tls" : $option->getValue();

        // SMTP AUTHENTICATION ( None = "", Plain = "plain", Login = "login", CRAM-MD5 = "cram-md5" )
        $option = $options->where("code", "MAILER_AUTH_MODE")->first();
        self::$smtpAuthentication = self::$smtpTransport === "gmail" ? "login" : $option->getValue(); //

        // SMTP VERIFY SSL CERTIFICATE?
        $option = $options->where("code", "MAILER_VERIFY_SSL_CERTIFICATES")->first();
        self::$smtpVerifySslCertificate = (bool)$option->getValue();

        // SMTP SENDER ADDRESS
        $option = $options->where("code", "MAILER_SENDER_ADDRESS")->first();
        self::$smtpSenderEmail = $option->getValue();

        // GOOGLE API KEY
        $option = $options->where("code", "GOOGLE_API_KEY")->first();
        self::$googleApiKey = $option->getValue();

        // TIMEZONE
        $option = $options->where("code", "APP_TIMEZONE")->first();
        self::$timezone = $option->getValue();

        // SERVER IP
        $option = $options->where("code", "SERVER_IP")->first();
        self::$serverIP = $option->getValue();

        // SERVER FQDN
        $option = $options->where("code", "SERVER_FQDN")->first();
        self::$serverFQDN = $option->getValue();

        // SERVER PORT
        $option = $options->where("code", "SERVER_PORT")->first();
        self::$serverPort = (int)$option->getValue();

        $properties = get_class_vars(Config::class);
        $properties["smtpPassword"] = str_repeat("*", strlen($properties["smtpPassword"]));

        if(class_exists("\\UCRM\\Plugins\\Settings") && call_user_func("\\UCRM\\Plugins\\Settings::getVerboseLogging"))
            Log::info("CONFIGURATION: ".json_encode($properties, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

        return true;
    }

}