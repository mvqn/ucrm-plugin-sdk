<?php
declare(strict_types=1);

namespace UCRM\REST\Endpoints\Lookups;

/**
 * Class ClientBankAccount
 *
 * @package UCRM\REST\Endpoints
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 * @final
 *
 * @method string|null getAccountNumber()
 * @method ClientBankAccount setAccountNumber(string $number)
 *
 */
final class ClientBankAccount extends Lookup
{
    // =================================================================================================================
    // PROPERTIES
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @var string
     */
    protected $accountNumber;
}
