<?php

declare(strict_types=1);

namespace xeonch\ClaimAndProtect\commands;

use xeonch\ClaimAndProtect\libs\commando\BaseCommand;
use pocketmine\command\CommandSender;
use xeonch\ClaimAndProtect\commands\subcommands\AdminLandSubCommand;
use xeonch\ClaimAndProtect\commands\subcommands\ClaimLandSubCommand;
use xeonch\ClaimAndProtect\commands\subcommands\HereSubCommand;
use xeonch\ClaimAndProtect\commands\subcommands\InfoLandSubCommand;
use xeonch\ClaimAndProtect\commands\subcommands\MyLandSubCommand;
use xeonch\ClaimAndProtect\commands\subcommands\RemoveLandSubCommand;
use xeonch\ClaimAndProtect\commands\subcommands\SetFisrtPositionSubCommand;
use xeonch\ClaimAndProtect\commands\subcommands\SetSecondPositionSubCommand;
use xeonch\ClaimAndProtect\commands\subcommands\SettingsLandSubCommand;
use xeonch\ClaimAndProtect\commands\subcommands\TeleportToLandSubCommand;
use pocketmine\player\Player;
use xeonch\ClaimAndProtect\utils\Language;

class ClaimAndProtectCommand extends BaseCommand
{

    protected function prepare(): void
    {
        $this->setPermission($this->getPermission());
        $this->registerSubCommand(new SetFisrtPositionSubCommand("setfirst", "Set the first position for claiming land.", ["first", "f"]));
        $this->registerSubCommand(new SetSecondPositionSubCommand("setsecond", "Set the second position for claiming land.", ["second", "sec", "s"]));
        $this->registerSubCommand(new ClaimLandSubCommand("claim", "Claim your land after setup.", []));
        $this->registerSubCommand(new SettingsLandSubCommand("settings", "Settings your land.", ["setting"]));
        $this->registerSubCommand(new HereSubCommand("here", "Whose land is in this area?", []));
        $this->registerSubCommand(new MyLandSubCommand("myland", "View all owned land", []));
        $this->registerSubCommand(new InfoLandSubCommand("info", "Information of land with id", []));
        $this->registerSubCommand(new RemoveLandSubCommand("remove", "Remove/sell your land", ["sell", "rm", "delete"]));
        $this->registerSubCommand(new TeleportToLandSubCommand("tp", "Teleport to land with id", ["move", "teleport", "mv"]));
        $this->registerSubCommand(new AdminLandSubCommand("admin", "Admin feature", []));
    }

    public function getPermission()
    {
        return "claimandprotect.command";
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player) {
            return;
        }

        if (empty($args)) {
            $this->sendHelp($sender);
            return;
        }

        $sender->sendMessage(Language::get($sender, "command-invalid-subcommand", true));
    }


    private function sendHelp(Player $player): void
    {
        $player->sendMessage(Language::get($player, "command-help-title"));
        $player->sendMessage("§a/claimandprotect setfirst: §fSet the first position for claiming land.");
        $player->sendMessage("§a/claimandprotect setsecond: §fSet the second position for claiming land.");
        $player->sendMessage("§a/claimandprotect claim: §fClaim your land after setup.");
        $player->sendMessage("§a/claimandprotect settings: §fSettings your land.");
        $player->sendMessage("§a/claimandprotect here: §fWhose land is in this area?");
        $player->sendMessage("§a/claimandprotect myland: §fView all owned land.");
        $player->sendMessage("§a/claimandprotect info: §fInformation of land with id.");
        $player->sendMessage("§a/claimandprotect remove: §fRemove/sell your land.");
        $player->sendMessage("§a/claimandprotect tp: §fTeleport to land with id.");
        $player->sendMessage("§a/claimandprotect admin: §fAdmin feature.");
    }
}
