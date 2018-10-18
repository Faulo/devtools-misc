<?php
declare(strict_types = 1);
namespace Slothsoft\Farah\Module\Results\Files;

use GuzzleHttp\Psr7\LazyOpenStream;
use Psr\Http\Message\StreamInterface;
use Slothsoft\Core\IO\HTTPFile;
use Slothsoft\Core\IO\Writable\FileWriterStringFromFileTrait;
use Slothsoft\Core\StreamWrapper\StreamWrapperInterface;
use Slothsoft\Farah\Module\Results\ResultBase;

abstract class FileResult extends ResultBase
{
    use FileWriterStringFromFileTrait;

    protected $file;

    public function __construct(HTTPFile $file)
    {
        $this->file = $file;
    }
    
    protected function loadStream(string $type): StreamInterface
    {
        return new LazyOpenStream($this->file->getPath(), StreamWrapperInterface::MODE_OPEN_READONLY);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Slothsoft\Farah\Module\Results\ResultBase::toFile()
     */
    public function toFile(): HTTPFile
    {
        return $this->file;
    }
}

