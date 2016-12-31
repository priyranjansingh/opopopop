myApp.controller('PaymenthistoryCtrl', ["$scope", "$rootScope", "authFact", "$http", "$location", "$routeParams", "payment_history_list", "total_payment_history_count",
    function($scope, $rootScope, authFact, $http, $location, $routeParams, payment_history_list, total_payment_history_count) {
        var userObj = authFact.getUserObject();
        $scope.first_name = userObj.user_detail.first_name;
        $scope.last_name = userObj.user_detail.last_name;
        $scope.email = userObj.user_detail.email;


        $scope.payment_history_list = payment_history_list.data;
        $scope.totalItems = total_payment_history_count.data;
        $scope.currentPage = 1;
        $scope.processing = false;
        $scope.show_pagination = false;
        if ($scope.totalItems > $rootScope.itemPerPage)
        {
            $scope.show_pagination = true;
        }


    }]);







