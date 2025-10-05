<?php

declare(strict_types=1);

namespace xeonch\ClaimAndProtect\forms\type;

use jojoe77777\FormAPI\SimpleForm;
use jojoe77777\FormAPI\ModalForm;
use pocketmine\player\Player;
use xeonch\ClaimAndProtect\manager\LandManager;
use xeonch\ClaimAndProtect\utils\Language;

class InfoLandForm
{
    public function open(Player $player, int $landId, array $landData): void
    {
        $pos1 = $landData['pos']['first'] ?? "Not Found";
        $pos2 = $landData['pos']['second'] ?? "Not Found";
        $world = $landData['world'] ?? "Not Found";
        $owner = $landData['owner'] ?? "Not Found";
        $member = isset($landData['member']) ? implode(", ", $landData['member']) : "Not Found";
        $price = $landData['price'] ?? "Not Found";
        $wide = $landData['wide'] ?? "Not Found";
        $fly = isset($landData['fly']) && $landData['fly'] ? "Enable" : "Disable";
        $break = isset($landData['break']) && $landData['break'] ? "Enable" : "Disable";
        $place = isset($landData['place']) && $landData['place'] ? "Enable" : "Disable";
        $pvp = isset($landData['pvp']) && $landData['pvp'] ? "Enable" : "Disable";
        $interact = isset($landData['interact']) && $landData['interact'] ? "Enable" : "Disable";
        $drop = isset($landData['drop']) && $landData['drop'] ? "Enable" : "Disable";
        $content = str_replace(
            ["{ID}", "{OWNER}", "{POS1}", "{POS2}", "{WORLD}", "{MEMBER}", "{PRICE}", "{WIDE}", "{FLY}", "{BREAK}", "{PLACE}", "{PVP}", "{INTERACT}", "{DROP}", "{LINE}"],
            [
                (string)$landId,
                $owner,
                $pos1,
                $pos2,
                $world,
                $member,
                $price,
                $wide,
                $fly,
                $break,
                $place,
                $pvp,
                $interact,
                $drop,
                "\n"
            ],
            Language::get($player, "info-land-content-form")
        );
        $form = new ModalForm(function (Player $player, $data) use ($landData) {
            if ($data === null) {
                return;
            }
            if ($data) {
                /*(new MyLandForm())->open($player, $landData['owner']);**/
            }
        });
        $form->setTitle(str_replace(["{ID}"], [(string)$landId], Language::get($player, "info-land-title-form")));
        $form->setContent($content);
        $form->setButton1(Language::get($player, "info-land-button1-form"));
        $form->setButton2(Language::get($player, "info-land-button2-form"));
        $player->sendForm($form);
    }
}
