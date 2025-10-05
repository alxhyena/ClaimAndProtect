<?php

declare(strict_types=1);

namespace xeonch\ClaimAndProtect;

use onebone\economyland\EconomyLand;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;
use xeonch\ClaimAndProtect\commands\ClaimAndProtectCommand;
use xeonch\ClaimAndProtect\events\CheckEvent;
use xeonch\ClaimAndProtect\events\EventListener;
use xeonch\ClaimAndProtect\libs\commando\PacketHooker;
use xeonch\ClaimAndProtect\libs\multieconomy\MultiEconomy;
use xeonch\ClaimAndProtect\libs\multieconomy\providers\EconomyProvider;
use xeonch\ClaimAndProtect\manager\LandManager;
use xeonch\ClaimAndProtect\sessions\PlayerSession;
use xeonch\ClaimAndProtect\utils\Configuration;
use xeonch\ClaimAndProtect\utils\Language;

class Main extends PluginBase
{
    use SingletonTrait;

    private static EconomyProvider $multiEconomy;
    private array $playerSessions = [];
    private static ?EconomyLand $ecoLand;

    private const RESOURCES = ["language/en_US.yml" => false, "language/id_ID.yml" => false];

    public function onLoad(): void
    {
        $this->setInstance($this);
        $this->createFolder();
        foreach (self::RESOURCES as $file => $r) $this->saveResource($file, $r);
    }

    public function onEnable(): void
    {
        Configuration::init($this->getConfig());
        Language::init($this);
        MultiEconomy::init();
        CheckEvent::init($this);

        $this->saveDefaultConfig();
        if (!PacketHooker::isRegistered()) {
            PacketHooker::register($this);
        }
        self::$multiEconomy = MultiEconomy::getProvider($this->getConfig()->get("economy"));
        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
        $this->getServer()->getCommandMap()->register("claimandprotect", new ClaimAndProtectCommand($this, "claimandprotect", "Claim your land and protect", ["cnp"]));
        if (!class_exists(EconomyLand::class)) {
            $this->getServer()->getLogger()->info("Economy land not found, plugin is running safely");
            return;
        }

        self::$ecoLand = EconomyLand::getInstance();
        $config = $this->getConfig()->get("economy-land");

        try {
            if ($config === "disable") {
                self::$ecoLand->getServer()->getPluginManager()->disablePlugin(self::$ecoLand);
                $this->getServer()->getLogger()->info("Economy land disabled");
            } elseif ($config === "enable") {
                $this->getServer()->getLogger()->alert("Economy land enabled");
            } else {
                throw new \InvalidArgumentException("Invalid economy-land config value: $config");
            }
        } catch (\Exception $e) {
            $this->getServer()->getLogger()->error($e->getMessage());
        }
    }

    public function reload(): void
    {
        Language::init($this);
        Configuration::init($this->getConfig(), true);
    }

    public static function getEconomyLand(): ?EconomyLand
    {
        return self::$ecoLand ?? null;
    }

    public static function getEconomy(): EconomyProvider
    {
        return self::$multiEconomy;
    }

    public function createFolder()
    {
        $paths = [
            $this->getDataFolder() . "/lands/",
            $this->getDataFolder() . "/migration/"
        ];
        foreach ($paths as $path) {
            if (!is_dir($path)) {
                mkdir($path, 0777, true);
            }
        }
    }

    public function getPlayerSession(Player $player): PlayerSession
    {
        $playerName = $player->getName();
        if (!isset($this->playerSessions[$playerName])) {
            $this->playerSessions[$playerName] = new PlayerSession($player);
        }
        return $this->playerSessions[$playerName];
    }

    public function removePlayerSession(Player $player): void
    {
        $playerName = $player->getName();
        unset($this->playerSessions[$playerName]);
    }
}
