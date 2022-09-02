$(document).ready(function () {

    /***------------    Time Zone    ------------***/
    function defineTimeZone(receivedTimeZone = false) {
        let timeZone = ''

        try {
            if((receivedTimeZone === false) && (UserCurrentTimeZone === '' || UserCurrentTimeZone === '')) {
                timeZone = Intl.DateTimeFormat().resolvedOptions().timeZone
            }
        } catch (e) {}

        if(receivedTimeZone) {
            timeZone = receivedTimeZone
        }

        $.ajax({
            url : _timeZoneAssigning,
            type : 'POST',
            data : {'_csrf-frontend': _csrf_frontend, 'timeZone': timeZone},
            beforeSend: function() {
                $('#time-zone .make-payment .spinner-grow').removeClass('display-none')
            },
            success : function(data) {
                location.reload()
                window.location.reload()
            },
            error : function(request, error) {
                console.log('error')
            },
            complete: function() {
                $('#time-zone .make-payment .spinner-grow').addClass('display-none')
            }
        });

    }

    if (typeof boarding === 'undefined') {
        (UserCurrentTimeZone.length < 4 || UserCurrentTimeZone === '' || UserCurrentTimeZone === undefined) ? defineTimeZone() : '';
    }

    $('#time-zone .search-input').keyup(function() {
        let typedText = $(this).val()
        typedText = typedText.substr(0,1).toUpperCase() + typedText.substr(1)

        if(typedText.length > 0) {
            $('#time-zone .search-input').addClass('background-image-none');
        } else {
            $('#time-zone .search-input').removeClass('background-image-none');
        }

        $('.time-zone-content').hide();
        $('.time-zone-content:contains(' + typedText + ')').show();
    });

    $('#time-zone .list-group li').click(function () {
        $('#time-zone .search-input').val($(this).data('time-zone')).addClass('background-image-none');
    });


    let timeZoneSearchInput = false

    $('#time-zone .make-payment').click(function() {
        if($('#time-zone .search-input').val().length <= 3) {
            alert(chooseTimeZone);
        } else if($('#time-zone .search-input').val().length > 3) {
            $('[data-bs-target="#time-range-change-confirm"]').click()
            timeZoneSearchInput = $('#time-zone .search-input').val()
            defineTimeZone($('#time-zone .search-input').val())
        }
    })

    $('#time-range-change-confirm .primary-button').click(function () {
        $('#time-range-change-confirm .close').click()
        defineTimeZone(timeZoneSearchInput)
    })



    $('#dropdownMenuButton1').on('show.bs.dropdown', function () {
        window.location.hash = '#user-profile'
    })

    $('#dropdownMenuButton1').on('hide.bs.dropdown', function () {
        window.location.hash = '#...'
    })
})