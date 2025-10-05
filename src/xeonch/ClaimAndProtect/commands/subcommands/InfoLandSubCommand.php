<?php

declare(strict_types=1);

namespace xeonch\ClaimAndProtect\commands\subcommands;

use xeonch\ClaimAndProtect\libs\commando\args\IntegerArgument;
use xeonch\ClaimAndProtect\libs\commando\args\RawStringArgument;
use xeonch\ClaimAndProtect\libs\commando\BaseSubCommand;
use xeonch\ClaimAndProtect\libs\commando\constraint\InGameRequiredConstraint;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use xeonch\ClaimAndProtect\forms\type\InfoLandForm;
use xeonch\ClaimAndProtect\forms\type\MyLandForm;
use xeonch\ClaimAndProtect\manager\LandManager;
use xeonch\ClaimAndProtect\utils\Language;

class InfoLandSubCommand extends BaseSubCommand
{
    protected function prepare(): void
    {
        $this->setPermission("claimandprotect.command.infoland");
        $this->addConstraint(new InGameRequiredConstraint($this));
        $this->registerArgument(0, new IntegerArgument("id"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if ($sender instanceof Player) {
            if (isset($args['id'])) {
                if (is_numeric($args['id'])) {
                    $landData = ($landManager = new LandManager())->getLand($args['id']);
                    if ($landData !== null){
                        (new InfoLandForm())->open($sender, $args["id"], $landData);
                    } else {
                        $sender->sendMessage(str_replace("{ID}", (string)$args['id'], Language::get($sender, "info-land-not-found", true)));
                    }
                } else {
                    Language::sendMessage($sender, "info-land-invalid-command");
                }
            } else {
                $this->sendUsage();
            }
        }
    }
}
