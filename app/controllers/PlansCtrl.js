myApp.controller('PlansCtrl', ["$scope", "$rootScope", "authFact", "$http", "$location", "$routeParams", "plan_list",
    function($scope, $rootScope, authFact, $http, $location, $routeParams, plan_list) {
        $scope.plan_list = plan_list.data;
        var user_payment_plan_obj = authFact.getUserPaymentPlanObject();
        if (user_payment_plan_obj)
        {
            $scope.is_paid = user_payment_plan_obj.user_payment_status;
            $scope.user_last_plan = user_payment_plan_obj.user_last_plan;
            $scope.user_last_transaction = user_payment_plan_obj.user_last_transaction;
        }
        else
        {
            $scope.is_paid = 0;
            $scope.user_last_plan = '';
            $scope.user_last_transaction = '';
        }    




        $scope.PlanDetail = function(id) {
            $location.path('/plans/' + id + '/plandetail');
        }

    }]);





