<?php
declare(strict_types = 1);
namespace Slothsoft\Farah\Module\Results\Files;

use GuzzleHttp\Psr7\Stream;
use Psr\Http\Message\StreamInterface;
use Slothsoft\Blob\BlobUrl;
use Slothsoft\Core\IO\Writable\DOMWriterElementFromDocumentTrait;
use DOMDocument;

/**
 *
 * @author Daniel Schulz
 *        
 */
class HtmlFileResult extends FileResult
{
    use DOMWriterElementFromDocumentTrait;
    
    private $resultDoc;
    
    protected function loadStream(string $type): StreamInterface
    {
        if ($type === self::STREAM_XML) {
            $blob = BlobUrl::createTemporaryObject();
            $doc = $this->toDocument();
            $doc->save(BlobUrl::createObjectURL($blob));
            return new Stream($blob);
        }
        return parent::loadStream($type);
    }
    
    /**
     *
     * {@inheritdoc}
     * @see \Slothsoft\Farah\Module\Results\ResultBase::toDocument()
     */
    public function toDocument(): DOMDocument
    {
        if ($this->resultDoc === null) {
            $this->resultDoc = new DOMDocument();
            $this->resultDoc->loadHTMLFile($this->file->getPath());
        }
        return $this->resultDoc;
    }
}

