<?php 
$app->post('/customers', function() use ($app) {
    $response = array();
    $db = new DbHandler();
    $customers = $db->getRecords("SELECT distinct c.customerNumber, c.customerName, c.email, c.address, c.city, c.state, c.postalCode, c.country FROM angularcode_customers c order by c.customerNumber desc");
    echoResponse(200, $this->json($customers));
    if ($customers != NULL) 
    {
        //echoResponse(200, $this->json($customers));
    }
    else
        echoResponse(200, '');
});
?>