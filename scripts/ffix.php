<?php
declare(strict_types = 1);

use Slothsoft\Core\DOMHelper;
foreach ([
    __DIR__ . '/../../../autoload.php',
    __DIR__ . '/../vendor/autoload.php'
] as $file) {
    if (file_exists($file)) {
        require_once $file;
        break;
    }
}

const WORKSPACE = 'ffix';
$workspace = realpath(WORKSPACE) or die("failed to find workspace '" . WORKSPACE . "'");

$eoi = ', ';

$characters = [];
$characters[] = 'Zidane';
$characters[] = 'Vivi';
$characters[] = 'Garnet';
$characters[] = 'Steiner';
$characters[] = 'Freia';
$characters[] = 'Quina';
$characters[] = 'Eiko';
$characters[] = 'Mahagon';

$translations = [];
$translations[','] = '';
$translations['Dagger'] = 'Garnet';
$translations['Freya'] = 'Freia';
$translations['Amarant'] = 'Mahagon';
$translations['Cinna'] = '';
$translations['Marcus'] = '';
$translations['Blank'] = '';
$translations['Beatrix'] = '';
$translations['Everybody'] = implode($eoi, $characters);
$translations['All'] = implode($eoi, $characters);
$translations['—'] = '';
$translations['None'] = '';
$translations['Name'] = '';
$translations['Command'] = '';
$translations['Original'] = '';
$translations['Diamond Light'] = '';
$translations['Pearl Light'] = '';

$items = [];

$urls = [];
$urls[] = 'https://finalfantasy.fandom.com/wiki/Final_Fantasy_IX_armor';
$urls[] = 'https://finalfantasy.fandom.com/wiki/Add-on_(Final_Fantasy_IX)';
$urls[] = 'https://finalfantasy.fandom.com/wiki/Final_Fantasy_IX_jewels';

$rowPath = '//table[.//th[1][normalize-space(.) = "%s"]]//tr[th/@rowspan]';

$meta = [];
$meta[sprintf($rowPath, 'Hats')] = [
    'name' => 'th[1]',
    'url' => 'th[1]//a/@href',
    'abilities' => 'td[4]/node()',
    'characters' => 'td[6]/node()'
];
$meta[sprintf($rowPath, 'Helmets')] = [
    'name' => 'th[1]',
    'url' => 'th[1]//a/@href',
    'abilities' => 'td[2]/node()',
    'characters' => 'td[4]/node()'
];
$meta[sprintf($rowPath, 'Armlets')] = [
    'name' => 'th[1]',
    'url' => 'th[1]//a/@href',
    'abilities' => 'td[3]/node()',
    'characters' => 'td[5]/node()'
];
$meta[sprintf($rowPath, 'Gauntlets')] = [
    'name' => 'th[1]',
    'url' => 'th[1]//a/@href',
    'abilities' => 'td[3]/node()',
    'characters' => 'td[5]/node()'
];
$meta[sprintf($rowPath, 'Clothes')] = [
    'name' => 'th[1]',
    'url' => 'th[1]//a/@href',
    'abilities' => 'td[3]/node()',
    'characters' => 'td[5]/node()'
];
$meta[sprintf($rowPath, 'Armor')] = [
    'name' => 'th[1]',
    'url' => 'th[1]//a/@href',
    'abilities' => 'td[3]/node()',
    'characters' => 'td[5]/node()'
];
$meta[sprintf($rowPath, 'Robes')] = [
    'name' => 'th[1]',
    'url' => 'th[1]//a/@href',
    'abilities' => 'td[3]/node()',
    'characters' => 'td[5]/node()'
];
$meta[sprintf($rowPath, 'Add-on')] = [
    'name' => 'th[1]',
    'url' => 'th[1]/a[@title]/@href',
    'abilities' => 'td[1]/node()',
    'characters' => 'td[3]/node()'
];
$meta[sprintf($rowPath, 'Jewel')] = [
    'name' => 'th[1]',
    'url' => 'th[1]/a[span]/@href',
    'abilities' => 'td[1]/node()',
    'characters' => '"' . implode($eoi, $characters) . '"'
];

foreach ($urls as $url) {
    echo $url . PHP_EOL;

    $dom = new DOMHelper();
    $document = @$dom->loadDocument($url, true);
    $xpath = $dom->loadXPath($document);

    foreach ($meta as $itemPath => $m) {
        foreach ($xpath->evaluate($itemPath) as $node) {
            $item = [];

            foreach ($m as $key => $path) {
                $item[$key] = [];
                $result = $xpath->evaluate($path, $node);
                if (is_string($result)) {
                    $item[$key][] = $result;
                } else {
                    foreach ($result as $n) {
                        $value = $xpath->evaluate('normalize-space(.)', $n);
                        $value = $translations[$value] ?? $value;
                        if (strlen($value) > 0) {
                            $item[$key][] = $value;
                        }
                    }
                }
                $item[$key] = implode($eoi, $item[$key]);
            }

            if ($item['name'] and $item['characters']) {
                $items[$item['name']] = $item;
            }
        }
    }
}

