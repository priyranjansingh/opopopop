myApp.controller('MusicCtrl', ["$scope", "$rootScope", "authFact", "$http", "$location", "$routeParams", "music_list", "total_music_count",
    function($scope, $rootScope, authFact, $http, $location, $routeParams, music_list, total_music_count) {
        $scope.music_list = music_list.data;
        $scope.totalItems = total_music_count.data;
        $scope.currentPage = 1;
        $scope.processing = false;
        $scope.show_pagination = false;
        $scope.accordion = '';
        if ($scope.totalItems > $rootScope.itemPerPage)
        {
            $scope.show_pagination = true;
        }

        $scope.test = function(file_name, folder_name, subfolder_name) {
            $('#player_container').show();
            $('.play.jp-control').addClass('active');
            if (folder_name)
            {
                wavesurfer.load('../remix/assets/uploads/remix/' + folder_name + '/' + file_name);
            }
            else if(subfolder_name)
            {
                wavesurfer.load('../remix/assets/uploads/remix/' + folder_name + '/'+subfolder_name+'/'+ file_name);
            }
            $('#waveform').css('background-image','./assets/developer/img/download.png');
            wavesurfer.play();
            wavesurfer.on('loading', function(percents) {
                if (percents == "100") {
                    $('#waveform').css('background-image', 'none');
                }
            });
        }

        $scope.pageChanged = function() {
            $scope.processing = true;
            var current_page = $scope.currentPage;
            var page_limit = (current_page - 1) * $rootScope.itemPerPage;
            $http.get(base_url + "music/listing/" + page_limit + "/" + $rootScope.itemPerPage).then(function(response) {
                $scope.music_list = response.data;
                $scope.processing = false;
            });
        };

        $scope.maxSize = 5;
    }]);





