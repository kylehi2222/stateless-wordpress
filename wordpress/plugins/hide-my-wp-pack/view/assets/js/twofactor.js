(function ($) {
    "use strict";

    var ajax_error = "Ajax Error. Please refresh the page!";

    $.fn.hmwp_loading = function (status) {
        var $this = this;

        if(typeof status === 'undefined'){
            status = true;
        }

        if(status){
            $this.addClass('hmwp_loading');
        }else{
            $this.removeClass('hmwp_loading');
        }

        setTimeout(function () {
            $this.removeClass('hmwp_loading');
        }, 15000);
    }

    //Add the Listener for Settings
    $.fn.hmwp_totListen = function () {
        //set $this as #hmwp_wrap
        var $this = this;
        var $link = $this.find('#hmwp_qr_code').find('a');

        if($link.length){
            var $qrcode = qrcode( 0, 'L' );

            $qrcode.addData( $link.attr('href') );
            $qrcode.make();

            $link.html($qrcode.createSvgTag( 5 ));
        }

        $this.find('#hmwpp_totp_submit').on('click', function( e ) {
            e.preventDefault();
            var $action = $this.find('input[name=hmwp_totp_action]').val();
            var $nonce = $this.find('input[name=hmwp_totp_nonce]').val();
            var $referer = $this.find('input[name=hmwp_totp_referer]').val();
            var $key = $this.find('input[name=hmwp_totp_key]').val();
            var $authcode = $this.find('input[name=hmwp_totp_authcode]').val();
            var $user_id = $this.find('input[name=hmwp_totp_user_id]').val();

            var $button = $(this);
            $button.hmwp_loading();

            $.post(
                ajaxurl,
                {
                    action: $action,
                    key: $key,
                    authcode: $authcode,
                    user_id: $user_id,
                    hmwp_nonce: $nonce,
                    _wp_http_referer: $referer
                }
            ).done(function (response) {
                if(typeof response !== 'undefined' ){
                    if(typeof response.success !== 'undefined' && response.success){
                        $this.html(response.data);
                        $this.hmwp_totListen();
                        $this.hmwp_codesListen();
                    }else{
                        alert(response.data);
                    }
                }
                $button.hmwp_loading(false);

            }).fail(function (){
                $button.hmwp_loading(false);
                alert(ajax_error);
            });
        });

        $this.find('#hmwpp_totp_reset').on('click', function( e ) {
            e.preventDefault();
            var $action = $this.find('input[name=hmwp_totp_action]').val();
            var $nonce = $this.find('input[name=hmwp_totp_nonce]').val();
            var $referer = $this.find('input[name=hmwp_totp_referer]').val();
            var $user_id = $this.find('input[name=hmwp_totp_user_id]').val();

            var $button = $(this);
            $button.hmwp_loading();

            $.post(
                ajaxurl,
                {
                    action: $action,
                    user_id: $user_id,
                    hmwp_nonce: $nonce,
                    _wp_http_referer: $referer
                }
            ).done(function (response) {
                if(typeof response !== 'undefined' ){
                    if(typeof response.success !== 'undefined' && response.success){
                        $this.html(response.data);
                        $this.hmwp_totListen();
                    }else{
                        alert(response.data);
                    }
                }
                $button.hmwp_loading(false);

            }).fail(function (){
                $button.hmwp_loading(false);
                alert(ajax_error);
            });
        });

        return $this;
    };

    $.fn.hmwp_codesListen = function (){
        var $this = this;

        $this.find('#hmwpp_codes_generate').on('click', function( e ) {
            e.preventDefault();
            var $action = $this.find('input[name=hmwp_codes_action]').val();
            var $nonce = $this.find('input[name=hmwp_codes_nonce]').val();
            var $referer = $this.find('input[name=hmwp_codes_referer]').val();
            var $user_id = $this.find('input[name=hmwp_codes_user_id]').val();

            var $button = $(this);
            $button.hmwp_loading();

            $.post(
                ajaxurl,
                {
                    action: $action,
                    user_id: $user_id,
                    hmwp_nonce: $nonce,
                    _wp_http_referer: $referer
                }
            ).done(function (response) {
                if(typeof response !== 'undefined' ){
                    if(typeof response.success !== 'undefined' && response.success){
                        $this.html(response.data);
                        $this.hmwp_codesListen();
                    }else{
                        alert(response.data);
                    }
                }

                $button.hmwp_loading(false);
            }).fail(function (){
                $button.hmwp_loading(false);
                alert(ajax_error);
            });
        });

        $this.find('#hmwp_codes_finalize').on('click', function( e ) {
            e.preventDefault();

            location.reload();
        });
    }

    $.fn.hmwp_emailListen = function () {
        //set $this as #hmwp_wrap
        var $this = this;

        $this.find('#hmwpp_email_submit').on('click', function( e ) {
            e.preventDefault();
            var $action = $this.find('input[name=hmwp_email_action]').val();
            var $nonce = $this.find('input[name=hmwp_email_nonce]').val();
            var $referer = $this.find('input[name=hmwp_email_referer]').val();
            var $email = $this.find('input[name=hmwp_user_email]').val();
            var $user_id = $this.find('input[name=hmwp_email_user_id]').val();

            var $button = $(this);
            $button.hmwp_loading();

            $.post(
                ajaxurl,
                {
                    action: $action,
                    email: $email,
                    user_id: $user_id,
                    hmwp_nonce: $nonce,
                    _wp_http_referer: $referer
                }
            ).done(function (response) {
                if(typeof response !== 'undefined' ){
                    if(typeof response.success !== 'undefined' && response.success){
                        $this.html(response.data);
                        $this.hmwp_emailListen();
                        $this.hmwp_codesListen();
                    }else{
                        alert(response.data);
                    }
                }
                $button.hmwp_loading(false);

            }).fail(function (){
                $button.hmwp_loading(false);
                alert(ajax_error);
            });
        });

        $this.find('#hmwpp_email_reset').on('click', function( e ) {
            e.preventDefault();
            var $action = $this.find('input[name=hmwp_email_action]').val();
            var $nonce = $this.find('input[name=hmwp_email_nonce]').val();
            var $referer = $this.find('input[name=hmwp_email_referer]').val();
            var $user_id = $this.find('input[name=hmwp_email_user_id]').val();

            var $button = $(this);
            $button.hmwp_loading();

            $.post(
                ajaxurl,
                {
                    action: $action,
                    user_id: $user_id,
                    hmwp_nonce: $nonce,
                    _wp_http_referer: $referer
                }
            ).done(function (response) {
                if(typeof response !== 'undefined' ){
                    if(typeof response.success !== 'undefined' && response.success){
                        $this.html(response.data);
                        $this.hmwp_emailListen();
                    }else{
                        alert(response.data);
                    }
                }

                $button.hmwp_loading(false);

            }).fail(function (){
                $button.hmwp_loading(false);
                alert(ajax_error);
            });
        });

        return $this;
    };

    $(document).ready(function(){
        $('#hmwp_totp_options').hmwp_totListen();
        $('#hmwp_totp_options').hmwp_codesListen();
        $('#hmwp_totp_options').hmwp_emailListen();
    });

})(jQuery);




