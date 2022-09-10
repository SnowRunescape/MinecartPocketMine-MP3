<?php

namespace Minecart\utils;

use pocketmine\Player;
use Minecart\Minecart;

class Cooldown
{
    const COOLDOWN = 5;

    public function isInCooldown(Player $player) : bool
    {
        $playerName = strtolower($player->getName());

        if (!empty(Minecart::getInstance()->cooldown[$playerName])) {
            return (time() - Minecart::getInstance()->cooldown[$playerName]) <= self::COOLDOWN;
        }

        return false;
    }

    public function setPlayerInCooldown(Player $player) : void
    {
        $playerName = strtolower($player->getName());
        Minecart::getInstance()->cooldown[$playerName] = time();
    }

    public function removePlayerCooldown(Player $player) : void
    {
        $playerName = strtolower($player->getName());

        if (!empty(Minecart::getInstance()->cooldown[$playerName])) {
            unset(Minecart::getInstance()->cooldown[$playerName]);
        }
    }

    public function getCooldownTime(Player $player) : int
    {
        $playerName = strtolower($player->getName());

        if (!empty(Minecart::getInstance()->cooldown[$playerName])) {
            return self::COOLDOWN - (time() - Minecart::getInstance()->cooldown[$playerName]);
        }

        return 0;
    }
}
