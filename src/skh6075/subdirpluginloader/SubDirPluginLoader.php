<?php

namespace skh6075\subdirpluginloader;

use DevTools\DevTools;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\AsyncTask;
use poggit\virion\devirion\DEVirion;
use skh6075\subdirpluginloader\command\SubDirectoryPluginLoadCommand;
use function skh6075\injectorutils\pushEntitySkinCompoundTag;

class SubDirPluginLoader extends PluginBase{

    public function onEnable(): void{
        $this->saveDefaultConfig();

        foreach ($this->getConfig()->get("load", []) as $dirName) {
            $this->loadSubDirPlugins($dirName);
        }
        $this->getServer()->getCommandMap()->register(strtolower($this->getName()), new SubDirectoryPluginLoadCommand($this));
    }

    public function canLoadSubDirPlugins(string $dirName): bool{
        return is_dir($this->getServer()->getPluginPath() . $dirName . DIRECTORY_SEPARATOR);
    }

    public function loadSubDirPlugins(string $dirName, bool $allDisable = false): void{
        if (!$this->canLoadSubDirPlugins($dirName))
            return;

        if ($allDisable) {
            $this->onUnloadSubDirPlugins();
        }

        $temp = [];
        $temp_ = [];
        foreach (array_diff(scandir($this->getServer()->getPluginPath() . $dirName . DIRECTORY_SEPARATOR), [".", ".."]) as $value) {
            $yml = yaml_parse(file_get_contents($this->getServer()->getPluginPath() . $dirName . DIRECTORY_SEPARATOR . $value . "/plugin.yml"));

            $this->getServer()->getPluginManager()->loadPlugin($this->getServer()->getPluginPath() . $dirName . DIRECTORY_SEPARATOR . $value);
            $this->getServer()->getPluginManager()->enablePlugin($this->getServer()->getPluginManager()->getPlugin($name = $yml["name"]));
            $temp[] = $temp_[] = $name;
        }

        $temp_ = array_merge($temp_, ["DevTools", "DEVirion", "SubDirPluginLoader"]);
        $temp = array_filter($temp, function ($item) use ($temp_) { return !in_array($item, $temp_); }, ARRAY_FILTER_USE_KEY);

        foreach ($this->getServer()->getPluginManager()->getPlugins() as $plugin) {
            if (in_array($plugin->getName(), $temp)) {
                if (!$plugin->isEnabled())
                    $this->getServer()->getPluginManager()->enablePlugin($plugin);
            } else {
                $this->getServer()->getPluginManager()->disablePlugin($plugin);
            }
        }
        if (($plugin = $this->getServer()->getPluginManager()->getPlugin("DevTools")) instanceof Plugin) {
            $this->getServer()->getPluginManager()->enablePlugin($plugin);
        }
    }

    private function onUnloadSubDirPlugins(): void{
        foreach ($this->getServer()->getPluginManager()->getPlugins() as $plugin) {
            if ($plugin instanceof self)
                continue;

            $this->getServer()->getPluginManager()->disablePlugin($plugin);
        }
    }
}