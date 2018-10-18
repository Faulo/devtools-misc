<?php
declare(strict_types = 1);
namespace Slothsoft\Farah\Module\Results;

use GuzzleHttp\Psr7\Stream;
use Psr\Http\Message\StreamInterface;
use Slothsoft\Blob\BlobUrl;

/**
 *
 * @author Daniel Schulz
 *        
 */
class NullResult extends ResultBase
{
    protected function loadStream(string $type): StreamInterface
    {
        switch ($type) {
            case self::STREAM_XML:
                $blob = BlobUrl::createTemporaryObject();
                file_put_contents(BlobUrl::createObjectURL($blob), '<null/>');
                return new Stream($blob);
            default:
                $blob = BlobUrl::createTemporaryObject();
                file_put_contents(BlobUrl::createObjectURL($blob), '');
                return new Stream($blob);
        }
    }
}

