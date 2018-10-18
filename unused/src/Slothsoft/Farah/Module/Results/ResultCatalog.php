<?php
declare(strict_types = 1);
namespace Slothsoft\Farah\Module\Results;

use Slothsoft\Core\IO\HTTPFile;
use Slothsoft\Core\IO\Writable\DOMWriterInterface;
use Slothsoft\Core\IO\Writable\FileWriterInterface;
use Slothsoft\Farah\Exception\ResultTypeNotSupportedException;
use Slothsoft\Farah\Module\FarahUrl\FarahUrl;
use Slothsoft\Farah\Module\Node\InstructionCollector;
use Slothsoft\Farah\Module\Results\Files\BinaryFileResult;
use Slothsoft\Farah\Module\Results\Files\HtmlFileResult;
use Slothsoft\Farah\Module\Results\Files\TextFileResult;
use Slothsoft\Farah\Module\Results\Files\XmlFileResult;
use Slothsoft\Farah\Module\Results\Proxies\ClosureResult;
use Closure;
use DOMDocument;
use DOMElement;
use Psr\Http\Message\MessageInterface;
use Slothsoft\Farah\Module\FarahUrl\FarahUrlResolver;

/**
 *
 * @author Daniel Schulz
 *        
 */
class ResultCatalog
{

    public static function createFromMixed(FarahUrl $url, $result): ResultInterface
    {
        switch (true) {
            case $result instanceof ResultInterface:
                return $result;
            case $result instanceof DOMWriterInterface:
                return self::createDOMWriterResult($url, $result);
            case $result instanceof FileWriterInterface:
                return self::createFileWriterResult($url, $result);
            case $result instanceof DOMDocument:
                return self::createDOMDocumentResult($url, $result);
            case $result instanceof DOMElement:
                return self::createDOMElementResult($url, $result);
            case $result instanceof Closure:
                return self::createClosureResult($url, $result);
            case is_object($result):
                throw new ResultTypeNotSupportedException(get_class($result));
            default:
                throw new ResultTypeNotSupportedException(gettype($result));
        }
    }

    public static function createNullResult(FarahUrl $url): NullResult
    {
        $result = new NullResult();
        $result->init(FarahUrlResolver::resolveToAsset($url), $url->getArguments());
        return $result;
    }

    public static function createFileWriterResult(FarahUrl $url, FileWriterInterface $writer): FileWriterResult
    {
        $result = new FileWriterResult($writer);
        $result->init(FarahUrlResolver::resolveToAsset($url), $url->getArguments());
        return $result;
    }

    public static function createDOMWriterResult(FarahUrl $url, DOMWriterInterface $writer): DOMWriterResult
    {
        $result = new DOMWriterResult($writer);
        $result->init(FarahUrlResolver::resolveToAsset($url), $url->getArguments());
        return $result;
    }

    public static function createTransformationResult(FarahUrl $url, string $name, InstructionCollector $collector): TransformationResult
    {
        $result = new TransformationResult($name, $collector);
        $result->init(FarahUrlResolver::resolveToAsset($url), $url->getArguments());
        return $result;
    }

    public static function createDOMDocumentResult(FarahUrl $url, DOMDocument $document): DOMDocumentResult
    {
        $result = new DOMDocumentResult($document);
        $result->init(FarahUrlResolver::resolveToAsset($url), $url->getArguments());
        return $result;
    }

    public static function createDOMElementResult(FarahUrl $url, DOMElement $element): DOMElementResult
    {
        $result = new DOMElementResult($element);
        $result->init(FarahUrlResolver::resolveToAsset($url), $url->getArguments());
        return $result;
    }

    public static function createBinaryFileResult(FarahUrl $url, HTTPFile $file): BinaryFileResult
    {
        $result = new BinaryFileResult($file);
        $result->init(FarahUrlResolver::resolveToAsset($url), $url->getArguments());
        return $result;
    }

    public static function createTextFileResult(FarahUrl $url, HTTPFile $file): TextFileResult
    {
        $result = new TextFileResult($file);
        $result->init(FarahUrlResolver::resolveToAsset($url), $url->getArguments());
        return $result;
    }

    public static function createXmlFileResult(FarahUrl $url, HTTPFile $file): XmlFileResult
    {
        $result = new XmlFileResult($file);
        $result->init(FarahUrlResolver::resolveToAsset($url), $url->getArguments());
        return $result;
    }

    public static function createHtmlFileResult(FarahUrl $url, HTTPFile $file): HtmlFileResult
    {
        $result = new HtmlFileResult($file);
        $result->init(FarahUrlResolver::resolveToAsset($url), $url->getArguments());
        return $result;
    }

    public static function createClosureResult(FarahUrl $url, Closure $closure): ClosureResult
    {
        $result = new ClosureResult($closure);
        $result->init(FarahUrlResolver::resolveToAsset($url), $url->getArguments());
        return $result;
    }

    public static function createMessageResult(FarahUrl $url, MessageInterface $message)
    {
        $result = new NullResult($message);
        $result->init(FarahUrlResolver::resolveToAsset($url), $url->getArguments());
        return $result;
    }
}

