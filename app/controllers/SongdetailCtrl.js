myApp.controller('SongdetailCtrl', ["$scope", "$rootScope", "authFact", "$http", "$location", "$routeParams", "song_detail",
    function($scope, $rootScope, authFact, $http, $location, $routeParams, song_detail) {
        $scope.song_detail = song_detail.data;
        console.log(song_detail);
    }]);





