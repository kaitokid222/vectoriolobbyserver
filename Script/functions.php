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
        if(!empty($t[0]) && !empty($t[1]))
          $newstring .= $t[0].":".$t[1]."|";
      }
    }
    if($player_is_in_file == 0){
      $newstring .= $id.":".$ip."|";
    }
    file_put_contents($userfile, $newstring);
  }

  function get_user_ip_from_id($id){
    $r = "";
    $userfile = $GLOBALS["doc_root"] . "/Data/users.txt";
    $userfile_data_string = file_get_contents($userfile);
    $userfile_data = explode("|", $userfile_data_string);
    foreach($userfile_data as $player){
      $t = explode(":",$player);
      if($t[0] == $id){
        $r = $t[1];
        break;
      }
    }
    return $r;
  }
  
  function get_handshake_string($lobbyid){
    $return_string = "";
    $players = array();
    $lobby = explode("|", lobby_to_string($lobbyid));
    $i = 2;
    while($i<6){
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

  function get_index(){
    $file = $GLOBALS["doc_root"] . "/Data/index.txt";
    $file_data_string = file_get_contents($userfile);
    $file_data_string = intval($file_data_string);
    return $file_data_string;
  }

  function get_next_index(){
    $i = get_index();
    $i = $i+1;
    return $i;
  }

  function create_lobby($s){
    $lobbysting = "P|0|". $s;
    $i = get_next_index();
    file_put_contents($GLOBALS["doc_root"] . "/Data/lobby_" . $i . ".txt", $lobbysting);
    file_put_contents($GLOBALS["doc_root"] . "/Data/index.txt", $i);
    return $i;
  }

  function join_lobby($lobbyid){
    $lobby = lobby_to_string($lobbyid);
    $lobby_array = explode("|", $lobby);
    $lobby_array[1] = intval($lobby_array[1])+1;
    $lobby = implode("|", $lobby_array);
    $lobby .= "|" . $GLOBALS["user_id"];
    file_put_contents($GLOBALS["doc_root"] . "/Data/lobby_" . $lobbyid . ".txt", $lobby);
    return $lobby;
  }

  function leave_lobby($lobbyid){
    $lobby = lobby_to_string($lobbyid);
    $lobby_array = explode("|", $lobby);
    $lobby_array[1] = intval($lobby_array[1])-1;
    $players = array_slice($lobby_array, 2,4);
    $narr = array();
    $narr[] = $lobby_array[0];
    $narr[] = $lobby_array[1];
    foreach($players as $player){
      if($player != $GLOBALS["user_id"]){
        $narr[] = $player;
      }
    }
    $lobby = implode("|", $narr);
    file_put_contents($GLOBALS["doc_root"] . "/Data/lobby_" . $lobbyid . ".txt", $lobby);
  }

  function lobby_to_string($lobbyid){
    $lobbyfile = $GLOBALS["doc_root"] . "/Data/lobby_" . $lobbyid . ".txt";
    $lobbyfile_data_string = file_get_contents($lobbyfile);
    $return = $lobbyfile_data_string;
    return $return;
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
    $lobbyfile_data_array = explode("|", $lobbyfile_data);
    $playercount = $lobbyfile_data_array[0];
    $return = $playercount;
    return $return;
  }

?>