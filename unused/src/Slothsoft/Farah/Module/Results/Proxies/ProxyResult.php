<?php
declare(strict_types = 1);
namespace Slothsoft\Farah\Module\Results\Proxies;

use Psr\Http\Message\StreamInterface;
use Slothsoft\Core\IO\HTTPFile;
use Slothsoft\Farah\Module\Results\ResultBase;
use Slothsoft\Farah\Module\Results\ResultInterface;
use DOMDocument;
use DOMElement;

/**
 *
 * @author Daniel Schulz
 *        
 */
abstract class ProxyResult extends ResultBase
{
    private $result;
    
    private function toResult(): ResultInterface
    {
        if ($this->result === null) {
            $this->result = $this->loadProxiedResult();
        }
        return $this->result;
    }

    abstract protected function loadProxiedResult(): ResultInterface;

    protected function loadStream(string $type): StreamInterface
    {
        return $this->toResult()->lookupStream($type);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Slothsoft\Farah\Module\Results\ResultBase::toDocument()
     */
    public function toDocument(): DOMDocument
    {
        return $this->toResult()->toDocument();
    }

    /**
     *
     * {@inheritdoc}
     * @see \Slothsoft\Farah\Module\Results\ResultBase::toElement()
     */
    public function toElement(DOMDocument $targetDoc): DOMElement
    {
        return $this->toResult()->toElement($targetDoc);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Slothsoft\Farah\Module\Results\ResultBase::toFile()
     */
    public function toFile(): HTTPFile
    {
        return $this->toResult()->toFile();
    }

    /**
     *
     * {@inheritdoc}
     * @see \Slothsoft\Farah\Module\Results\ResultBase::toString()
     */
    public function toString(): string
    {
        return $this->toResult()->toString();
    }
}

