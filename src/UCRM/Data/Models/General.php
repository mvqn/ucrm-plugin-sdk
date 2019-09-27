<?php
declare(strict_types=1);

namespace UCRM\Data\Models;

use MVQN\Data\Models\Model;
use MVQN\Data\Annotations\TableNameAnnotation as TableName;
use MVQN\Data\Annotations\ColumnNameAnnotation as ColumnName;

/**
 * Class General
 *
 * @package UCRM\UNMS
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 * @final
 *
 * @TableName ucrm.general
 *
 * @method int|null getGeneralId()
 * @method string|null getCode()
 * @method string|null getValue()
 */
final class General extends Model
{
    /**
     * @var int
     * @ColumnName general_id
     */
    protected $generalId;

    /**
     * @var string
     */
    protected $code;

    /**
     * @var string
     */
    protected $value;

}