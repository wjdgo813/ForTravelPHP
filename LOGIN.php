<?php
	require_once 'include/DB_Functions.php';
	$db = new DB_Functions();

	$response = array("error"=>FALSE);

	if(isset($_POST['id']) && isset($_POST['password'])){
		$id = $_POST['id'];
		$password = $_POST['password'];

		$user = $db->getUserByIdAndPassword($id,$password);
		if($user){
			$response["error"]=FALSE;
			$response["user"]["name"]=$user["name"];
			$response["user"]["id"]=$user["id"];
			echo json_encode($response);}
		else{
			$response["error"]=TRUE;
			$response["error_msg"]="Login credentials are wrong. Please try again!";
			echo json_encode($response);
		}
	}else{
		$response["error"]=TRUE;
		$response["error_msg"]="Required parameters email or password is missing!";
		echo json_encode($response);
	}
?>
