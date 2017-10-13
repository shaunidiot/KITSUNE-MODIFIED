<?php

namespace Kitsune\ClubPenguin\Handlers\Play;

use Kitsune\ClubPenguin\Packets\Packet;

trait Message {

protected function handleSendMessage($socket) {
        $penguin = $this->penguins[$socket];
        $spamTime = time();

        if(!$penguin->muted) {
            $message = Packet::$Data[3];
            if(time($spamTime) > 5) {
                $penguin->room->send("%xt%sm%{$penguin->room->internalId}%{$penguin->id}%I've been kicked for spamming messages%");
                return $this->removePenguin($penguin);
            }
            if(strlen($message) > 50) {
                $penguin->room->send("%xt%sm%{$penguin->room->internalId}%{$penguin->id}%I've been kicked for hacking messages%");
                return $this->removePenguin($penguin);
            }
            if($message == "") { // Blank message
            	return;
            }
            strip_tags($message, '!?.,');
            $penguin->room->send("%xt%sm%{$penguin->room->internalId}%{$penguin->id}%$message%");
        }
    }
}

?>