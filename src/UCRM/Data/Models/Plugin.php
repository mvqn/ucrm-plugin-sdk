<?php
declare(strict_types=1);

namespace UCRM\Data\Models;

use MVQN\Data\Models\Model;
use MVQN\Data\Annotations\TableNameAnnotation as TableName;
use MVQN\Data\Annotations\ColumnNameAnnotation as ColumnName;

/**
 * Class Plugin
 *
 * @package UCRM\UNMS
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 * @final
 *
 * @TableName plugin
 *
 * @method int|null         getId()
 * @method string|null      getName()
 * @method Plugin           setName(string $name)
 * @method string|null      getDisplayName()
 * @method Plugin           setDisplayName(string $displayName)
 * @method string|null      getDescription()
 * @method Plugin           setDescription(string $description)
 * @method bool|null        getUrl()
 * @method Plugin           setUrl(string $url)
 * @method string|null      getAuthor()
 * @method Plugin           setAuthor(string $author)
 * @method string|null      getVersion()
 * @method Plugin           setVersion(string $name)
 * @method string|null      getMinUcrmVersion()
 * @method Plugin           setMinUcrmVersion(string $minVersion)
 * @method string|null      getMaxUcrmVersion()
 * @method Plugin           setMaxUcrmVersion(string|null $maxVersion)
 * @method bool|null        getEnabled()
 * @method Plugin           setEnabled(bool $enabled)
 * @method string|null      getExecutionPeriod()
 * @method Plugin           setExecutionPeriod(string|null $executionPeriod)
 */
final class Plugin extends Model
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     * @ColumnName display_name
     */
    protected $displayName;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $author;

    /**
     * @var string
     */
    protected $version;

    /**
     * @var string
     * @ColumnName min_ucrm_version
     */
    protected $minUcrmVersion;

    /**
     * @var string
     * @ColumnName max_ucrm_version
     */
    protected $maxUcrmVersion;

    /**
     * @var bool
     */
    protected $enabled;

    /**
     * @var string
     * @ColumnName execution_period
     */
    protected $executionPeriod;

}