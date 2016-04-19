<?php
require_once 'include/DB_Functions.php';

$db = new DB_Functions();
$response = array("error"=>FALSE);

if(isset($_POST['gps']) && isset($_POST['id'])){
	$gps = $_POST['gps'];
	$id = $_POST['id'];

	$user = $db->registerGps($id,$gps);
	if($user){
		$response["error"]=false;
		$response["user"]["gps"]=$user["pos"];
		echo json_encode($response);
	}
	else{
		$response["error"]=true;
		$response["error_msg"]="you can't register your GPS!";
		echo json_encode($response);
	}
	
	
}else{
	$response["error"]=TRUE;
	$response["error_msg"]="Required parameters ( id or gps) is missing!";
	echo json_encode($response);
}
?>
