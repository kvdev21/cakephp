(function () {
    $(window).load(function () {
        $("#update-title").html("Importing latest tracks...");
        $.ajax({
            url: app.baseUrl + "jukebox/get_latest_config_dates.json",
            type: "get",
            complete: function() {
                $("#update-title").html("Generating waveform cache...");
                $.ajax({
                    url: app.baseUrl + "tracks/generate_waveforms.json",
                    type: "get",
                    complete: function() {
                        window.location = app.baseUrl + "jukebox/index";
                    }
                });
            }
        });
    });
})();