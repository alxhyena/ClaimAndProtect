<?php

declare(strict_types=1);

namespace xeonch\ClaimAndProtect\events;

use Closure;
use pocketmine\block\Block;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityExplodeEvent;
use pocketmine\event\EventPriority;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\player\Player;
use pocketmine\world\Position;
use xeonch\ClaimAndProtect\Main;
use xeonch\ClaimAndProtect\manager\LandManager;
use xeonch\ClaimAndProtect\utils\Configuration;
use xeonch\ClaimAndProtect\utils\Language;

class CheckEvent implements Listener
{

    public function __construct() {}

    public static function init(Main $plugin)
    {
        $checkEvent = new CheckEvent();
        $plugin->getServer()->getPluginManager()->registerEvent("pocketmine\\event\\player\\PlayerInteractEvent", Closure::fromCallable([$checkEvent, 'onInteractEvent']), EventPriority::LOWEST, $plugin);
        $plugin->getServer()->getPluginManager()->registerEvent("pocketmine\\event\\block\\BlockBreakEvent", Closure::fromCallable([$checkEvent, 'onBreakEvent']), EventPriority::LOWEST, $plugin);
        $plugin->getServer()->getPluginManager()->registerEvent("pocketmine\\event\\player\\PlayerDropItemEvent", Closure::fromCallable([$checkEvent, 'onDropEvent']), EventPriority::LOWEST, $plugin);
        $plugin->getServer()->getPluginManager()->registerEvent("pocketmine\\event\\block\\BlockPlaceEvent", Closure::fromCallable([$checkEvent, 'onPlaceEvent']), EventPriority::LOWEST, $plugin);
        $plugin->getServer()->getPluginManager()->registerEvent("pocketmine\\event\\entity\\EntityDamageEvent", Closure::fromCallable([$checkEvent, 'onPvpEvent']), EventPriority::LOWEST, $plugin);
        $plugin->getServer()->getPluginManager()->registerEvent("pocketmine\\event\\player\\PlayerMoveEvent", Closure::fromCallable([$checkEvent, 'onFlyEvent']), EventPriority::LOWEST, $plugin);
        $plugin->getServer()->getPluginManager()->registerEvent("pocketmine\\event\\entity\\EntityExplodeEvent", Closure::fromCallable([$checkEvent, 'onExplodeEvent']), EventPriority::LOWEST, $plugin);
    }

    public function onPlaceEvent(BlockPlaceEvent $event)
    {
        $player = $event->getPlayer();
        foreach ($event->getTransaction()->getBlocks() as [$x, $y, $z, $block]) {
            if ($block instanceof Block) {
                $posBlock = new Position($x, $y, $z, $block->getPosition()->getWorld());
                $landManager = new LandManager();
                $landsInArea = $landManager->getLandsIn($posBlock);
                foreach ($landsInArea as $landId => $landData) {
                    if ($landData['owner'] !== $player->getName()) {
                        if (!$player->hasPermission("claimandprotect.bypass")) {
                            if (!$landData['place'] && !in_array($player->getName(), $landData['member'])) {
                                $event->cancel();
                                $msg = str_replace(["{OWNER}", "{ID}"], [$landData['owner'], $landId], Language::get($player, "land-around-here", true));
                                $player->sendMessage($msg);
                                return;
                            }
                        }
                    }
                }
            }
        }
    }

    public function onBreakEvent(BlockBreakEvent $event)
    {
        $player = $event->getPlayer();
        $block = $event->getBlock();
        $posBlock = new Position($block->getPosition()->getX(), $block->getPosition()->getY(), $block->getPosition()->getZ(), $block->getPosition()->getWorld());
        $landManager = new LandManager();
        $landsInArea = $landManager->getLandsIn($posBlock);
        foreach ($landsInArea as $landId => $landData) {
            if ($landData['owner'] !== $player->getName()) {
                if (!$player->hasPermission("claimandprotect.bypass")) {
                    if (!$landData['break'] && !in_array($player->getName(), $landData['member'])) {
                        $event->cancel();
                        $msg = str_replace(["{OWNER}", "{ID}"], [$landData['owner'], $landId], Language::get($player, "land-around-here", true));
                        $player->sendMessage($msg);
                        return;
                    }
                }
            }
        }
    }

