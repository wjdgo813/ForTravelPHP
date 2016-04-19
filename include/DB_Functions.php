<?php
//db�� ����� ������ �Է��ϰ�, db���� ����� ������ ������ ����
class DB_Functions{
	private $conn;
	//������
	function __construct(){
		require_once 'DB_Connect.php';
		$db = new DB_Connect();
		$this->conn = $db->connect();
	}
	//����� ���� �Է� 
	//input : name,id,password
	//output : user
	public function storeUser($name,$id,$password){
		$hash = $this->hashSSHA($password);
		$encrypted_password = $hash["encrypted"]; //��й�ȣ ��ȣȭ
		$salt = $hash["salt"]; // salt�� �̿��� �ؽ�ȭ �� ��й�ȣ �յڿ� �߰������� ���� ����, ��, ��й�ȣ ���� ��ȭ
	
		$stmt = $this->conn->prepare("INSERT INTO USER(id,name,encrypted_password,salt) VALUES(?, ?, ?, ?)");
		$stmt->bind_param("ssss",$id, $name, $encrypted_password, $salt);
		$result = $stmt->execute();
		$stmt->close();

		if($result){
			//$stmt = mysqli_query($this->conn,"SELECT * FROM USER WHERE id"
			$stmt = $this->conn->prepare("SELECT id,name FROM USER WHERE id = ?");
			$stmt->bind_param("s", $id);
			$stmt->execute();
			//$user = $stmt->get_result()->fetch_assoc();
			$stmt->bind_result($myId,$myName);
			while($stmt->fetch())
			{
				$user = array("id"=>$myId,"name"=>$myName);
			}
			$stmt->close();

			return $user;
		}
		else{
			return false;
		}
	}


	//input : id,password
	//output : user

	public function getUserByIdAndPassword($id,$password){
		$stmt = $this->conn->prepare("SELECT id, name, encrypted_password, salt FROM USER WHERE id =?");
		$stmt->bind_param("s",$id);
		if($stmt->execute()){
			$stmt->bind_result($myId,$myName,$enpass,$sal);
			if($stmt->fetch()){
			$user = array("id"=>$myId,"name"=>$myName,"encrypted_password"=>$enpass,"salt"=>$sal);
			}
			else{
				return NULL;
			}
			$salt = $user["salt"];
			$encrypted_password = $user["encrypted_password"];
			$hash = $this->checkhashSSHA($salt,$password);


			if( $hash == $encrypted_password ){ //��й�ȣ Ȯ�� �۾� 
				$stmt->close();
				return $user;
			}
			else{
				return null;
			}
		}
	}

	public function isUserExisted($id){
		$stmt = $this->conn->prepare("SELECT id FROM USER WHERE id = ?");
		$stmt->bind_param("s",$id);
		$stmt->execute();
		$stmt->store_result();
		if($stmt->num_rows > 0){
			//user�� �����Ҷ�
			$stmt->close();
			return true;
		}else{
			//user�� �������� ������
			$stmt->close();
			return false;
		}
			
	}
	

	//ģ�� ��û
	//input : myId,frdId
	//output : �������� �����
	public function requestFriend($myId,$frdId){
		$stmt = $this->conn->prepare("insert into friendRequest values(?,(select id from USER where id = ?)
		)");
		$stmt->bind_param("ss",$myId,$frdId);
		$result = $stmt->execute();
		$stmt->close();
		if($result){
			$stmt = $this->conn->prepare("select sendId from friendRequest where sendId = ? and receiveId =? ");
			$stmt->bind_param("ss",$myId,$frdId);
			$stmt->execute();
			$stmt->bind_result($sendId);

			while($stmt->fetch()){
				$resultRequest = array('id'=>$sendId);
			}
			$stmt->close();
			if($resultRequest)
			{
				return $resultRequest;
			}
			else{
				return false;
			}
		}
		else {
			return false;
		}

	}
	
	public function postFriend($id){
		$stmt = $this->conn->prepare("select name from USER,friendRequest where USER.id = friendRequest.sendId AND friendRequest.sendId IN (SELECT sendId from friendRequest where receiveId = ?)");
		$stmt->bind_param("s",$id);

		$stmt->bind_result($result);
		$stmt->execute();
		$stmt->store_result();

		$name=array();
		while($stmt->fetch()){	
			array_push($name,$result);
		}
		$stmt->free_result();
		$stmt->close();
		if($name)				
			return $name;
		else
			return $false;
			
	}
	


