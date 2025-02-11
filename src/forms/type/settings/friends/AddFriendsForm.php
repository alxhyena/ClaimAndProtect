<?php

declare(strict_types=1);

namespace xeonch\ClaimAndProtect\forms\type\settings\friends;

use jojoe77777\FormAPI\CustomForm;
use pocketmine\player\Player;
use xeonch\ClaimAndProtect\Main;
use xeonch\ClaimAndProtect\manager\LandManager;
use xeonch\ClaimAndProtect\utils\Configuration;
use xeonch\ClaimAndProtect\utils\Language;

class AddFriendsForm
{

    public $playerList = [];

    public function open(Player $player, int $landId): void
    {
        $landManager = new LandManager();
        $landData = $landManager->getLand($landId);

        if ($landData === null) {
            $message = str_replace("{ID}", (string)$landId, Language::get($player, "command-settings-id-not-exist", true));
            $player->sendMessage($message);
            return;
        }
        if (is_numeric(Configuration::getMaxFriends())) {
            $maxFriends = (int)Configuration::getMaxFriends() + 1;
            if ($maxFriends !== "infinity" && (count($landData["member"]) + 1) >= (int)$maxFriends) {
                $message = Language::get($player, "max-friends-reached", true);
                $player->sendMessage($message);
                return;
            }
        }
        $list = [];
        foreach (Main::getInstance()->getServer()->getOnlinePlayers() as $p) {
            if ($p->getName() !== $player->getName()) {
                $list[] = $p->getName();
            }
        }
        $this->playerList[$player->getName()] = $list;
        if (empty($this->playerList[$player->getName()])) {
            $player->sendMessage(Language::get($player, "no-online-players", true));
            return;
        }

        $form = new CustomForm(function (Player $player, $data) use ($landId, $landManager, $landData) {
            if ($data === null) {
                return;
            }
            $selectedPlayerName = $this->playerList[$player->getName()][$data[0]];
            if (in_array($selectedPlayerName, $landData["member"])) {
                $message = str_replace("{NAME}", (string)$selectedPlayerName, Language::get($player, "already-friend", true));
                $player->sendMessage($message);
                return;
            }
            $landData["member"][] = $selectedPlayerName;
            $landManager->saveLand($landId, $landData);
            $message = str_replace("{NAME}", (string)$selectedPlayerName, Language::get($player, "friend-added", true));
            $player->sendMessage($message);
        });
        $form->setTitle(Language::get($player, "settings-friends-title-form"));
        $form->addDropdown(Language::get($player, "select-player-add"), $this->playerList[$player->getName()]);
        $player->sendForm($form);
    }
}
