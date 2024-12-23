<?php

declare(strict_types=1);

namespace xeonch\ClaimAndProtect\forms\type\remove;

use jojoe77777\FormAPI\ModalForm;
use pocketmine\player\Player;
use xeonch\ClaimAndProtect\Main;
use xeonch\ClaimAndProtect\manager\LandManager;
use xeonch\ClaimAndProtect\utils\Configuration;
use xeonch\ClaimAndProtect\utils\Language;
use xeonch\ClaimAndProtect\utils\Math;

class ConfirmationRemoveLand
{

    public function open(Player $player, $id, $name = null)
    {
        $landData = (new LandManager())->getLand($id);
        $disc = Math::applyDiscountWithPercentage((int)$landData['price'], (int)Configuration::getPercentSold());
        $moneyEarned = (int)$landData['price'] - (int)$disc;
        $form = new ModalForm(function (Player $player, $data) use ($id, $moneyEarned, $name) {
            if ($data == null) return;
            if ($data) {
                $landManager = new LandManager();
                Main::getEconomy()->giveMoney($player, (float)$moneyEarned, function (bool $success) use ($player, $moneyEarned, $landManager, $id) {
                    if (!$success) {
                        Language::sendMessage($player, "generic-error");
                        return;
                    }
                    $msg = str_replace(["{ID}", "{EARN}"], [$id, $moneyEarned], Language::get($player, "confirm-remove-success", true));
                    $player->sendMessage($msg);
                    $landManager->deleteLand($id);
                });
            } else {
                if ($name === null) {
                    return;
                }
                (new RemoveLandForm())->open($player, $name);
            }
        });
        $form->setTitle(Language::get($player, "confirm-remove-title-form"));
        $content = str_replace(
            ["{ID}", "{PRICE}", "{EARN}", "{PERCENT}"],
            [
                $id,
                (string)$landData['price'],
                (string)$moneyEarned,
                (string)Configuration::getPercentSold()
            ],
            Language::get($player, "confirm-remove-content-form")
        );
        $form->setContent($content);
        $form->setButton1(Language::get($player, "confirm-remove-button1-form"));
        $form->setButton2(Language::get($player, "confirm-remove-button2-form"));
        $player->sendForm($form);
    }
}
