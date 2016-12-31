myApp.controller('RegisterCtrl', ["$scope", "authFact", "$http", "$location", "$routeParams", "$cookies", "$rootScope", "CountryStateService", "DjTypeService",
    function($scope, authFact, $http, $location, $routeParams, $cookies, $rootScope, CountryStateService, DjTypeService) {

        $scope.dj_company_name = '';
        $scope.dj_year_count = '';
        $scope.street = '';

        if ($rootScope.currentUserSignedIn)
        {
            $location.path('/profile');
        }


        CountryStateService.getCountry().then(function(d) {
            $scope.countries = d;
        });
        DjTypeService.getDjTYpe().then(function(d) {
            $scope.djtype = d;
        });

        $scope.getCountryStates = function() {
            CountryStateService.getCountryState($scope.country).then(function(d) {
                $scope.states = d;
            });
        };

        $scope.registerUser = function() {
            $scope.processing = true;
            $scope.djTypeIdArray = [];
            angular.forEach($scope.djtype, function(dtype) {
                if (!!dtype.selected)
                    $scope.djTypeIdArray.push(dtype.id);
            })

            var data = {
                "first_name": $scope.first_name,
                "last_name": $scope.last_name,
                "dj_name": $scope.dj_name,
                "dj_company_name": $scope.dj_company_name,
                "dj_year_count": $scope.dj_year_count,
                "dj_type_id_arr": $scope.djTypeIdArray,
                "address": $scope.address,
                "street": $scope.street,
                "zip": $scope.zip,
                "country_id": $scope.country,
                "state_id": $scope.state,
                "email": $scope.email,
                "mobile": $scope.mobile,
                "password": $scope.password,
                "confirm_password": $scope.confirm_password
            }

            $http.post(base_url + "user/add", data)
                    .success(function(response) {
                         $scope.processing = false;
                        authFact.setAccessToken(response.user_detail.id);
                        authFact.setUserObject(response);
                        $rootScope.currentUserSignedIn = true;
                        $location.path('/plans');
                    });
        }
    }]);

myApp.directive('pwCheck', function() {
    return {
        require: 'ngModel',
        link: function(scope, elem, attrs, ctrl) {
            var firstPassword = '#' + attrs.pwCheck;
            elem.add(firstPassword).on('keyup', function() {
                scope.$apply(function() {
                    var v = elem.val() === $(firstPassword).val();
                    ctrl.$setValidity('pwmatch', v);
                });
            });
        }
    };
});


myApp.directive('uniqueEmail', function($http) {
    return {
        require: 'ngModel',
        link: function(scope, element, attrs, ctrl) {
            element.bind('keyup', function(e) {
                ctrl.$setValidity('unique', true);

                var data = {
                    "user_email": element.val()
                };
                $http.post(base_url + "user/checkEmail", data)
                        .success(function(response) {
                            if (response.status == 'exists')
                            {
                                ctrl.$setValidity('unique', false);
                            }
                            else if (response.status == 'not_exists')
                            {
                                ctrl.$setValidity('unique', true);
                            }
                        });

            });
        }
    };
});






