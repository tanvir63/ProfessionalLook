app.controller('editCustomerCtrl', function ($scope, $rootScope, $routeParams, $location, $http, Data) {
    //initially set those objects to null to avoid undefined error
    
    var customerId = ($routeParams.customerID) ? parseInt($routeParams.customerID) : 0;
    
    $rootScope.title = (customerId > 0) ? 'Edit Customer' : 'Add Customer';
    $scope.buttonText = (customerId > 0) ? 'Update Customer' : 'Add New Customer';
    
    $scope.originalCustomer ={};
    $scope.modifiedCustomer={};
    if(customerId>0)
    {
        Data.post('customer',{customerId: customerId}).then(function (result) {
            $scope.originalCustomer=result;
            $scope.originalCustomer._id = customerId;
            $scope.modifiedCustomer = angular.copy($scope.originalCustomer);
            $scope.modifiedCustomer._id = customerId;
        });
    }

    $scope.isClean = function() {
        return angular.equals($scope.originalCustomer, $scope.modifiedCustomer);
    }

    $scope.deleteCustomer = function(customer) {
        $location.path('/customers');
        if(confirm("Are you sure to delete customer number: "+$scope.originalCustomer._id)==true)
        //console.log(customer.customerNumber);
            Data.post('deleteCustomer',{customerId: customer.customerNumber}).then(function (result) {
                
            });
      };

    $scope.saveCustomer = function(customer) {
        $location.path('/customers');
        $controllerName="";
        if (customerId > 0) {
            $controllerName='editCustomer';
        }
        else
        {
            $controllerName='addCustomer';
        }
        
        Data.post($controllerName,{customer:customer}).then(function (result) {
              //console.log(result);
        });
    };
});