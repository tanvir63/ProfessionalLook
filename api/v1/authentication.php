<?php 
$app->get('/session', function() {
    $db = new DbHandler();
    $session = $db->getSession();
    $response["uid"] = $session['uid'];
    $response["email"] = $session['email'];
    $response["name"] = $session['name'];
    echoResponse(200, $session);
});

$app->post('/login', function() use ($app) {
    require_once 'passwordHash.php';
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('email', 'password'),$r->customer);
    $response = array();
    $db = new DbHandler();
    $password = $r->customer->password;
    $email = $r->customer->email;
    $user = $db->getOneRecord("select uid,name,password,email,created from customers_auth where phone='$email' or email='$email'");
    if ($user != NULL) {
        if(passwordHash::check_password($user['password'],$password)){
        $response['status'] = "success";
        $response['message'] = 'Logged in successfully.';
        $response['name'] = $user['name'];
        $response['uid'] = $user['uid'];
        $response['email'] = $user['email'];
        $response['createdAt'] = $user['created'];
        if (!isset($_SESSION)) {
            session_start();
        }
        $_SESSION['uid'] = $user['uid'];
        $_SESSION['email'] = $email;
        $_SESSION['name'] = $user['name'];
        } else {
            $response['status'] = "error";
            $response['message'] = 'Login failed. Incorrect credentials';
        }
    }else {
            $response['status'] = "error";
            $response['message'] = 'No such user is registered';
        }
    echoResponse(200, $response);
});

$app->get('/customers', function() use ($app) {
	require_once '../libs/ChromePhp.php'; 
    $response = array();
    $db = new DbHandler();
    
    $customers = $db->getRecords("SELECT distinct c.customerNumber, c.customerName, c.email, c.address, c.city, c.state, c.postalCode, c.country FROM angularcode_customers c order by c.customerNumber desc");
    ChromePhp::log($customers);
    if ($customers != NULL) 
    {
        echoResponse(200, $customers);
    }
    else
        echoResponse(200, '');
});

$app->post('/customer', function() use ($app) {
    require_once '../libs/ChromePhp.php';    
    $response = array();
    $db = new DbHandler();
    $r = json_decode($app->request->getBody());
    $id=$r->customerId;
    $customer = $db->getOneRecord("SELECT distinct c.customerNumber, c.customerName, c.email, c.address, c.city, c.state, c.postalCode, c.country FROM angularcode_customers c where c.customerNumber=$id");
    ChromePhp::log($customer);
    if ($customer != NULL) 
    {
        echoResponse(200, $customer);
    }
    else
        echoResponse(200, '');
});

$app->post('/editCustomer', function() use ($app) {
    require_once '../libs/ChromePhp.php'; 
    $customer = json_decode(file_get_contents("php://input"),true);
    $primary_key='customerNumber';
    $table_name='angularcode_customers';
	$column_names = array('customerName', 'email', 'city', 'address', 'country');
    $response = array();
    $db = new DbHandler();
    $r = json_decode($app->request->getBody());
    //ChromePhp::log($r);
    //ChromePhp::log("r is ");
    //ChromePhp::log($r);
    
    //$id=$r->customer->customerNumber;
    
    $update_result = $db->updateTable($customer['customer'],$column_names,$primary_key,$table_name);
    //ChromePhp::log("result is : ");
    //ChromePhp::log($update_result);
    if ($update_result != NULL) 
    {
        echoResponse(200, $update_result);
    }
    else
        echoResponse(200, '');
});

$app->post('/addCustomer', function() use ($app) {
    require_once '../libs/ChromePhp.php'; 
    $customer = json_decode(file_get_contents("php://input"),true);
    $primary_key='customerNumber';
    $table_name='angularcode_customers';
	$column_names = array('customerName', 'email', 'city', 'address', 'country');
    $response = array();
    $db = new DbHandler();
    $r = json_decode($app->request->getBody());
    
    $update_result = $db->insertIntoTable($customer['customer'],$column_names,$table_name);
    if ($update_result != NULL) 
    {
        echoResponse(200, $update_result);
    }
    else
        echoResponse(200, '');
});

$app->post('/deleteCustomer', function() use ($app) {
    $primary_key_name='customerNumber';
    $table_name='angularcode_customers';
    
    $db = new DbHandler();
    $r = json_decode($app->request->getBody());
    $id=$r->customerId;
    
    $delete_result = $db->deleteRecord($primary_key_name, $id, $table_name);
    if ($delete_result != NULL) 
    {
        echoResponse(200, $delete_result);
    }
    else
        echoResponse(200, '');
});
$app->post('/signUp', function() use ($app) {
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('email', 'name', 'password'),$r->customer);
    require_once 'passwordHash.php';
    $db = new DbHandler();
    $phone = $r->customer->phone;
    $name = $r->customer->name;
    $email = $r->customer->email;
    $address = $r->customer->address;
    $password = $r->customer->password;
    $isUserExists = $db->getOneRecord("select 1 from customers_auth where phone='$phone' or email='$email'");
    if(!$isUserExists){
        $r->customer->password = passwordHash::hash($password);
        $tabble_name = "customers_auth";
        $column_names = array('phone', 'name', 'email', 'password', 'city', 'address');
        $result = $db->insertIntoTable($r->customer, $column_names, $tabble_name);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "User account created successfully";
            $response["uid"] = $result;
            if (!isset($_SESSION)) {
                session_start();
            }
            $_SESSION['uid'] = $response["uid"];
            $_SESSION['phone'] = $phone;
            $_SESSION['name'] = $name;
            $_SESSION['email'] = $email;
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to create customer. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "An user with the provided phone or email exists!";
        echoResponse(201, $response);
    }
});
$app->get('/logout', function() {
    $db = new DbHandler();
    $session = $db->destroySession();
    $response["status"] = "info";
    $response["message"] = "Logged out successfully";
    echoResponse(200, $response);
});
?>