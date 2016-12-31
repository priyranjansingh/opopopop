(function ($) {
    $(document).ready(function () {
        'use strict';

        function born_init_owl_carousel() {

            $('.born-owl-carousel').each(function () {
                var $this            = $(this),
                    $loop            = $this.attr('data-loop') == 'yes',
                    $numberItem      = parseInt($this.attr('data-number')),
                    $Nav             = $this.attr('data-navControl') == 'yes',
                    $Dots            = $this.attr('data-Dots') == 'yes',
                    $autoplay        = $this.attr('data-autoPlay') == 'yes',
                    $autoplayTimeout = parseInt($this.attr('data-autoPlayTimeout')),
                    $marginItem      = parseInt($this.attr('data-margin')),
                    $rtl             = $this.attr('data-rtl') == 'yes',
                    $resNumber; // Responsive Settings
                $numberItem          = (isNaN($numberItem)) ? 1 : $numberItem;
                $autoplayTimeout     = (isNaN($autoplayTimeout)) ? 4000 : $autoplayTimeout;
                $marginItem          = (isNaN($marginItem)) ? 0 : $marginItem;
                switch ($numberItem) {
                    case 1 :
                        $resNumber = {
                            0 : {
                                items : 1
                            }
                        };
                        break;
                    case 2 :
                        $resNumber = {
                            0 : {
                                items : 1
                            },
                            480 : {
                                items : 1
                            },
                            768 : {
                                items : 2
                            },
                            992 : {
                                items : $numberItem
                            }
                        };
                        break;
                    case 3 :
                    case 4 :
                        $resNumber = {
                            0 : {
                                items : 1
                            },
                            480 : {
                                items : 1
                            },
                            768 : {
                                items : 2
                            },
                            992 : {
                                items : 3
                            },
                            1200 : {
                                items : $numberItem
                            }
                        };
                        break;
                    default : // $numberItem > 4
                        $resNumber = {
                            0 : {
                                items : 1
                            },
                            480 : {
                                items : 2
                            },
                            768 : {
                                items : 3
                            },
                            992 : {
                                items : 4
                            },
                            1200 : {
                                items : $numberItem
                            }
                        };
                        break;
                } // Endswitch

                $(this).owlCarousel({
                    loop : $loop, 
                    nav : $Nav,
                    navText : ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],
                    dots : $Dots,
                    autoplay : $autoplay,
                    autoplayTimeout : $autoplayTimeout,
                    margin : $marginItem,
                    //responsiveClass:true,
                    rtl : $rtl,
                    responsive : $resNumber,
                    autoplayHoverPause : true,
                    //center: true,
                    onRefreshed : function () {
                        var total_active = $this.find('.owl-item.active').length;
                        var i            = 0;
                        $this.find('.owl-item').removeClass('active-first active-last');
                        $this.find('.owl-item.active').each(function () {
                            i++;
                            if (i == 1) {
                                $(this).addClass('active-first');
                            }
                            if (i == total_active) {
                                $(this).addClass('active-last');
                            }
                        });
                    },
                    onTranslated : function () {
                        var total_active = $this.find('.owl-item.active').length;
                        var i            = 0;
                        $this.find('.owl-item').removeClass('active-first active-last');
                        $this.find('.owl-item.active').each(function () {
                            i++;
                            if (i == 1) {
                                $(this).addClass('active-first');
                            }
                            if (i == total_active) {
                                $(this).addClass('active-last');
                            }
                        });
                    },
                    onResized : function () {
                    }
                });
            });
        }

        born_init_owl_carousel();
        // List Music
        $('.single-playlist-inner').MusicPlayer(myPlaylist, {
            tracksToShow : 4,
            jPlayer : {
                swfPath : 'jquery-jplayer'
            }
        });
        $('.list-play').MusicPlayer(myPlaylist2, {
            tracksToShow : 4,
            jPlayer : {
                swfPath : 'jquery-jplayer'
            }
        });

        // CountDown 
        if (jQuery('.born-countdown').length) {
            $('.born-countdown').each(function () {
                var $this                = $(this);
                var ts_countdown_to_date = $this.attr('data-time');
                $this.countdown(ts_countdown_to_date, function (event) {
                    var ts_day    = event.strftime('%-D');
                    var ts_hour   = event.strftime('%-H');
                    var ts_minute = event.strftime('%-M');
                    var ts_second = event.strftime('%-S');
                    $('.born-days').html(ts_day);
                    $('.born-hours').html(ts_hour);
                    $('.born-minutes').html(ts_minute);
                    $('.born-seconds').html(ts_second);

                });
            });
        }
        //FUNFACT
        $('.item-funfact').appear(function () {
            var count_element = $('.funfact-number', this).html();
            $('.funfact-number', this).countTo({
                from : 0,
                to : count_element,
                speed : 2500,
                refreshInterval : 50
            });
        });
        //BACK TO TOP
        $(window).scroll(function () {
            if ($(window).scrollTop() > 50) {
                $('a.backtotop').fadeIn(1000);
            } else {
                $('a.backtotop').fadeOut(500);
            }

        });

        $('a.backtotop').click(function () {
            $('html, body').animate({scrollTop : 0}, 800);
            return false;
        });
        //Popup Event
        $('.tour-link').on('click', function () {
            $('#event-detail').fadeIn('1000');
        });
        $('.popup-close, .popup-overlay').on('click', function () {
            $('#event-detail').fadeOut('slow');
        });
        //nicescroll Cart
       // $('.content-event').scrollbar();
        //EQUAL ELEM
        function born_equal_elems() {
            $('.equal-container').each(function () {
                var $this = $(this);
                if ($this.find('.equal-elem').length) {
                    $this.find('.equal-elem').css({
                        'height' : '132'
                    });
                    var elem_height = 0;
                    $this.find('.equal-elem').each(function () {
                        var this_elem_h = $(this).height();
                        if (elem_height < this_elem_h) {
                            elem_height = this_elem_h;
                        }
                    });
                    $this.find('.equal-elem').height(elem_height);
                }
            });
        }

        if ($(window).width() > 1024) {
            born_equal_elems();
        }
        function popupHeight() {

            var height_p = $(window).height() - 160;
            $('.content-event').css('height', height_p);
            $('.event-detail,.media-event').css('height', height_p);

        }

        if ($(window).width() > 767) {
            popupHeight();
            $(window).resize(function () {
                popupHeight();
            });
        }
        //Wow animate
        new WOW().init();
        $('#horizontalTab').easyResponsiveTabs({
            type : 'default', //Types: default, vertical, accordion
            width : 'auto', //auto or any width like 600px
            fit : true,   // 100% fit in a container
            closed : 'accordion', // Start closed if in accordion view
            activate : function (event) { // Callback function if tab is switched
                var $tab  = $(this);
                var $info = $('#tabInfo');
                var $name = $('span', $info);
                $name.text($tab.text());
                $info.show();
            }
        });
    });
}(jQuery));