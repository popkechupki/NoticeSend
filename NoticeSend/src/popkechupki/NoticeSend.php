<?php

namespace popkechupki;

use pocketmine\Player;
use pocketmine\Server;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\utils\Config;

class NoticeSend extends PluginBase implements Listener{ 
    public function onEnable(){                                      
        $this->getLogger()->info(TextFormat::GREEN."NoticeSendを読み込みました".TextFormat::GOLD." By popkechupki");
        $this->getLogger()->info(TextFormat::RED."このプラグインはpopke LICENSEに同意した上で使用してください。");                               
        if (!file_exists($this->getDataFolder())) @mkdir($this->getDataFolder(), 0740, true);
        $this->config = new Config($this->getDataFolder() . "Notices.yml", Config::YAML);
        $this->getServer()->getPluginManager()->registerEvents($this,$this);
    }
    public function onPlayerJoin(PlayerJoinEvent $event){
        $player = $event->getPlayer();
        $Name = $event->getPlayer()->getName();
        $CheckName = $this->config->get($Name);
        if ($CheckName){
            $player->sendMessage("[NoticeSend]通知が届いています。/readで読むことができます。");
        }
    }
    public function onCommand(CommandSender $sender, Command $command, $label, array $args){
        $Name = $sender->getName();
        $CheckName = $this->config->get($Name);
        if($command->getName() =="notice"){
                    switch (strtolower(array_shift($args))){
                        case "send":
                            if(!isset($args[0])) return $sender->sendMessage("/notice send <player> <message>");
                            $this->config->set($args[0], $args[1]);
                            $this->config->save();
                            if (!$sender instanceof Player){
                                $this->getLogger()->info($args[0]."に通知を送信しました。");
                            }else{
                                $sender->sendMessage("[NoticeSend]".$args[0]."通知を送信しました。");
                            }
                            break;

                        default:
                            $sender->sendMessage("/notice send <player> <message>");
                            break;
                    }
        }
        if(!$sender instanceof Player){
            $this->getLogger()->info("このコマンドはゲーム内で実行してください。");
        }else{
            switch (strtolower($command->getName())){
                case 'read':
                    if ($CheckName){
                        $Message = $this->config->get($Name);
                        $sender->sendMessage("[Notice]".$Message);
                        $sender->sendMessage("[Notice]/delこの通知を削除することができます。");
                    }else{
                        $sender->sendMessage("[NoticeSend]通知が届いていません。");
                    }
                    break;

                case 'del':
                    if ($CheckName){
                        $this->config->remove($Name);
                        $this->config->save();
                        $sender->sendMessage("[NoticeSend]通知を消去しました。");
                    }else{
                        $sender->sendMessage("[NoticeSend]通知が届いていません。");
                    }
                    break;
            }
        }
    }
}