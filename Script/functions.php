<?php

  function get_user_ip(){
    if(!empty($_SERVER['HTTP_CLIENT_IP'])) {
      $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
      $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
  }

  function create_user_array(){
    $result_array = array();
    $fileList = glob($GLOBALS["doc_root"] . "/Data/*.txt");
	$i = 0;
    foreach($fileList as $filename){
      if(is_file($filename)){
        $tfn = explode("/", $filename);
        $fn = end($tfn);
        if($fn[0] == "u"){
          $t = explode(".", $fn, 2);
          $tn = explode("_", $t[0], 2);
          $result_array[$i]["id"] = $tn[1];
          $result_array[$i]["ip"] = file_get_contents($GLOBALS["doc_root"] . "/Data/user_" . $userid . ".txt")
		  $i = $i+1;
        }
      }
    }
    return $result_array;
  }

  function update_user_file($userid,$ip){
	$p = $GLOBALS["doc_root"] . "/Data/user_" . $userid . ".txt";
    if(!file_exists($p))
	  create_user_file($userid,$ip);
    else{
	  $sip = file_get_contents($p);
	  if($sip != $ip)
        create_user_file($userid,$ip);
	}
  }

  function create_user_file($userid,$ip){
    file_put_contents($GLOBALS["doc_root"] . "/Data/user_" . $userid . ".txt", $ip);
  }

  function get_user_ip_from_id($id){
    $r = "";
    foreach($GLOBALS["users"] as $player){
      if($player["id"] == $id){
        $r = $player["ip"];
        break;
      }
    }
    return $r;
  }

  function wait_in_lobby($lobbyid){
    $ls = get_lobby_status($lobbyid);
    if($ls == "P"){
      $result = lobby_to_string($lobbyid);
    }elseif($ls == "S"){
      $result = get_handshake_string($lobbyid);
    }
    return $result;
  }

  function start_lobby($lobbyid){
    $ls = get_lobby_status($lobbyid);
    if($ls == "P")
      set_lobby_started($lobbyid);

    return get_handshake_string($lobbyid);
  }

  function get_handshake_string($lobbyid){
    $return_string = "";
	$GLOBALS["users"] = create_user_array();
    $players = array();
    $lobby = explode("|", lobby_to_string($lobbyid));
    $i = 3;
    while($i<7){
      if(!empty($lobby[$i])){
        $players[] = $lobby[$i];
      }else{
        break 1;
      }
      $i++;
    }
    $tarr = array();
    foreach($players as $player){
        $tarr[] = $player . ":" . get_user_ip_from_id($player);
    }
    $return_string = implode("|", $tarr);
    return $return_string;
  }

  function get_next_index(){
    $i = 1;
	$p = "";
	$c = false;
	$r = 0;
	while($c === false){
	  $p = $GLOBALS["doc_root"] . "/Data/index_" . $i . ".txt";
	  if(!file_exists($p)){
		$r = $i;
	    $c = true;
	    break;
	  }
	  $i = $i+1;
	}
    return $i;
  }

  function create_lobby($s){
    $i = get_next_index();
    $lobbysting = $i . "|P|0|". $s;
    file_put_contents($GLOBALS["doc_root"] . "/Data/lobby_" . $i . ".txt", $lobbysting);
    file_put_contents($GLOBALS["doc_root"] . "/Data/index_" . $i . ".txt", $i);
    return $i;
  }

  function join_lobby($lobbyid){
	$fls = get_free_lobbyspot($lobbyid);
    file_put_contents($GLOBALS["doc_root"] . "/Data/lobby_" . $lobbyid . "_" . $fls . ".txt", $GLOBALS["user_id"]);
    $lobby = lobby_to_string($lobbyid);
    return $lobby;
  }

  function leave_lobby($lobbyid){
    delete_user_in_lobby_file($lobbyid);
  }
  
  function delete_user_in_lobby_file($lobbyid){
	$i = 1;
	$p = "";
	$r = 0;
	while($i <= 4){
	  $p = $GLOBALS["doc_root"] . "/Data/lobby_" . $lobbyid . "_" . $i . ".txt";
	  if(file_exists($p)){
	    $fuid = file_get_contents($p);
		if($fuid == $GLOBALS["user_id"]){
		  unlink($p);
		  break;
		}
	  }
	  $i = $i+1;
	}
	return $r;
  }

  function lobby_to_string($lobbyid){
    $lobbyfile = $GLOBALS["doc_root"] . "/Data/lobby_" . $lobbyid . ".txt";
    $lobbyfile_data_string = file_get_contents($lobbyfile);
	$lobby_array = explode("|", $lobbyfile_data_string);
	$lobby_array[2] = get_lobby_player_count($lobbyid);
	$lobbyfile_data_string = implode("|", $lobby_array);
    $return = $lobbyfile_data_string . "" . create_lobby_player_string($lobbyid);
    return $return;
  }
  
  function create_lobby_player_string($lobbyid){
	$i = 1;
	$p = "";
	$r = "";
	while($i <= 4){
	  $p = $GLOBALS["doc_root"] . "/Data/lobby_" . $lobbyid . "_" . $i . ".txt";
	  if(file_exists($p)){
		$id = file_get_contents($p);
	    $r .= "|" . $id;
	  }
	  $i = $i+1;
	}
    return $r;
  }
  
  function get_free_lobbyspot($lobbyid){
	$i = 1;
	$p = "";
	$r = 0;
	while($i <= 4){
	  $p = $GLOBALS["doc_root"] . "/Data/lobby_" . $lobbyid . "_" . $i . ".txt";
	  if(!file_exists($p)){
	    $r = $i;
		break;
	  }
	  $i = $i+1;
	}
	return $r;
  }
  
  function create_lobbylist(){
    $result_array = array();
    $lobbycount = 0;
    $fileList = glob($GLOBALS["doc_root"] . "/Data/*.txt");
    foreach($fileList as $filename){
      if(is_file($filename)){
        $tfn = explode("/", $filename);
        $fn = end($tfn);
        if($fn[0] == "l"){
          $t = explode(".", $fn, 2);
          $tn = explode("_", $t[0]);
		  if(empty($tn[3])){
			$lobbyid = $tn[1];
            if(get_lobby_status($lobbyid) == "P"){
              $result_array[] = get_lobby_player_count($lobbyid) . ":" . $lobbyid;
              $lobbycount++;
            }
		  }
        }
      }
    }
    $return = $lobbycount . "|" . implode("|", $result_array);
    return $return;
  }

  function get_lobby_player_count($lobbyid){
	$i = 1;
	$p = "";
	$r = 0;
	while($i <= 4){
	  $p = $GLOBALS["doc_root"] . "/Data/lobby_" . $lobbyid . "_" . $i . ".txt";
	  if(file_exists($p)){
	    $r = $r+1;
	  }
	  $i = $i+1;
	}
    return $r;
  }

  function get_lobby_status($lobbyid){
    $lobbyfile = $GLOBALS["doc_root"] . "/Data/lobby_" . $lobbyid . ".txt";
    $lobbyfile_data = file_get_contents($lobbyfile);
    $lobbyfile_data_array = explode("|", $lobbyfile_data);
    $s = $lobbyfile_data_array[1];
    $return = $s;
    return $return;
  }

  function set_lobby_started($lobbyid){
    $lobbyfile = $GLOBALS["doc_root"] . "/Data/lobby_" . $lobbyid . ".txt";
    $lobbyfile_data = file_get_contents($lobbyfile);
    $lobbyfile_data_array = explode("|", $lobbyfile_data);
    $lobbyfile_data_array[1] = "S";
    $new_status_lobby = implode("|", $lobbyfile_data_array);
    file_put_contents($GLOBALS["doc_root"] . "/Data/lobby_" . $lobbyid . ".txt", $new_status_lobby);
  }

?>