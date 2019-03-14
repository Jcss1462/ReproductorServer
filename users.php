<?php

require_once './headers.php';
require_once 'libs/Pabhoz/MySQLiManager/MySQLiManager.php';

$db = new MySQLiManager('localhost','root','','AplicacionSuperCool');


if(isset($_GET["ejecute"])){
	if($_GET["ejecute"] != null && $_GET["ejecute"] != ""){
			switch ($_GET["ejecute"]){

                //////////////////////////////////////////////////////////////ususarios
                case "select":
                    $table = "users";
					select();
                break;
                case "login":
                    $table = "users";
					login();
                break;
                case "signup":
                    $table = "users";
                    signUp();
                break;
                /////////////////////////////////////////////////////////////canciones
                case "subsong":
                    $table = "songs";
                    signUp();
                break;
                case "selectSongs":
                    $table = "songs";
                    selectcan();
                break;
                ///////////////////////////////////////////////////////////////artistas
                case "subartist":
                    $table = "artists";
                    signUp();
                break;
                ///////////////////////////////////////////////////////////////albums
                case "subalbum":
                    $table = "albums";
                    signUp();
                break;

                case "getMyInfo":
            
                    $table = "users";
                    getMyInfo();
            
                break;

                case "nmc":
                    nmc();
                break;




			}
		}else{
			die("La función <b>".$_GET['ejecute']."</b> no existe");
		}
}

function login(){
    global $db,$table;

    if (!isset($_POST["username"])) die("Username must be provided");

    //si ussername es igual al de la bace de datos asignarlo, sino dejarlo vacio
    $where = (isset($_POST["username"])) ? "username = '$_POST[username]'" : "";
   
    //busque todo en la tabla, donde la condicion se cumpla
    $fetch = $db->select("*",$table,$where);
    $fetch = ($fetch == NULL) ? [] : $fetch;
    $response = ($fetch[0]["password"] == $_POST["password"]) ? 1 : 0;

    print json_encode(["login"=> $response]);
}

function select(){
    
    global $db,$table;

    $where =  "username = '$_GET[username]'";

    $fetch = $db->select("*",$table,$where);
    $fetch = ($fetch == NULL) ? [] : $fetch;
    print json_encode($fetch);	

}

function selectcan(){
    global $db,$table;
    $fetch = $db->select("*",$table);
    $fetch = ($fetch == NULL) ? [] : $fetch;
    print json_encode($fetch);	

}

function signUp(){
    global $db,$table;

    $data = $_POST;
    $data["id"] = "";

    $fetch = $db->insert($table,$data);
    print json_encode($fetch);
}

function getMyInfo(){
    global $db,$table;

    $where =  "username = '$_GET[username]'";

    $user = $db->select("id,username,email",$table,$where)[0];

    $user["library"] = $db->select("songs_id as id","libraries","users_id = $user[id]");
    foreach ($user["library"] as $key => $song) {
        $user["library"][$key] = $db->select("*","songs","id = $song[id]")[0];
    }

    $user["playlist"] = $db->select("id,name,isPublic","playlists","owner = $user[id]");
    
    echo json_encode($user);
}

//////////////////
function getMysongNoLIBRARY(){
    global $db,$table;

    $where =  "username = '$_GET[username]'";

    $user = $db->select("id,username,email",$table,$where)[0];

    $user["library"] = $db->select("songs_id as id","libraries","users_id = $user[id]");
    foreach ($user["library"] as $key => $song) {
        $user["library"][$key] = $db->select("*","songs","id = $song[id]")[0];
    }

    $user["playlist"] = $db->select("id,name,isPublic","playlists","owner = $user[id]");
    
    echo json_encode($user);
}

function nmc(){

    global $db;

    $where =  "username = '$_GET[username]'";
    $user = $db->select("id,username,email","users",$where)[0];

    $id = $user["id"];

    $songs = $db->selectAdvanced("SELECT * FROM songs o
    WHERE NOT EXISTS (SELECT 1
    FROM (select * from songs INNER JOIN libraries where users_id=$id) c 
    WHERE c.songs_id = o.id )");

    echo json_encode($songs);
    
 
}


