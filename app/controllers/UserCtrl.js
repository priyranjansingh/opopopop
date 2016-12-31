myApp.controller('UserCtrl', ["$scope", "authFact", "$http", "$location", "$routeParams", "$uibModal", "$log", "$document",
    function($scope, authFact, $http, $location, $routeParams, $uibModal, $log, $document) {
        var userObj = authFact.getUserObject();
        $scope.first_name = userObj.user_detail.first_name;
        $scope.last_name = userObj.user_detail.last_name;
        $scope.email = userObj.user_detail.email;

        $scope.items = ['item1', 'item2', 'item3'];


        $scope.open = function() {
            var modalInstance = $uibModal.open({
                templateUrl: 'myModalContent.html',
                controller: 'ModalInstanceCtrl',
                controllerAs: '$ctrl',
                resolve: {
                    items: function() {
                        return $scope.items;
                    }
                }
            });

            modalInstance.result.then(function(selectedItem) {
                $scope.selected = selectedItem;
            }, function() {
                $log.info('Modal dismissed at: ' + new Date());
            });


        };

    }]);


myApp.controller('ModalInstanceCtrl', ["$scope", "authFact", "$http", "$location", "$routeParams", "$uibModal", "$log", "$document", "$uibModalInstance",
    function($scope, authFact, $http, $location, $routeParams, $uibModal, $log, $document, $uibModalInstance) {
        var $ctrl = this;


        $scope.password_changed_status = false;

        $ctrl.changePassword = function() {
            var data = {
                "password": $ctrl.password,
                "confirm_password": $ctrl.confirm_password
            }
            $http.post(base_url + "user/changePassword", data)
                    .success(function(response) {
                        $scope.password_changed_status = true;
                    });
        };

        $ctrl.ok = function() {
            $uibModalInstance.close();
        };
        $ctrl.cancel = function() {
            $uibModalInstance.dismiss('cancel');
        };

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







