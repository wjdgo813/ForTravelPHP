<?php
require_once 'include/DB_Functions.php';

$db = new DB_Functions();
$response = array("error"=>FALSE);

if(isset($_POST['myId']) ){
	$id = $_POST['myId'];
	$result = $db->postFriend($id);

	if($result){
		$length = count($result);
		$repsonse["error"]=false;
		for($i=0;$i<$length;$i++){
		$response["name"]=$result;
		//$response["name"]["i"]=$result[$i];
		}

		//$response["name"]=$result["name"];
		echo json_encode($response);
	}
	else{
		$response["error"]=true;
		$response["error_msg"]="you don't have friendRequest";
	}

	
}else{
	$response["error"]=TRUE;
	$response["error_msg"]="Required parameters (id or frendId) is missing!";
	echo json_encode($response);
}
?>
