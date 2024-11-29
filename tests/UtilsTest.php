<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Tests;

use PHPUnit\Framework\TestCase;
use Slothsoft\Devtools\Misc\Utils;

class UtilsTest extends TestCase {

    /**
     *
     * @dataProvider urls
     */
    public function testToUrl(string $input, string $expected) {
        $actual = Utils::toUrl($input);

        $this->assertEquals($expected, $actual);
    }

    public function urls(): iterable {
        yield 'de.ulisses-spiele.core.utilities' => [
            'de.ulisses-spiele.core.utilities',
            'de_2eulisses-spiele_2ecore_2eutilities'
        ];
        yield '_' => [
            '_',
            '_5f'
        ];
    }
}