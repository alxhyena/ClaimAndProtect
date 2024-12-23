<?php

declare(strict_types=1);

namespace xeonch\ClaimAndProtect\forms\type\settings;

use jojoe77777\FormAPI\CustomForm;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use xeonch\ClaimAndProtect\Main;
use xeonch\ClaimAndProtect\manager\LandManager;
use xeonch\ClaimAndProtect\utils\Language;

class SettingPermissionForm
{
    public function open(Player $player, int $landId)
    {
        $landManager = new LandManager();
        $landData = $landManager->getLand($landId);

        if ($landData === null) {
            $replace = str_replace("{ID}", (string)$landId, Language::get($player, "command-settings-id-not-exist", true));
            $player->sendMessage($replace);
            return;
        }

        $isOwner = $landData["owner"] === $player->getName();
        $isAdmin = $player->hasPermission("claimandprotect.admin.settings");

        if (!$isOwner && !$isAdmin) {
            Language::sendMessage($player, "settings-no-have-permission");
            return;
        }
        $form = new CustomForm(function (Player $player, $data) use ($landManager, $landId, $landData) {
            if ($data === null) {
                return;
            }
            $permissions = $landData['permission-member'];
            $permissions["teleport"] = (bool)$data[0]; 
            $permissions["settings"] = (bool)$data[1];
            $permissions["addfriend"] = (bool)$data[2];
            $permissions["removefriend"] = (bool)$data[3];
            $landData['permission-member'] = $permissions;
            $landManager->saveLand($landId, $landData);

            $player->sendMessage(str_replace(["{ID}"], [$landId], Language::get($player, "settings-permission-updated", true)));
        });

        $form->setTitle(Language::get($player, "settings-permission-title"));
        $form->addToggle(Language::get($player, "settings-permission-teleport"), $landData['permission-member']["teleport"]);
        $form->addToggle(Language::get($player, "settings-permission-settings"), $landData['permission-member']["settings"]);
        $form->addToggle(Language::get($player, "settings-permission-addfriend"), $landData['permission-member']["addfriend"]);
        $form->addToggle(Language::get($player, "settings-permission-removefriend"), $landData['permission-member']["removefriend"]);

        $player->sendForm($form);
    }
}
