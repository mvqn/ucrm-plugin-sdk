<?php
declare(strict_types=1);

namespace UCRM\REST\Endpoints\Lookups;

use MVQN\REST\Annotations\PostAnnotation as Post;
use MVQN\REST\Annotations\PatchAnnotation as Patch;

use UCRM\REST\Endpoints\Helpers\QuoteItemHelper;

/**
 * Class QuoteItem
 *
 * @package UCRM\REST\Endpoints
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 * @final
 *
 * @method int|null getId()
 * @method QuoteItem setId(int $id)
 * @method string|null getLabel()
 * @method QuoteItem setLabel(string $label)
 * @method float|null getPrice()
 * @method QuoteItem setPrice(float $price)
 * @method float|null getQuantity()
 * @method QuoteItem setQuantity(float $quantity)
 * @method string|null getUnit()
 * @method QuoteItem setUnit(string $unit)
 * @method int|null getTax1Id()
 * @method QuoteItem setTax1Id(int $id)
 * @method int|null getTax2Id()
 * @method QuoteItem setTax2Id(int $id)
 * @method int|null getTax3Id()
 * @method QuoteItem setTax3Id(int $id)
 *
 */
final class QuoteItem extends Lookup
{
    use QuoteItemHelper;

    // =================================================================================================================
    // PROPERTIES
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     * @Patch
     */
    protected $label;

    /**
     * @var float
     * @Patch
     */
    protected $price;

    /**
     * @var float
     * @Patch
     */
    protected $quantity;

    /**
     * @var string
     * @Patch
     */
    protected $unit;

    /**
     * @var int
     * @Patch
     */
    protected $tax1Id;

    /**
     * @var int
     * @Patch
     */
    protected $tax2Id;

    /**
     * @var int
     * @Patch
     */
    protected $tax3Id;

}
