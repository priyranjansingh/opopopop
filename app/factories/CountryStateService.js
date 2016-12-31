myApp.factory("CountryStateService", ['$http',
    function($http) {

        var service = {};

        service.getCountry = function() {
            var promise = $http.get(base_url + "country/listing").then(function(response) {
                // The then function here is an opportunity to modify the response
                // The return value gets picked up by the then in the controller.
                return response.data;
            });
            // Return the promise to the controller
            return promise;
        };
        
        
         service.getCountryState = function(country_id) {
            var promise = $http.get(base_url + "country/getStateByCountry/"+country_id).then(function(response) {
                // The then function here is an opportunity to modify the response
                // The return value gets picked up by the then in the controller.
                return response.data;
            });
            // Return the promise to the controller
            return promise;
        };
        return service;
    }]);