<?php

declare(strict_types=1);

namespace xeonch\ClaimAndProtect\forms\type\admin;

use jojoe77777\FormAPI\CustomForm;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\player\Player;
use xeonch\ClaimAndProtect\manager\LandManager;
use xeonch\ClaimAndProtect\utils\Language;

class AdminForm
{
    public function open(Player $player, $name): void
    {
        $landManager = new LandManager();
        $lands = $landManager->getLandsByOwner($name);

        if (empty($lands)) {
            $player->sendMessage(str_replace("{NAME}", $name, Language::get($player, "no-land-owned", true)));
            return;
        }
        $form = new SimpleForm(function (Player $player, $data) use ($lands, $name) {
            if ($data === null) return;

            $keys = array_keys($lands);
            if (!isset($keys[$data])) {
                $player->sendMessage("Data error");
                return;
            }

            $landId = $keys[$data];
            $landData = $lands[$landId];
        });

        $form->setTitle(str_replace("{NAME}", $name, Language::get($player, "admin-land-title")));
        $form->setContent(Language::get($player, "admin-land-list-content"));
        foreach ($lands as $landId => $landData) {
            $button = str_replace(["{ID}", "{OWNER}", "{LINE}"], [(string)$landId, $landData['owner'], "\n"], Language::get($player, "admin-land-button-form"));
            $form->addButton($button);
        }
        $player->sendForm($form);
    }
}
