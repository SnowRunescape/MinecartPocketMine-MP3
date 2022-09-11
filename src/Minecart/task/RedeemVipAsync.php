<?php

namespace Minecart\task;

use pocketmine\command\ConsoleCommandSender;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\Player;
use Minecart\utils\Form;
use Minecart\utils\API;
use Minecart\Minecart;
use Minecart\utils\Errors;
use Minecart\utils\Messages;

class RedeemVipAsync extends AsyncTask
{
    private $username;
    private $key;
    private $authorization;
    private $shopServer;

    public function __construct(string $username, string $key, string $authorization, string $shopServer)
    {
        $this->username = $username;
        $this->key = $key;
        $this->authorization = $authorization;
        $this->shopServer = $shopServer;
    }

    public function onRun() : void
    {
        $api = new API();
        $api->setAuthorization($this->authorization);
        $api->setShopServer($this->shopServer);
        $api->setParams(["username" => $this->username, "key" => $this->key]);
        $api->setURL(API::REDEEMVIP_URI);

        $this->setResult($api->send());
    }

    public function onCompletion(Server $server) : void
    {
        $player = $server->getPlayerExact($this->username);
        $response = $this->getResult();

        if (!empty($response)) {
            $statusCode = $response["statusCode"];

            if ($statusCode == 200) {
                $response = $response["response"];

                if ($this->executeCommands($player, $response)) {
                    $messages = new Messages();
                    $messages->sendGlobalInfo($player, "vip", $response["group"]);

                    $message = $this->parseText(Minecart::getInstance()->getMessage("success.active-key"), $player, $response);
                    $player->sendMessage($message);
                } else {
                    $error = $this->parseText(Minecart::getInstance()->getMessage("error.redeem-vip"), $player, $response);
                    $player->sendMessage($error);
                }
            } else {
                $form = new Form();
                $form->setTitle("Resgatar VIP");
                $form->setPlaceholder("Insira sua key");
                $form->setRedeemType(Form::REDEEM_VIP);
                $form->setKey($this->key);

                $errors = new Errors();
                $error = $errors->getError($player, $response["response"]["code"] ?? $statusCode, true);
                $form->showRedeem($player, $error);
            }
        } else {
            $player->sendMessage(Minecart::getInstance()->getMessage("error.internal-error"));
        }
    }

    private function executeCommands(Player $player, array $response) : bool
    {
        $result = true;

        foreach ($response["commands"] as $command) {
            $command = $this->parseText($command, $player, $response);

            if (!Minecart::getInstance()->getServer()->dispatchCommand(new ConsoleCommandSender(), $command)) {
                $result = false;
            }
        }

        return $result;
    }

    private function parseText(string $text, Player $player, array $response) : string
    {
        return str_replace(["{player.name}", "{key.group}", "{key.duration}"], [$player->getName(), $response["group"], $response["duration"]], $text);
    }
}
