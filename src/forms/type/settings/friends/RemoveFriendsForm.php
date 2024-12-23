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

class RemoveFriendsForm
{

    public function open(Player $player, int $landId): void
    {
        $landManager = new LandManager();
        $landData = $landManager->getLand($landId);

        if ($landData === null) {
            $message = str_replace("{ID}", (string)$landId, Language::get($player, "command-settings-id-not-exist", true));
            $player->sendMessage($message);
            return;
        }

        $ownerName = $landData["owner"];

        $currentMembers = array_filter($landData["member"], function ($name) use ($ownerName) {
            return $name !== $ownerName;
        });

        $currentMembers = array_values($currentMembers);

        if (empty($currentMembers)) {
            $player->sendMessage(Language::get($player, "no-friends-to-remove", true));
            return;
        }

        $form = new CustomForm(function (Player $player, $data) use ($landId, $landManager, $landData, $currentMembers) {
            if ($data === null) {
                return;
            }

            $selectedMemberName = $currentMembers[$data[0]];
            if (!in_array($selectedMemberName, $landData["member"])) {
                $message = str_replace("{NAME}", (string)$selectedMemberName, Language::get($player, "friend-not-found", true));
                $player->sendMessage($message);
                return;
            }

            $landData["member"] = array_values(array_filter($landData["member"], function ($member) use ($selectedMemberName) {
                return $member !== $selectedMemberName;
            }));
            $landManager->saveLand($landId, $landData);
            $message = str_replace("{NAME}", (string)$selectedMemberName, Language::get($player, "friend-removed", true));
            $player->sendMessage($message);
        });

        $form->setTitle(Language::get($player, "settings-friends-title-form"));
        $form->addDropdown(Language::get($player, "select-player-remove"), $currentMembers);
        $player->sendForm($form);
    }
}
