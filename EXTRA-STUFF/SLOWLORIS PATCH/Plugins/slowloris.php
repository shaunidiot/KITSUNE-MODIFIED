<?php

namespace Kitsune\ClubPenguin\Plugins\slowloris;

use Kitsune\Logging\Logger;
use Kitsune\ClubPenguin\Packets\Packet;
use Kitsune\ClubPenguin\Plugins\Base\Plugin;

/*
By:
Zaseth#7550
Dev#0832
*/

final class slowloris extends Plugin {

	public function __construct($server){
		$this->server = $server;
	}

	public function onReady(){
		parent::__construct(__CLASS__);
	}

	/*
	Method v1
	*/

	public function unknownHandler($socket){
		$penguin = $this->penguins[$socket];
		if(strpos(Packet::$Data[0], 'HTTP') !== false){
			echo 'Stop using slowloris you skid.';
			return $this->removePenguin($penguin);
		}
	}

	/*
	Method v2
	*/

	public function checkIfPost($socket){
		$penguin = $this->penguins[$socket];
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			echo 'Stop using slowloris you skid.';
			return $this->removePenguin($penguin);
		}
	}
}
?>