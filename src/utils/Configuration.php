<?php

declare(strict_types=1);

namespace xeonch\ClaimAndProtect\utils;

use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class Configuration {

    private static string $prefix = "[&bClaim And Protect&r]";
    private static string $defaultLang = "en_US";
    private static string|int $maxFriends = 5;
    private static string|int $limitLand = 5;
    private static string|int $pricePerBlock = 100;
    private static float|int $percentSold = 10;
    private static array $blacklistWorld = [];

    public static function init(Config $config, bool $reload = false) 
    {
        if ($reload) $config->reload();
        self::$prefix = TextFormat::colorize($config->get("prefix"));
        self::$defaultLang = $config->get("default-language");
        self::$maxFriends = $config->get("cnp-settings")['max-friends'];
        self::$limitLand = $config->get("cnp-settings")['player-limit-land'];
        self::$pricePerBlock = $config->get("cnp-settings")['price-per-block'];
        self::$blacklistWorld = $config->get("cnp-settings")['blacklist-world'];
        self::$percentSold = $config->get("cnp-settings")['percent-sold'];
    }

    public static function getPrefix(): string
    {
        return self::$prefix;
    }

    public static function getDefaultLang(): string
    {
        return self::$defaultLang;
    }

    public static function getMaxFriends(): int|string
    {
        return self::$maxFriends;
    }

    public static function getPrice(): int|string
    {
        return self::$pricePerBlock;
    }

    public static function getBlackListWorld():array
    {
        return self::$blacklistWorld;
    }

    public static function getPlayerLimitLand(): string|int
    {
        return self::$limitLand;
    }

    public static function getPercentSold(): string|int
    {
        return self::$percentSold;
    }
}