$(document).ready(function () {
    $('#ur-close-button').on('click', function () {
        slideUpChooseBlock();
    });

    $('#ur-city-link'). on('click', function () {
        $('#ur-choose-block').slideDown();
    });

    $('a.ur-pred-city-li').on('click', function () {
        var city = $(this).text();
        $('#ur-submit-button').removeClass('disabled');
        $('#ur-city-auto').val(city);
    });

    $('#ur-city-auto').on('input', function () {
        $('#ur-submit-button').removeClass('disabled');
    });
});

function slideUpChooseBlock() {
    $('#ur-choose-block').slideUp();
}

function sendCity(url) {
    var city = document.getElementById('ur-city-auto');
    if (city.value != '') {
        $.ajax({
            url: url,
            type: 'post',
            data: {
                city: city.value
            },
            success : function(data) {
                if(data === 'good') {
                    $('#ur-city-link').html(city.value);
                    slideUpChooseBlock();
                }
            }
        });
    }
}