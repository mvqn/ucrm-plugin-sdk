<?php
declare(strict_types=1);

namespace UCRM\REST\Endpoints\Helpers;

use MVQN\Collections\Collection;
use UCRM\REST\Endpoints\Invoice;
use UCRM\REST\Endpoints\Lookups\InvoiceItem;

/**
 * Trait InvoiceHelper
 * @package UCRM\REST\Endpoints\Helpers
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 */
trait InvoiceHelper
{
    use Common\ClientHelpers;
    use Common\ClientCountryHelpers;
    use Common\ClientStateHelpers;
    use Common\InvoiceTemplateHelpers;
    use Common\OrganizationCountryHelpers;
    use Common\OrganizationStateHelpers;

    // =================================================================================================================
    // OBJECT METHODS
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @param InvoiceItem $item
     * @return Invoice
     *
     * @deprecated Not implemented!
     */
    public function addInvoiceItem(InvoiceItem $item): Invoice
    {
        echo "Invoice::addInvoiceItem($item) is not currently implemented!\n";

        // TODO: Implement when available!

        /** @var Invoice $this */
        return $this;
    }

    /**
     * @param int $line
     * @return Invoice
     *
     * @deprecated Not implemented!
     */
    public function delInvoiceItem(int $line): Invoice
    {
        echo "Invoice::delInvoiceItem($line) is not currently implemented!\n";

        // TODO: Implement when available!

        /** @var Invoice $this */
        return $this;
    }

    // =================================================================================================================
    // CREATE METHODS
    // -----------------------------------------------------------------------------------------------------------------

    // =================================================================================================================
    // READ METHODS
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @param int $clientId The Client ID for which to match Invoices.
     * @return Collection Returns a collection of Invoices that belong to the specified Client ID.
     * @throws \Exception
     */
    public static function getByClientId(int $clientId): Collection
    {
        return Invoice::get("", [], [ "clientId" => $clientId ]);
    }

    /**
     * @param \DateTime $date The Creation Date for which to match Invoices.
     * @return Collection Returns a collection of Invoices created on the specified date.
     * @throws \Exception
     */
    public static function getByCreatedDate(\DateTime $date): Collection
    {
        return Invoice::get("", [], [ "createdDateFrom" => $date->format("Y-m-d"),
            "createdDateTo" => $date->format("Y-m-d") ]);
    }

    /**
     * @param \DateTime $from The Creation Date from which to start matching Invoices.
     * @param \DateTime $to The Creation Date from which to stop matching Invoices.
     * @return Collection Returns a collection of Invoices created between the specified dates.
     * @throws \Exception
     */
    public static function getByCreatedDateBetween(\DateTime $from, \DateTime $to): Collection
    {
        return Invoice::get("", [], [ "createdDateFrom" => $from->format("Y-m-d"),
            "createdDateTo" => $to->format("Y-m-d") ]);
    }

    /**
     * @param int ...$statuses A list of possible statuses for which to match Invoices.
     * @return Collection Returns a collection of Invoices that have any of the statuses specified.
     * @throws \Exception
     */
    public static function getByStatuses(int ...$statuses): Collection
    {
        // EXAMPLE: ...?statuses[]=1&statuses[]=3
        $statusesString = implode("&statuses[]=", $statuses);

        return Invoice::get("", [], [ "statuses[]" => $statusesString ]);
    }

    /**
     * @param string $number The Number for which to match an Invoice.
     * @return Invoice|null Returns an Invoice that matches the specified number, or NULL if no matches.
     * @throws \Exception
     */
    public static function getByNumber(string $number): ?Invoice
    {
        // EXAMPLE: ...?number=000003 - MUST MATCH EXACTLY, as it is a string!

        /** @var Invoice $invoice */
        $invoice = Invoice::get("", [], [ "number" => $number ])->first();
        return $invoice;
    }

    /**
     * @param bool $overdue If TRUE, matches all overdue Invoices; FALSE, matches all non-overdue Invoices.
     * @return Collection Returns a collection of Invoices that are flagged with the specified overdue status.
     * @throws \Exception
     */
    public static function getByOverdue(bool $overdue): Collection
    {
        // EXAMPLE: ...?overdue=0|1
        return Invoice::get("", [], [ "overdue" => $overdue ? 1 : 0 ]);
    }

    // =================================================================================================================
    // UPDATE METHODS
    // -----------------------------------------------------------------------------------------------------------------

    // STANDARD UPDATE METHODS USED

    // =================================================================================================================
    // DELETE METHODS
    // -----------------------------------------------------------------------------------------------------------------

    // NO DELETE ENDPOINTS

    // =================================================================================================================
    // EXTRA FUNCTIONS
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return Invoice
     * @throws \Exception
     */
    public function send(): Invoice
    {
        /** @var Invoice $invoice */
        $invoice = Invoice::patch(null, [ "id" => $this->getId() ], "/send");
        return $invoice;
    }

}