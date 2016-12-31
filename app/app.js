var myApp = angular.module('myApp', ['ngRoute', 'ngCookies','ngProgress','angularPayments','angularSpinner','ui.bootstrap']).run(function($rootScope) {
    $rootScope.backend_base_url = "http://localhost/opopopop/services/";
    $rootScope.front_base_url = "http://localhost/opopopop/";
    $rootScope.music_base_url = "http://localhost/";
    $rootScope.currentUserSignedIn = false;
    $rootScope.itemPerPage = 10;

});
var base_url = "http://localhost/opopopop/services/";

Stripe.setPublishableKey('pk_test_jwghFvJb4NmKWhA16j1wgQva');

myApp.filter('capitalize', function() {
    return function(input) {
        return (!!input) ? input.charAt(0).toUpperCase() + input.substr(1).toLowerCase() : '';
    }
});
























