<?php

case 'a':   return $this->handleTablePacket        ($u, $c, $p);
case 'b':   return $this->handleBuddyPacket        ($u, $c, $p);
case 'e':   return $this->handleSurveyPacket       ($u, $c, $p);
case 'f':   return $this->handleEPFPacket          ($u, $c, $p);
case 'g':   return $this->handleIglooPacket        ($u, $c, $p);
case 'i':   return $this->handleItemPacket         ($u, $c, $p);
case 'j':   return $this->handleNavigationPacket   ($u, $c, $p);
case 'l':   return $this->handleMailPacket         ($u, $c, $p);
case 'm':   return $this->handleMessagePacket      ($u, $c, $p);
case 'n':   return $this->handleIgnorePacket       ($u, $c, $p);
case 'o':   return $this->handleModerationPacket   ($u, $c, $p);
case 'p':   return $this->handlePetPacket          ($u, $c, $p);
case 'r':   return $this->handleRoomPacket         ($u, $c, $p);
case 's':   return $this->handleSettingPacket      ($u, $c, $p);
case 't':   return $this->handleToyPacket          ($u, $c, $p);
case 'u':   return $this->handlePlayerPacket       ($u, $c, $p);
case 'w':   return $this->handleWaddlePacket       ($u, $c, $p);

case 'ni':  return $this->handleNinjaPacket        ($u, $c, $p);

// TODO:
case 'cd':  return $this->handleCardPacket         ($u, $c, $p);
case 'pt':  return $this->handleTransformPacket    ($u, $c, $p); // "Player Transformation"
case 'gb':  return $this->handleGhostBusterPacket  ($u, $c, $p); // Ha, what?
case 'tic': return $this->handleTicketPacket       ($u, $c, $p); // "Player Ticket"
case 'ba':  return $this->handleCookieBakeryPacket ($u, $c, $p);

// TODO level 2:
case 'nx':  return $this->handleExperiencePacket   ($u, $c, $p); // "New User Experience"
case 'bi':  return $this->handleBiPacket           ($u, $c, $p);
case 'st':  return $this->handleStampPacket        ($u, $c, $p);
case 'rpq': return $this->handleQuestPacket        ($u, $c, $p);

case 'iCP': return $this->handleCostumPacket       ($u, $c, $p);
case 'bo':  return $this->handleBotPacket          ($u, $c, $p);


/*
****
****
*/

public function loadPlugin($pluginID, $pluginURL) {
      return $this->sendPacket('%xt%pl%-1%' . $pluginID . '%' . $pluginURL . '%');
    }

    public function sendPage($func_page) {
      return $this->sendPacket('%xt%pg%' . join('%', func_get_args()) . '%');
    }

    public function sendErrorBox($size, $message, $buttonLabel, $errorCode) {
      $data = func_get_args();
      $data[1] = str_replace(array('$playerName', '$playerId', '$$'), array($this->name, $this->id, '$'), $message);
      return $this->sendPacket('%xt%gs%-1%' . join('%', $data) . '%');
    }

    public function sendFlashCommand() {
      return $this->sendPacket('%xt%fc%-1%' . join('%', func_get_args()) . '%');
    }
    
    public function sendPrivateMessage($playerName, $playerId, $message) {
      return $this->sendPacket('%xt%pmsg%-1%' . join('%', func_get_args()) . '%');
    }


