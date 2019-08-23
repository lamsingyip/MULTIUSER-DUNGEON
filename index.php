<?php
	//put all file in the same directory.
	//to run this file, just simply set up the runing php environment in terminal, 
	//and go to the directory of the file, type "php index.php" in terminal 
    //to start this game.

	/*Things to consider:
	Race: Collect all users names with the x,y,z value in my code. 
		for later improved porject, find users by the range of x, y, z values.

	PK: Add level, hp, attack damage, armour, cool down into role.
		wirte command "pk <name>", the player <name> will get request from sender.
		when the player accept, using cool down to decide who attacke first 
		then calculate damage = attack damage - armour, hp = hp - damage. 
		the player who reach hp = 0 to lose the fight.

	So on...
	*/

	//intro
	echo "**********************\n";
	echo "* Welcome to Dungeon *\n";
	echo "**********************\n";
	startGame(); //press return to start the game

	echo "0.Adam, 1.Bob, 2.Carol \n"; //We can load id and name from json file, but I do this for saving time
	echo "Type 0,1,or 2 to pick a character:";
	$id = rtrim(fgets(STDIN));

	do{
		$strJsonFileContents = file_get_contents("role.json");
		$array = json_decode($strJsonFileContents, true);
		//get the name of the room by x,y,z
		$mapName = loadMap($array[$id]["x"],$array[$id]["y"],$array[$id]["z"]);
		//store name and position 
		$numOfPlayers = sizeof($array);
		$name = $array[$id]["playerName"];
		$x = $array[$id]["x"];
		$y = $array[$id]["y"];
		$z = $array[$id]["z"];

		//list information about the player
		echo "\nHi, ${name}, you are in ${mapName}, position (X:${x} Y:${y} Z:${z}) \n";
		echo "Message:";
		loadMessage($id); //load the message box from json
		echo "Command list:\n\"say <dialog>\" send a chat message to everyone in this room\n\"tell <person_name> <dialog>\" send a message to a person\n\"yell <dialog>\" send a chat message to everyone in the game\n\"north\",\"south\",\"east\",\"west\",\"up\",\"down\", move to next room\n";

		echo "What do you want to do? (Type \"exit\" to exit the game)";
		$input = rtrim(fgets(STDIN)); //get input command

		//check which command 
		//this is easy to extend more command like "pickup <item>", "fight <person>", or "put <item> <item>"
		//To implement those command, just add the related function later
		if (strpos($input, "say") === 0){
			say($input,$name,$x,$y,$z,$numOfPlayers);
		}else if (strpos($input, "tell") === 0){
			tell($input,$name,$numOfPlayers);
		}else if (strpos($input, "yell") === 0){
			yell($input,$name,$numOfPlayers);
		}else if ($input === "north" || $input === "south" || $input === "east"
					|| $input === "west" || $input === "up" || $input === "down"){
			move($input,$array,$id);
		}else if ($input === "exit"){
			echo $input." command received.\n";
		}else{
			//invalid command message
			echo "Invalid command! please enter again.\n";
		}

	}while($input != "exit"); //keep asking for input command until user type "exit"

	//quit game sentence
	echo "\nSee you next time!\n";
?>

