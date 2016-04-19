<?php
require_once 'include/DB_Functions.php';

$db = new DB_Functions();
$response = array("error"=>FALSE);

if(isset($_POST['id']) ){
	$id = $_POST['id'];
	$result = $db->readFrdGPS($id);

	if($result){
		$length = count($result);
		$response["error"]=false;

		//for($i=0;$i<$length;$i++){
		
		$response["friend"]=$result;
		//$response["position"]=$result["position"];
		//}
		//$response["user"]["name"]=$result
		echo json_encode($response);
	}
	else{
		$response["error"]=true;
		$response["error_msg"]="you don't have friend";
	}

	
}else{
	$response["error"]=TRUE;
	$response["error_msg"]="Required parameters (id) is missing!";
	echo json_encode($response);
}
?>
