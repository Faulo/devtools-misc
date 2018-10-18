<?php
declare(strict_types = 1);
namespace Slothsoft\Farah\Module\Results;

use GuzzleHttp\Psr7\Stream;
use Psr\Http\Message\StreamInterface;
use Slothsoft\Blob\BlobUrl;
use Slothsoft\Core\DOMHelper;
use Slothsoft\Core\IO\Writable\DOMWriterElementFromDocumentTrait;
use Slothsoft\Farah\Dictionary;
use Slothsoft\Farah\Exception\EmptyTransformationException;
use Slothsoft\Farah\Exception\ExceptionContext;
use Slothsoft\Farah\LinkDecorator\DecoratorFactory;
use Slothsoft\Farah\Module\FarahUrl\FarahUrlResolver;
use Slothsoft\Farah\Module\Node\InstructionCollector;
use Slothsoft\Farah\Module\Node\Instruction\UseDocumentInstructionInterface;
use Slothsoft\Farah\Module\Node\Instruction\UseManifestInstructionInterface;
use DOMDocument;
use DOMElement;
use Throwable;

/**
 *
 * @author Daniel Schulz
 *        
 */
class TransformationResult extends ResultBase
{
    use DOMWriterElementFromDocumentTrait;

    const TAG_ROOT = 'fragment';

    const TAG_DOCUMENT = 'document';

    const ATTR_NAME = 'name';

    const ATTR_ID = 'url';

    const ATTR_HREF = 'href';

    private $name;

    private $collector;

    private $resultDoc;

    public function __construct(string $name, InstructionCollector $collector)
    {
        $this->name = $name;
        $this->collector = $collector;
    }
    
    protected function loadStream(string $type): StreamInterface
    {
        $blob = BlobUrl::createTemporaryObject();
        $document = $this->toDocument();
        if ($type === self::STREAM_DEFAULT) {
            $this->decorateDocument($document);
        }
        $fileName = $this->guessFileName($document);
        $document->save(BlobUrl::createObjectURL($blob));
        return new Stream($blob, ['metadata' => ['uri' => $fileName]]);
        
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
            $dataNode = $this->resultDoc->createElementNS(DOMHelper::NS_FARAH_MODULE, $this->getOwnerAsset()->getElementTag());
            $dataNode->setAttribute(self::ATTR_NAME, $this->name);
            $dataNode->setAttribute(self::ATTR_ID, $this->getId());
            
            foreach ($this->collector->documentInstructions as $instruction) {
                $dataNode->appendChild($this->createElementFromDocumentInstruction($instruction));
            }
            
            foreach ($this->collector->manifestInstructions as $instruction) {
                $dataNode->appendChild($this->createElementFromManifestInstruction($instruction));
            }
            
            $this->resultDoc->appendChild($dataNode);
            
            if ($this->collector->templateInstruction) {
                $templateAsset = $this->collector->templateInstruction->getReferencedTemplateAsset();
                $templateUrl = $templateAsset->createUrl($this->getArguments(), self::STREAM_XML);
                
                $dom = new DOMHelper();
                
                $this->resultDoc = $dom->transformToDocument($this->resultDoc, (string) $templateUrl);
                
                if (! $this->resultDoc->documentElement) {
                    throw ExceptionContext::append(new EmptyTransformationException($templateAsset), [
                        'asset' => $templateAsset
                    ]);
                }
                
                // translating
                Dictionary::getInstance()->translateDoc($this->resultDoc, FarahUrlResolver::resolveToModule($templateAsset->createurl()));
            }
        }
        
        return $this->resultDoc;
    }

    private function createElementFromDocumentInstruction(UseDocumentInstructionInterface $instruction)
    {
        $asset = $instruction->getReferencedDocumentAsset();
        
        $element = $this->createElement(self::TAG_DOCUMENT, $instruction->getReferencedDocumentAlias(), $asset->getId());
        try {
            $result = $asset->lookupOperation($this->getArguments())->lookupXmlResult();
            $element->appendChild($result->toElement($this->resultDoc));
        } catch (Throwable $exception) {
            ExceptionContext::append($exception, [
                'asset' => $asset,
                'class' => __CLASS__
            ]);
            $element = $exception->exceptionContext->toElement($this->resultDoc);
        }
        return $element;
    }

    private function createElementFromManifestInstruction(UseManifestInstructionInterface $instruction): DOMElement
    {
        $asset = $instruction->getReferencedManifestAsset();
        $element = $this->createElement($asset->getElementTag(), $asset->getName(), $asset->getId());
        return $element;
    }

    private function createElement(string $tag, string $name, string $id): DOMElement
    {
        $element = $this->resultDoc->createElementNS(DOMHelper::NS_FARAH_MODULE, $tag);
        $element->setAttribute(self::ATTR_NAME, $name);
        $element->setAttribute(self::ATTR_ID, $id);
        $element->setAttribute(self::ATTR_HREF, str_replace('farah://', '/getAsset.php/', $id));
        
        return $element;
    }
    
    private function decorateDocument(DOMDocument $document) 
    {
        $stylesheetList = $this->getLinkedStylesheets();
        $scriptList = $this->getLinkedScripts();
        if ($stylesheetList or $scriptList) {
            $decorator = DecoratorFactory::createForDocument($document);
            $decorator->linkStylesheets(...$stylesheetList);
            $decorator->linkScripts(...$scriptList);
        }
    }
    
    private function getLinkedStylesheets(): array
    {
        return array_values($this->getOwnerAsset()->collectInstructions()->stylesheetAssets);
    }
    
    private function getLinkedScripts(): array
    {
        return array_values($this->getOwnerAsset()->collectInstructions()->scriptAssets);
    }
    
    private function guessFileName(DOMDocument $document) : string {
        return $this->name .'.'. $this->guessExtension((string) $document->documentElement->namespaceURI);
    }

    private function guessExtension(string $namespaceURI) : string
    {
        switch ($namespaceURI) {
            case DOMHelper::NS_HTML:
                return 'xhtml';
            case DOMHelper::NS_SVG:
                return 'svg';
            default:
                return 'xml';
        }
    }
}

