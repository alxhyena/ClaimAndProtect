<?php

declare(strict_types=1);

namespace xeonch\ClaimAndProtect\forms\type;

use jojoe77777\FormAPI\SimpleForm;
use jojoe77777\FormAPI\ModalForm;
use pocketmine\player\Player;
use xeonch\ClaimAndProtect\manager\LandManager;
use xeonch\ClaimAndProtect\utils\Language;

class MyLandForm
{

    public function open(Player $player, $name): void
    {
        $landManager = new LandManager();
        $lands = $landManager->getLandsByOwner($name);

        if (empty($lands)) {
            $player->sendMessage(str_replace("{NAME}", $name, Language::get($player, "no-land-owned", true)));
            return;
        }
        $form = new SimpleForm(function (Player $player, $data) use ($lands) {
            if ($data === null) return;

            $keys = array_keys($lands);
            if (!isset($keys[$data])) {
                $player->sendMessage("Data error");
                return;
            }

            $landId = $keys[$data];
            $landData = $lands[$landId];
            (new InfoLandForm())->open($player, $landId, $landData);
        });

        $form->setTitle(Language::get($player, "my-land-title"));
        $form->setContent(Language::get($player, "my-land-list-content"));
        foreach ($lands as $landId => $landData) {
            $button = str_replace(["{ID}", "{OWNER}", "{LINE}"], [(string)$landId, $landData['owner'], "\n"], Language::get($player, "my-land-button-form"));
            $form->addButton($button);
        }
        $player->sendForm($form);
    }
}
