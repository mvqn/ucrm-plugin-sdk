<?php
declare(strict_types=1);

namespace UCRM\REST\Endpoints\Lookups;

use MVQN\REST\Annotations\PostAnnotation as Post;
use MVQN\REST\Annotations\PatchAnnotation as Patch;

use UCRM\REST\Endpoints\Helpers\Common;

/**
 * Class PaymentCover
 *
 * @package UCRM\REST\Endpoints
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 * @final
 *
 * @method int|null getId()
 * @method PaymentCover setId(int $id)
 * @method int|null getPaymentId()
 * @method PaymentCover setPaymentId(int $id)
 * @method int|null getInvoiceId()
 * @method PaymentCover setInvoiceId(int $id)
 * @method int|null getRefundId()
 * @method PaymentCover setRefundId(int $id)
 * @method float|null getAmount()
 * @method PaymentCover setAmount(float $amount)
 *
 */
final class PaymentCover extends Lookup
{
    use Common\InvoiceHelpers;
    use Common\PaymentHelpers;
    use Common\RefundHelpers;

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
    protected $paymentId;

    /**
     * @var int
     */
    protected $invoiceId;

    /**
     * @var int
     */
    protected $refundId;

    /**
     * @var float
     */
    protected $amount;

}
