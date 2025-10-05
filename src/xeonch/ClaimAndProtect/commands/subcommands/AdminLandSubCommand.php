<?php

declare(strict_types=1);

namespace xeonch\ClaimAndProtect\commands\subcommands;

use xeonch\ClaimAndProtect\libs\commando\args\RawStringArgument;
use xeonch\ClaimAndProtect\libs\commando\BaseSubCommand;
use xeonch\ClaimAndProtect\libs\commando\constraint\InGameRequiredConstraint;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use xeonch\ClaimAndProtect\forms\type\admin\AdminForm;
use xeonch\ClaimAndProtect\forms\type\MyLandForm;
use xeonch\ClaimAndProtect\forms\type\remove\RemoveLandForm;
use xeonch\ClaimAndProtect\utils\Language;

class AdminLandSubCommand extends BaseSubCommand
{
    protected function prepare(): void
    {
        $this->setPermission("claimandprotect.command.admin");
        $this->addConstraint(new InGameRequiredConstraint($this));
        $this->registerArgument(0, new RawStringArgument("owner", false));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if ($sender instanceof Player) {
            if (isset($args['owner'])) {
                if ($sender->hasPermission("claimandprotect.command.admin")) {
                    (new AdminForm())->open($sender, $args['owner']);
                } 
            } else {
                $this->sendUsage();
            }
        }
    }
}
