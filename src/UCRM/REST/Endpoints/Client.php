<?php
declare(strict_types=1);

namespace UCRM\REST\Endpoints;

use MVQN\Collections\Collection;
use MVQN\REST\Endpoints\EndpointObject;
use MVQN\REST\Annotations\EndpointAnnotation as Endpoint;
use MVQN\REST\Annotations\PostAnnotation as Post;
use MVQN\REST\Annotations\PostRequiredAnnotation as PostRequired;
use MVQN\REST\Annotations\PatchAnnotation as Patch;
use MVQN\REST\Annotations\PatchRequiredAnnotation as PatchRequired;
use MVQN\REST\Annotations\KeepNullAnnotation as KeepNull;


use UCRM\REST\Endpoints\ClientContact;
use UCRM\REST\Endpoints\Lookups\ClientBankAccount;
use UCRM\REST\Endpoints\Lookups\ClientContactAttribute;
use UCRM\REST\Endpoints\Lookups\ClientTag;

/**
 * Class Client
 *
 * @package UCRM\REST\Endpoints
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 * @final
 *
 * @Endpoint { "get": "/clients", "getById": "/clients/:id" }
 * @Endpoint { "post": "/clients" }
 * @Endpoint { "patch": "/clients/:id" }
 * @Endpoint { "delete": "/clients/:id" }
 *
 * @method string|null getUserIdent()
 * @method Client setUserIdent(string $ident)
 *
 * @method string|null getPreviousIsp()
 * @method Client setPreviousIsp(string $isp)
 *
 * @method bool|null getIsLead()
 * @method Client setIsLead(bool $lead)
 *
 * @method int|null getClientType()
 * @method Client setClientType(int $type)
 *
 * @method string|null getCompanyName()
 * @method Client setCompanyName(string $name)
 *
 * @method string|null getCompanyRegistrationNumber()
 * @method Client setCompanyRegistrationNumber(string $number)
 *
 * @method string|null getCompanyTaxId()
 * @method Client setCompanyTaxId(string $id)
 *
 * @method string|null getCompanyWebsite()
 * @method Client setCompanyWebsite(string $website)
 *
 * @method string|null getCompanyContactFirstName()
 * @method Client setCompanyContactFirstName(string $first)
 *
 * @method string|null getCompanyContactLastName()
 * @method Client setCompanyContactLastName(string $last)
 *
 * @method string|null getFirstName()
 * @method Client setFirstName(string $first)
 *
 * @method string|null getLastName()
 * @method Client setLastName(string $last)
 *
 * @method string|null getStreet1()
 * @method Client setStreet1(string $street1)
 *
 * @method string|null getStreet2()
 * @method Client setStreet2(string $street2)
 *
 * @method string|null getCity()
 * @method Client setCity(string $city)
 *
 * @method int|null getCountryId()
 * @method Client setCountryId(int $id)
 *
 * @method int|null getStateId()
 * @method Client setStateId(int $id)
 *
 * @method string|null getZipCode()
 * @method Client setZipCode(string $zip)
 *
 * @method string|null getInvoiceStreet1()
 * @method Client setInvoiceStreet1(string $street1)
 *
 * @method string|null getInvoiceStreet2()
 * @method Client setInvoiceStreet2(string $street2)
 *
 * @method string|null getInvoiceCity()
 * @method Client setInvoiceCity(string $city)
 *
 * @method int|null getInvoiceCountryId()
 * @method Client setInvoiceCountryId(int $id)
 *
 * @method int|null getInvoiceStateId()
 * @method Client setInvoiceStateId(int $id)
 *
 * @method string|null getInvoiceZipCode()
 * @method Client setInvoiceZipCode(string $zip)
 *
 * @method bool|null getInvoiceAddressSameAsContact()
 * @method Client setInvoiceAddressSameAsContact(bool $same)
 *
 * @method string|null getNote()
 * @method Client setNote(string $note)
 *
 * @method bool|null getSendInvoiceByPost()
 * @method Client setSendInvoiceByPost(bool $send)
 *
 * @method int|null getInvoiceMaturityDays()
 * @method Client setInvoiceMaturityDays(int $days)
 *
 * @method bool|null getStopServiceDue()
 * @method Client setStopServiceDue(bool $stop)
 *
 * @method int|null getStopServiceDueDays()
 * @method Client setStopServiceDueDays(int $days)
 *
 * @method int|null getOrganizationId()
 * @method Client setOrganizationId(int $id)
 *
 * @method int|null getTax1Id()
 * @method Client setTax1Id(int $id)
 *
 * @method int|null getTax2Id()
 * @method Client setTax2Id(int $id)
 *
 * @method int|null getTax3Id()
 * @method Client setTax3Id(int $id)
 *
 * @method string|null getRegistrationDate()
 * @see    Client::setRegistrationDate()
 *
 * @method string|null getUsername()
 * @method Client setUsername(string|null $username)
 *
 * @method string|null getAvatarColor()
 * @method Client setAvatarColor(string $color)
 *
 * @method float|null getAddressGpsLat()
 * @method Client setAddressGpsLat(float $latitude)
 *
 * @method float|null getAddressGpsLon()
 * @method Client setAddressGpsLon(float $longitude)
 *
 * @method bool|null getGenerateProformaInvoices()
 * @method Client setGenerateProformaInvoices(bool|null $proforma)
 *
 * @see    Client::getContacts()
 * @method Client setContacts(ClientContact[]|null $contacts)
 *
 * @see    Client::getAttributes()
 * @see    Client::setAttributes()
 *
 * @method float|null getAccountBalance()
 *
 * @method float|null getAccountCredit()
 *
 * @method float|null getAccountOutstanding()
 *
 * @method string|null getCurrencyCode()
 *
 * @method string|null getOrganizationName()
 *
 * @see    Client::getBankAccounts()
 *
 * @method string|null getInvitationEmailSentDate()
 *
 * @see    Client::getTags()
 *
 */
