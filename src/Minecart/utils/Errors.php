<?php

namespace Minecart\utils;

use pocketmine\Player;
use Minecart\Minecart;

class Errors
{
    public function getError(Player $player, int $code, bool $return = false) : string
    {
        switch ($code) {
            case 40010:
                $message = Minecart::getInstance()->getMessage("error.invalid-key");
                break;
            case 40011:
                $message = Minecart::getInstance()->getMessage("error.invalid-shopserver");
                break;
            case 40012:
                $message = Minecart::getInstance()->getMessage("error.nothing-products-cash");
                break;
            case 40013:
                $message = Minecart::getInstance()->getMessage("error.commands-product-not-registred");
                break;
            case 401:
                $message = Minecart::getInstance()->getMessage("error.invalid-shopkey");
                break;
            default:
                $message = Minecart::getInstance()->getMessage("error.internal-error");
                break;
        }

        if (!$return) {
            $player->sendMessage($message);
        }

        return $message;
    }
}
