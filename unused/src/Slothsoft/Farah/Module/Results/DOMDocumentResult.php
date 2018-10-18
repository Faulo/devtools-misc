<?php
declare(strict_types = 1);
namespace Slothsoft\Farah\Module\Results;

use GuzzleHttp\Psr7\Stream;
use Psr\Http\Message\StreamInterface;
use Slothsoft\Blob\BlobUrl;
use DOMDocument;
use DOMElement;

/**
 *
 * @author Daniel Schulz
 *        
 */
class DOMDocumentResult extends ResultBase
{

    private $document;

    public function __construct(DOMDocument $doc)
    {
        $this->document = $doc;
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
        return $this->document;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Slothsoft\Farah\Module\Results\ResultBase::toElement()
     */
    public function toElement(DOMDocument $targetDoc): DOMElement
    {
        return $targetDoc->importNode($this->document->documentElement, true);
    }
}

