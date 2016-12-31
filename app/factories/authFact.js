myApp.factory('authFact', ["$cookies", function($cookies) {
        var authFact = {};
        authFact.setAccessToken = function(accessToken) {
            $cookies.put('accessToken', accessToken);
        };

        authFact.getAccessToken = function() {
            authFact.authToken = $cookies.get('accessToken');
            return authFact.authToken;
        };

        authFact.removeAccessToken = function() {
            $cookies.remove('accessToken');
        };

        authFact.setUserObject = function(userObj) {
            $cookies.putObject('userObj', userObj);
        };


        authFact.getUserObject = function() {
            var userObj = $cookies.getObject('userObj');
            if (userObj)
            {
                return userObj;
            }
            else
            {
                console.log("User could not be found");
            }
        };

        authFact.removeUserObject = function() {
            $cookies.remove('userObj');
        };

        authFact.setUserPaymentPlanObject = function(userPaymentPlanObj) {
            $cookies.putObject('userPaymentPlanObj', userPaymentPlanObj);
        };

        authFact.getUserPaymentPlanObject = function() {
            var userPaymentPlanObj = $cookies.getObject('userPaymentPlanObj');
            if (userPaymentPlanObj)
            {
                return userPaymentPlanObj;
            }
            else
            {
                console.log("User Payment Plan details could not be found");
            }
        };
        authFact.removeUserPaymentPlanObject = function() {
            $cookies.remove('userPaymentPlanObj');
        };


        return authFact;
    }]);