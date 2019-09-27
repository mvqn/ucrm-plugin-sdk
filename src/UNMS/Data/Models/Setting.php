<?php
declare(strict_types=1);

namespace MVQN\UNMS\Data\Models;

use MVQN\Data\Models\Model;
use MVQN\Data\Annotations\TableNameAnnotation as TableName;
use MVQN\Data\Annotations\ColumnNameAnnotation as ColumnName;

/**
 * Class Option
 *
 * @package MVQN\UNMS
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 * @final
 *
 * @TableName unms.setting
 *
 * @method string|null getName()
 * @method string|null getValue()
 */
final class Setting extends Model
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $value;

}