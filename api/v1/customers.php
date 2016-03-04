<?php 
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
?>