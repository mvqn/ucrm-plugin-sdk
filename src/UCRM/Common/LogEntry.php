<?php
declare(strict_types=1);

namespace UCRM\Common;

use MVQN\Collections\Collection;
use MVQN\Dynamics\AutoObject;

/**
 * Class LogEntry
 *
 * @package MVQN\UCRM\Plugins
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 *
 * @method \DateTimeImmutable getTimestamp()
 * @method string getSeverity()
 * @method string getText()
 */
class LogEntry extends AutoObject
{
    public const REGEX_TEXT = '/^\[([\w|\-]* [\w|\:|\.\+]*)](?: \[(\w*)\])? (.*)$/m';

    /** @const string The format to be used as the timestamp. */
    public const TIMESTAMP_FORMAT_DATETIME = "Y-m-d H:i:s.uP";

    public const SEVERITY_NONE      = "";
    public const SEVERITY_INFO      = "INFO";
    public const SEVERITY_DEBUG     = "DEBUG";
    public const SEVERITY_WARNING   = "WARNING";
    public const SEVERITY_ERROR     = "ERROR";



    /**
     * @var \DateTimeImmutable|null
     */
    protected $timestamp;

    /**
     * Gets the LogEntry's timestamp represented in the specified timezone.
     *
     * @param string $timezone A PHP supported timezone in the typical format "Country/Region", defaults to "UTC".
     *
     * @return \DateTimeImmutable Returns the local timestamp.
     * @throws \Exception
     */
    public function getTimestampLocal(string $timezone = ""): \DateTimeImmutable
    {
        $timezone = $timezone ?: Config::getTimezone() ?: "UTC";
        return $this->timestamp->setTimezone(new \DateTimeZone($timezone));
    }

    /**
     * @var string|null
     */
    protected $severity;

    /**
     * @var string|null
     */
    protected $text;


    public function __construct(\DateTimeImmutable $timestamp, string $severity, string $text)
    {
        $this->timestamp = $timestamp;
        $this->severity = $severity;
        $this->text = $text;
    }

    /**
     * @param string $text
     * @return Collection|null
     * @throws \Exception
     */
    public static function fromText(string $text): ?Collection
    {
        // Match the text against the RegEx pattern.
        preg_match_all(self::REGEX_TEXT, $text, $matches);

        // Remove the full matches array.
        array_shift($matches);

        if(count($matches) !== 3 || count($matches[0]) === 0)
            return null;

        $collection = new Collection(LogEntry::class);

        // Trim away any extra whitespace from the text part of the entry.
        foreach(range(0, count($matches[0]) - 1) as $index)
        {
            $logEntry = new LogEntry(
                new \DateTimeImmutable(trim($matches[0][$index])),
                trim($matches[1][$index]),
                trim($matches[2][$index])
            );

            $collection->push($logEntry);
        }

        return $collection;
    }

    public function __toString()
    {
        if($this->timestamp === null || $this->severity === null || $this->text === null)
            return "";

        return
            "[{$this->timestamp->format(self::TIMESTAMP_FORMAT_DATETIME)}] ".
            ($this->severity !== "" ? "[{$this->severity}] " : "").
            str_replace("\n", "\n                             ", $this->text).
            PHP_EOL;
    }


}
