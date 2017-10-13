<?php

namespace Kitsune\ClubPenguin\Plugins\Friends;

use Kitsune\Database;
use Kitsune\Logging\Logger;
use Kitsune\ClubPenguin\Packets\Packet;
use Kitsune\ClubPenguin\Plugins\Base\Plugin;

final class Friends extends Plugin {

	public $worldHandlers = array(
		"s" => array(
			"j#js" => array("handleJoinWorld", self::After),
			"u#bf" => array("handleGetPlayerLocationById", self::Override),

			"friends#acceptedFriends" => array("handleGetAcceptedFriends", self::Override),
			"friends#friendRequests" => array("handleGetFriendRequests", self::Override),

			"friends#acceptFriend" => array("handleAcceptFriend", self::Override),
			"friends#removeFriend" => array("handleRemoveFriend", self::Override),
			"friends#sendRequest" => array("handleSendRequest", self::Override),

			"friends#getUsername" => array("handleGetUsername", self::Override),
			"friends#getPlayerInfoById" => array("handleGetPlayerInfoById", self::Override),
			"friends#getPlayerLocation" => array("handleGetPlayerLocation", self::Override),

			"friends#getBesties" => array("handleGetBestFriends", self::Override),
			"friends#addBestie" => array("handleAddBestFriend", self::Override),
			"friends#removeBestie" => array("handleRemoveBestFriend", self::Override)
	));

	public $xmlHandlers = array(null);

	public function __construct($server) {
		$this->server = $server;
		$this->database = new FriendsDatabase();
	}
	
	public function onReady() {
		parent::__construct(__CLASS__);

		Logger::Info("Friends plugin loaded!");
	}

	public function handleRemoveBestFriend($penguin) {
		Logger::Debug("Request to remove best friend received");
		$playerId = Packet::$Data[2];

		if(in_array($playerId, $penguin->bestFriends)) {
			$this->database->removeBestFriend($penguin->id, $playerId);

			$bestFriendIndex = array_search($playerId, $penguin->bestFriends);
			unset($penguin->bestFriends[$bestFriendIndex]);

			Logger::Debug("{$penguin->id} removed $playerId from their best friends array");
		}
	}

	public function handleAddBestFriend($penguin) {
		$playerId = Packet::$Data[2];

		if(!in_array($playerId, $penguin->bestFriends)) {
			if($this->database->playerIdExists($playerId)) {
				$penguin->bestFriends[] = $playerId;
				$this->database->addBestFriend($penguin->id, $playerId);

				Logger::Debug("{$penguin->id} has made $playerId their best friend!");
			}
		}
	}

	public function handleGetBestFriends($penguin) {
		$bestFriends = $this->database->getBestFriends($penguin->id);
		$penguin->send("%xt%getBesties%-1%$bestFriends%"); // Vertical line is necessary
	}

	public function handleGetPlayerLocationById($penguin) {
		$playerId = Packet::$Data[2];
		$clipId = Packet::$Data[3];
		$findTrue = Packet::$Data[4];

		if(($buddy = $this->server->getPlayerById($playerId)) !== null) {
			$roomId = $buddy->room->externalId;
			if($roomId == ($buddy->id + 1000)) {
				Logger::Debug("Buddy is in an igloo!");

				$penguin->send("%xt%bf%{$penguin->room->internalId}%{$buddy->room->externalId}%igloo%{$buddy->id}%$clipId%$findTrue%");
			} else {
				Logger::Debug("Buddy is in a game or an ordinary room");

				$penguin->send("%xt%bf%{$penguin->room->internalId}%{$buddy->room->externalId}%invalid%{$buddy->id}%$clipId%$findTrue%");
			}
		} else {
			Logger::Debug("Buddy is offline");

			$penguin->send("%xt%bf%-1%-1%invalid%-1%$clipId%$findTrue%");
		}

	}

	public function handleRemoveFriend($penguin) {
		$playerId = Packet::$Data[2];

		if(in_array($playerId, $penguin->acceptedFriends)) {
			$this->database->removeFriend($penguin->id, $playerId);
			$this->database->removeFriend($playerId, $penguin->id);

			$friendIndex = array_search($playerId, $penguin->acceptedFriends);
			unset($penguin->acceptedFriends[$friendIndex]);

			if(($remotePlayer = $this->server->getPlayerById($playerId)) !== null) {
				$friendIndex = array_search($penguin->id, $remotePlayer->acceptedFriends);
				unset($remotePlayer->acceptedFriends[$friendIndex]);

				$remotePlayer->send("%xt%friendRemoved%-1%");
			}

			Logger::Info("{$penguin->id} removed $playerId from their friends list!");
		}
	}

