(function ($) {
    Drupal.behaviors.weatherstation = {
        attach: function (context, settings) {
            $(drupalSettings.weatherstation_settings.id).ready(
                $.ajax({
                    url: "/weatherstation/get-weather.json",
                    type: 'get',
                    success: function (data) {
                        drupalSettings.weatherstation = {'current': JSON.parse(data)};
                        var icon = drupalSettings.weatherstation.current.weather["0"].icon;
                        var background_img =  drupalSettings.weatherstation_settings.photos[icon];
                        var slogan =  drupalSettings.weatherstation_settings.slogans[icon] ;
                        $('#weatherstation-slogan').html( "<p>"+ slogan + "</p>" );
                        $(drupalSettings.weatherstation_settings.id).hide();
                        $(drupalSettings.weatherstation_settings.id).css('background-image', 'url(' + background_img + ')');
                        $(drupalSettings.weatherstation_settings.id).fadeIn('300');
                        $('.spinner').hide();
                    },
                    failure: function (errMsg) {
                        // $('.ev-loader').fadeOut('slow');
                    }
                })
            );
        }
    }
})(jQuery);
