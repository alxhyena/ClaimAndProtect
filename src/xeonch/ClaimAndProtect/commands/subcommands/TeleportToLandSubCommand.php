<?php

declare(strict_types=1);

namespace xeonch\ClaimAndProtect\commands\subcommands;

use xeonch\ClaimAndProtect\libs\commando\args\IntegerArgument;
use xeonch\ClaimAndProtect\libs\commando\BaseSubCommand;
use xeonch\ClaimAndProtect\libs\commando\constraint\InGameRequiredConstraint;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use xeonch\ClaimAndProtect\forms\type\settings\SettingsForm;
use xeonch\ClaimAndProtect\forms\type\settings\SettingsMenuForm;
use xeonch\ClaimAndProtect\manager\LandManager;
use xeonch\ClaimAndProtect\Main;
use xeonch\ClaimAndProtect\utils\Language;

class TeleportToLandSubCommand extends BaseSubCommand
{

    protected function prepare(): void
    {
        $this->setPermission("claimandprotect.command.tp");
        $this->registerArgument(0, new IntegerArgument("id"));
        $this->addConstraint(new InGameRequiredConstraint($this));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player) {
            return;
        }
        if (!isset($args["id"]) || !is_numeric($args["id"])) {
            Language::sendMessage($sender, "command-tp-invalid-id");
            return;
        }
        $landId = (int)$args["id"];
        $landManager = new LandManager();
        $landData = $landManager->getLand($landId);

        if ($landData === null) {
            $replace = str_replace("{ID}", (string)$landId, Language::get($sender, "command-tp-id-not-exist", true));
            $sender->sendMessage($replace);
            return;
        }
        $isOwner = $landData["owner"] === $sender->getName();
        $isAdmin = $sender->hasPermission("claimandprotect.admin.tp");
        if ($isOwner || $isAdmin) {
            $landManager->teleportToLandCenter($sender, $landId);
            $msg = str_replace("{ID}", (string)$landId, Language::get($sender, "tp-success", true));
            $sender->sendMessage($msg);
            return;
        }
        
        if (in_array($sender->getName(), $landData['member'])) {
            if (!$landData['permission-member']['teleport']) {
                Language::sendMessage($sender, "tp-no-have-permission");
                return;
            }
            $landManager->teleportToLandCenter($sender, $landId);
            $msg = str_replace("{ID}", (string)$landId, Language::get($sender, "tp-success", true));
            $sender->sendMessage($msg);
            return;
        }
        Language::sendMessage($sender, "tp-no-have-permission");
    }
}