final class Client extends EndpointObject
{
    use Helpers\ClientHelper;

    // =================================================================================================================
    // CONSTANTS
    // -----------------------------------------------------------------------------------------------------------------

    /** @var string The format to use for date and time values. */
    public const DATETIME_FORMAT = "Y-m-d\TH:i:sO";

    // =================================================================================================================
    // ENUMS
    // -----------------------------------------------------------------------------------------------------------------

    public const CLIENT_TYPE_RESIDENTIAL                        = 1;
    public const CLIENT_TYPE_COMMERCIAL                         = 2;

    // =================================================================================================================
    // PROPERTIES
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * "Custom ID" in UCRM, not to be confused with entity "ID" used in URL. (i.e. "ABC1000")
     *
     * @var string
     *
     * @Post
     * @Patch
     * @Unique
     */
    protected $userIdent;

    /**
     * @var string
     *
     * @Post
     * @Patch
     */
    protected $previousIsp;

    /**
     * @var bool
     *
     * @PostRequired
     * @PatchRequired
     */
    protected $isLead;

    /**
     * Defaults to "Residential", both here in the SDK and in the UCRM API!
     *
     * @var int
     *
     * @PostRequired
     * @PatchRequired
     */
    protected $clientType = Client::CLIENT_TYPE_RESIDENTIAL;

    /**
     * Require in case of type "Company".
     *
     * @var string
     *
     * @PostRequired `$this->clientType === Client::CLIENT_TYPE_COMMERCIAL`
     * @PatchRequired `$this->clientType === Client::CLIENT_TYPE_COMMERCIAL`
     */
    protected $companyName;

    /**
     * @var string
     *
     * @Post
     * @Patch
     */
    protected $companyRegistrationNumber;

    /**
     * @var string
     *
     * @Post
     * @Patch
     */
    protected $companyTaxId;

    /**
     * @var string
     *
     * @Post
     * @Patch
     */
    protected $companyWebsite;

    /**
     * @var string
     *
     * @Post
     * @Patch
     */
    protected $companyContactFirstName;

    /**
     * @var string
     *
     * @Post
     * @Patch
     */
    protected $companyContactLastName;

