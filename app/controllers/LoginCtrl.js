myApp.controller('LoginCtrl', ["$scope", "authFact", "$http", "$location", "$routeParams", "$cookies", "$rootScope", "FlashService", "$interval",
    function($scope, authFact, $http, $location, $routeParams, $cookies, $rootScope, FlashService, $interval) {

        if ($rootScope.currentUserSignedIn)
        {
            $location.path('/profile');
        }

        $scope.loginUser = function() {

            var data = {
                "email": $scope.email,
                "password": $scope.password
            }

            $http.post(base_url + "user/login", data)
                    .success(function(response) {
                        if (response.status == 'exists')
                        {
                            authFact.setAccessToken(response.user_detail.id);
                            $cookies.putObject('userObj', response);
                            $rootScope.currentUserSignedIn = true;
                            authFact.setUserPaymentPlanObject(response.user_payment_plan_obj);
//                            $interval(function() {
//                                $http.post(base_url + "user/getUserPlan")
//                                        .success(function(response) {
//                                            authFact.setUserPaymentPlanObject(response);
//                                        });
//
//                            }, 1000);
                            $location.path('/profile');
                        }
                        else if (response.status == 'not_exists')
                        {
                            FlashService.Error('Email or Password is incorrect');
                        }

                    });
        }


    }]);








