<?php

declare(strict_types=1);

namespace xeonch\ClaimAndProtect\forms\type\admin;

use jojoe77777\FormAPI\CustomForm;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\player\Player;
use xeonch\ClaimAndProtect\forms\type\InfoLandForm;
use xeonch\ClaimAndProtect\forms\type\remove\ConfirmationRemoveLand;
use xeonch\ClaimAndProtect\forms\type\settings\friends\FriendsForm;
use xeonch\ClaimAndProtect\forms\type\settings\SettingPermissionForm;
use xeonch\ClaimAndProtect\forms\type\settings\SettingsForm;
use xeonch\ClaimAndProtect\forms\type\settings\TransferOwnerForm;
use xeonch\ClaimAndProtect\manager\LandManager;
use xeonch\ClaimAndProtect\utils\Language;

class AdminMenuForm
{
    public function open(Player $player,int $landId): void
    {
        $landData = (new LandManager())->getLand($landId);
        if ($landData === null) {
            $replace = str_replace("{ID}", (string)$landId, Language::get($player, "command-admin-id-not-exist", true));
            $player->sendMessage($replace);
            return;
        }
        $landManager = new LandManager();
        $form = new SimpleForm(function (Player $player, $data) use ($landData, $landId, $landManager) {
            if ($data === null) return;
            switch($data){
                case "settings":
                    (new SettingsForm())->open($player, $landId);
                    break;
                case "friends":
                    (new FriendsForm())->open($player, $landId);
                    break;
                case "info":
                    (new InfoLandForm())->open($player, $landId, $landData);
                    break;
                case "transfer":
                    (new TransferOwnerForm())->open($player, $landId);
                    break;
                case "tp":
                    $landManager->teleportToLandCenter($player, $landId);
                    $msg = str_replace("{ID}", (string)$landId, Language::get($player, "tp-success", true));
                    $player->sendMessage($msg);
                    break;
                case "settings-perms":
                    (new SettingPermissionForm())->open($player, $landId);
                    break;
                case "remove":
                    (new ConfirmationRemoveLand())->open($player, $landId);
                    break;
            }
        });

        $form->setTitle(str_replace("{ID}", (string)$landId, Language::get($player, "admin-menu-title")));
        $form->setContent(Language::get($player, "admin-menu-list-content"));
        $form->addButton(Language::get($player, "admin-menu-settings-form"), -1, "", "settings");
        $form->addButton(Language::get($player, "admin-menu-friends-form"), -1, "", "friends");
        $form->addButton(Language::get($player, "admin-menu-info-form"), -1, "", "info");
        $form->addButton(Language::get($player, "admin-menu-transfer-form"), -1, "", "transfer");
        $form->addButton(Language::get($player, "admin-menu-teleport-form"), -1, "", "tp");
        $form->addButton(Language::get($player, "admin-menu-settings-perms-form"), -1, "", "settings-perms");
        $form->addButton(Language::get($player, "admin-menu-remove-form"), -1, "", "remove");
        $player->sendForm($form);
    }
}
