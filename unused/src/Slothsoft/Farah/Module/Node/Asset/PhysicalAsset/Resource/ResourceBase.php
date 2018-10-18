<?php
declare(strict_types = 1);
namespace Slothsoft\Farah\Module\Node\Asset\PhysicalAsset\Resource;

use Slothsoft\Core\IO\HTTPFile;
use Slothsoft\Core\IO\Writable\FileWriterStringFromFileTrait;
use Slothsoft\Farah\Module\FarahUrl\FarahUrl;
use Slothsoft\Farah\Module\Node\Asset\PhysicalAsset\PhysicalAssetBase;
use Slothsoft\Farah\Module\Node\Enhancements\MimeTypeTrait;
use Slothsoft\Farah\Module\ParameterFilters\DenyAllFilter;
use Slothsoft\Farah\Module\ParameterFilters\ParameterFilterInterface;
use Slothsoft\Farah\Module\Results\ResultCatalog;
use Slothsoft\Farah\Module\Results\ResultInterface;

/**
 *
 * @author Daniel Schulz
 *        
 */
abstract class ResourceBase extends PhysicalAssetBase implements ResourceInterface
{
    use MimeTypeTrait;
    use FileWriterStringFromFileTrait;

    protected function loadParameterFilter(): ParameterFilterInterface
    {
        return new DenyAllFilter();
    }

    protected function loadExecutable(FarahUrl $url): ResultInterface
    {
        return ResultCatalog::createBinaryFileResult($url, $this->toFile());
    }

    public function toFile(): HTTPFile
    {
        return HTTPFile::createFromPath($this->getRealPath(), $this->getPath());
    }
}

