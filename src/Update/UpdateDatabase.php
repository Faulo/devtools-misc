<?php
declare(strict_types = 1);
namespace Slothsoft\Devtools\Misc\Update;

class UpdateDatabase {

    private static $_instance;

    public static function instance(): UpdateDatabase {
        if (self::$_instance === null) {
            self::$_instance = new UpdateDatabase();
            self::$_instance->updateFactories[] = new DefaultUpdateFactory();
        }

        return self::$_instance;
    }

    public array $updateFactories = [];

    public function getUpdates(string ...$ids): iterable {
        $updates = [];

        foreach ($ids as $id) {
            $update = null;

            foreach ($this->updateFactories as $updateFactory) {
                $update = $updateFactory->createUpdate($id);
                if ($update !== null) {
                    break;
                }
            }

            if ($update === null) {
                throw new \Exception("Failed to create Update for '$id'");
            }

            $updates[] = $update;
        }

        return $updates;
    }
}

