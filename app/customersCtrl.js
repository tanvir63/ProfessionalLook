app.controller('customersCtrl', function ($scope, $rootScope, $routeParams, $location, $http, Data) {
    //initially set those objects to null to avoid undefined error
    //$scope.customers={};
    //alert("before function");
    //$scope.customers = function() {
      //  alert("before customers");
    Data.post('customers').then(function (results) {
            alert("after customers");
            //console.log(results);
        });
    //};
});