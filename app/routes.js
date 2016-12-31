myApp.config(["$routeProvider", function($routeProvider) {
        $routeProvider

                .when("/", {
                    templateUrl: 'views/home/home.html',
                    controller: 'HomeCtrl',
                })
                .when('/about', {
                    templateUrl: 'views/home/about.html',
                    controller: 'HomeCtrl',
                })
                .when('/register', {
                    templateUrl: 'views/home/register.html',
                    controller: 'RegisterCtrl',
                })
                .when('/login', {
                    templateUrl: 'views/home/login.html',
                    controller: 'LoginCtrl',
                })
                .when('/forgotpassword', {
                    templateUrl: 'views/home/forgotpassword.html',
                    controller: 'ForgotpasswordCtrl',
                })
                .when('/logout', {
                    template: '',
                    controller: 'LogoutCtrl',
                })
                .when('/profile', {
                    templateUrl: 'views/user/profile.html',
                    controller: 'UserCtrl',
                    authenticated: true
                })
                .when('/payment_history', {
                    templateUrl: 'views/user/payment_history_list.html',
                    controller: 'PaymenthistoryCtrl',
                    resolve: {
                        payment_history_list: function($http) {
                            return $http.get(base_url + "paymenthistory/listing/");
                        },
                        total_payment_history_count: function($http) {
                            return $http.get(base_url + "paymenthistory/getTotalCount/");
                        },
                    },
                    authenticated: true
                })
                .when('/thankyou', {
                    templateUrl: 'views/user/thankyou.html',
                    controller: 'UserCtrl',
                    authenticated: true
                })
                .when('/cancel', {
                    templateUrl: 'views/user/cancel.html',
                    controller: 'UserCtrl',
                    authenticated: true
                })
                .when('/plans', {
                    templateUrl: 'views/plans/plans.html',
                    controller: 'PlansCtrl',
                    resolve: {
                        plan_list: function($http) {
                            return $http.get(base_url + "plans/listing/");
                        },
                    },
                    authenticated: true
                })
                .when('/plans/:id/plandetail', {
                    templateUrl: 'views/plans/plandetail.html',
                    controller: 'PlandetailCtrl',
                    resolve: {
                        plan_detail: function($route, $http) {
                            return $http.get(base_url + "plans/plandetail/" + $route.current.params.id);
                        }
                    },
                    authenticated: true
                })
                .when('/music', {
                    templateUrl: 'views/music/music_list.html',
                    controller: 'MusicCtrl',
                    resolve: {
                        music_list: function($route, $http) {
                            return $http.get(base_url + "music/listing/");
                        },
                        total_music_count: function($route, $http) {
                            return $http.get(base_url + "music/getTotalCount/");
                        },
                    },
                    authenticated: true
                })
                .when('/music/:slug', {
                    templateUrl: 'views/music/song_detail.html',
                    controller: 'SongdetailCtrl',
                    resolve: {
                        song_detail: function($route, $http) {
                            return $http.get(base_url + "music/detail/" + $route.current.params.slug);
                        }
                    },
                    authenticated: true
                })
                .when('/videos', {
                    templateUrl: 'views/videos/videos_list.html',
                    controller: 'VideosCtrl',
                    authenticated: true
                })

                .otherwise('/', {
                    templateUrl: 'views/home/home.html',
                    controller: 'HomeCtrl'
                });
    }]);

myApp.run(["$rootScope", "$location", "authFact", "ngProgressFactory", "$interval", function($rootScope, $location, authFact, ngProgressFactory, $interval) {
        var user_log = authFact.getAccessToken();
        if (!user_log) {
            $rootScope.currentUserSignedIn = false;
        }
        else
        {
            $rootScope.currentUserSignedIn = true;
        }


        $rootScope.$on('$routeChangeStart', function(event, next, current) {
            $rootScope.progressbar = ngProgressFactory.createInstance();
            $rootScope.progressbar.start();
            $rootScope.progressbar.setHeight('4px');
            $rootScope.progressbar.setColor('#dd2121');
            /*If route is authenticated then check if the user has access token, else return to login screen*/
            if (next.$$route.authenticated) {
                var userAuth = authFact.getAccessToken();
                if (!userAuth) {
                    $location.path("/");
                }
            }
        });


        $rootScope.$on('$routeChangeSuccess', function() {
            $rootScope.progressbar.complete();
        });




    }]);

