myApp.controller('LogoutCtrl', ["$scope", "authFact", "$http", "$location", "$routeParams", "$cookies", "$rootScope", "FlashService",
    function($scope, authFact, $http, $location, $routeParams, $cookies, $rootScope, FlashService) {
       

             $http.get(base_url + "user/logout")
                .success(function(response) {
                    authFact.removeAccessToken();
                    authFact.removeUserObject();
                    authFact.removeUserPaymentPlanObject();
                    $rootScope.currentUserSignedIn = false;
                    $location.path('/');
                    $scope.$apply();

                });
      

    }]);








