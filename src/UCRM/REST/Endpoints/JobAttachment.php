<?php
declare(strict_types=1);

namespace UCRM\REST\Endpoints;

use MVQN\Common\Encodings;
use MVQN\REST\Endpoints\EndpointObject;
use MVQN\REST\Annotations\EndpointAnnotation as Endpoint;
use MVQN\REST\Annotations\PostAnnotation as Post;
use MVQN\REST\Annotations\PostRequiredAnnotation as PostRequired;
use MVQN\REST\Annotations\PatchAnnotation as Patch;
use MVQN\REST\Annotations\PatchRequiredAnnotation as PatchRequired;

/**
 * Class JobAttachment
 *
 * @package UCRM\REST\Endpoints
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 * @final
 *
 * @Endpoint { "get": "/scheduling/jobs/attachments", "getById": "/scheduling/jobs/attachments/:id" }
 * @Endpoint { "post": "/scheduling/jobs/attachments" }
 * @Endpoint { "patch": "/scheduling/jobs/attachments/:id" }
 * @Endpoint { "delete": "/scheduling/jobs/attachments/:id" }
 *
 * @method int|null getJobId()
 * @method JobAttachment setJobId(int $id)
 * @method string|null getFilename()
 * @method JobAttachment setFilename(string $filename)
 * @method int|null getSize()
 * @method JobAttachment setSize(int $size)
 * @method string|null getMimeType()
 * @method JobAttachment setMimeType(string $type)
 * @see    JobAttachment setFile(string $data)
 */
final class JobAttachment extends EndpointObject
{
    use Helpers\JobAttachmentHelper;

    // =================================================================================================================
    // PROPERTIES
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @var int
     * @PostRequired
     * @PatchRequired
     */
    protected $jobId;

    /**
     * @var string
     * @PostRequired
     * @PatchRequired
     */
    protected $filename;

    /**
     * @var int
     */
    protected $size;

    /**
     * @var string
     */
    protected $mimeType;

    /**
     * @var string
     * @PostRequired
     * @PatchRequired
     */
    protected $file;

    public function setFile(string $data): JobAttachment
    {
        $this->file = Encodings::isBase64Encoded($data) ? $data : base64_encode($data);
        return $this;
    }

}