	public function handleGetPlayerLocation($penguin) {
		$playerId = Packet::$Data[2];
		$roomId = "0";

		if(isset($this->server->penguinsById[$playerId])) {
			$roomId = $this->server->penguinsById[$playerId]->room->externalId;
		}

		$penguin->send("%xt%playerLocation%-1%$roomId%");
	}

	public function handleGetFriendRequests($penguin) {
		$friendRequests = $this->database->getFriendRequests($penguin->id);

		$penguin->send("%xt%friendRequests%-1%$friendRequests%");
	}

	// TODO: Add checks
	public function handleAcceptFriend($penguin) {
		$acceptedId = Packet::$Data[2];

		Logger::Debug("{$penguin->id} is accepting $acceptedId's request");

		var_dump($penguin->friendRequests);

		if(in_array($acceptedId, $penguin->friendRequests)) {
			Logger::Debug("$acceptedId's request is {$penguin->id}'s requests array");

			$this->database->acceptFriend($penguin->id, $acceptedId);
			$penguin->acceptedFriends[] = $acceptedId;

			$requestIndex = array_search($acceptedId, $penguin->friendRequests);
			unset($penguin->friendRequests[$requestIndex]);

			Logger::Info("{$penguin->id} accepted $acceptedId's friend request!");

			if(($remotePlayer = $this->server->getPlayerById($acceptedId)) !== null) {
				Logger::Debug("$acceptedId is online!");
				$remotePlayer->acceptedFriends[] = $penguin->id;
				$remotePlayer->send("%xt%requestAccepted%-1%{$penguin->username}%{$penguin->swid}%");
			} else {
				Logger::Debug("$acceptedId is not online!");
			}
		} else {
			Logger::Debug("Request not found in array");
		}
	}

	public function handleGetAcceptedFriends($penguin) {
		$acceptedFriends = $this->database->getAcceptedFriends($penguin->id);

		$penguin->send("%xt%acceptedFriends%-1%$acceptedFriends%");
	}

	public function handleGetPlayerInfoById($penguin) {
		$playerId = Packet::$Data[2];

		if($this->database->playerIdExists($playerId)) {
			$playerArray = $this->database->getColumnsById($playerId, array("SWID", "ID", "Username"));
			
			$penguin->send("%xt%getPlayerInfoById%{$penguin->room->internalId}%{$playerArray["SWID"]}%{$playerArray["ID"]}%{$playerArray["Username"]}%");
		}
	}

	public function handleGetUsername($penguin) {
		$playerId = Packet::$Data[2];
		$clipId = Packet::$Data[3];
		$intSearch = Packet::$Data[4];

		if($this->database->playerIdExists($playerId)) {
			$playerUsername = $this->database->getColumnById($playerId, "Username");

			$penguin->send("%xt%getUsername%-1%$playerUsername%$clipId%$intSearch%");
		}
	}

	public function handleSendRequest($penguin) {
		$playerId = Packet::$Data[2];

		$remotePlayer = $this->server->getPlayerById($playerId);

		if($remotePlayer !== null) { // We can do this efficiently
			Logger::Debug("Sending an efficient friend request");

			if(!in_array($penguin->id, $remotePlayer->friendRequests)) {
				Logger::Debug("Efficient friend request is possible!");

				$this->database->sendRequest($playerId, $penguin->id);
				$remotePlayer->friendRequests[] = $penguin->id;
				$remotePlayer->send("%xt%friendRequest%-1%");
			}
		} else {
			if($this->database->playerIdExists($playerId)) {
				Logger::Debug("Non-efficient friend request");

				$friendRequests = $this->database->getFriendRequests($playerId);
				if(!in_array($penguin->id, explode(",", $friendRequests))) {
					Logger::Debug("Non-efficient request sent!");

					$this->database->sendRequest($playerId, $penguin->id);
				}
			}
		}
	}

