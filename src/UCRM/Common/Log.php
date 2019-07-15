<?php
declare(strict_types=1);

namespace UCRM\Common;

use Monolog\Handler\AbstractProcessingHandler;
use UCRM\MonoLog\Handlers\Sqlite3Handler;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\IntrospectionProcessor;
use Monolog\Processor\WebProcessor;
use MVQN\Collections\Collection;
use \Exception;
use SebastianBergmann\Diff\Line;

/**
 * Class Log
 *
 * @package MVQN\UCRM\Plugins
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 * @final
 */
final class Log
{
    // =================================================================================================================
    // CONSTANTS
    // -----------------------------------------------------------------------------------------------------------------

    /** @const int The options to be used when json_encode() is called. */
    private const DEFAULT_JSON_OPTIONS = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;


    private const DEFAULT_TIMESTAMP_FORMAT = "Y-m-d H:i:s.uP";
    private const DEFAULT_ROW_ENTRY_FORMAT = "[%datetime%] [%level_name%] %message% %context% %extra%\n";
    private const CHANNEL_ROW_ENTRY_FORMAT = "[%datetime%] [%channel%.%level_name%] %message% %context% %extra%\n";

    public const UCRM = "UCRM";
    public const HTTP = "HTTP";
    public const REST = "REST";
    public const DATA = "DATA";

    // =================================================================================================================
    // FILE-BASED LOGGING
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Provides the path to the current log file.
     *
     * @return string Returns the absolute path to the 'data/plugin.log' file and creates it if missing.
     * @throws Exceptions\PluginNotInitializedException
     */
    public static function pluginFile(): string
    {
        // Get the absolute path to the data folder, creating the folder as needed.
        $path = Plugin::getDataPath()."/plugin.log";

        // IF the current log file does not exist...
        if(!file_exists($path))
        {
            // THEN create it and append a notification.
            file_put_contents($path, "");
            self::info("Log file created!");
        }

        // Return the absolute path to the current log file.
        return realpath($path) ?: $path;
    }

    // =================================================================================================================
    // SQL-BASED LOGGING
    // -----------------------------------------------------------------------------------------------------------------

    private static $_loggers = [];

    private static function getLoggers(): array
    {
        if(self::$_loggers === [])
        {
            self::$_loggers = [
                self::UCRM => self::addUcrmFileLogger("UCRM"), // plugin.log
                self::HTTP => self::addDatabaseLogger("HTTP"),
                self::REST => self::addDatabaseLogger("REST"),
                self::DATA => self::addDatabaseLogger("DATA"),
            ];
        }

        return self::$_loggers;
    }

    public static function getLogger(string $name = self::UCRM): ?Logger
    {
        return array_key_exists($name, self::getLoggers()) ? self::$_loggers[$name] : null;
    }



    private static $_standardHandler = null;
    private static $_databaseHandler = null;
    private static $_ucrmFileHandler = null;

    private static function addDatabaseLogger(string $name = self::UCRM): Logger
    {


        // Instantiate a new logger.
        $logger = new Logger($name);

        // IF the current system is running the PHP Built-In Web Server (CLI)...
        if(PHP_SAPI === "cli-server")
        {
            // ...THEN setup the "stdout" StreamHandler.

            // NOTE: Here we configure a static instance of the StreamHandler, if one is not already instantiated!
            if(!self::$_standardHandler)
                self::$_standardHandler = (new StreamHandler("php://stdout"))
                    ->setFormatter(new LineFormatter(self::CHANNEL_ROW_ENTRY_FORMAT, self::DEFAULT_TIMESTAMP_FORMAT));

            // And add it as a secondary Handler.
            $logger->pushHandler(self::$_standardHandler);
        }

        // NOTE: Here we configure a static instance of the Sqlite3Handler, if one is not already instantiated!
        if(!self::$_databaseHandler)
            self::$_databaseHandler = new Sqlite3Handler(Plugin::getDataPath()."/plugin.db");

        // And add it as the primary Handler.
        $logger->pushHandler(self::$_databaseHandler);

        // NOTE: Add any additional Processors below, custom or otherwise...
        $logger->pushProcessor(new IntrospectionProcessor());
        $logger->pushProcessor(new WebProcessor());

        // Finally, return the newly added Logger!
        return $logger;
    }

