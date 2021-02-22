<?php

namespace skh6075\subdirpluginloader\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use skh6075\subdirpluginloader\SubDirPluginLoader;

final class SubDirectoryPluginLoadCommand extends Command{

    protected SubDirPluginLoader $plugin;


    public function __construct(SubDirPluginLoader $plugin) {
        parent::__construct("pluginload", "subDirectory plugin all load command.");
        $this->setPermission("subdir.pluginload.permission");
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $player, string $label, array $args): bool{
        if (trim($dirName = array_shift($args) ?? "") === "") {
            $player->sendMessage("/" . $this->getName() . " [dirName] [allDisable true:false]");
            return false;
        }
        $allDisable = (bool) array_shift($args) ?? false;
        if (!$this->plugin->canLoadSubDirPlugins($dirName)) {
            $player->sendMessage($dirName . " directory not founded.");
            return false;
        }
        $this->plugin->loadSubDirPlugins($dirName, $allDisable);
        $player->sendMessage("Successed " . $dirName . " directory all load plugins.");
        return true;
    }
}