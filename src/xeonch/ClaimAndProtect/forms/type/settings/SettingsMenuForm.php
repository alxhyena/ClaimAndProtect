<?php

declare(strict_types=1);

namespace xeonch\ClaimAndProtect\forms\type\settings;

use jojoe77777\FormAPI\SimpleForm;
use pocketmine\player\Player;
use xeonch\ClaimAndProtect\forms\type\settings\friends\FriendsForm;
use xeonch\ClaimAndProtect\manager\LandManager;
use xeonch\ClaimAndProtect\utils\Language;

class SettingsMenuForm
{
    public function open(Player $player, int $landId)
    {
        $landData = (new LandManager())->getLand($landId);
        if ($landData === null) {
            $replace = str_replace("{ID}", (string)$landId, Language::get($player, "command-settings-id-not-exist", true));
            $player->sendMessage($replace);
            return;
        }
        $form = new SimpleForm(function (Player $player, $data) use ($landId, $landData) {
            if ($data === null) {
                return;
            }
            $isOwner = $landData["owner"] === $player->getName();
            $isAdmin = $player->hasPermission("claimandprotect.admin.settings");
            switch ($data) {
                case "settings":
                    (new SettingsForm())->open($player, $landId);
                    break;
                case "friends":
                    (new FriendsForm())->open($player, $landId);
                    break;
                case "transfer":
                    if ($isOwner || $isAdmin) {
                        (new TransferOwnerForm())->open($player, $landId);
                    } else {
                        Language::sendMessage($player, "settings-no-have-permission");
                    }
                    break;
                case "permission":
                    if ($isOwner || $isAdmin) {
                        (new SettingPermissionForm())->open($player, $landId);
                    } else {
                        Language::sendMessage($player, "settings-no-have-permission");
                    }
                    break;
            }
        });
        $form->setTitle(Language::get($player, "settings-menu-title-form"));
        $form->addButton(Language::get($player, "settings-menu-settings-form"), -1, "", "settings");
        $form->addButton(Language::get($player, "settings-menu-friends-form"), -1, "", "friends");
        $form->addButton(Language::get($player, "settings-menu-transfer-form"), -1, "", "transfer");
        $form->addButton(Language::get($player, "settings-menu-permission-form"), -1, "", "permission");
        $player->sendForm($form);
    }
}