private function handleTablePacket(&$u, $c, $p) { switch($c) {
      case 'gt': return $u->getTables($p);
      case 'jt': $table = $u->room->tables[(integer)$p[1]]; return is_null($table) ? dismiss_selector() : $u->joinGame($table);
      case 'lt': return $u->leaveGame();
      
      default:   return dismiss_selector();
    }}
    
    private function handleBuddyPacket(&$u, $c, $p) { switch($c) {
      case 'gb': return $u->sendPacket   ("%xt%gb%-1%{$u->getPlayerBuddies()}");
      case 'br': return $u->requestBuddy ((integer)$p[1]);
      case 'ba': return $u->acceptBuddy  ((integer)$p[1]);
      case 'rb': return $u->removeBuddy  ((integer)$p[1]);
      case 'bf': return $u->findBuddy    ((integer)$p[1]);
      
      default:   return dismiss_selector();
    }}
    
    private function handleSurveyPacket(&$u, $c, $p) { switch($c) {
      case 'spl': return $u->votePenguinAwards($p[1]); // TODO: Actually, this is called "poll"
      case 'sig': return $u->signIglooContest();
      case 'dc':  return $u->handleDonateCoins((int) $p[1], (int) $p[2]);
      
      default:    return dismiss_selector();
    }}
    
    private function handleEPFPacket(&$u, $c, $p) { switch($c) {
      case 'epfga': return $u->sendPacket            ("%xt%epfga%-1%{$u->isEPF_A}%");
      case 'epfsa': return $this->updateUserProperty ($u->id, 'flags', $p[1] ? $u->flags | 8 : $u->flags & ~8);
      case 'epfgf': return $u->sendPacket            ("%xt%epfgf%-1%{$u->currentOP}%");
      case 'epfsf': return $this->updateUserProperty ($u->id, 'currentOP', (integer)$p[1]);
      case 'epfgr': return $u->sendPacket            ("%xt%epfgr%-1%{$u->medalsTotal}%{$u->medalsUnused}%");
      case 'epfgm': return $u->sendPacket            ("%xt%epfgm%-1%1%Alex has a cat named Sarcasm.|" . time() . "|17%");
    //case 'epfai': return var_dump($p);
      
      default:      return dismiss_selector();
    }}
    
    private function handleIglooPacket(&$u, $c, $p) { switch($c) {
      case 'im':   return; // Starting to edit igloo
      case 'ur':   return $u->updateIglooFurniture ($p);
      case 'gm':   return $u->getIglooDetails      ((integer)$p[1]);
      case 'gf':   return $u->sendPacket           ("%xt%gf%{$u->room}%{$u->iglooInventory}"); // Not used anymore
      case 'ag':   return $u->updateIglooFloor     ((integer)$p[1]);
      case 'au':   return $u->updateIglooType      ((integer)$p[1]);
      case 'af':   return $u->addFurniture         ((integer)$p[1]);
      case 'um':   return $u->updateIglooMusic     ((integer)$p[1]);
      case 'or':   return $u->openIgloo            ();
      case 'cr':   return $u->closeIgloo           ();
      case 'gr':   return $u->sendPacket           ("%xt%gr%{$u->room}%{$this->getIglooString()}");
      case 'go':   return $u->sendPacket           ("%xt%go%{$u->room}%%"); // TODO: Get owned igloos
      case 'pio':  return $u->sendPacket           ("%xt%pio%{$u->room}%" . (isset($SERVER->data['OpenIgloos'][(integer)$p[1]])?1:0) . "%");
      case 'ggd':  return $u->sendPacket           ("%xt%ggd%{$u->room}%%"); // TODO: Investigate.
      case 'uic':  return $u->updateIglooLayout    ((integer)$p[1], array_slice($p, 2));
      case 'aloc': return $u->updateIglooLocation  ((integer)$p[1]);
      case 'gii':  return $u->sendPacket           ("%xt%gii%{$u->room}%{$u->iglooInventory}%");
      case 'cli':  return $u->sendPacket           ('%xt%cli%' . $u->room . '%' . $u->id . '%200%{"canLike":false,"periodicity":"ScheduleDaily","nextLike_msecs":21017986}%');
      case 'gili': return $u->sendPacket           ('%xt%gili%' . $u->room . '%' . $u->id . '%200%{"likedby":{"counts":{"count":1,"maxCount":1,"accumCount":1},"IDs":[{"id":"{412e87f8-cd70-4909-be05-7a1e73e5a18a}","time":1397994523081,"count":1}]}}%'); // TODO: Investigate
      case 'gail': return $u->sendAllIglooLayouts  ((integer)$p[1]);
      case 'al':   return $u->addIglooLayout       ($p[1]);
      case 'uiss': return $u->updateIglooSlots     ((integer)$p[1], $p[2]);
      
      default:   return dismiss_selector();
    }}
    
    private function handleItemPacket(&$u, $c, $p) { switch($c) {
      case 'gi': return $u->sendPacket ("%xt%gi%-1%{$u->inventory}");
      case 'ai': return $u->addItem    ((integer)$p[1]);
      
      default:   return dismiss_selector();
    }}
    
    private function handleNavigationPacket(&$u, $c, $p) { switch($c) {
      case 'js': return $u->joinServer();
      
      case 'jp': $u->joinIgloo((integer)$p[1], (string)$p[2]); break;
      case 'jr': if($p[1] < 900) { $u->joinRoom((integer)$p[1], (integer)$p[2], (integer)$p[3]); break; };
      case 'jg': $u->joinGameRoom((integer)$p[1]); break;
      
      case 'crl': /* The client loaded the room. Great. Why would we care? */; break;
      case 'grs': $u->sendPacket("%xt%grs%-1%{$u->room->friendlyId}%" . $u->room->serializePlayersFor($u));
      
      default:   return dismiss_selector();
    }}
    
    private function handleMailPacket(&$u, $c, $p) { switch($c) {
      case 'mst': return $u->startMailEngine ();
      case 'ms':  return $u->sendMail        ((integer)$p[1], (integer)$p[2], "");
      case 'mg':  return $u->sendPacket      ("%xt%mg%2%{$u->getPlayerMail()}");
      case 'mc':  return $u->checkMail       ();
      
      default:    return dismiss_selector();
    }}
    
    private function handleIgnorePacket(&$u, $c, $p) { switch($c) {
      case 'gn': return $u->sendPacket   ("%xt%gn%-1%{$u->getPlayerIgnores()}");
      case 'an': return $u->addIgnore    ((integer)$p[1]);
      case 'rn': return $u->removeIgnore ((integer)$p[1]);
      
      default:   return dismiss_selector();
    }}
    
    private function handleModerationPacket(&$u, $c, $p) { // TODO: Verify this.
      if(isset($this->alias[$p[1] = (integer)$p[1]]) && ($u->isMod || $u->kick())) switch($c) {
      
      case 'm': return $this->alias[$p[1]]->mute ($u->name, $u->isAdmin);
      case 'k': return $this->alias[$p[1]]->kick ($u->name, $u->isAdmin);
      case 'b': return $this->alias[$p[1]]->ban  ($u->name, $u->isAdmin);
      case 'initban': return; // TODO: Implement this?
      
      default:  return dismiss_selector();
    }}
    
    private function handlePetPacket(&$u, $c, $p) { switch($c) {
      case 'pgu': return $u->sendPacket       ("%xt%pgu%{$u->room}%" . \iCPro\Users\World::getPuffles($u->id)); // TODO: Shouldn't this be in Server?
      case 'pw':  return $u->sendWalkPuffle   ((integer)$p[1], (integer)$p[2]);
      // TODO: Investigate '2', maybe count? (below)
      case 'pg':  return $u->sendPacket       ("%xt%pg%{$u->room}%2%" . \iCPro\Users\World::getPuffles((integer)$p[1], $p[2]));
      case 'pn':  return $u->adoptPuffle      ((integer)$p[1], $p[2], (integer)$p[3]);
      case 'ps':  return $u->sendRoomPacket   ("%xt%ps%{$u->room}%" . ((integer)$p[1]) . "%" . ((integer)$p[2]) . "%");
      case 'pm':  return $u->sendPuffleMove   ((integer)$p[1], (integer)$p[2], (integer)$p[3]);
      case 'pb':  return $u->sendPuffleAction ('pb', 10, (integer)$p[1]);
      case 'pr':  return $u->sendPuffleAction ('pr',  5, (integer)$p[1]);
      case 'pp':  return $u->sendPuffleAction ('pp',  5, (integer)$p[1]);
      case 'pt':  return $u->sendPuffleAction ('pt', 20, (integer)$p[1]);
      case 'ir':  return $u->sendRoomPacket   ("%xt%ir%{$u->room}%{$u->getPuffleString($p[1])}%{$p[2]}%{$p[3]}%");
      case 'ip':  return $u->sendRoomPacket   ("%xt%ip%{$u->room}%{$u->getPuffleString($p[1])}%{$p[2]}%{$p[3]}%");
      case 'if':  return $u->sendRoomPacket   ("%xt%if%{$u->room}%{$u->getPuffleString($p[1])}%{$p[2]}%{$p[3]}%");
      case 'pir': return $u->sendRoomPacket   ("%xt%pir%{$u->room}%{$p[1]}%{$p[2]}%{$p[3]}%");
      case 'pip': return $u->sendRoomPacket   ("%xt%pip%{$u->room}%{$p[1]}%{$p[2]}%{$p[3]}%");
      case 'pgpi': return $u->sendPacket("%xt%pgpi%19%27|1%142|1%8|1%2|1%37|1%29|1%1|1%79|3%3|11%"); // TODO: Analyze
      case 'pgmps': return; // TODO: Nothing sent back, really?
      case 'puffledig': return $u->sendPuffleDig(false);
      case 'puffleswap': return $u->sendPuffleSwap((integer)$p[1], (string)$p[2]);
      case 'puffletrick': return $u->sendPuffleTrick((integer)$p[1]);
      case 'pufflewalkswap': return $u->swapWalkingPuffle((integer)$p[1]);
      case 'checkpufflename': return $u->sendPacket("%xt%checkpufflename%{$u->room}%{$p[1]}%1%"); // Yar har! Allow all names!
      case 'puffledigoncommand': return $u->sendPuffleDig(true);
      
      default:    return dismiss_selector();
    }}
    
    private function handleExperiencePacket(&$u, $c, $p) { switch($p) {
      case 'pcos': return; // TODO: Player card opened.
      case 'bimp': return; // TODO: Tracking?
      case 'binx': return; // TODO: Tracking?
      case 'mcs':  return; // TODO: Set saved map category.
      default: return dismiss_selector();
    }}
    
    private function handleRoomPacket(&$u, $c, $p) { switch($c) {
      case 'cdu': return $u->digForCoins();
      case 'dc':  return $u->donateCoins((integer)$p[1], (integer)$p[2]);
      
      default: return dismiss_selector();
    }}
    
    private function handleSettingPacket(&$u, $c, $p) { switch($c) {
      case 'upc': case 'uph': case 'upf': case 'upn': case 'upb': case 'upa': case 'upe': case 'upl': case 'upp':
        return $u->updateLayer($c, (integer)$p[1]);
      default: return dismiss_selector();
    }}
    
    private function handleToyPacket(&$u, $c, $p) { switch($c) {
      case 'at': return $u->sendRoomPacket("%xt%at%{$u->room}%{$u->id}%"); // TODO: Status management? Investigate!
      case 'rt': return $u->sendRoomPacket("%xt%rt%{$u->room}%{$u->id}%");
      default:   return dismiss_selector();
    }}

  private function handleBiPacket($socket) {
    $penguin = $this->penguins[$socket];
    $x = Packet::$Data[2];
    $penguin->send("%xt%ack%-1%$x%");
  }

  private function handleTeleport($socket) {
    $penguin = $this->penguins[$socket];
    $x = Packet::$Data[2];
    $y = Packet::$Data[3];
    $penguin->send("%xt%tp%{$penguin->room->internalId}%$x%$y%");
  }

  private function 



    private function handlePlayerPacket(&$u, $c, $p) { switch($c) {
      case 'gp':  return $u->sendPacket     ("%xt%gp%{$u->room}%{$this->getPlayer($p[1])}%");
      case 'glr': return $u->getRevision    ();
      case 'sp':  return $u->sendPlayerMove ((integer)$p[1], (integer)$p[2]);
      case 'tp':  return $u->sendPlayerMove ((integer)$p[1], (integer)$p[2], true); // Teleport
      case 'sb':  return $u->sendRoomPacket ("%xt%sb%{$u->room}%{$u->id}%{$p[1]}%{$p[2]}%");
      case 'se':  return $u->sendRoomPacket ("%xt%se%{$u->room}%{$u->id}%{$p[1]}%");
      case 't':   return; // TODO: The client timed out!
      case 'h':   return $u->sendPacket     ("%xt%h%{$u->room}%");
      case 'sa':  return $u->sendAction     ((integer)$p[1]);
      case 'sf':  return $u->sendFrame      ((integer)$p[1]);
      case 'ss':  return $u->sendRoomPacket ("%xt%ss%{$u->room}%{$u->id}%{$p[1]}%");
      case 'sl':  return $u->sendRoomPacket ("%xt%sl%{$u->room}%{$u->id}%{$p[1]}%");
      case 'sq':  return $u->sendRoomPacket ("%xt%sq%{$u->room}%{$u->id}%{$p[1]}%");
      case 'sg':  return $u->sendRoomPacket ("%xt%sg%{$u->room}%{$u->id}%{$p[1]}%");
      case 'sj':  return $u->sendRoomPacket ("%xt%sj%{$u->room}%{$u->id}%{$p[1]}%");
      case 'sma': return $u->sendRoomPacket ("%xt%sma%{$u->room}%{$u->id}%{$p[1]}%");
      
      case 'pbi':  return $u->sendPacket("%xt%pbi%-1%{$this->getPlayerBi($p[1])}%");
      case 'pbsu': return $u->sendPacket("%xt%pbsu%-1%{$u->name}%");
      
      case 'pbsms': return $u->sendPacket("%xt%pbsms%-1%"); // pbsm-start
      case 'pbsm':  return $u->sendPacket("%xt%pbsm%-1%{$this->swidsToIds($p[1])}%");
      case 'pbsmf': return $u->sendPacket("%xt%pbsmf%-1%"); // pbsm-finish
      
      case 'gabcms': return $u->sendPacket('%xt%gabcms%-1%{"FurnitureCatalogueLocationTest":{"variant":0},"PreActivatedPlay":{"num_seconds_play":604800,"num_seconds_grace":172800,"variant":1},"MapTest":{"MapSettingId":0,"variant":0},"PuffleOopsMemberVsFree":{"variant":2},"BOGO":{"pageId":0},"HelloRockhopperTest":{"YarrColourID":5},"MembershipUpsell":{"free":"Limited Play","membership":"Unlimited Play"},"JustForTest":{"Last":"Smith","First":"John"},"NewPlayerLoginRoom":{"roomId":100,"variant":1},"FurnitureCatalogTest":{"catalogID":1,"variant":1},"XDayTrialOffers":{"TrialMembershipSettingID":2,"variant":2},"PenguinStyleTest":{"catalogID":"1"},"ClientConfigTest":{"icon":"http://www.clubpenguin.com/sites/default/files/EN0130-PuffleParty-Homepage-Billboard-Main-1361908642-1362026304.jpg","ShowLevelAccessPopup":true,"ClassName":"MemberGameLevel2","BonusPoints":500},"EndGameScreenTest":{"variant":0},"DinosaurTransformTest":{"variant":0},"QuestTest":{"startRoom":100,"isControl":false,"variant":1},"TeenBeachItems":{"CatalogID":"0"}}%');
      
      default:    return dismiss_selector();
    }}


?>