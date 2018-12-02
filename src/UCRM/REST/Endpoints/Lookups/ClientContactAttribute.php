<?php
declare(strict_types=1);

namespace UCRM\REST\Endpoints\Lookups;

use MVQN\REST\Annotations\PostAnnotation as Post;
use MVQN\REST\Annotations\PatchAnnotation as Patch;

use UCRM\REST\Endpoints\Helpers\Common\ClientHelpers;

/**
 * Class ClientContactAttribute
 *
 * @package UCRM\REST\Endpoints
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 * @final
 *
 * @method int|null getId()
 * @method ClientContactAttribute setId(int $id)
 * @method int|null getClientId()
 * @method ClientContactAttribute setClientId(int $id)
 * @method string|null getName()
 * @method ClientContactAttribute setName(string $name)
 * @method string|null getKey()
 * @method ClientContactAttribute setKey(string $key)
 * @method string|null getValue()
 * @method ClientContactAttribute setValue(string $value)
 * @method int|null getCustomAttributeId()
 * @method ClientContactAttribute setCustomAttributeId(int $id)
 *
 */
final class ClientContactAttribute extends Lookup
{
    use ClientHelpers;

    // =================================================================================================================
    // PROPERTIES
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     */
    protected $clientId;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     * @Post
     * @Patch
     */
    protected $value;

    /**
     * @var int
     * @Post
     * @Patch
     */
    protected $customAttributeId;

}
