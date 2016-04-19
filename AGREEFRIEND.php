<?php
require_once 'include/DB_Functions.php';

$db = new DB_Functions();
$response = array("error"=>FALSE);

if(isset($_POST['myId']) && isset($_POST['frdName']) ){
	$myId = $_POST['myId'];
	$frdName = $_POST['frdName'];

	$result = $db->agreeFriend($myId,$frdName);

	if($result == true){
		$response["error"]=false;
		$response["error_msg"]=$result;
		echo json_encode($response);
	}
	else{
		$response["error"]=true;
		$response["error_msg"]=$result;
		echo json_encode($response);
	}

}else{
	$response["error"]=TRUE;
	$response["error_msg"]="Required parameters (id or frdName) is missing!";
	echo json_encode($response);
}
?>