ksort($items);

foreach ($items as &$item) {
    if ($item['url'][0] === '/') {
        $item['url'] = 'https://finalfantasy.fandom.com' . $item['url'];
    }
}
unset($item);

$abilities = [];

$characterMeta = [
    'name' => 'th[1]',
    'url' => 'th[1]/a/@href',
    'items' => 'td[1]/node()'
];

$skilLMeta = [
    'name' => 'th[1]',
    'url' => 'th[1]/a/@href',
    'items' => 'td[2]/node()'
];

$rowPath = '//table[.//th[contains(., "Learned")]]//tr';

$urls = [];
$urls[] = [
    'url' => 'https://finalfantasy.fandom.com/wiki/Zidane_(Final_Fantasy_IX_party_member)',
    'character' => 'Zidane',
    'path' => $rowPath,
    'meta' => $characterMeta
];
$urls[] = [
    'url' => 'https://finalfantasy.fandom.com/wiki/Skill_(Final_Fantasy_IX)',
    'character' => 'Zidane',
    'path' => $rowPath,
    'meta' => $skilLMeta
];

$urls[] = [
    'url' => 'https://finalfantasy.fandom.com/wiki/Vivi_(Final_Fantasy_IX_party_member)',
    'character' => 'Vivi',
    'path' => $rowPath,
    'meta' => $characterMeta
];
$urls[] = [
    'url' => 'https://finalfantasy.fandom.com/wiki/Blk_Mag_(Final_Fantasy_IX)',
    'character' => 'Vivi',
    'path' => '//table[.//th[contains(., "Equipment")]]//tr',
    'meta' => $characterMeta
];

$urls[] = [
    'url' => 'https://finalfantasy.fandom.com/wiki/Dagger_(Final_Fantasy_IX_gameplay)',
    'character' => 'Garnet',
    'path' => $rowPath,
    'meta' => $characterMeta
];
$urls[] = [
    'url' => 'https://finalfantasy.fandom.com/wiki/Wht_Mag_(Final_Fantasy_IX)',
    'character' => 'Garnet',
    'path' => '//table[.//th[contains(., "Equipment")]]//tr[contains(., "Dagger")]',
    'meta' => $characterMeta
];
$urls[] = [
    'url' => 'https://finalfantasy.fandom.com/wiki/Summon_(Final_Fantasy_IX)',
    'character' => 'Garnet',
    'path' => '//h3[contains(., "Dagger")]/following::table[1]//tr',
    'meta' => [
        'name' => 'th[1]',
        'url' => 'th[1]/a/@href',
        'items' => 'td[4]/a'
    ]
];

$urls[] = [
    'url' => 'https://finalfantasy.fandom.com/wiki/Steiner_(Final_Fantasy_IX_gameplay)',
    'character' => 'Steiner',
    'path' => $rowPath,
    'meta' => $characterMeta
];
$urls[] = [
    'url' => 'https://finalfantasy.fandom.com/wiki/Swd_Art_(Final_Fantasy_IX)',
    'character' => 'Steiner',
    'path' => $rowPath,
    'meta' => $skilLMeta
];

$urls[] = [
    'url' => 'https://finalfantasy.fandom.com/wiki/Freya_(Final_Fantasy_IX_party_member)',
    'character' => 'Freia',
    'path' => $rowPath,
    'meta' => $characterMeta
];
$urls[] = [
    'url' => 'https://finalfantasy.fandom.com/wiki/Dragon_(Final_Fantasy_IX_command)',
    'character' => 'Freia',
    'path' => $rowPath,
    'meta' => $skilLMeta
];

$urls[] = [
    'url' => 'https://finalfantasy.fandom.com/wiki/Quina_(Final_Fantasy_IX_party_member)',
    'character' => 'Quina',
    'path' => $rowPath,
    'meta' => $characterMeta
];

$urls[] = [
    'url' => 'https://finalfantasy.fandom.com/wiki/Eiko_(Final_Fantasy_IX_party_member)',
    'character' => 'Eiko',
    'path' => $rowPath,
    'meta' => $characterMeta
];
$urls[] = [
    'url' => 'https://finalfantasy.fandom.com/wiki/Wht_Mag_(Final_Fantasy_IX)',
    'character' => 'Eiko',
    'path' => '//table[.//th[contains(., "Equipment")]]//tr[contains(., "Eiko")]',
    'meta' => $characterMeta
];
$urls[] = [
    'url' => 'https://finalfantasy.fandom.com/wiki/Summon_(Final_Fantasy_IX)',
    'character' => 'Garnet',
    'path' => '//h3[contains(., "Eiko")]/following::table[1]//tr',
    'meta' => [
        'name' => 'th[1]',
        'url' => 'th[1]/a/@href',
        'items' => 'td[3]/a'
    ]
];

$urls[] = [
    'url' => 'https://finalfantasy.fandom.com/wiki/Amarant_(Final_Fantasy_IX_gameplay)',
    'character' => 'Mahagon',
    'path' => $rowPath,
    'meta' => $characterMeta
];
$urls[] = [
    'url' => 'https://finalfantasy.fandom.com/wiki/Flair_(Final_Fantasy_IX)',
    'character' => 'Mahagon',
    'path' => $rowPath,
    'meta' => $skilLMeta
];