    /**
     * Required in case of type "Residential".
     *
     * @var string
     *
     * @PostRequired `$this->clientType === Client::CLIENT_TYPE_RESIDENTIAL`
     * @PatchRequired `$this->clientType === Client::CLIENT_TYPE_RESIDENTIAL`
     */
    protected $firstName;

    /**
     * Required in case of type "Residential".
     *
     * @var string
     *
     * @PostRequired `$this->clientType === Client::CLIENT_TYPE_RESIDENTIAL`
     * @PatchRequired `$this->clientType === Client::CLIENT_TYPE_RESIDENTIAL`
     */
    protected $lastName;

    /**
     * @var string
     *
     * @Post
     * @Patch
     */
    protected $street1;

    /**
     * @var string
     *
     * @Post
     * @Patch
     */
    protected $street2;

    /**
     * @var string
     *
     * @Post
     * @Patch
     */
    protected $city;

    /**
     * If not specified, taken from default organization.
     *
     * @var int
     *
     * @Post
     * @Patch
     */
    protected $countryId;

    /**
     * Applicable in case of "United States" and "Canada" only.
     * If not specified, taken from default organization.
     *
     * @var int
     *
     * @Post
     * @Patch
     */
    protected $stateId;

    /**
     * @var string
     *
     * @Post
     * @Patch
     */
    protected $zipCode;

    // REMOVED: For the below Invoice Address properties, as it is not a requirement of the UCRM API.
    // @PostRequired `! $this->invoiceAddressSameAsContact`
    // @PatchRequired `! $this->invoiceAddressSameAsContact`

    /**
     * @var string
     *
     * @Post
     * @Patch
     */
    protected $invoiceStreet1;

    /**
     * @var string
     *
     * @Post
     * @Patch
     */
    protected $invoiceStreet2;

    /**
     * @var string
     *
     * @Post
     * @Patch
     */
    protected $invoiceCity;

    /**
     * If not specified, taken from default organization.
     *
     * @var int
     *
     * @Post
     * @Patch
     */
    protected $invoiceCountryId;

    /**
     * Applicable in case of "United States" and "Canada" only.
     * If not specified, taken from default organization.
     *
     * @var int
     *
     * @Post
     * @Patch
     */
    protected $invoiceStateId;

    /**
     * @var string
     *
     * @Post
     * @Patch
     */
    protected $invoiceZipCode;

    /**
     * Defaults to TRUE, only here in the SDK!
     *
     * @var bool
     *
     * @PostRequired
     * @PatchRequired
     */
    protected $invoiceAddressSameAsContact = true;

    /**
     * @var string
     *
     * @Post
     * @Patch
     */
    protected $note;

    /**
     * Mark client's invoices as to be sent by post.
     * If null, system default is used.
     *
     * @var bool
     *
     * @Post
     * @Patch
     *
     * NOTE: Necessary to allow for resetting the value to system defaults.
     * @KeepNull
     */
    protected $sendInvoiceByPost;

    /**
     * If null, system default is used.
     *
     * @var int
     *
     * @Post
     * @Patch
     *
     * NOTE: Necessary to allow for resetting the value to system defaults.
     * @KeepNull
     */
    protected $invoiceMaturityDays;

    /**
     * Suspend client's service in case of overdue invoice.
     * If null, system default is used.
     *
     * @var bool
     *
     * @Post
     * @Patch
     *
     * NOTE: Necessary to allow for resetting the value to system defaults.
     * @KeepNull
     */
    protected $stopServiceDue;

    /**
     * Number of days for which suspend is deferred. (i.e. if 3 days are set and invoice due date is 17th March, suspend
     * will start from 20th March).
     * If null, system default is used.
     *
     * @var int
     *
     * @Post
     * @Patch
     *
     * NOTE: Necessary to allow for resetting the value to system defaults.
     * @KeepNull
     */
    protected $stopServiceDueDays;

    /**
     * If not specified, default organization will be used.
     *
     * @var int
     *
     * @Post
     * @Patch
     */
    protected $organizationId;

