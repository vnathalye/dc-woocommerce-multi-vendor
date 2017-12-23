jQuery(document).ready(function ($) {
    //checkbox_custome_design();
    $(".hasmenu > a").click(function (e) {

        if ($(this).attr('href') == '#')
            e.preventDefault();

        if (!$(this).hasClass("active")) {
            $(".hasmenu").find("a").removeClass("active");
            $(this).addClass("active");
            if (!$(this).closest("ul").hasClass('submenu')) {
                $(".hasmenu").find("ul").slideUp();
            }
            $(this).next("ul").slideDown();
        } else {
            $(this).removeClass("active");
            $('.hasmenu').find("ul").slideUp();
        }

    });


    $(".wcmp_stat_start_dt").datepicker({
        dateFormat: 'dd-mm-yy',
        onClose: function (selectedDate) {
            $(".wcmp_stat_end_dt").datepicker("option", "minDate", selectedDate);
        }
    });
    $(".wcmp_stat_end_dt").datepicker({
        dateFormat: 'dd-mm-yy',
        onClose: function (selectedDate) {
            $(".wcmp_stat_start_dt").datepicker("option", "maxDate", selectedDate);
        }
    });
    $(".wcmp_start_date_order").datepicker({
        dateFormat: 'dd-mm-yy',
        onClose: function (selectedDate) {
            $(".wcmp_end_date_order").datepicker("option", "minDate", selectedDate);
        }
    });
    $(".wcmp_end_date_order").datepicker({
        dateFormat: 'dd-mm-yy',
        onClose: function (selectedDate) {
            $(".wcmp_start_date_order").datepicker("option", "maxDate", selectedDate);
        }
    });
    $(".wcmp_tab").tabs();

    var sideslider = $('[data-toggle=collapse-side]');
    var sel = sideslider.attr('data-target');
    var sel2 = sideslider.attr('data-target-2');
    sideslider.click(function (event) {
        // $(this).toggleClass('collapsed');
        $(sel).toggleClass('in');
        $(sel2).toggleClass('out');
        $('.side-collapse-container').toggleClass('large');

        if ($(window).width() < 768) {
            $('#page-wrapper').toggleClass('overlay');
        }
    });


    if ($(window).width() <= 768) {
        $('.sidebar').addClass('in');
        $('#page-wrapper').removeClass('overlay');
        $('.side-collapse-container').removeClass('large');
        $('#page-wrapper').click(function (event) {
            if (!$('.navbar-default.sidebar').hasClass('in')) {
                $('.side-collapse-container').toggleClass('large');
                $('.navbar-default.sidebar').addClass('in');
                $('#page-wrapper').toggleClass('overlay');
            }
        });
    } else {
        $('.sidebar').removeClass('in');
        $('#page-wrapper').removeClass('overlay');
        $('.side-collapse-container').removeClass('large');
    }


});