$meta = [];
$meta[$rowPath] = [
    'name' => 'th[1]',
    'url' => 'th[1]/a/@href',
    'items' => 'td[1]/node()'
];

foreach ($urls as $config) {
    $url = $config['url'];
    $character = $config['character'];
    $meta = [
        $config['path'] => $config['meta']
    ];

    echo $url . PHP_EOL;

    $dom = new DOMHelper();
    $document = @$dom->loadDocument($url, true);
    $xpath = $dom->loadXPath($document);

    foreach ($meta as $itemPath => $m) {
        foreach ($xpath->evaluate($itemPath) as $node) {
            $ability = [];

            foreach ($m as $key => $path) {
                $ability[$key] = [];
                $result = $xpath->evaluate($path, $node);
                if (is_string($result)) {
                    $ability[$key][] = $result;
                } else {
                    foreach ($result as $n) {
                        $value = $xpath->evaluate('normalize-space(.)', $n);
                        $value = $translations[$value] ?? $value;
                        if (strlen($value) > 0) {
                            $ability[$key][] = $value;
                        }
                    }
                }
                $ability[$key] = implode($eoi, $ability[$key]);
            }

            if ($ability['name']) {
                if (! $ability['url']) {
                    var_dump($ability);
                }
            }

            if ($ability['name'] and $ability['items']) {
                foreach (explode($eoi, $ability['items']) as $name) {
                    if (! isset($items[$name])) {
                        // echo "Unknown item '$name'!" . PHP_EOL;
                    }
                }

                $name = $ability['name'];

                if (isset($abilities[$name])) {
                    $abilities[$name]['items'] .= $eoi . $ability['items'];
                    $abilities[$name]['characters'][] = $character;
                } else {
                    $ability['characters'] = [
                        $character
                    ];

                    $abilities[$name] = $ability;
                }
            }
        }
    }
}

foreach ($abilities as &$ability) {
    $ability['items'] = implode($eoi, array_unique(explode($eoi, $ability['items'])));
    $ability['characters'] = implode($eoi, $ability['characters']);
    if ($ability['url'][0] === '/') {
        $ability['url'] = 'https://finalfantasy.fandom.com' . $ability['url'];
    }
}
unset($ability);

ksort($abilities);

$itemTable = [];
$itemTable[] = [
    'URL',
    'Gegenstand',
    ...$characters,
    'Abilities'
];
foreach ($items as $item) {
    $allowed = [];
    foreach ($characters as $c) {
        $allowed[] = strpos($item['characters'], $c) === false ? 'FALSE' : 'TRUE';
    }
    $itemTable[] = [
        $item['url'],
        $item['name'],
        ...$allowed,
        $item['abilities']
    ];
}

file_put_csv($workspace . DIRECTORY_SEPARATOR . 'items.csv', $itemTable);

$abilityTable = [];
$abilityTable[] = [
    'URL',
    'Ability',
    ...$characters,
    'Gegenstände'
];
foreach ($abilities as $ability) {
    $allowed = [];
    foreach ($characters as $c) {
        $allowed[] = strpos($ability['characters'], $c) === false ? 'FALSE' : 'TRUE';
    }
    $abilityTable[] = [
        $ability['url'],
        $ability['name'],
        ...$allowed,
        $ability['items']
    ];
}

file_put_csv($workspace . DIRECTORY_SEPARATOR . 'abilities.csv', $abilityTable);

$mapTable = [];
$mapTable[] = [
    'URL',
    'Gegenstand',
    ...$characters
];
foreach ($items as $item) {
    $map = [];
    foreach ($characters as $c) {
        $canEquip = strpos($item['characters'], $c) !== false;
        $hasOwner = strpos($item['characters'], $eoi) === false;
        $canLearn = false;
        if ($item['abilities']) {
            foreach (explode($eoi, $item['abilities']) as $name) {
                $ability = $abilities[$name];
                if (strpos($ability['characters'], $c) !== false) {
                    $canLearn = true;
                    break;
                }
            }
        }

        if ($canEquip and $hasOwner) {
            $m = 'Eigentümer';
        } elseif ($canEquip and $canLearn) {
            $m = 'Möchte und kann';
        } elseif ($canEquip) {
            $m = 'Kann, braucht nicht';
        } elseif ($canLearn) {
            $m = 'Möchte, kann nicht';
        } else {
            $m = 'Kein Bedarf';
        }

        $map[] = $m;
    }
    $mapTable[] = [
        $item['url'],
        $item['name'],
        ...$map
    ];
}

file_put_csv($workspace . DIRECTORY_SEPARATOR . 'map.csv', $mapTable);

echo '...done!';

function file_put_csv(string $filename, array $data) {
    if (($handle = fopen($filename, "w")) !== false) {
        foreach ($data as $row) {
            fputcsv($handle, $row);
        }
        fclose($handle);
    }
}