    public function onInteractEvent(PlayerInteractEvent $event)
    {
        $player = $event->getPlayer();
        $block = $event->getBlock()->getSide($event->getFace());
        $posBlock = new Position($block->getPosition()->getX(), $block->getPosition()->getY(), $block->getPosition()->getZ(), $block->getPosition()->getWorld());
        $landManager = new LandManager();
        $landsInArea = $landManager->getLandsIn($posBlock);
        foreach ($landsInArea as $landId => $landData) {
            if ($landData['owner'] !== $player->getName()) {
                if (!$player->hasPermission("claimandprotect.bypass")) {
                    if (!$landData['interact'] && !in_array($player->getName(), $landData['member'])) {
                        $event->cancel();
                        $msg = str_replace(["{OWNER}", "{ID}"], [$landData['owner'], $landId], Language::get($player, "land-around-here", true));
                        $player->sendMessage($msg);
                        return;
                    }
                }
            }
        }
    }

    public function onDropEvent(PlayerDropItemEvent $event)
    {
        $player = $event->getPlayer();
        $pos = new Position($player->getPosition()->getX(), $player->getPosition()->getY(), $player->getPosition()->getZ(), $player->getPosition()->getWorld());
        $landManager = new LandManager();
        $landsInArea = $landManager->getLandsIn($pos);
        foreach ($landsInArea as $landId => $landData) {
            if ($landData['owner'] !== $player->getName()) {
                if (!$player->hasPermission("claimandprotect.bypass")) {
                    if (!$landData['drop'] && !in_array($player->getName(), $landData['member'])) {
                        $event->cancel();
                        $msg = str_replace(["{OWNER}", "{ID}"], [$landData['owner'], $landId], Language::get($player, "land-around-here", true));
                        $player->sendMessage($msg);
                        return;
                    }
                }
            }
        }
    }

    public function onFlyEvent(PlayerMoveEvent $event)
    {
        $player = $event->getPlayer();
        $pos = new Position($player->getPosition()->getX(), $player->getPosition()->getY(), $player->getPosition()->getZ(), $player->getPosition()->getWorld());
        $landManager = new LandManager();
        $landsInArea = $landManager->getLandsIn($pos);
        foreach ($landsInArea as $landId => $landData) {
            if ($landData['owner'] !== $player->getName()) {
                if (!$player->hasPermission("claimandprotect.bypass")) {
                    if (!$landData['fly'] && !in_array($player->getName(), $landData['member'])) {
                        if ($player->isFlying()) {
                            $player->setFlying(false);
                            $msg = str_replace(["{OWNER}", "{ID}"], [$landData['owner'], $landId], Language::get($player, "land-around-here", true));
                            $player->sendMessage($msg);
                            return;
                        }
                    }
                }
            }
        }
    }

    public function onPvpEvent(EntityDamageEvent $event)
    {
        $victim = $event->getEntity();
        if ($event instanceof EntityDamageByEntityEvent) {
            $damager = $event->getDamager();
            if ($victim instanceof Player and $damager instanceof Player) {
                $pos = new Position($victim->getPosition()->getX(), $victim->getPosition()->getY(), $victim->getPosition()->getZ(), $victim->getPosition()->getWorld());
                $landManager = new LandManager();
                $landsInArea = $landManager->getLandsIn($pos);
                foreach ($landsInArea as $landId => $landData) {
                    if ($landData['owner'] !== $damager->getName()) {
                        if (!$damager->hasPermission("claimandprotect.bypass")) {
                            if (!$landData['pvp'] && !in_array($damager->getName(), $landData['member'])) {
                                $event->cancel();
                                $msg = str_replace(["{OWNER}", "{ID}"], [$landData['owner'], $landId], Language::get($damager, "land-around-here", true));
                                $damager->sendMessage($msg);
                                return;
                            }
                        }
                    }
                }
            }
        }
    }

    public function onExplodeEvent(EntityExplodeEvent $event)
    {
        $blocks = $event->getBlockList();
        $landManager = new LandManager();
        foreach ($blocks as $block) {
            $pos = $block->getPosition();
            if ($landManager->isInArea($pos)) {
                if (!Configuration::getExplosion()) {
                    $event->cancel();
                }
            }
        }
    }
}
