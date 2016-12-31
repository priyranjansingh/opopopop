jQuery(document).ready(function ($) {

    'use strict';

    // Menu hover Mouse
    $('.main-menu li').mouseenter(function () {
            $('.main-menu li a').addClass('menu-item-hover');
            $(this).find('a').removeClass('menu-item-hover');
            $(this).find('a').addClass('menu-item-active');
        })
        .mouseleave(function () {
            $('.main-menu li a').removeClass('menu-item-hover menu-item-active');
        });

    $('.navbar-button').on('click', function () {
        $('.site-header').toggleClass('site-header-active');
        $('.header-style-2 .site-navigation').slideToggle('slow');
    });
    $('.nav-close').on('click', function () {
        $('.site-header').toggleClass('site-header-active');
    });
    //Menu sticky
    var $born_header = $('.main-top');
    var heightoff    = 0;
    if ($('#wpadminbar').length) {
        heightoff = $('#wpadminbar').outerHeight();
    }

    if ($(window).width() > 767) {
        $('.has-sticky-menu .main-top').sticky({topSpacing : heightoff});
    }

    $(window).load(function () {
        //Text rotato
        height_descript_banner();
    });
    full_height_banner();
    $(window).resize(function () {
        full_height_banner();
    });
    if ($(window).width() <= 1024) {
        //Add icon child
        $('.main-menu li.menu-item-has-children').append('<span class="ts-has-children"><i class="fa fa-caret-down"></i></span>');

        //Menu open mobile
        $(document).on('click', 'li.menu-item-has-children .ts-has-children', function (e) {

            var $this  = $(this);
            var thisLi = $this.closest('li');
            var thisUl = thisLi.closest('ul');
            var thisA  = $this.closest('a');

            if (thisLi.is('.sub-menu-open')) {
                thisLi.find('> .sub-menu').stop().slideUp('slow');
                thisLi.removeClass('sub-menu-open').find('> a').removeClass('active');
            }
            else {
                thisUl.find('> li.sub-menu-open > .sub-menu').stop().slideUp('slow');
                thisUl.find('> li.sub-menu-open').removeClass('sub-menu-open');
                thisUl.find('> li > a.active').removeClass('active');
                thisLi.find('> .sub-menu').stop().slideDown('slow');
                thisLi.addClass('sub-menu-open').find('> a').addClass('active');
            }

            e.stopPropagation();

        });
    }
    ;

});
function height_descript_banner() {
    var heightdescriptbanner = $('.descript-banner').outerWidth();
    var heightcategory       = $('.single-category').outerWidth();
    var leftplus             = 45;
    if (($(window).width() > 1024 ) && ($(window).width() < 1440 )) {
        leftplus = 30;
    }
    $('.descript-banner').css({
        'width' : heightdescriptbanner,
        'top' : heightdescriptbanner / 2 + 60,
        'left' : -( heightdescriptbanner / 2 + leftplus )

    });
    $('.single-category').css({
        'width' : heightcategory,
        'top' : heightcategory / 2 + 60,
        'left' : -( heightcategory / 2 + leftplus )

    });

}
function full_height_banner() {
    var heightoff = 0;
    if ($('#wpadminbar').length) {
        heightoff = $('#wpadminbar').outerHeight();
    }
    var full_height = $(window).outerHeight() - heightoff;
    $('.full-height').css('height', full_height);
}