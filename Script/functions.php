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

  function update_users($id,$ip){
	$newstring = "";
	$player_is_in_file = 0;
    $userfile = $GLOBALS["doc_root"] . "/Data/users.txt";
	$userfile_data_string = file_get_contents($userfile);
	$userfile_data = explode("|", $userfile_data_string);
	if(count($userfile_data) > 0){
      foreach($userfile_data as $player){
	    $t = explode(":",$player);
		if($t[0] == $id){
		  $player_is_in_file = 1;
		  $t[1] = $ip;
		}
		$newstring .= $t[0].":".$t[1]."|";
	  }
	}
	if($player_is_in_file == 0){
	  $newstring .= $id.":".$ip."|";
	}
	file_put_contents($userfile, $newstring);
  }
  
  

  function get_index(){
    $file = $GLOBALS["doc_root"] . "/Data/index.txt";
	$file_data_string = file_get_contents($userfile);
    return $file_data_string;
  }

  function create_lobby($s){
	$lobbysting = "0|". $s;
	$i = get_index();
	file_put_contents($GLOBALS["doc_root"] . "/Data/lobby_" . ($i+1) . ".txt", $lobbysting);
	file_put_contents($GLOBALS["doc_root"] . "/Data/index.txt", ($i+1));
    return ($i+1);
  }

  function join_lobby($lobbyid){
    $lobby = lobby_to_string($lobbyid);
	$lobby_array = explode("|", $lobby);
	$lobby_array[0] = $lobby_array[0]+1;
	$lobby = implode("|", $lobby_array);
	$lobby .= "|" . $GLOBALS["user_id"];
	file_put_contents($GLOBALS["doc_root"] . "/Data/lobby_" . $lobbyid . ".txt", $lobby);
	return $lobby;
  }

  function lobby_to_string($lobbyid){
    $lobbyfile = $GLOBALS["doc_root"] . "/Data/lobby_" . $lobbyid . ".txt";
	$lobbyfile_data = file_get_contents($lobbyfile);
	$lobbyfile_data_array = explode("|", $lobbyfile_data);
	$playercount = $lobbyfile_data_array[0];
	$lobbysettings = $lobbyfile_data_array[1];
	$lobbyfile_data_array = explode("|", $lobbyfile_data, -2);
	$return = $playercount;
	$return .= $lobbysettings;
	foreach($lobbyfile_data_array as $player){
	  $return .= "|".$player;
	}
    return $return;
  }
  
  function create_lobbylist(){
	$result_array = array();
    $lobbycount = 0;
    $fileList = glob($GLOBALS["doc_root"] . "/Data/*.txt");
    foreach($fileList as $filename){
      if(is_file($filename)){
	    if($filename[0] == "l"){
		  $t = explode(".", $filename, 2);
		  $tn = explode("_", $t[0], 2);
		  $lobbyid = $tn[1];
		  $result_array[] = get_lobby_player_count($lobbyid) . ":" . $lobbyid;
		  $lobbycount++;
		}
      }
	}
	$return = $lobbycount . "|" . implode("|", $result_array);
    return $return;
  }

  function get_lobby_player_count($lobbyid){
    $lobbyfile = $GLOBALS["doc_root"] . "/Data/lobby_" . $lobbyid . ".txt";
	$lobbyfile_data = file_get_contents($lobbyfile);
	$lobbyfile_data_array = explode("|", $lobbyfile_data, 2);
	$playercount = $lobbyfile_data_array[0];
	$return = $playercount;
    return $return;
  }

?>