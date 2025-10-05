<?php

declare(strict_types=1);

namespace xeonch\ClaimAndProtect\forms\type\claim;

use jojoe77777\FormAPI\SimpleForm;
use pocketmine\player\Player;
use xeonch\ClaimAndProtect\Main;
use xeonch\ClaimAndProtect\manager\LandManager;
use xeonch\ClaimAndProtect\utils\Configuration;
use xeonch\ClaimAndProtect\utils\Language;
use xeonch\ClaimAndProtect\utils\Math;

class ClaimForm {

    public function open(Player $player): void
    {
        $session = Main::getInstance()->getPlayerSession($player);
        $wide = Math::calculateArea($session->getFirstPos(), $session->getSecondPos());
        $price = (int)$wide * (int)Configuration::getPrice();
        $pos1 = $session->getFirstPos();
        $pos2 = $session->getSecondPos();
        Main::getEconomy()->getMoney($player, function (int|float $balance) use ($player, $session, $wide, $price, $pos1, $pos2) {
            $playerBalance = $balance;
            $form = new SimpleForm(function (Player $player, $data) use ($session, $wide, $price, $playerBalance, $pos1, $pos2) {
                if ($data === null) return;

                if ($data == "claim") {
                    if ($playerBalance < $price) {
                        Language::sendMessage($player, "not-enough-money");
                        return;
                    }
                    $firstPos = explode(",", $pos1);
                    $secondPos = explode(",", $pos2);
                    $world1 = $firstPos[3];
                    $world2 = $secondPos[3];
                    if ($world1 !== $world2){
                        Language::sendMessage($player, "different-world");
                        return;
                    }
                    Main::getEconomy()->takeMoney($player, $price, function (bool $success) use ($player, $session, $price, $wide) {
                        if (!$success) {
                            Language::sendMessage($player, "generic-error");
                            return;
                        }

                        $landManager = new LandManager();
                        $landId = $landManager->generateLandId();
                        $landData = [
                            "owner" => $player->getName(),
                            "pos" => [
                                "first" => $session->getFirstPos(),
                                "second" => $session->getSecondPos()
                            ],
                            "member" => [$player->getName()],
                            "permission-member"=> [
                                "teleport" => true,
                                "settings" => false,
                                "addfriend" => false,
                                "removefriend" => false
                            ],
                            "wide" => $wide,
                            "price" => $price,
                            "world" => $player->getWorld()->getFolderName(),
                            "fly" => true,
                            "break" => false,
                            "place" => false,
                            "pvp" => true,
                            "interact" => false,
                            "drop" => false
                        ];
                        Main::getInstance()->removePlayerSession($player);
                        $landManager->saveLand($landId, $landData);

                        $player->sendMessage(str_replace(
                            ["{ID}"],
                            [$landId],
                            Language::get($player, "claim-succes", true)
                        ));
                    });
                }
            });
            $replace = str_replace(
                ["{LINE}", "{WIDE}", "{PRICE}", "{POS1}", "{POS2}", "{MONEY}"],
                ["\n", $wide, $price, $pos1, $pos2, $playerBalance],
                Language::get($player, "claim-content-form")
            );

            $form->setTitle(Language::get($player, "claim-title-form"));
            $form->setContent($replace);
            $form->addButton(Language::get($player, "claim-button-form"), -1, "", "claim");
            $player->sendForm($form);
        });
    }
}