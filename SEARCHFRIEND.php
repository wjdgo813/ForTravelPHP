<?php
require_once 'include/DB_Functions.php';

$db = new DB_Functions();
$response = array("error"=>FALSE);

if(isset($_POST['frdName']) ){
	$frdName = $_POST['frdName'];

	$result = $db->searchFriend($frdName);

	if($result != "2"){
		$response["error"]=false;
		$response["position"]=$result;
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
