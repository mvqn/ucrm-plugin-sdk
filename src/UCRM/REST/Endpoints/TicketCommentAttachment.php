<?php
declare(strict_types=1);

namespace UCRM\REST\Endpoints;

use MVQN\REST\Endpoints\EndpointObject;
use MVQN\REST\Annotations\EndpointAnnotation as Endpoint;
use MVQN\REST\Annotations\PostAnnotation as Post;
use MVQN\REST\Annotations\PostRequiredAnnotation as PostRequired;
use MVQN\REST\Annotations\PatchAnnotation as Patch;
use MVQN\REST\Annotations\PatchRequiredAnnotation as PatchRequired;
use MVQN\REST\RestClient;
use UCRM\REST\Endpoints\Lookups\TicketActivityCommentAttachment;

//use UCRM\REST\Endpoints\Helpers\PaymentHelper;
//use UCRM\REST\Endpoints\Lookups\PaymentCover;

/**
 * Class TicketCommentAttachment
 *
 * @package UCRM\REST\Endpoints
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 * @final
 *
 * @method string                       getFilename()
 * @method int|null                     getSize()
 * @method string|null                  getMimeType()
 *
 */
final class TicketCommentAttachment extends EndpointObject
{
    //use PaymentHelper;

    // =================================================================================================================
    // PROPERTIES
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @var string
     * @PostRequired
     */
    protected $filename;

    /**
     * @var string
     * @PostRequired
     */
    protected $file;

    /**
     * @var int
     */
    protected $size;

    /**
     * @var string
     */
    protected $mimeType;






    public static function download(int $attachmentId)
    {
        return RestClient::download("/ticketing/tickets/comments/attachments/$attachmentId/file");
    }

}