	//����� gps ��� 
	//input : id,gps����
	//output : ���� ���� true or false.
	public function registerGps($id,$gps){
		$stmt = $this->conn->prepare("insert into gpsInfo(id,position) values(?,?) on duplicate key update position=?");
		$stmt->bind_param("sss",$id,$gps,$gps);
		$result = $stmt->execute();
		$stmt->close();

		if($result){
			$stmt = $this->conn->prepare("SELECT id,position FROM gpsInfo WHERE id = ? ");
			$stmt->bind_param("s",$id);
			$stmt->execute();

			$stmt->bind_result($myId,$myPos);
			while($stmt->fetch())
			{
				$user = array('id'=>$myId,'pos'=>$myPos);
			}
			$stmt->close();

			if($user)
			{
				return true;
			}
			else{
				return false;
			}
		}
		else
		{
			return false;
		}		
	}

	public function agreeFriend($myId,$frdName){

		//friend ���̺� myId,frdId ������ insert
		$stmt = $this->conn->prepare("insert into friend(myId,frdId) values( ? , (select id from USER where USER.name = ? ))");
		$stmt->bind_param("ss",$myId,$frdName);
		$result = $stmt->execute();//���� ��� �� ����
		$stmt->close();
		
		if($result){
			//friend ���̺� frdId,myId ������ insert	
			$stmt = $this->conn->prepare("insert into friend(myId,frdId) values((select id from USER where USER.name = ?), ? )");
			$stmt->bind_param("ss",$frdName,$myId);
			$result2 = $stmt->execute(); //���� ��� �� ����
			$stmt->close();	

			if($result2){
				//friendRequest ���̺� �ִ� ��û �����
				$stmt = $this->conn->prepare("delete from friendRequest where receiveId = ? and sendId =(select id from USER where USER.name = ? )");
				$stmt->bind_param("ss",$myId,$frdName);
				$stmt->execute();
				$stmt->close();

				$stmt = $this->conn->prepare("select receiveId from friendRequest where receiveId = ? and sendId = (select id from USER where USER.name = ?)");
				$stmt->bind_param("ss",$myId,$frdName);
				$stmt->execute();
				$stmt->store_result();
				$num_of_rows=$stmt->num_rows;
				$stmt->close();
				if($num_of_rows >= 1){
					$resultStr3 = "failed to result3";
					return $resultStr3;
				}
				else
					return true;

			}
			else{
				$resultStr2 = "failed to result2";
				return $resultStr2;
			}
		}
		else{
			$resultStr = "failed to result1";
			return $resultStr;
		}
	}
	
	public function readFrdGPS($id){

		$stmt = $this->conn->prepare("SELECT name,position from gpsInfo,USER where gpsInfo.id=USER.id AND USER.id in (SELECT frdId from friend where myId = ? )");
		$stmt->bind_param("s",$id);
		$stmt->bind_result($name,$position);
		$stmt->execute();
		$stmt->store_result();
		$result = array();

		while($stmt->fetch())
		{

			$row_array["name"]=$name;
			$row_array["position"]=$position;
			array_push($result,$row_array);
			//$result = array("name"=>$name,"position"=>$position);
		}
		$stmt->close();
		if($result)
			return $result;
		else
			return false;
					
	
	}


	public function searchFriend($frdName){
		$stmt=$this->conn->prepare("SELECT position,isAble FROM gpsInfo where id = (SELECT id FROM USER where name = ?)");
		$stmt->bind_param("s",$frdName);
		$stmt->bind_result($position,$isAble);
		$result = $stmt->execute();
		$stmt->store_result();
		
		if($result){
			if($stmt->fetch()){
				if($isAble==1)
					return $position;
				else{
					$prohibit="2";
					return $prohibit;
				}
			}
		}
		else{
			return false;
		}
	}
	
	public function agreeTracking($id,$isAble){
		$stmt=$this->conn->prepare("UPDATE gpsInfo SET isAble = ? WHERE id = ? ");
		$stmt->bind_param("ss",$isAble,$id);
		$result = $stmt->execute();
		$stmt->store_result();
		if(result){
			return true;
		}
		else{
			return false;
		}
	}

	//��й�ȣ ��ȣȭ 
	// input : password 
	// output : hash (��ȣȭ�� ��й�ȣ)
	public function hashSSHA($password){
		$salt = sha1(rand());
		$salt = substr($salt,0,10);
		$encrypted = base64_encode(sha1($password.$salt,true).$salt);
		$hash = array("salt"=>$salt,"encrypted"=>$encrypted);
		return $hash;
	}

	public function checkhashSSHA($salt,$password){
		$hash = base64_encode(sha1($password.$salt,true).$salt);
		return $hash;
	}


	
	 public function fetchAssocStatement($stmt) {
      if($stmt->num_rows>0)
      {
         $result = array();
         $md = $stmt->result_metadata();
         $params = array();
         while($field = $md->fetch_field()) {
            $params[] = &$result[$field->name];
         }
         call_user_func_array(array($stmt, 'bind_result'), $params);
         if($stmt->fetch())
            return $result;
      }

      return null;
   }


}
?>