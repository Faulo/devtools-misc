<?php
namespace Slothsoft\Farah\Module\Results\Xml;

use Slothsoft\Core\IO\Writable\DOMWriterInterface;
use Slothsoft\Farah\Module\Result\ResultInterface;

interface XmlResultInterface extends ResultInterface, DOMWriterInterface
{
}

