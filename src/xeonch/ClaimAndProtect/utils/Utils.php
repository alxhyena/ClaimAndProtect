<?php

declare(strict_types=1);

namespace xeonch\ClaimAndProtect\utils;

use pocketmine\plugin\Plugin;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\Config;
use pocketmine\world\Position;
use pocketmine\world\World;
use xeonch\ClaimAndProtect\Main;

class Utils
{

    public static function prefixMessage($string): string
    {
        return str_replace("&", "\xc2\xa7", Configuration::getPrefix() . " " . $string);
    }

    public static function checkConfig(Main $plugin, Config $config, string $key, int $version): void
    {
        if ($config->get($key) != $version) {
            $path = $config->getPath();
            $info = pathinfo($path);

            $oldFile = $info["filename"] . "_old." . $info["extension"];
            rename($path, $info["dirname"] . "/" . $oldFile);

            $configDir = str_replace($plugin->getDataFolder(), "", $path);

            $plugin->saveResource($configDir);
            $message = "Your {$info["basename"]} file is outdated. Your old {$info["basename"]} has been saved as $oldFile and a new {$info["basename"]} file has been created. Please update accordingly.";

            $plugin->getScheduler()->scheduleDelayedTask(
                new ClosureTask(function () use ($plugin, $message): void {
                    $plugin->getLogger()->critical($message);
                }),
                1
            );
        }
    }
}
