# vectoriolobbyserver
This is an ultra lightweight lobby service which can be deployed on freehosters since it only requires any version of PHP5+

Actions:

 -list lobbies

  -> index.php?u=<PLAYERID(0-9)>&a=2

  <- Number of Lobbies|Number of players in Lobby:Lobby ID| `2|3:123|2:124`


 -create lobby

  -> index.php?u=<PLAYERID(0-9)>&a=3&s=<GAMEMODE(0-9)>

  <- Number of players in Lobby|Player ID `1|324`


 -join lobby

  -> index.php?u=<PLAYERID(0-9)>&a=4&l=<LOBBYID(0-9)>

  <- Number of players in Lobby|Player ID|Player ID|Player ID|Player ID `3|324|532|145`

 
 -start game

  -> index.php?u=<PLAYERID(0-9)>&a=5&l=<LOBBYID(0-9)>

  <- Player ID:Player IP|Player ID:Player IP|Player ID:Player IP|Player ID:Player IP `324:123.123.123.123|532:123.123.123.123|145:123.123.123.123|452:123.123.123.123`


 -leave lobby

  -> index.php?u=<PLAYERID(0-9)>&a=6&l=<LOBBYID(0-9)>

  <- Number of Lobbies|Number of players in Lobby:Lobby ID| `2|3:123|2:124`

 