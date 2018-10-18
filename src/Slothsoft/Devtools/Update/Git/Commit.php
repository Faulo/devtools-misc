<?php
namespace Slothsoft\Devtools\Update\Git;

use Slothsoft\Devtools\Update\UpdateInterface;


class Commit implements UpdateInterface
{
    private $message;
    
    public function __construct(string $message)
    {
        $this->message = $message;
    }
    
    public function runOn(array $project)
    {
        passthru('git add .');
        passthru('git commit -m ' . escapeshellarg($this->message));
    }
}

