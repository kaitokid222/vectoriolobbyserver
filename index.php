<?php
  $GLOBALS["doc_root"] = $_SERVER["DOCUMENT_ROOT"];
  require("Script/functions.php");
  
  // Gateway
  
  //index.php?u=1234567890&a=1
  //index.php?u=1234567890&a=2
  //index.php?u=1234567890&a=3$s=1 (gamemode?)
  //index.php?u=1234567890&a=4&l=12345
  //index.php?u=1234567890&a=5&l=12345

  $user_id = $_GET["u"];
  $GLOBALS["user_id"] = $user_id;
  $user_ip = get_user_ip();
  $users = update_users($user_id, $user_ip);
  
  if($_GET["a"] == 1 || $_GET["a"] == 2){
    $action = "listlobbies";
  }elseif($_GET["a"] == 3){
    $action = "createlobby";
	$gamemode = 0;
	if(is_int($_GET["s"])){
	  $lobbyid = $_GET["s"];
	}
	if(lobbyid == 0){
      die();
	}
  }elseif($_GET["a"] == 4){
    $action = "joinlobby";
	$lobbyid = 0;
	if(is_int($_GET["l"])){
	  $lobbyid = $_GET["l"];
	}
	if(lobbyid == 0){
      die();
	}
  }elseif($_GET["a"] == 5){
	$action = "startgame";
	$lobbyid = 0;
	if(is_int($_GET["l"])){
	  $lobbyid = $_GET["l"];
	}
	if(lobbyid == 0){
      die();
	}
  }else{
	die();
  }
 
  if($action == "listlobbies"){
    $output = create_lobbylist();
  }elseif($action == "createlobby"){
	$lid = create_lobby($gamemode);
    $output = join_lobby($lid);
  }elseif($action == "startgame"){
    $output = get_handshake_string($lobbyid);
  }elseif($action == "joinlobby"){
    $output = join_lobby($lobbyid);
  }

  echo $output;
  die();
?>