    private static function addUcrmFileLogger(string $name = self::UCRM): Logger
    {
        // Instantiate a new logger.
        $logger = new Logger($name);

        // IF the current system is running the PHP Built-In Web Server (CLI)...
        if(PHP_SAPI === "cli-server")
        {
            // ...THEN setup the "stdout" StreamHandler.

            // NOTE: Here we configure a static instance of the StreamHandler, if one is not already instantiated!
            if(!self::$_standardHandler)
                self::$_standardHandler = (new StreamHandler("php://stdout"))
                    ->setFormatter(new LineFormatter(self::CHANNEL_ROW_ENTRY_FORMAT, self::DEFAULT_TIMESTAMP_FORMAT));

            // And add it as a secondary Handler.
            $logger->pushHandler(self::$_standardHandler);
        }

        // NOTE: Here we configure a static instance of the Streamhandler, if one is not already instantiated!
        if(!self::$_ucrmFileHandler)
            self::$_ucrmFileHandler = (new StreamHandler(Plugin::getDataPath()."/plugin.log"))
                ->setFormatter(new LineFormatter(self::DEFAULT_ROW_ENTRY_FORMAT, self::DEFAULT_TIMESTAMP_FORMAT));

        // And add it as the primary Handler.
        $logger->pushHandler(self::$_ucrmFileHandler);

        // NOTE: Add any additional Processors below, custom or otherwise...
        $logger->pushProcessor(new IntrospectionProcessor());
        $logger->pushProcessor(new WebProcessor());

        // Finally, return the newly added Logger!
        return $logger;
    }

    public static function addLogger(Logger $logger): Logger
    {
        // NOTE: Here I assume that the end-user wants to override the identically named Logger.

        // IF the Logger's name already exists in the current loggers, THEN remove it!
        if(array_key_exists($logger->getName(), self::getLoggers()))
            unset(self::$_loggers[$logger->getName()]);

        // And then add the new Logger to the current loggers.
        self::$_loggers[$logger->getName()] = $logger;

        // Finally, return the newly added Logger!
        return $logger;
    }

    // =================================================================================================================
    // WRITING
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @param AbstractProcessingHandler $handler
     * @param string $property
     *
     * @return mixed
     * @throws \ReflectionException
     */
    private static function getHandlerProperty(AbstractProcessingHandler $handler, string $property)
    {
        $reflection = new \ReflectionClass($handler);
        $prop = $reflection->getProperty($property);
        $prop->setAccessible(true);
        return $prop->getValue($handler);
    }

