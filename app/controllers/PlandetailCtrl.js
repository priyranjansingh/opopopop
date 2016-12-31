myApp.controller('PlandetailCtrl', ["$scope", "$rootScope", "authFact", "$http", "$location", "$routeParams", "plan_detail",
    function($scope, $rootScope, authFact, $http, $location, $routeParams, plan_detail) {
        $scope.plan_detail = plan_detail.data;
        var userObj = authFact.getUserObject();
        //$scope.active_plan_id = userObj.user_plan_detail.plan_id;
        $scope.coupon_code_message_flag = false;
        $scope.coupon_code_message = '';
        $scope.final_plan_amount = '';



        $scope.applyCoupon = function() {
            $scope.processing = true;
            var a = $rootScope.backend_base_url + "transactions/applycoupon";
            var data = {
                "coupon_code": $scope.coupon_code,
                "plan_id": $scope.plan_detail.id
            }
            $http.post(a, data)
                    .success(function(response) {
                        $scope.processing = false;
                        if (response.status == 'success')
                        {
                            $scope.coupon_code_message_flag = true;
                            $scope.coupon_code_message = response.message;
                            $scope.final_plan_amount = response.amount;
                        }
                        else if (response.status == 'failure')
                        {
                            $scope.coupon_code_message_flag = true;
                            $scope.coupon_code_message = response.message;
                            $scope.final_plan_amount = '';
                        }

                    });
        }

        $scope.paypalSubmit = function() {
            var data = {
                "plan_detail": $scope.plan_detail,
                "plan_id": $scope.plan_detail.id,
            }
            $http.post(base_url + "transactions/add", data)
                    .success(function(response) {
                        var data = {};
                        data["business"] = response.paypal_detail.PAYPAL_BUSINESS_EMAIL;
                        data["notify_url"] = response.paypal_detail.PAYPAL_NOTIFY_URL;
                        data["cancel_return"] = response.paypal_detail.PAYPAL_NOTIFY_URL;
                        data["return"] = response.paypal_detail.PAYPAL_RETURN_URL;
                        data["rm"] = '2';
                        data["lc"] = '';
                        data["no_shipping"] = '1';
                        data["no_note"] = '1';
                        data["currency_code"] = 'USD';
                        data["page_style"] = 'paypal';
                        data["charset"] = 'utf-8';
                        data["item_name"] = response.trans_detail.plan_name;
                        data["custom"] = response.trans_detail.user_id + '#' + response.trans_detail.plan_id;
                        data["cmd"] = '_xclick-subscriptions';
                        data["src"] = '1';
                        data["srt"] = '0';
                        if ($scope.final_plan_amount)
                        {
                            data["a1"] = $scope.final_plan_amount;
                        }
                        else
                        {
                            data["a1"] = response.trans_detail.amount;
                        }
                        data["p1"] = response.trans_detail.plan_duration;
                        data["t1"] = response.trans_detail.plan_duration_type;
                        data["a3"] = response.trans_detail.amount;
                        data["p3"] = response.trans_detail.plan_duration;
                        data["t3"] = response.trans_detail.plan_duration_type;
                        var form = $('<form/></form>');
                        form.attr("action", "https://www.sandbox.paypal.com/cgi-bin/webscr");
                        form.attr("method", "POST");
                        form.attr("style", "display:none;");
                        $.each(data, function(name, value) {
                            if (value != null) {
                                var input = $("<input></input>").attr("type", "hidden").attr("name", name).val(value);
                                form.append(input);
                            }
                        });
                        $("body").append(form);

                        // submit form
                        form.submit();
                        form.remove();
                        //console.log(response);

                    });
        }

        $scope.onSubmit = function() {
            $scope.processing = true;
        };

        $scope.stripeCallback = function(code, result) {
            $scope.processing = false;
            $scope.hideAlerts();
            if (result.error) {
                $scope.stripeError = result.error.message;
            } else {
                $scope.stripeToken = result.id;
                var data = {
                    "plan_detail": $scope.plan_detail,
                    "plan_id": $scope.plan_detail.id,
                    "stripe_token":$scope.stripeToken,
                }
                $http.post(base_url + "transactions/process", data)
                        .success(function(response) {
                            $location.path('/thankyou');
                        });



            }
        };

        $scope.hideAlerts = function() {
            $scope.stripeError = null;
            $scope.stripeToken = null;
        };




    }]);




myApp.directive("ngFormCommit", [function() {
        return {
            require: "form",
            link: function($scope, $el, $attr) {
                $el[0].commit = function() {
                    $el[0].submit();
                };
            }
        };
    }])





