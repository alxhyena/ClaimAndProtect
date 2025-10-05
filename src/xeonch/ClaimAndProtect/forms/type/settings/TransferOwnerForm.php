<?php

declare(strict_types=1);

namespace xeonch\ClaimAndProtect\forms\type\settings;

use jojoe77777\FormAPI\CustomForm;
use pocketmine\player\Player;
use xeonch\ClaimAndProtect\manager\LandManager;
use xeonch\ClaimAndProtect\utils\Language;

class TransferOwnerForm
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
            $player->sendMessage(Language::get($player, "no-members-to-transfer", true));
            return;
        }

        $form = new CustomForm(function (Player $player, $data) use ($landId, $landManager, $landData, $currentMembers, $ownerName) {
            if ($data === null) {
                return;
            }

            $selectedIndex = (int)$data[0];
            if (!isset($currentMembers[$selectedIndex])) {
                $player->sendMessage(Language::get($player, "invalid-member-selected", true));
                return;
            }

            $newOwner = $currentMembers[$selectedIndex];

            $landData["owner"] = $newOwner;

            if (!in_array($newOwner, $landData["member"])) {
                $landData["member"][] = $newOwner;
            }

            $landManager->saveLand($landId, $landData);

            $message = str_replace(
                ["{OLD_OWNER}", "{NEW_OWNER}"],
                [$ownerName, $newOwner],
                Language::get($player, "transfer-owner-success", true)
            );
            $player->sendMessage($message);
        });

        $form->setTitle(Language::get($player, "transfer-owner-title-form"));
        $form->addDropdown(Language::get($player, "select-new-owner"), $currentMembers);
        $player->sendForm($form);
    }
}
