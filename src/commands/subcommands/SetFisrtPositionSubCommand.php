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

class SetFisrtPositionSubCommand extends BaseSubCommand
{

    protected function prepare(): void
    {
        $this->setPermission("claimandprotect.command.setfirst");
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
            $session->setFirstSession();
            Language::sendMessage($sender, "ready-first-position");
            if ($config->get("session-timer")["first-session"]) {
                $sender->sendMessage(str_replace("{TIMER}", (string)$config->get("session-timer")["first-timer"], Language::get($sender, "ready-first-position-timer", true)));
                Main::getInstance()->getScheduler()->scheduleDelayedTask(
                    new ClosureTask(
                        function () use ($sender, $session) {
                            if (!$session->getFirtsSession()){
                                return;
                            }
                            $session->setFirstSession(false);
                            Language::sendMessage($sender, "time-expired-first-set");
                        }
                    ),
                    20 * $config->get("session-timer")["first-timer"]
                );
            }
        }
    }
}
