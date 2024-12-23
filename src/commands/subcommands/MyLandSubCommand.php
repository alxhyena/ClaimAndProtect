<?php

declare(strict_types=1);

namespace xeonch\ClaimAndProtect\commands\subcommands;

use xeonch\ClaimAndProtect\libs\commando\args\RawStringArgument;
use xeonch\ClaimAndProtect\libs\commando\BaseSubCommand;
use xeonch\ClaimAndProtect\libs\commando\constraint\InGameRequiredConstraint;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use xeonch\ClaimAndProtect\forms\type\MyLandForm;
use xeonch\ClaimAndProtect\utils\Language;

class MyLandSubCommand extends BaseSubCommand
{
    protected function prepare(): void
    {
        $this->setPermission("claimandprotect.command.myland");
        $this->addConstraint(new InGameRequiredConstraint($this));
        $this->registerArgument(0, new RawStringArgument("owner", true));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if ($sender instanceof Player) {
            if (isset($args['owner'])) {
                if ($sender->hasPermission("claimandprotect.admin.myland")) {
                    (new MyLandForm())->open($sender, $args['owner']);
                } elseif ($args['owner'] == $sender->getName()){
                    (new MyLandForm())->open($sender, $args['owner']);
                } else {
                    Language::sendMessage($sender, "no-have-permission-admin");
                    return;
                }
            } else {
                (new MyLandForm())->open($sender, $sender->getName());
            }
        }
    }
}
