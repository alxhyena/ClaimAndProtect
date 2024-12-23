<?php

declare(strict_types=1);

namespace xeonch\ClaimAndProtect\commands\subcommands;

use xeonch\ClaimAndProtect\libs\commando\BaseSubCommand;
use xeonch\ClaimAndProtect\libs\commando\constraint\InGameRequiredConstraint;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\TextFormat;
use xeonch\ClaimAndProtect\Main;
use xeonch\ClaimAndProtect\utils\Language;

class SetSecondPositionSubCommand extends BaseSubCommand
{

    protected function prepare(): void
    {
        $this->setPermission("claimandprotect.command.setsecond");
        $this->addConstraint(new InGameRequiredConstraint($this));
    }


    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if ($sender instanceof Player) {
            $session = Main::getInstance()->getPlayerSession($sender);
            if ($session === null) {
                $sender->sendMessage(TextFormat::RED . "Session data not found.");
                return;
            }
            if ($session->getSecondSession()){
                Language::sendMessage($sender, "already-second-session");
                return;
            }
            if ($session->getFirtsSession()) {
                Language::sendMessage($sender, "already-first-session");
                return;
            }
            $config = Main::getInstance()->getConfig();
            $session->setSecondSession();
            Language::sendMessage($sender, "ready-second-position");
            if ($config->get("session-timer")["second-session"]) {
                $sender->sendMessage(str_replace("{TIMER}", (string)$config->get("session-timer")["second-timer"], Language::get($sender, "ready-second-position-timer", true)));
                Main::getInstance()->getScheduler()->scheduleDelayedTask(
                    new ClosureTask(
                        function () use ($sender, $session) {
                            if (!$session->getSecondSession()) {
                                return;
                            }
                            $session->setSecondSession(false);
                            Language::sendMessage($sender, "time-expired-second-set");
                        }
                    ),
                    20 * $config->get("session-timer")["second-timer"]
                );
            }
        }
    }
}
