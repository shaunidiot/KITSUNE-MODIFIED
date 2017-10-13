<?php

namespace Kitsune\ClubPenguin\Handlers\Play;

use Kitsune\ClubPenguin\Packets\Packet;

trait Election {

	protected function handleDonateCoins($socket) {
		$penguin = $this->penguins[$socket];
		$id = Packet::$Data[2];
		$amount = Packet::$Data[3];
		if($penguin->coins < $amount) {
			return $penguin->send("%xt%e%-1%401%");
		}
		$penguin->setCoins($penguin->coins - $amount);
		$penguin->send("%xt%dc%$id%{$penguin->coins}%");

	}
	
}

?>