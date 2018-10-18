<?php
declare(strict_types = 1);
namespace Slothsoft\Farah\Module\Results;

use Psr\Http\Message\StreamInterface;
use Slothsoft\Core\IO\HTTPFile;
use Slothsoft\Core\IO\Writable\DOMWriterInterface;
use Slothsoft\Core\IO\Writable\FileWriterInterface;
use Slothsoft\Farah\Module\FarahUrl\FarahUrl;
use Slothsoft\Farah\Module\FarahUrl\FarahUrlArguments;
use Slothsoft\Farah\Module\FarahUrl\FarahUrlStreamIdentifier;
use Slothsoft\Farah\Module\Node\Asset\AssetInterface;
use DOMDocument;
use DOMElement;

/**
 *
 * @author Daniel Schulz
 *        
 */
abstract class ResultBase implements ResultInterface, DOMWriterInterface, FileWriterInterface
{
    const STREAM_DEFAULT = '';
    const STREAM_XML = 'xml';
    
    private $url;
    
    private $ownerAsset;
    
    private $arguments;
    
    private $streams = [];
    
    public function init(AssetInterface $ownerAsset, FarahUrlArguments $args) {
        $this->url = $ownerAsset->createUrl($args);
        $this->ownerAsset = $ownerAsset;
        $this->arguments = $args;
    }
    
    protected final function getOwnerAsset() : AssetInterface {
        return $this->ownerAsset;
    }
    
    protected final function getArguments() : FarahUrlArguments {
        return $this->arguments;
    }
    public final function getId(): string
    {
        return (string) $this->url;
    }
    
    public final function createUrl($type = null): FarahUrl
    {
        return $this->getOwnerAsset()->createUrl($this->arguments, $type);
    }
    
    public final function lookupStream($type) : StreamInterface {
        $type = (string) $type;
        if (!isset($this->streams[$type])) {
            $this->streams[$type] = $this->loadStream($type);
        }
        return $this->streams[$type];
    }
    abstract protected function loadStream(string $type);
    
    
    protected function createDefaultUrl(): FarahUrl
    {
        return $this->createUrl(FarahUrlStreamIdentifier::createFromString(''));
    }
    
    /**
     *
     * {@inheritdoc}
     * @see \Slothsoft\Core\IO\Writable\FileWriterInterface::toFile()
     */
    public function toFile(): HTTPFile
    {
        return HTTPFile::createFromPath((string) $this->createDefaultUrl());
    }
    
    /**
     *
     * {@inheritdoc}
     * @see \Slothsoft\Core\IO\Writable\FileWriterInterface::toString()
     */
    public function toString(): string
    {
        return file_get_contents((string) $this->createDefaultUrl());
    }
    
    protected function createXmlUrl(): FarahUrl
    {
        return $this->createUrl(FarahUrlStreamIdentifier::createFromString(self::STREAM_XML));
    }

    /**
     *
     * {@inheritdoc}
     * @see \Slothsoft\Core\IO\Writable\DOMWriterInterface::toDocument()
     */
    public function toDocument(): DOMDocument
    {
        $targetDoc = new DOMDocument();
        $targetDoc->load((string) $this->createXmlUrl());
        return $targetDoc;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Slothsoft\Core\IO\Writable\DOMWriterInterface::toElement()
     */
    public function toElement(DOMDocument $targetDoc): DOMElement
    {
        $fragment = $targetDoc->createDocumentFragment();
        $xml = file_get_contents((string) $this->createXmlUrl());
        $xml = preg_replace('~^\<\?xml[^?]+\?\>~', '', $xml);
        $fragment->appendXML($xml);
        foreach ($fragment->childNodes as $node) {
            if ($node->nodeType === XML_ELEMENT_NODE) {
                return $fragment->removeChild($node);
            }
        }
        throw new \DOMException("$this->url#xml does not contain an element node");
    }
}

