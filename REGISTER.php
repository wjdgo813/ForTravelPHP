<?php
require_once 'include/DB_Functions.php';

$db = new DB_Functions();
$response = array("error"=>FALSE);

if(isset($_POST['name']) && isset($_POST['id']) && isset($_POST['password'])){
	$name = $_POST['name'];
	$id = $_POST['id'];
	$password = $_POST['password'];
	
	//�ߵ��� id�� �����ÿ� 
	if($db->isUserExisted($id)){
		$response["error"] = true;
		$response["error_msg"] = "User already existed with".$id;

		echo json_encode($response);
	}else{//�ߺ��� id�� �����ÿ� storeUser�� ���� ȸ������
		$user = $db->storeUser($name,$id,$password);
		if($user){
			$response["error"]=false;
			$response["user"]["name"]=$user["name"];
			$response["user"]["id"]=$user["id"];
			
			echo json_encode($response);
		}else{
			//user failed to store 
			$response["error"]=TRUE;
			$response["error_msg"]="Unknown error occurred in registration!";
			echo json_encode($response);
		}
	}
}else{
	$response["error"]=TRUE;
	$response["error_msg"]="Required parameters (name, id or password) is missing!";
	echo json_encode($response);
}
?>
