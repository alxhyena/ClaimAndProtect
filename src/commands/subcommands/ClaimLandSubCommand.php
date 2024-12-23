<?php

declare(strict_types=1);

namespace xeonch\ClaimAndProtect\commands\subcommands;

use xeonch\ClaimAndProtect\libs\commando\BaseSubCommand;
use xeonch\ClaimAndProtect\libs\commando\constraint\InGameRequiredConstraint;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\TextFormat;
use xeonch\ClaimAndProtect\forms\type\claim\ClaimForm;
use xeonch\ClaimAndProtect\Main;
use xeonch\ClaimAndProtect\manager\LandManager;
use xeonch\ClaimAndProtect\utils\Configuration;
use xeonch\ClaimAndProtect\utils\Language;

class ClaimLandSubCommand extends BaseSubCommand
{

    protected function prepare(): void
    {
        $this->setPermission("claimandprotect.command.claim");
        $this->addConstraint(new InGameRequiredConstraint($this));
    }


    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if ($sender instanceof Player) {
            $session = Main::getInstance()->getPlayerSession($sender);
            if ($session === null) {
                $sender->sendMessage(TextFormat::RED . "Session data not found.");
                return;
            }
            if ($session->getFirstPos() === null || $session->getSecondPos() === null) {
                Language::sendMessage($sender, "land-not-setup-yet");
                return;
            }
            $landManager = new LandManager();
            $pos1 = explode(",", $session->getFirstPos());
            $pos2 = explode(",", $session->getSecondPos());
            if ($landManager->checkOverlap($pos1[0], $pos2[0], $pos1[2], $pos2[2], $pos1[3])){
                Language::sendMessage($sender, "land-overlap");
                return;
            }
            if (Configuration::getPlayerLimitLand() !== "infinity"){
                $landCountPlayer = $landManager->getLandCountByPlayer($sender->getName());
                if ($landCountPlayer >= (int)Configuration::getPlayerLimitLand()){
                    Language::sendMessage($sender, "land-limit");
                    return;
                }
            }
            (new ClaimForm())->open($sender);
        }
    }
}
