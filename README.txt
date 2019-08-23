 Instruction:
    //put all file in the same directory.
	//to run this file, just simply set up the runing php environment in terminal, 
	//and go to the directory of the file, type "php index.php" in terminal 
    //to start this game.

Explanation:

Map: It is supposed to load from database through to server. 
	I load it from local file to display the basic function for convennience.
	the map is set as 27 cube-rooms, which are connecting like a Rubik's Cube. 
	Each room can alway go to next room which is adjacent with the current room.
	So, player can move by typing direction command to move. 
	the map information is stored in "map.json", it can load the map name or description by player position (x,y,z values).
	the x,y,z value for each room should be an range, it can be changed later. 
	For now, I just use specific integer value to identify the room.

Role: It is also supposed to load from database through to server.
	The information of role is stored in "role.json", which include playerName, message, and position.
	It is easy to put more detail information on json, such hp, level, attack damage...
	Any change will be stored in json immediately, for example, message and position.

Game start: It should be a login page, but I decide to let you choose which role you are going to use.
	To implement the message function, I stored 3 roles in json.
	Pick one of them, then start to use the available commands.
	The game will display role information and map information first, such as playerName, message, position, and mapType.
	Then, list all available command and introduction, and ask you what to do.
	You can move north, south, west, east, up, and down. Then x, y, z values will change and store immediately in "role.json".
	You can type "say <content>", all roles having same x, y, z values will store "<content>" into message in "role.json".
	So on...

	