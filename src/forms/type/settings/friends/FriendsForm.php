<?php

declare(strict_types=1);

namespace xeonch\ClaimAndProtect\forms\type\settings\friends;

use jojoe77777\FormAPI\CustomForm;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use xeonch\ClaimAndProtect\Main;
use xeonch\ClaimAndProtect\manager\LandManager;
use xeonch\ClaimAndProtect\utils\Language;

class FriendsForm
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
                case "add":
                    if ($isOwner || $isAdmin) {
                        (new AddFriendsForm())->open($player, $landId);
                        return;
                    }
                    if (in_array($player->getName(), $landData['member']) && $landData['permission-member']['addfriend']) {
                        (new AddFriendsForm())->open($player, $landId);
                        return;
                    }
                    Language::sendMessage($player, "settings-no-have-permission");
                    break;
                case "remove":
                    if ($isOwner || $isAdmin) {
                        (new RemoveFriendsForm())->open($player, $landId);
                        return;
                    }
                    if (in_array($player->getName(), $landData['member']) && $landData['permission-member']['removefriend']) {
                        (new RemoveFriendsForm())->open($player, $landId);
                        return;
                    }
                    Language::sendMessage($player, "settings-no-have-permission");
                    break;
            }
        });
        $form->setTitle(Language::get($player, "settings-friends-title-form"));
        $form->addButton(Language::get($player, "settings-friends-addbutton-form"), -1, "", "add");
        $form->addButton(Language::get($player, "settings-friends-removebutton-form"), -1, "", "remove");
        $player->sendForm($form);
    }
}
