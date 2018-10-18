<?php
declare(strict_types = 1);
namespace Slothsoft\Farah\Module\Results;

use GuzzleHttp\Psr7\LazyOpenStream;
use Psr\Http\Message\StreamInterface;
use Slothsoft\Core\IO\HTTPFile;
use Slothsoft\Core\IO\Writable\FileWriterInterface;
use Slothsoft\Core\StreamWrapper\StreamWrapperInterface;

/**
 *
 * @author Daniel Schulz
 *        
 */
class FileWriterResult extends ResultBase
{

    private $writer;

    public function __construct(FileWriterInterface $writer)
    {
        $this->writer = $writer;
    }
    
    protected function loadStream(string $type): StreamInterface
    {
        return new LazyOpenStream($this->toFile()->getPath(), StreamWrapperInterface::MODE_OPEN_READONLY);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Slothsoft\Farah\Module\Results\ResultBase::toFile()
     */
    public function toFile(): HTTPFile
    {
        return $this->writer->toFile();
    }

    /**
     *
     * {@inheritdoc}
     * @see \Slothsoft\Farah\Module\Results\ResultBase::toString()
     */
    public function toString(): string
    {
        return $this->writer->toString();
    }
}

