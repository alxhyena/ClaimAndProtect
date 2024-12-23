<?php

declare(strict_types=1);

namespace xeonch\ClaimAndProtect\events;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\utils\TextFormat;
use xeonch\ClaimAndProtect\Main;
use xeonch\ClaimAndProtect\manager\LandManager;
use xeonch\ClaimAndProtect\utils\Configuration;
use xeonch\ClaimAndProtect\utils\Language;
use xeonch\ClaimAndProtect\utils\Math;
use xeonch\ClaimAndProtect\utils\Utils;

class EventListener implements Listener
{

    public function onInteract(PlayerInteractEvent $event)
    {
        $player = $event->getPlayer();
        $block = $event->getBlock();
        $session = Main::getInstance()->getPlayerSession($player);
        if ($event->getAction() == PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
            if ($session->getFirtsSession() || $session->getSecondSession()) {
                if (in_array($player->getWorld()->getFolderName(), Configuration::getBlackListWorld())) {
                    Language::sendMessage($player, "blacklist-world");
                    $session->setFirstSession(false);
                    $session->setSecondSession(false);
                    $event->cancel();
                    return;
                }
            }
            if ($session->getFirtsSession()) {
                $event->cancel();
                $session->setFirstPos(Math::convertPosToString($block->getPosition()));
                $session->setFirstSession(false);
                $player->sendMessage(str_replace(["{PLAYER}", "{POSITION}"], [$player->getName(), Math::convertPosToString($block->getPosition())], Language::get($player, "saved-first-position", true)));
                if ($session->getSecondPos() !== null) {
                    $firstPos = explode(",", $session->getFirstPos());
                    $secondPos = explode(",", $session->getSecondPos());
                    $world1 = $firstPos[3];
                    $world2 = $secondPos[3];
                    if ($world1 !== $world2) {
                        $session->setFirstPos(null);
                        Language::sendMessage($player, "different-world");
                        return;
                    }
                    $wide = Math::calculateArea($session->getFirstPos(), $session->getSecondPos());
                    $price = (int)$wide * (int)Configuration::getPrice();
                    $player->sendMessage(str_replace(["{PRICE}", "{WIDE}"], [(string)$price, (string)$wide], Language::get($player, "done-set-position", true)));
                }
            } elseif ($session->getSecondSession()) {
                $event->cancel();
                $session->setSecondPos(Math::convertPosToString($block->getPosition()));
                $session->setSecondSession(false);
                $player->sendMessage(str_replace(["{PLAYER}", "{POSITION}"], [$player->getName(), Math::convertPosToString($block->getPosition())], Language::get($player, "saved-second-position", true)));
                if ($session->getFirstPos() !== null) {
                    $firstPos = explode(",", $session->getFirstPos());
                    $secondPos = explode(",", $session->getSecondPos());
                    $world1 = $firstPos[3];
                    $world2 = $secondPos[3];
                    if ($world1 !== $world2) {
                        $session->setSecondPos(null);
                        Language::sendMessage($player, "different-world");
                        return;
                    }
                    $wide = Math::calculateArea($session->getFirstPos(), $session->getSecondPos());
                    $price = (int)$wide * (int)Configuration::getPrice();
                    $player->sendMessage(str_replace(["{PRICE}", "{WIDE}"], [(string)$price, (string)$wide], Language::get($player, "done-set-position", true)));
                }
            }
        }
    }

    public function onPlayerQuit(PlayerQuitEvent $event): void
    {
        $player = $event->getPlayer();
        Main::getInstance()->removePlayerSession($player);
    }

    public function onPlayerMove(PlayerMoveEvent $event): void
    {
        $player = $event->getPlayer();
        $landManager = new LandManager();
        $landsInArea = $landManager->getLandsIn($player->getPosition());
        foreach ($landsInArea as $landId => $landData) {
            $player->sendTip("You are in land with ID: " . $landId);
        }
    }



    public function onJoin(PlayerJoinEvent $event)
    {
        $player = $event->getPlayer();
        $session = Main::getInstance()->getPlayerSession($player);
        $session->init();
    }
}
