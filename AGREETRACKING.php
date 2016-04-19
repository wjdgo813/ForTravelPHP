<?php
require_once 'include/DB_Functions.php';

$db = new DB_Functions();
$response = array("error"=>FALSE);

if(isset($_POST['isAble']) && isset($_POST['id']) ){
	$isAble = $_POST['isAble'];
	$id = $_POST['id'];

	$result = $db->agreeTracking($id,$isAble);

	if($result){
		$response["error"]=false;
		echo json_encode($response);
	}

	else{
		$response["error"]=true;
		$response["error_msg"]=$result;
		echo json_encode($response);
	}	
}else{
	$response["error"]=TRUE;
	$response["error_msg"]="Required parameters (frdName) is missing!";
	echo json_encode($response);
}
?>
