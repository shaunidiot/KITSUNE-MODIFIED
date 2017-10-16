<?php

namespace Kitsune\ClubPenguin\Handlers\Game;

class TreasureHunt {

    private $boardMap;
    private $currPlayer;
    private $turnAmount;
    private $gemValue;
    private $rareGemValue;
    private $coinValue;
    private $mapWidth;
    private $mapHeight;
    private $coinAmount;
    private $gemAmount;
    private $gemLocations;
    private $gemsFound;
    private $coinsFound;
    private $rareGemFound;
    private $recordNumbers;
    
    const $NONE = 0;
    const $COIN = 1;
    const $GEM = 2;
    const $GEM_PIECE = 3;
    const $RARE_GEM = 4;
    const $turnAmount = 12;
    const $gemValue = 25;
    const $rareGemValue = 100;
    const $coinValue = 1;
    const $mapWidth = 10;
    const $mapHeight = 10;
    const $coinAmount = 0;
    const $gemAmount = 0;

    public $currPlayer = 1;
    public $gameOver = false;
    public $gemLocations = ''; // This will change

    public $boardMap = array( // Correct
        array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
        array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
        array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
        array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
        array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
        array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
        array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
        array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
        array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
        array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
        array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
    );

    public function convertToString() {
        $gemLocations = implode(',', array_fill(0, 20, 0));
        $treasureMap = implode(',', array_fill(0, 100, 0));
        return "10%10%34%3%12%25%1%$gemLocations%$treasureMap";
        // This function is still under investigation...        
		return implode(",", array_map(function($row) {
			return implode(",", $row);
		}, $this->boardMap));
	}
	
	public function changePlayer() { // Correct
		if($this->currentPlayer == 1) {
			$this->currentPlayer = 2;
		} else {
			$this->currentPlayer = 1;
		}
    }

    // Still being coded
    public function generateTreasuresToMap() {
        $currentPlayer = $this->currentPlayer;
        $rows = count($this->boardMap);
        $streak = 0;
        for($row = 0; $row < $rows; $row++) {
            //asdf
        }
    }



}