<?php
	//functions area:
	function startGame($s='Press Return key to continue...') {
		echo $s."\n";
		fgetc(STDIN);
	}

	//load message, it can only display the newest message for now
	function loadMessage($ID){
		$strJsonFileContents = file_get_contents("role.json");
		$arr = json_decode($strJsonFileContents, true);
		echo $arr[$ID]["message"]."\n";
	}

	//check who else is in the same room, then store message in json, so other player can see it 
	function say($inputStr,$sender,$xS,$yS,$zS,$length){
		$msg = substr($inputStr,4);
		$jsonString = file_get_contents('role.json');
		$data = json_decode($jsonString, true);
		for($i = 0; $i <$length; $i++){
			if ($data[$i]["x"] === $xS){
				if ($data[$i]["y"] === $yS){
					if ($data[$i]["z"] === $zS){
						if ($data[$i]["playerName"] != $sender){
							$data[$i]["message"] = $sender.": ".$msg;
							$newJsonString = json_encode($data);
							file_put_contents('role.json', $newJsonString);
						}
					}
				}
			}
		}		
	}

	//send a message to some one with his name
	function tell($inputStr,$sender,$length){
		//collect the message from commond
		$pieces = explode(" ",$inputStr);
		$recipient = $pieces[1];
		$msg = substr($inputStr,5);
		$msg = str_replace($recipient, "", $msg);
		$msg = substr($msg,1);

		$jsonString = file_get_contents('role.json');
		$data = json_decode($jsonString, true);
		for ($i = 0; $i < $length; $i++){
			if ($data[$i]["playerName"] === $recipient){
				$data[$i]["message"] = $sender.": ".$msg;
				$newJsonString = json_encode($data);
				file_put_contents('role.json', $newJsonString);
			}
		}
	}

	//Store message to every player in json, but himself/herself
	function yell($inputStr,$sender,$length){
		$msg = substr($inputStr,5);
		$jsonString = file_get_contents('role.json');
		$data = json_decode($jsonString, true);
		for ($i = 0; $i < $length; $i++){
			if ($data[$i]["playerName"] != $sender){
				$data[$i]["message"] = $sender.": ".$msg;
				$newJsonString = json_encode($data);
				file_put_contents('role.json', $newJsonString);
			}
		}
	}

	//let say the map is a cube built by 9 cubes like a Rubik's Cube, 
	//player can go to next room which is adjacent with the current room, 
	//but it cannot go away from the big cube.
	function move($str,$arr,$ID){

		switch($str){
			case "north":
				if ($arr[$ID]["y"] === 0){
					echo "North side is a wall, you cannot go through!\n";
				}else{
					$arr[$ID]["y"] -= 1;
				}
				$newStr = json_encode($arr);
				file_put_contents('role.json', $newStr);
				break;
			case "south":
				if ($arr[$ID]["y"] === 2){
					echo "South side is a wall, you cannot go through!\n";
				}else{
					$arr[$ID]["y"] += 1;
				}
				$newStr = json_encode($arr);
				file_put_contents('role.json', $newStr);
				break;
			case "east":
				if ($arr[$ID]["x"] === 2){
					echo "East side is a wall, you cannot go through!\n";
				}else{
					$arr[$ID]["x"] += 1;
				}
				$newStr = json_encode($arr);
				file_put_contents('role.json', $newStr);
				break;
			case "west":
				if ($arr[$ID]["x"] === 0){
					echo "West side is a wall, you cannot go through!\n";
				}else{
					$arr[$ID]["x"] -= 1;
				}
				$newStr = json_encode($arr);
				file_put_contents('role.json', $newStr);
				break;
			case "up":
				if ($arr[$ID]["z"] === 2){
					echo "Up side is a wall, you cannot go through!\n";
				}else{
					$arr[$ID]["z"] += 1;
				}
				$newStr = json_encode($arr);
				file_put_contents('role.json', $newStr);
				break;
			case "down":
				if ($arr[$ID]["z"] === 0){
					echo "Down side is a wall, you cannot go through!\n";
				}else{
					$arr[$ID]["z"] -= 1;
				}
				$newStr = json_encode($arr);
				file_put_contents('role.json', $newStr);
				break;
			default:
				echo "error!";
		}
	}

	//load the map information by x, y, z values
	function loadMap($xM,$yM,$zM){
		$strMap = file_get_contents("map.json");
		//var_dump($strJsonFileContents);
		$mapArray = json_decode($strMap, true);

		for($i = 0; $i <27; $i++){
			if ($mapArray[$i]["x"] === $xM){
				if ($mapArray[$i]["y"] === $yM){
					if ($mapArray[$i]["z"] === $zM){
						return $mapArray[$i]["mapType"];
					}
				}
			}
		}
	}
?>
