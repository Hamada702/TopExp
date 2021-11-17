<?php

declare(strict_types=1);

namespace bink702;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class TopExp extends PluginBase implements Listener {

    private $particle = [];

    public function onEnable(){
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->config = (new Config($this->getDataFolder()."config.yml", Config::YAML))->getAll();
        if(empty($this->config["positions"])){
            $this->getServer()->getLogger()->Info("Set Location");
            return;
        }
        $pos = $this->config["positions"];
        $this->particle[] = new FloatingText($this, new Vector3($pos[0], $pos[1], $pos[2]));
        $this->getScheduler()->scheduleRepeatingTask(new UpdateTask($this), 40);
        $this->getServer()->getLogger()->Info("Location Have Been Load");
    }

    public function onCommand(CommandSender $p, Command $command, string $label, array $args): bool{
        if($command->getName() === "settopexp"){
            if(!$p instanceof Player) return false;
            if(!$p->isOp()) return false;
            $config = new Config($this->getDataFolder()."config.yml", Config::YAML);
            $config->set("positions", [round($p->getX()), round($p->getY()), round($p->getZ())]);
            $config->save();
            $p->sendMessage("§a* §oBerhasil menentukan lokasi daftar TopExp§r§f!");
        }
        return true;
    }

    public function createtopten(PlayerJoinEvent $event){
        $player = $event->getPlayer();
        $w = $this->getConfig()->get("world");
        $world = $player->getLevel()->getName() === "$w";
        $top = $this->getConfig()->get("enable");

        if($world){
            if($top == "true"){
                $this->getLeaderBoard();
            }
        }
    }

    public function settopdata(PlayerJoinEvent $event){
        $player = $event->getPlayer();
        $name = $player->getName();

        $farm = new Config($this->getDataFolder() . "topExp.yml", Config::YAML);
        if(!$farm->exists($name)){
            $farm->set($name, 0);
            $farm->save();
        }
    }

    public function saveExp(Player $player){
        $exp = $data = new Config($this->getDataFolder() . "topExp.yml", Config::YAML);
        $name = $player->getName();
        $xp = $player->getCurrentTotalXp();
        $exp->set($name, $xp);
        $exp->save();
    }

    public function getLeaderBoard(): string{
        $data = new Config($this->getDataFolder() . "topExp.yml", Config::YAML);
        $swallet = $data->getAll();
        $message = "";
        $top = "§g§lLeaderboard TopExp";
        if(count($swallet) > 0){
            arsort($swallet);
            $i = 1;
            foreach ($swallet as $name => $amount) {
                $message .= "\n§e".$i."§d# §f".$name." §7- §a".$amount." §7Exp\n";
                if($i >= 10){
                    break;
                }
                ++$i;
            }
        }
        $return = (string) $top.$message;
        return $return;
    }

    public function getParticles(): array{
        return $this->particle;
    }


}