//.................. for checkbox ............................. 
//function checkbox_custome_design() {
//    jQuery('.input-group-addon.beautiful').each(function () {
//        if (jQuery(this).find('span').hasClass('fa-check-square-o'))
//            return;
//        if (jQuery(this).find('span').hasClass('fa-square-o'))
//            return;
//
//        var $widget = jQuery(this),
//                $input = $widget.find('input'),
//                type = $input.attr('type');
//        settings = {
//            checkbox: {
//                on: {icon: 'fa  fa-check-square-o'},
//                off: {icon: 'fa fa-square-o'}
//            },
//            radio: {
//                on: {icon: 'fa fa-2x fa-dot-circle-o'},
//                off: {icon: 'fa fa-2x fa-circle-o'}
//            }
//        };
//
//        $widget.prepend('<span class="' + settings[type].off.icon + '"></span>');
//
//        $widget.find('span').on('click', function () {
//            $input.prop('checked', !$input.is(':checked'));
//            updateDisplay();
//
//            if ($input.hasClass('select_all_all')) {
//                if ($input.prop("checked") == true) {
//                    jQuery('.select_all').each(function () {
//                        jQuery(this).prop('checked', true);
//                        jQuery(this).parents('.input-group-addon').find('.fa').removeClass('fa-square-o');
//                        jQuery(this).parents('.input-group-addon').find('.fa').addClass('fa-check-square-o');
//                    });
//                } else {
//                    jQuery('.select_all').each(function () {
//                        jQuery(this).prop('checked', false);
//                        jQuery(this).parents('.input-group-addon').find('.fa').removeClass('fa-check-square-o');
//                        jQuery(this).parents('.input-group-addon').find('.fa').addClass('fa-square-o');
//                    });
//                }
//            }
//
//            if ($input.hasClass('select_all_processing')) {
//                if ($input.prop("checked") == true) {
//                    jQuery('.select_processing').each(function () {
//                        jQuery(this).prop('checked', true);
//                        jQuery(this).parent().find('.fa').removeClass('fa-square-o');
//                        jQuery(this).parent().find('.fa').addClass('fa-check-square-o');
//                    });
//                } else {
//                    jQuery('.select_processing').each(function () {
//                        jQuery(this).prop('checked', false);
//                        jQuery(this).parent().find('.fa').removeClass('fa-check-square-o');
//                        jQuery(this).parent().find('.fa').addClass('fa-square-o');
//                    });
//                }
//            }
//
//            if ($input.hasClass('select_all_completed')) {
//                if ($input.prop("checked") == true) {
//                    jQuery('.select_completed').each(function () {
//                        jQuery(this).prop('checked', true);
//                        jQuery(this).parent().find('.fa').removeClass('fa-square-o');
//                        jQuery(this).parent().find('.fa').addClass('fa-check-square-o');
//                    });
//                } else {
//                    jQuery('.select_completed').each(function () {
//                        jQuery(this).prop('checked', false);
//                        jQuery(this).parent().find('.fa').removeClass('fa-check-square-o');
//                        jQuery(this).parent().find('.fa').addClass('fa-square-o');
//                    });
//                }
//            }
//
//            if ($input.hasClass('select_all_withdrawal')) {
//                if ($input.prop("checked") == true) {
//                    jQuery('.select_withdrawal').each(function () {
//                        jQuery(this).prop('checked', true);
//                        jQuery(this).parent().find('.fa').removeClass('fa-square-o');
//                        jQuery(this).parent().find('.fa').addClass('fa-check-square-o');
//                    });
//                } else {
//                    jQuery('.select_withdrawal').each(function () {
//                        jQuery(this).prop('checked', false);
//                        jQuery(this).parent().find('.fa').removeClass('fa-check-square-o');
//                        jQuery(this).parent().find('.fa').addClass('fa-square-o');
//                    });
//                }
//            }
//
//            if ($input.hasClass('select_all_transaction')) {
//                if ($input.prop("checked") == true) {
//                    jQuery('.select_transaction').each(function () {
//                        jQuery(this).prop('checked', true);
//                        jQuery(this).parent().find('.fa').removeClass('fa-square-o');
//                        jQuery(this).parent().find('.fa').addClass('fa-check-square-o');
//                    });
//                } else {
//                    jQuery('.select_transaction').each(function () {
//                        jQuery(this).prop('checked', false);
//                        jQuery(this).parent().find('.fa').removeClass('fa-check-square-o');
//                        jQuery(this).parent().find('.fa').addClass('fa-square-o');
//                    });
//                }
//            }
//        });
//
//
//
//        function updateDisplay() {
//            var isChecked = $input.is(':checked') ? 'on' : 'off';
//            $widget.find('.fa').attr('class', settings[type][isChecked].icon);
//            //Just for desplay
//            isChecked = $input.is(':checked') ? 'checked' : 'not Checked';
//            $widget.closest('.input-group').find('input[type="text"]').val('Input is currently ' + isChecked)
//
//        }
//
//        updateDisplay();
//    });
//}
jQuery(document).ready(function ($) {
    var window_width = $(window).width();
    if (window_width <= 640) {
        var active_menu = $(".wcmp_main_menu ul li.hasmenu>a.active");
        var parent_ele = active_menu.parent();
        var submenu = parent_ele.find('ul.submenu');
        submenu.hide();
        active_menu.removeClass('active');
        if (!active_menu.hasClass('responsive_active')) {
            active_menu.addClass('responsive_active');
        }
    }
});
jQuery(window).resize(function () {
    var $ = jQuery.noConflict();
    var window_width = jQuery(window).width();
    if (window_width <= 640) {
        var active_menu = jQuery(".wcmp_main_menu ul li.hasmenu>a.active");
        var parent_ele = active_menu.parent();
        var submenu = parent_ele.find('ul.submenu');
        submenu.hide();
        active_menu.removeClass('active');
        if (!active_menu.hasClass('responsive_active')) {
            active_menu.addClass('responsive_active');
        }
    } else {
        var active_menu = jQuery(".wcmp_main_menu ul li.hasmenu>a.responsive_active");
        var parent_ele = active_menu.parent();
        var submenu = parent_ele.find('ul.submenu');
        submenu.show();
        active_menu.removeClass('responsive_active');
        if (!active_menu.hasClass('active')) {
            active_menu.addClass('active');
        }
    }

    // menu

    if ($(window).width() <= 768) {
        $('.sidebar').addClass('in');
        $('#page-wrapper').removeClass('overlay');
        $('.side-collapse-container').removeClass('large');
        $('#page-wrapper').click(function (event) {
            if (!$('.navbar-default.sidebar').hasClass('in')) {
                $('.side-collapse-container').toggleClass('large');
                $('.navbar-default.sidebar').addClass('in');
                $('#page-wrapper').toggleClass('overlay');
            }
        });
    } else {
        $('.sidebar').removeClass('in');
        $('#page-wrapper').removeClass('overlay');
        $('.side-collapse-container').removeClass('large');
    }


});

function toggleAllCheckBox(self, tableId) {
    if (jQuery(self).is(':checked')) {
        jQuery('#' + tableId).find('tbody tr td input[type=checkbox]').prop('checked', true);
    } else {
        jQuery('#' + tableId).find('tbody tr td input[type=checkbox]').prop('checked', false);
    }
}


// dashboard nav depend on screen
// function 