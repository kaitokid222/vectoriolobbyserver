<?php

  $GLOBALS["doc_root"] = $_SERVER["DOCUMENT_ROOT"];
  require($GLOBALS["doc_root"] . "/Script/functions.php");
  
  //index.php?u=1234567890&a=1
  //index.php?u=1234567890&a=2
  //index.php?u=1234567890&a=3$s=1 (gamemode?)
  //index.php?u=1234567890&a=4&l=12345
  //index.php?u=1234567890&a=5&l=12345
  //index.php?u=1234567890&a=6&l=12345

  //Check Input
  if(!isset($_GET["u"]) || empty($_GET["u"])){
    $output = "Missing Input User ID";
    echo $output;
    die();
  }else{
    if(!preg_match("#^[0-9]+$#", $_GET["u"])){
      $output = "Input User ID is not numeric";
      echo $output;
      die();
    }else{
      $_GET["u"] = intval($_GET["u"]);
    }
  }

  if(!isset($_GET["a"]) || empty($_GET["a"])){
    $output = "Missing Input Action";
    echo $output;
    die();
  }else{
    if(!preg_match("#^[0-9]+$#", $_GET["a"])){
      $output = "Input Action is not numeric";
      echo $output;
      die();
    }else{
      $_GET["a"] = intval($_GET["a"]);
    }
  }

  if($_GET["a"] == 3){
    if(!isset($_GET["s"]) || empty($_GET["s"])){
      $output = "Missing Input Gamemode";
      echo $output;
      die();
    }else{
      if(!preg_match("#^[0-9]+$#", $_GET["s"])){
        $output = "Input Gamemode is not numeric";
        echo $output;
        die();
      }else{
        $_GET["s"] = intval($_GET["s"]);
      }
    }
  }

  if($_GET["a"] == 4 || $_GET["a"] == 5 || $_GET["a"] == 6 || $_GET["a"] == 7){
    if(!isset($_GET["l"]) || empty($_GET["l"])){
      $output = "Missing Input Lobby ID";
      echo $output;
      die();
    }else{
      if(!preg_match("#^[0-9]+$#", $_GET["l"])){
        $output = "Input Lobby ID is not numeric";
        echo $output;
        die();
      }else{
        $_GET["l"] = intval($_GET["l"]);
      }
    }
  }

  if($_GET["a"] <= 0 || $_GET["a"] >= 8){
    $output = "Unknown Request!";
    echo $output;
    die();
  }
  // $_GET is trustworthy now

  $GLOBALS["user_id"] = $_GET["u"];
  $user_ip = get_user_ip();
  $users = update_users($_GET["u"], $user_ip);

  
  if($_GET["a"] == 1 || $_GET["a"] == 2){
    $action = "listlobbies";
  }elseif($_GET["a"] == 3){
    $action = "createlobby";
    $gamemode = $_GET["s"];
  }elseif($_GET["a"] == 4){
    $action = "joinlobby";
    $lobbyid = $_GET["l"];
  }elseif($_GET["a"] == 5){
    $action = "startgame";
    $lobbyid = $_GET["l"];
  }elseif($_GET["a"] == 6){
    $action = "leavelobby";
    $lobbyid = $_GET["l"];
  }elseif($_GET["a"] == 7){
    $action = "waitinlobby";
    $lobbyid = $_GET["l"];
  }
 
  if($action == "listlobbies"){
    $output = create_lobbylist();
  }elseif($action == "createlobby"){
    $lid = create_lobby($gamemode);
    $output = join_lobby($lid);
  }elseif($action == "startgame"){
    $output = start_lobby($lobbyid);
  }elseif($action == "joinlobby"){
    $output = join_lobby($lobbyid);
  }elseif($action == "leavelobby"){
    leave_lobby($lobbyid);
    $output = create_lobbylist();
  }elseif($action == "waitinlobby"){
    $output = wait_in_lobby($lobbyid);
  }

  echo $output;
  die();
?>