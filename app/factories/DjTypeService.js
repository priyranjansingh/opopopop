myApp.factory("DjTypeService", ['$http',
    function($http) {

        var service = {};

        service.getDjTYpe = function() {
            var promise = $http.get(base_url + "djtype/listing").then(function(response) {
                return response.data;
            });
            return promise;
        };
        
        return service;
    }]);