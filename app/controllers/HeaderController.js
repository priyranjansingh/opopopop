myApp.controller('HeaderController', ["$scope", "authFact", "$location", "$cookies", "$http", "$rootScope", "$window", "$interval",
    function($scope, authFact, $location, $cookies, $http, $rootScope, $window, $interval) {
        $scope.isActive = function(viewLocation) {
            if (viewLocation === $location.path())
            {
                return "menu-item highlight-menu";
            }
            else
            {
                return "menu-item";
            }
        };

//        $window.onload = function() {
//            var user_log = authFact.getAccessToken();
//            if (user_log) {
//                $interval(function(){
//                   $http.post(base_url + "user/getUserPlan")
//                    .success(function(response) {
//                           authFact.setUserPaymentPlanObject(response);
//                    });
//                
//                }, 1000);
//            }
//            
//        };

    }]);