<?php

namespace bink702;

use pocketmine\Player;
use pocketmine\scheduler\Task;

class UpdateTask extends Task{

    public function __construct(TopExp $pl){
        $this->pl = $pl;
    }

    public function onRun($tick){
        foreach ($this->pl->getServer()->getOnlinePlayers() as $player){
            $this->pl->saveExp($player);
        }
        $lb = $this->pl->getLeaderBoard();
        $list = $this->pl->getParticles();
        foreach($list as $particle){
            $particle->setText($lb);
        }
    }

}