(function ($) {
    'use strict';

    /**
     * All of the code for your public-facing JavaScript source
     * should reside in this file.
     *
     * Note: It has been assumed you will write jQuery code here, so the
     * $ function reference has been prepared for usage within the scope
     * of this function.
     *
     * This enables you to define handlers, for when the DOM is ready:
     *
     * $(function() {
	 *
	 * });
     *
     * When the window is loaded:
     *
     * $( window ).load(function() {
	 *
	 * });
     *
     * ...and/or other possibilities.
     *
     * Ideally, it is not considered best practise to attach more than a
     * single DOM-ready or window-load handler for a particular page.
     * Although scripts in the WordPress core, Plugins and Themes may be
     * practising this, we should strive to set a better example in our own work.
     */


    $(function () {


        $('.bike-book-collapse').on('shown.bs.collapse', function () {
            $(this).prev('.bike-info').find('.btn').addClass('open').text('Avbryt');
            $(this).find('.booker-email').focus();
        });

        $('.bike-book-collapse').on('hidden.bs.collapse', function () {
            $('.selected-accessorie').empty();
            $(this).prev('.bike-info').find('.btn').addClass('open').text('Boka');
        });


        $('.bikebooking-period .btn-close').on('click', function () {
            $('.bike-book-collapse').collapse('hide');
        });



        $('.bikebooking-period .accessorie-info .btn').on('click', function () {
            var accessorie = $(this).closest('.accessorie-info');
            $(this).closest('.bike-book-collapse').find('.booker-email').focus();
            $('.selected-accessorie').empty();

            if ($(this).hasClass('remove')) {

                $('.selected-accessorie').empty();
                $('.bikebooking-period .accessorie-info').removeClass('selected');
                $('.bikebooking-period .accessorie-info .btn').text('Välj').removeClass('remove');

            } else {
                var selected = accessorie.clone(true);
                selected.appendTo( $(this).closest('.alert-warning').find('.alert-inner .selected-accessorie') );
                selected.find('.btn').text('Ta bort').addClass('remove');
            }
        });


        $('.bikebooking-period h4 .btn').on('click', function () {
            $('.bike-book-collapse').collapse('hide');
        });


        $('.book-a-bike').on('click', function () {
            var trigger_btn = $(this);
            var elm = $(this).closest('.alert');
            elm.find('.error-message').remove();

            //console.log($(this).closest('.bike-book-collapse').find('.booker-email').val());
            //console.log($(this).data('bike'));
            //console.log($(this).data('period'));

            var accessorie_id = $(this).closest('.bike-book-collapse').find('.selected-accessorie .btn').data('accessorie');
            //console.log(accessorie_id);

            //console.log(trigger_btn);
            var data = {
                action: 'book_bike',
                booker_email: $(this).closest('.bike-book-collapse').find('.booker-email').val(),
                booker_name: $(this).closest('.bike-book-collapse').find('.booker-name').val(),
                booker_phone: $(this).closest('.bike-book-collapse').find('.booker-phone').val(),
                bike_id: $(this).data('bike'),
                accessorie_id: accessorie_id,
                bike_period: $(this).data('period'),
                nonce: ajax_object.ajax_nonce
            };

            $.post(ajax_object.ajaxurl, data, function (response) {

                //console.log(response);
                //console.log(data);
                //console.log(this);


                if( typeof response.error !== 'undefined' ){
                    elm.find('form').prepend('<div class="alert alert-inner error-message"><b>Felmeddelande: </b> ' +response.error+'</div>');
                }else{
                    elm.closest('.bike').find('.btn.open').remove();
                    elm.empty();
                    elm.append('<div class="alert alert-inner">');
                    elm.find('.alert-inner').append('<p>Tack för din bokningsförfrågan!</p>');
                    elm.find('.alert-inner').append('<p>Vi har skickat ett meddelande till din e-postadress <b>' + data.booker_email + '</b>.<br>I meddelandet finns det en länk du måste klicka på för att bekräfta din bokning innan den blir giltig.</p>');
                    elm.find('.alert-inner').append('<p><b>Observera</b> att fram till dess att bokningen inte är bekräftad finns cykeln tillgänglig för andra att boka.</p>');
                }



            }).error(function () {
                alert("Problem calling: " + data.action + "\nCode: " + this.status + "\nException: " + this.statusText);
            });

        });


    });


})(jQuery);
