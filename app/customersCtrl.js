app.controller('customersCtrl', function ($scope, $rootScope, $routeParams, $location, $http, Data) {
    Data.get('customers').then(function (results) {
        $scope.customers=results;
        $scope.currentPage = 1; //current page
		$scope.entryLimit = 5; //max no of items to display in a page
		$scope.filteredItems = $scope.customers.length; //Initially for no filter
		$scope.totalItems = $scope.customers.length;
    });
    
    $scope.setPage = function(pageNo) {
		$scope.currentPage = pageNo;
	};
	$scope.filter = function() {
		$timeout(function() {
		      $scope.filteredItems = $scope.filtered.length;
		}, 10);
	};
	$scope.sort_by = function(predicate) {
		$scope.predicate = predicate;
		$scope.reverse = !$scope.reverse;
	};
});