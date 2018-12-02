<?php
declare(strict_types=1);

namespace UCRM\REST\Endpoints\Lookups;

use MVQN\REST\Annotations\PostAnnotation as Post;
use MVQN\REST\Annotations\PatchAnnotation as Patch;

/**
 * Class TicketActivityCommentAttachment
 *
 * @package UCRM\REST\Endpoints
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 * @final
 *
 * @method string|null getFilename()
 * @method TicketActivityCommentAttachment setFilename(string $filename)
 * @method int|null getId()
 * @method TicketActivityCommentAttachment setId(int $id)
 * @method int|null getSize()
 * @method TicketActivityCommentAttachment setSize(int $size)
 * @method string|null getMimeType()
 * @method TicketActivityCommentAttachment setMimeType(string $type)
 */
final class TicketActivityCommentAttachment extends Lookup
{
    // =================================================================================================================
    // PROPERTIES
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @var string
     */
    protected $filename;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     */
    protected $size;

    /**
     * @var string
     */
    protected $mimeType;

}
