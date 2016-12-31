myApp.controller('ForgotpasswordCtrl', ["$scope", "authFact", "$http", "$location", "$routeParams", "$cookies", "$rootScope", "FlashService", "$interval",
    function($scope, authFact, $http, $location, $routeParams, $cookies, $rootScope, FlashService, $interval) {

        if ($rootScope.currentUserSignedIn)
        {
            $location.path('/profile');
        }

        $scope.forgotPassword = function() {
            $scope.processing = true;
            var data = {
                "email": $scope.email,
            }

            $http.post(base_url + "user/forgotpassword", data)
                    .success(function(response) {
                        $scope.processing = false;
                        if (response.status == 'exists')
                        {
                            FlashService.Success('Password has been changed. Please check your mail.');
                        }
                        else if (response.status == 'not_exists')
                        {
                            FlashService.Error('Sorry! The email does not exist in our record.');
                        }

                    });
        }


    }]);








