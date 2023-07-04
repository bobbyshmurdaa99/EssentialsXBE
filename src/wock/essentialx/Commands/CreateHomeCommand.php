<?php

namespace wock\essentialx\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\PluginOwned;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use wock\essentialx\EssentialsX;
use wock\essentialx\Managers\HomeManager;
use wock\essentialx\Managers\WarpManager;

class CreateHomeCommand extends Command implements PluginOwned {

    public HomeManager $homeManager;

    /** @var EssentialsX */
    public EssentialsX $plugin;

    public function __construct(EssentialsX $plugin, HomeManager $homeManager){
        parent::__construct("sethome", "Teleport to your homes", "/sethome <home>", ["createhome"]);
        $this->setPermission("essentialsx.sethome");
        $this->setPermissionMessage(EssentialsX::NOPERMISSION);
        $this->homeManager = $homeManager;
        $this->plugin = $plugin;
    }

    /**
     * @throws \JsonException
     * @throws \Exception
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if (!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::RED . "This command can only be used in-game.");
            return false;
        }

        if (count($args) === 0) {
            $sender->sendMessage(TextFormat::RED . "Usage: /sethome <home-name>");
            return false;
        }

        $homeName = $args[0];
        $player = $sender;

        $this->homeManager->createHome($player, $homeName);
        return true;
    }

    public function getOwningPlugin(): EssentialsX
    {
        return $this->plugin;
    }
}