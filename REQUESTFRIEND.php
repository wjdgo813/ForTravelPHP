<?php
require_once 'include/DB_Functions.php';

$db = new DB_Functions();
$response = array("error"=>FALSE);

if(isset($_POST['myId']) && isset($_POST['frdId']) ){
	$myId = $_POST['myId'];
	$frdId = $_POST['frdId'];

	$result = $db->requestFriend($myId,$frdId);
	if($result){
		$response["error"]=false;
		echo json_encode($response);
	}
	else{
		$response["error"]=true;
		$response["error_msg"]="You can't request friend";
		echo json_encode($response);
	}


	
}else{
	$response["error"]=TRUE;
	$response["error_msg"]="Required parameters (id or frendId) is missing!";
	echo json_encode($response);
}
?>