    /**
     * Will be added by default to each client's taxable services and products.
     *
     * @var int
     *
     * @Post
     * @Patch
     */
    protected $tax1Id;

    /**
     * Will be added by default to each client's taxable services and products.
     *
     * @var int
     *
     * @Post
     * @Patch
     */
    protected $tax2Id;

    /**
     * Will be added by default to each client's taxable services and products.
     *
     * @var int
     *
     * @Post
     * @Patch
     */
    protected $tax3Id;

    /**
     * Date string in ISO 8601 format.
     * If not specified, current date will be used.
     *
     * @var string
     *
     * @Post
     * @Patch
     */
    protected $registrationDate;

    /**
     * Sets the Client's Registration Date using the correct ISO 8601 format, and uses the current DateTime if null.
     *
     * @param \DateTime|null $value
     * @return Client Returns the Client instance, for method chaining purposes.
     * @throws \Exception
     */
    public function setRegistrationDate(?\DateTime $value = null): Client
    {
        $this->registrationDate = ($value === null ? new \DateTime() : $value)->format(self::DATETIME_FORMAT);
        return $this;
    }

    /**
     * If null, client zone is disabled.
     *
     * @var string
     *
     * @Post
     * @Patch
     *
     * NOTE: Necessary to allow for disabling the Client's access.
     * @KeepNull
     */
    protected $username;

    /**
     * Color in hexadecimal format.
     * If not specified, will be assigned randomly.
     *
     * @var string
     *
     * @Post
     * @Patch
     */
    protected $avatarColor;

    /**
     * Latitude of address location.
     *
     * @var float
     *
     * @Post
     * @Patch
     */
    protected $addressGpsLat;

    /**
     * Longitude of address location.
     *
     * @var float
     *
     * @Post
     * @Patch
     */
    protected $addressGpsLon;


    /**
     * If true, proforma invoice will be generated.
     * If null, system default is used.
     *
     * @var bool
     *
     * @Post
     * @Patch
     *
     * NOTE: Necessary to allow for resetting the value to system defaults.
     * @KeepNull
     */
    protected $generateProformaInvoices;




    /**
     * @var ClientContact[]|null
     * @Post
     */
    protected $contacts;

    /**
     * @return Collection
     * @throws \Exception
     */
    public function getContacts(): Collection
    {
        return new Collection(ClientContact::class, $this->contacts);
    }

    /**
     * @param ClientContact[]|null $values
     * @return Client Returns the Client instance, for method chaining purposes.
     */
    /*
    public function setContacts(?array $values): Client
    {
        //$this->contacts = $values->elements();
        $this->contacts = $values;
        return $this;
    }
    */

    /**
     * @var ClientContactAttribute[]
     * @Post
     * @Patch
     */
    protected $attributes;

    /**
     * @return Collection
     * @throws \Exception
     */
    public function getAttributes(): Collection
    {
        return new Collection(ClientContactAttribute::class, $this->attributes);
    }

    /**
     * @param Collection $values
     * @return Client Returns the Client instance, for method chaining purposes.
     */
    public function setAttributes(Collection $values): Client
    {
        $this->attributes = $values->elements();
        return $this;
    }

    /**
     * @var float
     */
    protected $accountBalance;

    /**
     * @var float
     */
    protected $accountCredit;

    /**
     * @var float
     */
    protected $accountOutstanding;

    /**
     * @var string
     */
    protected $currencyCode;

    /**
     * @var string
     */
    protected $organizationName;

    /**
     * @var ClientBankAccount[]
     */
    protected $bankAccounts;

    /**
     * @return Collection
     * @throws \Exception
     */
    public function getBankAccounts(): Collection
    {
        $bankAccounts = new Collection(ClientBankAccount::class, $this->bankAccounts);
        return $bankAccounts;
    }

    /**
     * @var string
     */
    protected $invitationEmailSentDate;

    /**
     * @var ClientTag[]
     */
    protected $tags;

    /**
     * @return Collection
     * @throws \Exception
     */
    public function getTags(): Collection
    {
        $tags = new Collection(ClientTag::class, $this->tags);
        return $tags;
    }

}