/*
  _local3.MAP_WIDTH = parseint(resObj[3]);
   _local3.MAP_HEIGHT = parseint(resObj[4]);
   _local3.COIN_NUM_HIDDEN = parseint(resObj[5]);
   _local3.GEM_NUM_HIDDEN = parseint(resObj[6]);
   _local3.NUM_TURNS = parseint(resObj[7]);
   _local3.GEM_VALUE = parseint(resObj[8]);
   _local3.COIN_VALUE = parseint(resObj[9]);
   _local3.gemLocations = resObj[10];
   _local3.treasureMap = resObj[11];



   More updated version:
         this.debugTrace("MAP_WIDTH: " + resObj[3]);
         this.debugTrace("MAP_HEIGHT: " + resObj[4]);
         this.debugTrace("COIN_NUM_HIDDEN: " + resObj[5]);
         this.debugTrace("GEM_NUM_HIDDEN: " + resObj[6]);
         this.debugTrace("NUM_TURNS: " + resObj[7]);
         this.debugTrace("GEM_VALUE: " + resObj[8]);
         this.debugTrace("COIN_VALUE: " + resObj[9]);
         this.debugTrace("gemLocations: " + resObj[10]);
         this.debugTrace("treasureMap: " + resObj[11]);
         this.debugTrace("totalGemsFound: " + resObj[12]);
         this.debugTrace("totalCoinsFound: " + resObj[13]);
         this.debugTrace("superRareGemFound: " + resObj[14]);
         this.debugTrace("digRecordNames: " + resObj[15]);
         this.debugTrace("digRecordDirections: " + resObj[16]);
         this.debugTrace("digRecordNumbers: " + resObj[17]);
         _loc3_.MAP_WIDTH = parseInt(resObj[3]);
         _loc3_.MAP_HEIGHT = parseInt(resObj[4]);
         _loc3_.COIN_NUM_HIDDEN = parseInt(resObj[5]);
         _loc3_.GEM_NUM_HIDDEN = parseInt(resObj[6]);
         _loc3_.NUM_TURNS = parseInt(resObj[7]);
         _loc3_.GEM_VALUE = parseInt(resObj[8]);
         _loc3_.COIN_VALUE = parseInt(resObj[9]);
         _loc3_.gemLocations = resObj[10];
         _loc3_.treasureMap = resObj[11];
         _loc3_.totalGemsFound = resObj[12];
         _loc3_.totalCoinsFound = resObj[13];
         _loc3_.superRareGemFound = resObj[14];
         _loc3_.digRecordNames = resObj[15];
         _loc3_.digRecordDirections = resObj[16];
         _loc3_.digRecordNumbers = resObj[17];
         this.gameEngine.spectateGame(_loc3_);


      this.debugTrace("handleStartGameMessage");
      this.debugTrace("smartRoomID: " + resObj[0]);
      this.debugTrace("player1Name: " + resObj[1]);
      this.debugTrace("player2Name: " + resObj[2]);
      this.debugTrace("MAP_WIDTH: " + resObj[3]);
      this.debugTrace("MAP_HEIGHT: " + resObj[4]);
      this.debugTrace("COIN_NUM_HIDDEN: " + resObj[5]);
      this.debugTrace("GEM_NUM_HIDDEN: " + resObj[6]);
      this.debugTrace("NUM_TURNS: " + resObj[7]);
      this.debugTrace("GEM_VALUE: " + resObj[8]);
      this.debugTrace("COIN_VALUE: " + resObj[9]);
      this.debugTrace("gemLocations: " + resObj[10]);
      this.debugTrace("treasureMap: " + resObj[11]);
      var _loc3_ = new Object();
      _loc3_.player1Name = resObj[1];
      _loc3_.player2Name = resObj[2];
      _loc3_.MAP_WIDTH = parseInt(resObj[3]);
      _loc3_.MAP_HEIGHT = parseInt(resObj[4]);
      _loc3_.COIN_NUM_HIDDEN = parseInt(resObj[5]);
      _loc3_.GEM_NUM_HIDDEN = parseInt(resObj[6]);
      _loc3_.NUM_TURNS = parseInt(resObj[7]);
      _loc3_.GEM_VALUE = parseInt(resObj[8]);
      _loc3_.COIN_VALUE = parseInt(resObj[9]);
      _loc3_.gemLocations = resObj[10];
      _loc3_.treasureMap = resObj[11];
      this.gameEngine.initMultiplayer(_loc3_);


      this.debugTrace("handleGameOverMessage");
      this.debugTrace("smartRoomID: " + resObj[0]);
      this.debugTrace("totalCoinsFound: " + resObj[1]);
      this.debugTrace("totalGemsFound: " + resObj[2]);
      this.debugTrace("totalScore: " + resObj[3]);
      var _loc3_ = parseInt(resObj[1]);
      var _loc5_ = parseInt(resObj[2]);
      var _loc4_ = parseInt(resObj[3]);
      clearInterval(this.gameOverDelay);
      this.gameOverDelay = setInterval(this.gameEngine,"showGameOver",3000);


    this.debugTrace("handleGetGameMessage");
    this.debugTrace("smartRoomID: " + resObj[0]);
    this.debugTrace("player1Name: " + resObj[1]);
    this.debugTrace("player2Name: " + resObj[2]);


    this.gemLocations.push([_loc2_[0],_loc2_[1]]);
    _loc3_.push([_loc2_[0] - 1,_loc2_[1] - 1]);
    _loc3_.push([_loc2_[0],_loc2_[1] - 1]);
    _loc3_.push([_loc2_[0] + 1,_loc2_[1] - 1]);
    _loc3_.push([_loc2_[0] - 1,_loc2_[1]]);
    _loc3_.push([_loc2_[0],_loc2_[1]]);
    _loc3_.push([_loc2_[0] + 1,_loc2_[1]]);
    _loc3_.push([_loc2_[0] - 1,_loc2_[1] + 1]);
    _loc3_.push([_loc2_[0],_loc2_[1] + 1]);
    _loc3_.push([_loc2_[0] + 1,_loc2_[1] + 1]);
    _loc5_ = _loc5_ + 1;
    }
    _loc5_ = 0;

   var totalCoinsFound = 0;
   var totalGemsFound = 0;
   var currentPlayer = 0;
   var currentTurn = 0;
   var player1Name = "";
   var player2Name = "";
   static var TREASURE_NONE = 0;
   static var TREASURE_COIN = 1;
   static var TREASURE_GEM = 2;
   static var TREASURE_GEM_PIECE = 3;
   static var TREASURE_GEM_RARE = 4;
   static var SPECTATOR = -1;
   static var PLAYER_1 = 0;
   static var PLAYER_2 = 1;
   static var MAP_WIDTH = 10;
   static var MAP_HEIGHT = 10;
   static var TILE_WIDTH = 21;
   static var TILE_HEIGHT = 21;
   static var BORDER_SIZE = 10;
   static var COIN_NUM_HIDDEN = 34;
   static var GEM_NUM_HIDDEN = 3;
   static var GEM_VALUE = 25;
   static var COIN_VALUE = 1;
   static var NUM_TURNS = 12;
   static var PLAYING_MULTIPLAYER_GAME = true;


   static var DEBUG = true;
   static var MESSAGE_GET_GAME = "gz";
   static var MESSAGE_JOIN_GAME = "jz";
   static var MESSAGE_LEAVE_GAME = "lz";
   static var MESSAGE_PLAYER_DIG = "zm";
   static var MESSAGE_START_GAME = "sz";
   static var MESSAGE_UPDATE_PLAYERLIST = "uz";
   static var MESSAGE_ABORT_GAME = "cz";
   static var MESSAGE_GAME_OVER = "zo";
   static var SERVER_SIDE_EXTENSION_NAME = "z";
   static var SERVER_SIDE_MESSAGE_TYPE = "str";

   this.AIRTOWER.addListener("gz",this.handleGetGameMessage,this);
   this.AIRTOWER.addListener("jz",this.handleJoinGameMessage,this);
   this.AIRTOWER.addListener("lz",this.handleAbortGameMessage,this);
   this.AIRTOWER.addListener("uz",this.handleUpdateGameMessage,this);
   this.AIRTOWER.addListener("sz",this.handleStartGameMessage,this);
   this.AIRTOWER.addListener("cz",this.handleCloseGameMessage,this);
   this.AIRTOWER.addListener("zm",this.handlePlayerDigMessage,this);
   this.AIRTOWER.addListener("zo",this.handleGameOverMessage,this);

   this.AIRTOWER.removeListener("gz",this.handleGetGameMessage,this);
   this.AIRTOWER.removeListener("jz",this.handleJoinGameMessage,this);
   this.AIRTOWER.removeListener("lz",this.handleAbortGameMessage,this);
   this.AIRTOWER.removeListener("uz",this.handleUpdateGameMessage,this);
   this.AIRTOWER.removeListener("sz",this.handleStartGameMessage,this);
   this.AIRTOWER.removeListener("cz",this.handleCloseGameMessage,this);
   this.AIRTOWER.removeListener("zm",this.handlePlayerDigMessage,this);
   this.AIRTOWER.removeListener("zo",this.handleGameOverMessage,this);

   this.AIRTOWER.send(com.clubpenguin.games.treasure.net.TreasureHuntClient.SERVER_SIDE_EXTENSION_NAME,com.clubpenguin.games.treasure.net.TreasureHuntClient.MESSAGE_GET_GAME,"",com.clubpenguin.games.treasure.net.TreasureHuntClient.SERVER_SIDE_MESSAGE_TYPE,this.roomID);

   this.AIRTOWER.send(com.clubpenguin.games.treasure.net.TreasureHuntClient.SERVER_SIDE_EXTENSION_NAME,com.clubpenguin.games.treasure.net.TreasureHuntClient.MESSAGE_JOIN_GAME,"",com.clubpenguin.games.treasure.net.TreasureHuntClient.SERVER_SIDE_MESSAGE_TYPE,this.roomID);

   this.AIRTOWER.send(com.clubpenguin.games.treasure.net.TreasureHuntClient.SERVER_SIDE_EXTENSION_NAME,com.clubpenguin.games.treasure.net.TreasureHuntClient.MESSAGE_JOIN_GAME,[seatID],com.clubpenguin.games.treasure.net.TreasureHuntClient.SERVER_SIDE_MESSAGE_TYPE,this.roomID);

   this.AIRTOWER.send(com.clubpenguin.games.treasure.net.TreasureHuntClient.SERVER_SIDE_EXTENSION_NAME,com.clubpenguin.games.treasure.net.TreasureHuntClient.MESSAGE_PLAYER_DIG,[buttonName,buttonDir,buttonNum],com.clubpenguin.games.treasure.net.TreasureHuntClient.SERVER_SIDE_MESSAGE_TYPE,this.roomID);

   this.AIRTOWER.send(com.clubpenguin.games.treasure.net.TreasureHuntClient.SERVER_SIDE_EXTENSION_NAME,com.clubpenguin.games.treasure.net.TreasureHuntClient.MESSAGE_LEAVE_GAME,"",com.clubpenguin.games.treasure.net.TreasureHuntClient.SERVER_SIDE_MESSAGE_TYPE,this.roomID);




*/

?>