	protected function handleJoinWorld($penguin) {
		$acceptedFriends = $this->database->getAcceptedFriends($penguin->id);
		$friendIds = explode(",", $acceptedFriends);

		$penguin->acceptedFriends = array();

		foreach($friendIds as $friendId) {
			if(isset($this->server->penguinsById[$friendId])) {
				$this->server->penguinsById[$friendId]->send("%xt%friendOnline%-1%{$penguin->username}%{$penguin->swid}%");
			}

			if($friendId != null) {
				Logger::Info("Adding $friendId to accepted friends array");
				$penguin->acceptedFriends[] = $friendId;
			}
		}

		$penguin->send("%xt%acceptedFriends%-1%$acceptedFriends%");

		$friendRequests = $this->database->getFriendRequests($penguin->id);

		$penguin->send("%xt%friendRequests%-1%$friendRequests%");

		$friendRequests = explode(",", $friendRequests);
		$friendRequests = array_filter($friendRequests, function($friendRequest) {
			if($friendRequest == null) {
				Logger::Debug("Filtering null friend request");

				return false;
			}
			return true;
		});

		$penguin->friendRequests = $friendRequests;

		$bestFriendIds = $this->database->getBestFriends($penguin->id);
		$penguin->send("%xt%getBesties%-1%$bestFriendIds%");

		$bestFriends = explode("|", $bestFriendIds);
		$bestFriends = array_filter($bestFriends, function($bestFriend) {
			if($bestFriend == null) {
				Logger::Debug("Filtering null bestie :-(");

				return false;
			}
			return true;
		});

		$penguin->bestFriends = $bestFriends;
	}

}

class FriendsDatabase extends Database {

	public function __construct() {
		parent::__construct();

		$this->checkFriendsTable();
	}

	private function filterByDelimiter($delimiter, $string) {
		return array_filter(explode($delimiter, $string), function($id) {
			if($id == null) {
				return false;
			}
			return true;
		});
	}

	public function removeBestFriend($playerId, $bestFriendId) {
		try {
			$bestFriends = $this->getBestFriends($playerId);
			$bestFriends = $this->filterByDelimiter("|", $bestFriends);

			Logger::Debug("Dumping filtered bestFriends array");
			var_dump($bestFriends);

			$bestFriendIndex = array_search($bestFriendId, $bestFriends);
			unset($bestFriends[$bestFriendIndex]);

			$updatedBestFriends = sprintf("|%s|", implode("|", $bestFriends));

			$updateColumn = $this->prepare("UPDATE friends SET Besties = :Besties WHERE ID = :Player");

			$updateColumn->bindValue(":Besties", $updatedBestFriends);
			$updateColumn->bindValue(":Player", $playerId);

			$updateColumn->execute();
			$updateColumn->closeCursor();
		} catch(\PDOException $pdoException) {
			Logger::Warn($pdoException->getMessage());
		}
	}

	public function addBestFriend($playerId, $bestFriendId) {
		try {
			$addBestie = $this->prepare("UPDATE friends SET Besties = CONCAT(Besties, '|', :Bestie, '|') WHERE ID = :Player");

			$addBestie->bindValue(":Player", $playerId);
			$addBestie->bindValue(":Bestie", $bestFriendId);

			$addBestie->execute();

			$addBestie->closeCursor();
		} catch(\PDOException $pdoException) {
			Logger::Warn($pdoException->getMessage());
		}
	}

	public function getBestFriends($playerId) {
		try {
			$getBesties = $this->prepare("SELECT Besties FROM `friends` WHERE ID = :Player");
			$getBesties->bindValue(":Player", $playerId);

			$getBesties->execute();

			$getBesties->bindColumn("Besties", $bestiesIds);
			$getBesties->fetch(\PDO::FETCH_BOUND);

			$getBesties->closeCursor();

			return $bestiesIds;
		} catch(\PDOException $pdoException) {
			Logger::Warn($pdoException->getMessage());
		}
	}

	public function addFriend($playerOne, $playerTwo) {
		try {
			$addFriend = $this->prepare("UPDATE friends SET Accepted = CONCAT(Accepted, :Buddy, ',') WHERE ID = :Player");

			$addFriend->bindValue(":Buddy", $playerTwo);
			$addFriend->bindValue(":Player", $playerOne);

			$addFriend->execute();

			$addFriend->closeCursor();
		} catch(\PDOException $pdoException) {
			Logger::Warn($pdoException->getMessage());
		}
	}

	public function removeFriend($playerId, $buddyId) {
		try {
			$acceptedFriendsString = $this->getAcceptedFriends($playerId);
			$acceptedFriends = $this->filterByDelimiter(",", $acceptedFriendsString);

			$friendIndex = array_search($buddyId, $acceptedFriends);
			unset($acceptedFriends[$friendIndex]);

			$acceptedFriendsString = implode(",", $acceptedFriends);
			
			if($acceptedFriendsString !== "") {
				$acceptedFriendsString .= ","; // Extra comma needed!
			}

			$updateAcceptedFriends = $this->prepare("UPDATE friends SET Accepted = :Accepted WHERE ID = :Player");

			$updateAcceptedFriends->bindValue(":Accepted", $acceptedFriendsString);
			$updateAcceptedFriends->bindValue(":Player", $playerId);

			$updateAcceptedFriends->execute();

			$updateAcceptedFriends->closeCursor();
		} catch(\PDOException $pdoException) {
			Logger::Warn($pdoException->getMessage());
		}
	}

