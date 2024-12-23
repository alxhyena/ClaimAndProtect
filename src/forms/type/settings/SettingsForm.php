<?php

declare(strict_types=1);

namespace xeonch\ClaimAndProtect\forms\type\settings;

use jojoe77777\FormAPI\CustomForm;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use xeonch\ClaimAndProtect\Main;
use xeonch\ClaimAndProtect\manager\LandManager;
use xeonch\ClaimAndProtect\utils\Language;

class SettingsForm
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
            $landData["fly"] = (bool)$data[0];
            $landData["break"] = (bool)$data[1];
            $landData["place"] = (bool)$data[2];
            $landData["pvp"] = (bool)$data[3];
            $landData["interact"] = (bool)$data[4];
            $landData["drop"] = (bool)$data[5];
            $landManager->saveLand($landId, $landData);
            $player->sendMessage(str_replace(["{ID}"], [$landId], Language::get($player, "settings-succes", true)));
        });
        $form->setTitle(Language::get($player, "settings-title-form"));
        $form->addToggle(Language::get($player, "settings-fly-form"), $landData["fly"]);
        $form->addToggle(Language::get($player, "settings-break-form"), $landData["break"]);
        $form->addToggle(Language::get($player, "settings-place-form"), $landData["place"]);
        $form->addToggle(Language::get($player, "settings-pvp-form"), $landData["pvp"]);
        $form->addToggle(Language::get($player, "settings-interact-form"), $landData["interact"]);
        $form->addToggle(Language::get($player, "settings-drop-form"), $landData["drop"]);
        $player->sendForm($form);
    }
}
