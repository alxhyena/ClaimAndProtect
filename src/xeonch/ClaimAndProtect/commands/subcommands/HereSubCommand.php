<?php

declare(strict_types=1);

namespace xeonch\ClaimAndProtect\commands\subcommands;

use xeonch\ClaimAndProtect\libs\commando\BaseSubCommand;
use xeonch\ClaimAndProtect\libs\commando\constraint\InGameRequiredConstraint;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\TextFormat;
use pocketmine\world\Position;
use xeonch\ClaimAndProtect\Main;
use xeonch\ClaimAndProtect\manager\LandManager;
use xeonch\ClaimAndProtect\utils\Configuration;
use xeonch\ClaimAndProtect\utils\Language;

class HereSubCommand extends BaseSubCommand
{

    protected function prepare(): void
    {
        $this->setPermission("claimandprotect.command.here");
        $this->addConstraint(new InGameRequiredConstraint($this));
    }


    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if ($sender instanceof Player) {
            $landManager = new LandManager();
            $x = $sender->getPosition()->getFloorX();
            $y = $sender->getPosition()->getFloorY();
            $z = $sender->getPosition()->getFloorZ();
            $pos = new Position($x, $y, $z, $sender->getWorld());
            $landsInArea = $landManager->getLandsIn($pos);
            if (!empty($landsInArea)) {
                foreach ($landsInArea as $landId => $landData) {
                    $msg = str_replace(["{OWNER}", "{ID}"], [$landData['owner'], $landId], Language::get($sender, "land-around-here", true));
                    $sender->sendMessage($msg);
                }
            } else {
                Language::sendMessage($sender, "no-land-here");
            }
        }
    }
}