    /**
     * Clears the entries from all supported Loggers, matching the specified "channel".
     *
     * @param string $message An optional message to append to the cleared Logger.
     * @param string $name
     * @throws Exceptions\PluginNotInitializedException
     */
    public static function clear(string $message = "", string $name = self::UCRM): int
    {
        if(!($logger = self::getLogger($name)))
            return -1;

        $cleared = 0;

        foreach($logger->getHandlers() as $handler)
        {
            if(is_a($handler, StreamHandler::class) &&
                ($url = self::getHandlerProperty($handler, "url")) && $url !== "php://stdout")
            {
                $path = realpath($url);
                $lines = 0;

                if($path && is_file($path))
                {
                    $lines = count(explode("\n", file_get_contents($path)));
                    file_put_contents($path, "", LOCK_EX);
                }

                if(file_get_contents($path) === "")
                {
                    $cleared++;

                    if($message)
                        Log::info($message, $name, [ "cleared" => $lines ]);
                }

                continue;
            }

            if(is_a($handler, Sqlite3Handler::class))
            {
                $pdo = self::getHandlerProperty($handler, "pdo");

                $count = $pdo->exec("
                    DELETE FROM logs WHERE channel = '$name';                
                ");

                if($count > 0)
                {
                    $cleared++;

                    if($message)
                        Log::info($message, $name, [ "cleared" => $count ]);
                }

                continue;
            }

            // NOTE: Add any other types of clear() functionality for the other Handlers as needed!
            // ...
        }

        return $cleared;
    }





    /**
     * Writes a message to the current log file.
     *
     * @param string $message The message to be appended to the current log file.
     * @param string $severity An optional severity level to flag the log entry.
     * @return LogEntry Returns the logged entry.
     * @throws Exceptions\PluginNotInitializedException
     * @deprecated
     */
    public static function write(string $message, string $severity = LogEntry::SEVERITY_NONE): LogEntry
    {
        // Get the current log file's path.
        $logFile = self::logFile();

        //$message = str_replace("\n", "\n                             ", $message);
        $entry = new LogEntry(new \DateTimeImmutable(), $severity, $message);

        // Append the contents to the current log file, creating it as needed.
        file_put_contents($logFile, $entry, FILE_APPEND | LOCK_EX);

        // Return the exact representation of the line!
        return $entry;
    }

    /**
     * Writes a message to the current log file.
     *
     * @param array $array The array to write to the current log file.
     * @param string $severity An optional severity level to flag the log entry.
     * @param int $options An optional set of valid JSON_OPTIONS that should be used when encoding the array.
     * @return LogEntry Returns the logged entry.
     * @throws Exceptions\PluginNotInitializedException
     * @deprecated
     */
    public static function writeArray(array $array, string $severity = "",
        int $options = self::DEFAULT_JSON_OPTIONS): LogEntry
    {
        // JSON encode the array and then write it to the current log file.
        $text = json_encode($array, $options);
        return self::write($text, $severity);
    }

    /**
     * Writes a message to the current log file.
     *
     * @param \JsonSerializable $object The object (that implements JsonSerializable) to write to the current log file.
     * @param string $severity An optional severity level to flag the log entry.
     * @param int $options An optional set of valid JSON_OPTIONS that should be used when encoding the array.
     * @return LogEntry Returns the logged entry.
     * @throws Exceptions\PluginNotInitializedException
     * @deprecated
     */
    public static function writeObject(\JsonSerializable $object, string $severity = "",
        int $options = self::DEFAULT_JSON_OPTIONS): LogEntry
    {
        // JSON encode the object and then write it to the current log file.
        $text = json_encode($object, $options);
        return self::write($text, $severity);
    }

    /**
     * Writes a message to the current log file, automatically marking it as DEBUG.
     *
     * @param string $message The message to be appended to the current log file.
     * @return LogEntry Returns the logged entry.
     * @throws Exceptions\PluginNotInitializedException
     */
    public static function debug(string $message, string $log = self::UCRM, array $context = []): ?LogEntry
    {
        if(!($logger = self::getLogger($log)))
            return null;

        if(!($result = $logger->debug($message, $context)))
            return null;

        // TODO: Finish deprecating the old Log::debug().
        return null;
    }

    /**
     * Writes a message to the current log file, automatically marking it as INFO.
     *
     * @param string $message The message to be appended to the current log file.
     * @return LogEntry Returns the logged entry.
     * @throws Exceptions\PluginNotInitializedException
     */
    public static function info(string $message, string $log = self::UCRM, array $context = []): ?LogEntry
    {
        if(!($logger = self::getLogger($log)))
            return null;

        if(!($result = $logger->info($message, $context)))
            return null;

        // TODO: Finish deprecating the old Log::info().
        return null;
    }

    /**
     * Writes a message to the current log file, automatically marking it as WARNING.
     *
     * @param string $message The message to be appended to the current log file.
     * @return LogEntry Returns the logged entry.
     * @throws Exceptions\PluginNotInitializedException
     */
    public static function warning(string $message, string $log = self::UCRM): ?LogEntry
    {
        if(!($logger = self::getLogger($log)))
            return null;

        if(!($result = $logger->warning($message)))
            return null;

        // TODO: Finish deprecating the old Log::warning().
        return null;
    }

    /**
     * Writes a message to the current log file, automatically marking it as ERROR.
     *
     * @param string $message The message to be appended to the current log file.
     * @param string $exception An optional Exception that should be thrown when this error is logged, defaults to NONE.
     * @return LogEntry Returns the logged entry, when an Exception is not provided.
     * @throws Exceptions\PluginNotInitializedException
     */
    public static function error(string $message, string $log = self::UCRM, string $exception = ""): ?LogEntry
    {
        if(!($logger = self::getLogger($log)))
            return null;

        if(!($result = $logger->warning($message)))
            return null;

        // TODO: Finish deprecating the old Log::error().
        if($exception !== "" && is_subclass_of($exception, \Exception::class, true))
            throw new $exception($message);
        else
            return null;
    }


    // TODO: Fix this up to make it more useful!

    public static function http(string $message, string $log = self::UCRM, int $statusCode = 500, bool $die = true):
    ?LogEntry
    {
        if(!($logger = self::getLogger($log)))
            return null;

        if(!($result = $logger->alert($message)))
            return null;

        http_response_code($statusCode);

        // TODO: Finish deprecating the old Log::http().
        if($die)
            die($message);
        else
            return null;
    }

    // =================================================================================================================
    // VIEWING
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Reads the specified number of lines from a starting position in the current log file.
     *
     * @param int $start The line number (zero-based) from which to start returning lines, defaults to the file start.
     * @param int $count The number of lines from the start for which to return. 0 = All Lines (default)
     * @return Collection Returns the corresponding collection of LogEntry.
     * @throws Exceptions\PluginNotInitializedException
     * @throws Exceptions\RequiredFileNotFoundException
     */
    public static function lines(int $start = 0, int $count = 0): Collection
    {
        // Get the current log file's filename
        $logFile = self::logFile();

        // IF the file does NOT exist, THEN throw an Exception!
        if(!file_exists($logFile))
            throw new Exceptions\RequiredFileNotFoundException(
                "A plugin.log file could not be found at '".$logFile."'.");

        $lines = LogEntry::fromText(file_get_contents($logFile));

        // IF no valid LogEntry lines were found, THEN return NULL!
        if($lines === null || $lines->count() === 0)
            return new Collection(LogEntry::class);

        // IF the specified count is 0, THEN make the count from the start to the end of the file.
        if($count === 0)
            $count = count($lines) - $start;

        // IF the specified count is less than 0, THEN set the start and count to reflect a negative read.
        if($count < 0)
        {
            $start += $count;
            $count = -$count;
        }

        if($start < 0)
            $start = count($lines) + $start;

        // IF the specified start is less than 0 OR the start + count exceeds the amount of the lines, THEN return NULL!
        if($start < 0 || $start + $count > count($lines))
            return new Collection(LogEntry::class);

        // Get only the lines from the requested start through the count.
        $lines = $lines->slice($start, $count);

        return $lines;
    }

    /**
     * Reads the specified number of trailing lines from the current log file.
     *
     * @param int $tail The number of lines from the end of the file for which to return. 0 = All Lines (default)
     * @return Collection Returns the corresponding collection of LogEntry.
     * @throws Exceptions\PluginNotInitializedException
     * @throws Exceptions\RequiredFileNotFoundException
     */
    public static function tail(int $tail = 0): Collection
    {
        return self::lines(0, -$tail);
    }

    /**
     * Returns the specific line number from the current log file.
     *
     * @param int $number The line number (zero-based) of which to return from the log file.
     * @return LogEntry|null Returns the corresponding LogEntry, or NULL if not found!
     * @throws Exceptions\PluginNotInitializedException
     * @throws Exceptions\RequiredFileNotFoundException
     */
    public static function line(int $number): ?LogEntry
    {
        /** @var LogEntry $line */
        $line = self::lines($number, 1)->first();
        return $line;
    }


    public static function first(): ?LogEntry
    {
        /** @var LogEntry|null $first */
        $first = self::lines()->first();
        return $first;
    }

    public static function last(): ?LogEntry
    {
        /** @var LogEntry|null $last */
        $last = self::lines()->last();
        return $last;
    }


    // =================================================================================================================
    // HELPERS
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Provides a simple query to determine whether or not the current log file is empty.
     *
     * @return bool Returns TRUE if the current log file is empty, otherwise FALSE.
     * @throws Exceptions\PluginNotInitializedException
     */
    public static function isEmpty(): bool
    {
        // Get the current log file, but also make certain it exists.
        $logFile = self::logFile();

        // IF the file has NO content, THEN return TRUE, OTHERWISE return FALSE!
        return file_get_contents($logFile) === "";
    }

    // =================================================================================================================
    // SEARCHING
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * A helper function to return the specified date with the time set to 00:00:00.
     *
     * @param \DateTimeInterface $datetime The date/time to be modified, or NOW if none provided.
     * @return \DateTime Returns a date/time object with it's time truncated.
     * @throws \Exception
     */
    private static function dateOnly(\DateTimeInterface $datetime = null): \DateTime
    {
        if($datetime === null)
            $datetime = new \DateTime();

        return new \DateTime($datetime->format(self::TIMESTAMP_FORMAT_DATEONLY));
    }

    /**
     * Searches for log entries between the starting date/time (inclusive) and the ending date/time (exclusive).
     *
     * @param \DateTimeInterface $start A starting date/time for which to use when matching the earliest log entry.
     * @param \DateTimeInterface $end An ending date/time for which to use when matching the latest log entry.
     * @return array|null Returns a timestamp-indexed associative array of matching log entries.
     * @throws Exceptions\PluginNotInitializedException
     * @throws Exceptions\RequiredFileNotFoundException
     */
    public static function between(\DateTimeInterface $start, \DateTimeInterface $end = null): ?Collection
    {
        // IF no ending date/time is provided, THEN assume the current date/time.
        if($end === null)
            $end = new \DateTime();

        // Initialize an empty timestamp-indexed array to store the matching log lines.
        $matching = new Collection(LogEntry::class);

        // IF loading archived/rotated files for searching has been requested and a 'data/logs/' folder exists...
        if(file_exists(self::logsPath()))
        {
            // Set an inclusive starting date and and exclusive ending date based on the dates provided.
            $inclusiveStartDate = self::dateOnly($start);
            $exclusiveEndDate = self::dateOnly($end); //->add(new \DateInterval("P1D"));

            // Loop through each file and folder in the 'data/logs/' folder...
            foreach(scandir(self::logsPath()) as $filename)
            {
                // IF the current filename is a special file OR a directory/folder, THEN skip!
                if($filename === "." || $filename === ".." || is_dir($filename))
                    continue;

                // Generate the date/time associated with this file's name.
                $datetime = new \DateTime(str_replace(".log", "", $filename));

                // IF the filename's associated date/time is within the inclusive starting and exclusive ending dates...
                if($datetime >= $inclusiveStartDate && $datetime < $exclusiveEndDate)
                {
                    $matching = $matching->merge(self::load($datetime)->find(
                        function(LogEntry $entry) use ($start, $end)
                        {
                            $lineTimeStamp = $entry->getTimestamp();
                            return ($lineTimeStamp >= $start && $lineTimeStamp < $end);
                        }
                    ));
                }
            }
        }

        // IF the current log file is NOT empty...
        if(!self::isEmpty())
        {
            // THEN search through it for matching log entries as well!
            // NOTE: This section is necessary, in the cases where log rotation has not been used!

            $matching = $matching->merge(self::lines()->find(
                function(LogEntry $entry) use ($start, $end)
                {
                    $lineTimeStamp = $entry->getTimestamp();
                    return ($lineTimeStamp >= $start && $lineTimeStamp < $end);
                }
            ));
        }

        // Return all of the matching log entries!
        return $matching;
    }

    // =================================================================================================================
    // SAVING/LOADING
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Serialize a timestamp-indexed array of log lines into their textual equivalent.
     *
     * @param Collection $entries A set of timestamp-indexed log lines.
     * @return string|null
     */
    private static function serialize(Collection $entries): ?string
    {
        // Initialize an empty string builder to store the text lines for saving.
        $textLines = "";

        // IF the provided array is indexed and not an associative timestamp-indexed array, THEN return NULL!
        if($entries->type() !== LogEntry::class)
            return null;

        // Loop through each of the current date's log lines, convert them to text and append them to the builder.
        foreach($entries as $entry)
            $textLines .= $entry;

        // Return the textual log lines.
        return $textLines;
    }

    /**
     * Deserialize the textual log lines into their timestamp-indexed array equivalent.
     *
     * @param string $text A string containing log lines from this Plugin's log file.
     * @return Collection|null Returns an array of timestamp-indexed log lines, or NULL if none were found/matched.
     * @throws \Exception
     */
    private static function deserialize(string $text): ?Collection
    {
        $lines = LogEntry::fromText($text);

        // IF no valid LogEntry lines were found, THEN return NULL!
        if($lines === null || $lines->count() === 0)
            return null;

        return $lines;
    }

    /**
     * Saves the specified log lines to 'data/logs/<TIMESTAMP>.log', leaving the current day's logs intact.
     *
     * @param Collection $entries The timestamp-indexed log lines that should be saved.
     * @return int
     * @throws Exceptions\PluginNotInitializedException
     * @throws Exceptions\RequiredFileNotFoundException
     */
    private static function save(Collection $entries): int
    {
        // Initialize a file counter.
        $fileCount = 0;

        // IF the provided array is indexed and not an associative timestamp-indexed array, THEN return NULL!
        if($entries->type() !== LogEntry::class || $entries->count() === 0)
            return $fileCount;

        // Get the first entry from the current log file.
        /** @var LogEntry $first */
        $first = $entries->first();

        // Generate dates based on the first log line and today's date.
        $logFirstDate = self::dateOnly($first->getTimestamp());
        $today = self::dateOnly();

        // Create unreferenced copies of the first occurring log date, for use in the search loop.
        $currentDate = clone $logFirstDate;
        $currentNextDay = clone $logFirstDate;

        // Loop through each day from the starting date until today's date is reached...
        while($currentDate <= $today)
        {
            // Get all of the lines belonging to the current date.
            $currentLines = self::between($currentDate, $currentNextDay->add(new \DateInterval("P1D")));

            // IF the current day has at least one entry OR if it's today's date...
            if($currentLines->count() > 0 || $currentDate == $today)
            {
                // Generate the file path according to the date.
                $filePath = self::logsPath($currentDate);

                // Load any existing log files for the current date.
                //$loaded = ($currentDate != $today) ? self::load($currentDate) : [];

                // Serialize the log lines for writing to disk.
                $textLines = self::serialize($currentLines);

                // Save the textual logs to the corresponding day's log file or 'data/plugin.log' if they are from today.
                file_put_contents(($currentDate != $today) ? $filePath : self::logFile(), $textLines);

                // Increment the file counter of affected files.
                $fileCount++;
            }

            // Increment the current date for the loop's work.
            $currentDate = $currentDate->add(new \DateInterval("P1D"));
        }

        // Return the total number of affected files, excluding the current log file.
        return $fileCount - 1;
    }

    /**
     * Loads the log lines from the specified date.
     *
     * @param \DateTimeInterface $date The date for which to have log lines loaded.
     * @return Collection|null Returns an array of timestamp-indexed log lines, or NULL if none were found.
     * @throws Exceptions\PluginNotInitializedException
     */
    private static function load(\DateTimeInterface $date): ?Collection
    {
        // IF the date is from today...
        if($date->format(self::TIMESTAMP_FORMAT_DATEONLY) === (new \DateTime())->format(self::TIMESTAMP_FORMAT_DATEONLY))
            // THEN set the path to the 'data/plugin.log' file.
            $filePath = self::logFile();
        else
            // OTHERWISE set the path to the appropriate 'data/logs/<TIMESTAMP>.log' file.
            $filePath = self::logsPath($date);

        // IF the file exists, THEN deserialize it and return the timestamp-indexed array!
        if(file_exists($filePath))
            return self::deserialize(file_get_contents($filePath));

        // OTHERWISE, return NULL!
        return null;
    }

    /**
     * Archives the 'data/plugin.log' lines to 'data/logs/<TIMESTAMP>.log', leaving the current day's logs intact.
     *
     * @returns bool Returns TRUE if any files were created during the save, otherwise FALSE.
     * @throws Exceptions\PluginNotInitializedException
     * @throws Exceptions\RequiredFileNotFoundException
     */
    public static function rotate(): int
    {
        if(Log::isEmpty())
            return 0;

        return self::save(Log::lines());
    }

}

