<?php
declare(strict_types=1);

namespace UCRM\REST\Endpoints\Helpers;

use MVQN\Collections\Collection;
use UCRM\REST\Endpoints\Invoice;
use UCRM\REST\Endpoints\Payment;

/**
 * Trait PaymentHelper
 *
 * @package UCRM\REST\Endpoints\Helpers
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 */
trait PaymentHelper
{
    use Common\CurrencyHelpers;

    // =================================================================================================================
    // OBJECT METHODS
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return Invoice|null
     * @throws \Exception
     */
    public function getInvoice(): ?Invoice
    {
        $invoice = null;

        if($this->invoiceId !== null)
            $invoice = Invoice::getById($this->invoiceId);

        /** @var Invoice $invoice */
        return $invoice;
    }

    /**
     * @param Invoice $invoice
     * @return Payment
     */
    public function setInvoice(Invoice $invoice): Payment
    {
        $this->applyToInvoicesAutomatically = false;
        $this->invoiceId = $invoice->getId();
        /** @var Payment $this */
        return $this;
    }

    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return Collection
     * @throws \Exception
     */
    public function getInvoices(): Collection
    {
        if($this->invoiceIds !== null && $this->invoiceIds !== [])
        {
            $allInvoices = Invoice::get();
            $invoices = [];

            foreach($this->invoiceIds as $id)
                $invoices = $allInvoices->where("id", $id);

            return new Collection(Invoice::class, $invoices);
        }

        return new Collection(Invoice::class);
    }

    /**
     * @param Collection $invoices
     * @return Payment
     */
    public function setInvoices(Collection $invoices): Payment
    {
        if($invoices->type() === Invoice::class && $invoices->count() > 0)
        {
            /** @var Invoice $invoice */

            $ids = [];

            foreach($invoices as $invoice)
                $ids[] = $invoice->getId();

            $this->applyToInvoicesAutomatically = false;
            $this->invoiceIds = $ids;

            /** @var Payment $this */
            return $this;
        }

        /** @var Payment $this */
        return $this;
    }

    // =================================================================================================================
    // CREATE METHODS
    // -----------------------------------------------------------------------------------------------------------------

    // =================================================================================================================
    // READ METHODS
    // -----------------------------------------------------------------------------------------------------------------

    // =================================================================================================================
    // UPDATE METHODS
    // -----------------------------------------------------------------------------------------------------------------

    // =================================================================================================================
    // DELETE METHODS
    // -----------------------------------------------------------------------------------------------------------------

    // =================================================================================================================
    // EXTRA FUNCTIONS
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return Payment
     * @throws \Exception
     */
    public function sendReceipt(): Payment
    {
        /** @var Payment $payment */
        $payment = Payment::patch(null, [ "id" => $this->getId() ], "/send-receipt");
        return $payment;
    }

}