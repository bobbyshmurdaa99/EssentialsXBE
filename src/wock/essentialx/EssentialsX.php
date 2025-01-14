<?php

namespace wock\essentialx;

use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\item\enchantment\StringToEnchantmentParser;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use wock\essentialx\{API\WarpAPI,
    Commands\AddWarpCommand,
    Commands\AnvilCommand,
    Commands\BackCommand,
    Commands\BanCommand,
    Commands\BanIPCommand,
    Commands\BanLookupCommand,
    Commands\CondenseCommand,
    Commands\CreateHomeCommand,
    Commands\DeleteWarpCommand,
    Commands\ExpCommand,
    Commands\FeedCommand,
    Commands\FlyCommand,
    Commands\GamemodeCommand,
    Commands\GiveCommand,
    Commands\HealCommand,
    Commands\HomeCommand,
    Commands\HomesCommand,
    Commands\ItemDBCommand,
    Commands\KitCommand,
    Commands\NearCommand,
    Commands\ReloadCommand,
    Commands\RemoveHomeCommand,
    Commands\SpawnCommand,
    Commands\TempBanCommand,
    Commands\WarpCommand,
    Commands\WarpsCommand,
    Enchantments\BaneOfArthropodsEnchantment,
    Enchantments\DepthStriderEnchantment,
    Enchantments\FortuneEnchantment,
    Enchantments\LootingEnchantment,
    Enchantments\SmiteEnchantment,
    Events\EssentialsXEvent,
    Commands\AfkCommand,
    Events\VanillaEnchanatmentEvent,
    Managers\HomeManager,
    Managers\WarpManager,
    Utils\DatabaseConnection};
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

// TEST
class EssentialsX extends PluginBase {

    /** @var EssentialsX */
    private static EssentialsX $instance;

    public const NOPERMISSION = TextFormat::DARK_RED . "You do not have access to that command.";

    /** @var WarpAPI */
    private WarpAPI $api;

    /** @var WarpManager */
    private WarpManager $warpManager;

    public function onLoad(): void
    {
        self::$instance = $this;
        $enchants = [
            new FortuneEnchantment(),
            new LootingEnchantment(),
            new SmiteEnchantment(),
            new BaneOfArthropodsEnchantment(),
            new DepthStriderEnchantment()
        ];
        foreach ($enchants as $enchant) {
            EnchantmentIdMap::getInstance()->register($enchant->getMcpeId(), $enchant);
            StringToEnchantmentParser::getInstance()->register($enchant->getId(), fn() => $enchant);
        }
    }

    /**
     * @throws \Exception
     */
    public function onEnable(): void
    {
        $this->saveDefaultConfig();
        $this->registerCommands();
        $this->unregisterCommands();
        $this->registerEvents();
        $config = new Config($this->getDataFolder() . "warps.json", Config::JSON);
        $this->api = new WarpAPI($config);
        $this->warpManager = new WarpManager($config);
    }

    public function onDisable(): void
    {

    }

    /**
     * @throws \Exception
     */
    public function registerCommands() {
        $config = new Config($this->getDataFolder() . "warps.json", Config::JSON);
        $databaseConnection = new DatabaseConnection('db4free.net', 'startesting', 'startesting123', 'startesting', 3306);
        $this->getServer()->getCommandMap()->registerAll("essentialsx", [
            new AfkCommand($this),
            new AnvilCommand($this),
            new BackCommand($this, new EssentialsXEvent()),
            new BanCommand($this),
            new ExpCommand($this),
            new GamemodeCommand(),
            new FlyCommand(),
            new SpawnCommand($this),
            new BanIPCommand($this),
            new CondenseCommand($this),
            new ReloadCommand(),
            new KitCommand(),
            new HealCommand(),
            new BanLookupCommand($this),
            new NearCommand($this),
            new FeedCommand(),
            new ItemDBCommand(),
            new GiveCommand(),
            //new TempBanCommand($this),
            new WarpCommand($this, new WarpManager($config)),
            new AddWarpCommand($this, new WarpManager($config)),
            new WarpsCommand($this, new WarpManager($config)),
            new DeleteWarpCommand($this, new WarpManager($config)),
            new HomeCommand($this, new HomeManager($databaseConnection)),
            new RemoveHomeCommand($this, new HomeManager($databaseConnection)),
            new CreateHomeCommand($this, new HomeManager($databaseConnection)),
            new HomesCommand($this, new HomeManager($databaseConnection)),
        ]);
    }

    public function unregisterCommands() {
        Server::getInstance()->getCommandMap()->unregister(Server::getInstance()->getCommandMap()->getCommand("ban"));
        Server::getInstance()->getCommandMap()->unregister(Server::getInstance()->getCommandMap()->getCommand("ban-ip"));
        Server::getInstance()->getCommandMap()->unregister(Server::getInstance()->getCommandMap()->getCommand("gamemode"));
    }

    public function registerEvents() {
        $pluginMngr = $this->getServer()->getPluginManager();

        $pluginMngr->registerEvents(new EssentialsXEvent(), $this);
        $pluginMngr->registerEvents(new VanillaEnchanatmentEvent(), $this);
    }

    public static function getInstance(): EssentialsX
    {
        return self::$instance;
    }

    /**
     * @return WarpAPI
     */
    public function getApi(): WarpAPI{
        return $this->api;
    }
}

