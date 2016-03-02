app.controller('customersCtrl', function ($scope, $rootScope, $routeParams, $location, $http, Data) {
    //initially set those objects to null to avoid undefined error
    $scope.customers={};
    Data.get('customers').then(function (results) {
        $scope.customers=results;
    });
});