<?php
declare(strict_types = 1);
namespace Slothsoft\Farah\Module\Results;

use GuzzleHttp\Psr7\Stream;
use Psr\Http\Message\StreamInterface;
use Slothsoft\Blob\BlobUrl;
use Slothsoft\Core\IO\Writable\DOMWriterInterface;
use DOMDocument;
use DOMElement;

/**
 *
 * @author Daniel Schulz
 *        
 */
class DOMWriterResult extends ResultBase
{

    private $writer;

    public function __construct(DOMWriterInterface $writer)
    {
        $this->writer = $writer;
    }
    
    protected function loadStream(string $type): StreamInterface
    {
        $blob = BlobUrl::createTemporaryObject();
        $this->toDocument()->save(BlobUrl::createObjectURL($blob));
        return new Stream($blob);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Slothsoft\Farah\Module\Results\ResultBase::toDocument()
     */
    public function toDocument(): DOMDocument
    {
        return $this->writer->toDocument();
    }
    
    /**
     *
     * {@inheritdoc}
     * @see \Slothsoft\Farah\Module\Results\ResultBase::toElement()
     */
    public function toElement(DOMDocument $targetDoc): DOMElement
    {
        return $this->writer->toElement($targetDoc);
    }
}

