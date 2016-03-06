<?php

class DbHandler {

    private $conn;

    function __construct() {
        require_once 'dbConnect.php';
        // opening db connection
        $db = new dbConnect();
        $this->conn = $db->connect();
    }
    
    /**
     * Fetching single record
     */
    public function getOneRecord($query) {
        $r = $this->conn->query($query.' LIMIT 1') or die($this->conn->error.__LINE__);
        return $result = $r->fetch_assoc();    
    }
    
    /**
     * Fetching records
     */
    public function getRecords($query) {
        
        //echo("<script>console.log('PHP: ".$query."');</script>");
        $r = $this->conn->query($query) or die($this->conn->error.__LINE__);
        $result = array();
        if($r->num_rows > 0){        
            while($row = $r->fetch_assoc()){
                $result[] = $row;
            }
        }
        require_once '../libs/ChromePhp.php'; 
		ChromePhp::log($result);
        return $result;
    }
    
    /**
     * Creating new record
     */
    public function insertIntoTable($obj, $column_names, $table_name) {
        $c = (array) $obj;
        $keys = array_keys($c);
        $columns = '';
        $values = '';
        foreach($column_names as $desired_key){ // Check the obj received. If blank insert blank into the array.
           if(!in_array($desired_key, $keys)) {
                $$desired_key = '';
            }else{
                $$desired_key = $c[$desired_key];
            }
            $columns = $columns.$desired_key.',';
            $values = $values."'".$$desired_key."',";
        }
        $query = "INSERT INTO ".$table_name."(".trim($columns,',').") VALUES(".trim($values,',').")";
        
        $r = $this->conn->query($query) or die($this->conn->error.__LINE__);
        if ($r) 
        {
            //$new_row_id = $this->conn->insert_id;
            $result = array('status' => "Success", "msg" => " Added Successfully.");
            
        } 
        else 
        {
            $result= array('status' => "failed", "msg" => " Add failed.");
        }
        
        return $result;
    }
    
    /**
     * Updating a record
     */
    public function updateTable($obj, $column_names,$primary_key_name, $table_name) {
            $data = (array) $obj;
            $keys = array_keys($data);
  			$id = $data[$primary_key_name];
            
  			$columns = '';
  			$values = '';
  			foreach($column_names as $desired_key){ // Check the data received. If key does not exist, insert blank into the array.
  			   if(!in_array($desired_key, $keys)) {
  			   		$$desired_key = '';
  				}else{
  					$$desired_key = $data[$desired_key];
  				}
  				$columns = $columns.$desired_key."='".$$desired_key."',";
  			}
            
  			$query = "UPDATE ".$table_name." SET ".trim($columns,',')." WHERE ".$primary_key_name."=$id";
           
  			if(!empty($data)){
                $r = $this->conn->query($query) or die($this->conn->error.__LINE__);
  				$success = array('status' => "Success", "msg" => $table_name.$id." Updated Successfully.");
                ChromePhp::log($success);
  				return $success;
  			}
            else
            {
  				$failed = array('status' => "Failed", "msg" => $table_name.$id." Update Failed.");	// "No Content" status
                return $failed; 
            }
    }
    
    /**
     * Deleting a record
     */
    public function deleteRecord( $primary_key_name, $primary_key_value, $table_name){
        $query = "DELETE FROM ".$table_name." WHERE ".$primary_key_name." = ".$primary_key_value;
        
        $r = $this->conn->query($query) or die($this->conn->error.__LINE__);
        require_once '../libs/ChromePhp.php'; 
        ChromePhp::log("delete result is : ");
			ChromePhp::log($r);
        if ($r) 
        {
            //$new_row_id = $this->conn->insert_id;
            $result = array('status' => "Success", "msg" => " Deleted Successfully.");
        } 
        else 
        {
            $result= array('status' => "failed", "msg" => " Delete failed.");
        }
        
        return $result;
	}
        
    public function getSession(){
    if (!isset($_SESSION)) {
        session_start();
    }
    $sess = array();
    if(isset($_SESSION['uid']))
    {
        $sess["uid"] = $_SESSION['uid'];
        $sess["name"] = $_SESSION['name'];
        $sess["email"] = $_SESSION['email'];
    }
    else
    {
        $sess["uid"] = '';
        $sess["name"] = 'Guest';
        $sess["email"] = '';
    }
    return $sess;
}
public function destroySession(){
    if (!isset($_SESSION)) {
    session_start();
    }
    if(isSet($_SESSION['uid']))
    {
        unset($_SESSION['uid']);
        unset($_SESSION['name']);
        unset($_SESSION['email']);
        $info='info';
        if(isSet($_COOKIE[$info]))
        {
            setcookie ($info, '', time() - $cookie_time);
        }
        $msg="Logged Out Successfully...";
    }
    else
    {
        $msg = "Not logged in...";
    }
    return $msg;
}
 
}

?>
