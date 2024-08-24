(function ($) {
    "use strict";

    //Add the Listener for Settings
    $.fn.hmwpp_settingsListen = function () {
        //set $this as #hmwp_wrap
        var $this = this;

        $this.find("button.hmwp_2fa_totp").on(
            'click', function () {
                $this.find('input[name=hmwp_2fa_totp]').val(1);
                $this.find('input[name=hmwp_2fa_email]').val(0);

                $this.find('.group_autoload button').removeClass('active');
                $(this).addClass('active');

                $this.find('div.hmwp_2fa_totp').show();
                $this.find('div.hmwp_2fa_email').hide();
            }
        );

        $this.find("button.hmwp_2fa_email").on(
            'click', function () {
                $this.find('input[name=hmwp_2fa_email]').val(1);
                $this.find('input[name=hmwp_2fa_totp]').val(0);

                $this.find('.group_autoload button').removeClass('active');
                $(this).addClass('active');

                $this.find('div.hmwp_2fa_email').show();
                $this.find('div.hmwp_2fa_totp').hide();
            }
        );

        if ($('.hmwp_clipboard_copy').length > 0) {
            var clipboard_link = new Clipboard('.hmwp_clipboard_copy');

            clipboard_link.on(
                'success', function (e) {
                    var elem = e.trigger;
                    var id = elem.getAttribute('id');
                    var $copied = $('<span class="hmwp-clipboard-copied">Copied</span>').appendTo($('#'+id));
                    $copied.show();
                    setTimeout(function (){$copied.remove();}, 1000);
                }
            );
        }

    };


    $('#hmwp_wrap').ready(
        function () {
            $(this).hmwpp_settingsListen();
        }
    );
})(jQuery);