	public function acceptFriend($toId, $fromId) {
		try {
			$this->removeFriendRequest($toId, $fromId);
			$this->addFriend($toId, $fromId);
			$this->addFriend($fromId, $toId);
		} catch(\PDOException $pdoException) {
			Logger::Warn($pdoException->getMessage());
		}
	}

	public function removeFriendRequest($toId, $fromId) {
		try {
			$friendRequestsString = $this->getFriendRequests($toId);
			$friendRequests = $this->filterByDelimiter(",", $friendRequestsString);

			array_pop($friendRequests);

			$requestIndex = array_search($fromId, $friendRequests);
			unset($friendRequests[$requestIndex]);

			$friendRequestsString = implode(",", $friendRequests) . ","; // Extra comma needed

			$updateFriendRequests = $this->prepare("UPDATE friends SET Requests = :Requests WHERE ID = :To");

			$updateFriendRequests->bindValue(":Requests", $friendRequestsString);
			$updateFriendRequests->bindValue(":To", $toId);

			$updateFriendRequests->execute();

			$updateFriendRequests->closeCursor();
		} catch(\PDOException $pdoException) {
			Logger::Warn($pdoException->getMessage());
		}
	}

	public function sendRequest($toId, $fromId) {
		try {
			$friendRequests = $this->getFriendRequests($toId);
			// Clean requests list
			$friendRequests = implode(',', $this->filterByDelimiter(",", $friendRequests));

			$sendRequest = $this->prepare("UPDATE friends SET Requests = CONCAT(:Requests, :From, ',') WHERE ID = :To");

			$sendRequest->bindValue(":Requests", $friendRequests);
			$sendRequest->bindValue(":From", $fromId);
			$sendRequest->bindValue(":To", $toId);

			$sendRequest->execute();

			$sendRequest->closeCursor();
		} catch(\PDOException $pdoException) {
			Logger::Warn($pdoException->getMessage());
		}
	}

	public function getAcceptedFriends($playerId) {
		try {
			$acceptedFriends = $this->prepare("SELECT Accepted FROM `friends` WHERE ID = :Player");
			$acceptedFriends->bindValue(":Player", $playerId);
			$acceptedFriends->execute();

			$acceptedFriends->bindColumn("Accepted", $acceptedIds);
			$acceptedFriends->fetch(\PDO::FETCH_BOUND);

			$acceptedFriends->closeCursor();

			return $acceptedIds;
		} catch(\PDOException $pdoException) {
			Logger::Warn($pdoException->getMessage());
		}
	}

	public function getFriendRequests($playerId) {
		try {
			$friendRequests = $this->prepare("SELECT Requests FROM `friends` WHERE ID = :Player");
			$friendRequests->bindValue(":Player", $playerId);
			$friendRequests->execute();

			$friendRequests->bindColumn("Requests", $requestIds);
			$friendRequests->fetch(\PDO::FETCH_BOUND);

			$friendRequests->closeCursor();

			return $requestIds;
		} catch(\PDOException $pdoException) {
			Logger::Warn($pdoException->getMessage());
		}
	}

	public function checkFriendsTable() {
		try {
			$tableExists = $this->query("SHOW TABLES LIKE 'friends'")->rowCount() > 0;

			if($tableExists === false) {
				$this->query("CREATE TABLE `friends` (`ID` INT UNSIGNED NOT NULL AUTO_INCREMENT , `Accepted` TEXT NOT NULL , `Requests` TEXT NOT NULL , `Besties` TEXT NOT NULL , PRIMARY KEY (`ID`));")->closeCursor();

				$playerStatement = $this->query("SELECT * FROM penguins");

				foreach($playerStatement as $playerRow) {
					$playerId = $playerRow["ID"];
					$this->query("INSERT INTO `friends` (`ID`, `Accepted`, `Requests`, `Besties`) VALUES ('$playerId', '', '', '');");
				}

				$playerStatement->closeCursor();

				Logger::Info("Created friends table");
			} 
		} catch(\PDOException $pdoException) {
			Logger::Warn($pdoException->getMessage());
		}
	}

}

?>