<?php

declare(strict_types=1);

namespace xeonch\ClaimAndProtect\commands\subcommands;

use xeonch\ClaimAndProtect\libs\commando\BaseSubCommand;
use xeonch\ClaimAndProtect\libs\commando\constraint\InGameRequiredConstraint;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use xeonch\ClaimAndProtect\libs\commando\constraint\ConsoleRequiredConstraint;
use xeonch\ClaimAndProtect\Main;
use xeonch\ClaimAndProtect\manager\LandManager;
use xeonch\ClaimAndProtect\utils\Math;

use function yaml_parse_file;

class PluginMigration extends BaseSubCommand
{

    protected function prepare(): void
    {
        $this->setPermission("claimandprotect.command.migration");
        $this->addConstraint(new ConsoleRequiredConstraint($this));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        $ecoLandFile = Main::getInstance()->getDataFolder() . "migration/Land.yml";
        if (!is_file($ecoLandFile)) {
            $sender->sendMessage(TextFormat::RED . "Migration file not found, make sure to use the name 'Land.yml'");
            return;
        }
        $landManager = new LandManager();
        $data = yaml_parse_file($ecoLandFile);
        foreach ($data as $key => $value) {
            $id = $landManager->generateLandId();
            $owner = $value['owner'];
            $pos1 = $value['startX'] . "," . "0" . "," . $value['endX'] . "," . $value['level'];
            $pos2 = $value['endX'] . "," . "0" . "," . $value['endZ'] . "," . $value['level'];
            $members = [$owner];
            if (!empty($value['invitee'])) {
                foreach (array_keys($value['invitee']) as $member) {
                    $members[] = $member;
                }
            }
            $landData = [
                "owner" => $owner,
                "pos" => [
                    "first" => $pos1,
                    "second" => $pos2
                ],
                "member" => $members,
                "permission-member" => [
                    "teleport" => true,
                    "settings" => false,
                    "addfriend" => false,
                    "removefriend" => false
                ],
                "wide" => Math::calculateArea($pos1, $pos2),
                "price" => $value['price'],
                "world" => $value['level'],
                "fly" => true,
                "break" => false,
                "place" => false,
                "pvp" => true,
                "interact" => false,
                "drop" => false
            ];
            $landManager->saveLand($id, $landData);
        }
        $sender->sendMessage(TextFormat::GREEN . "Economyland plugin migration to claimandprotect successful, ". count($data) ." total lands successfully moved");
        rename($ecoLandFile, Main::getInstance()->getDataFolder() . "migration/Land_done.yml");
    }
}
