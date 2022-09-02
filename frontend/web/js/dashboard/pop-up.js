$(document).ready(function () {

    /***------------    Google Calendar    ------------***/
    $('.button-google-calendar').click(function () {
        $('#google-calendar').addClass('is-visible');
    });

    $('#google-calendar .connect').click(function () {
        let gmail = $('#google-calendar .google-calendar-input').val().trim();

        if (/@gmail\.com$/.test(gmail) && gmail.length > 12) {
            $('#google-calendar code').hide(300);

            $.ajax({
                url : _googleCalendar,
                type : 'POST',
                data : {'_csrf-frontend': _csrf_frontend, 'action': 'connect', 'gmail': gmail},
                beforeSend: function() {
                    $('#google-calendar .confirm-time .spinner-grow').removeClass('display-none')
                },
                success : function(data) {
                    data = JSON.parse(data);

                    if(data.success) {
                        alert(gmailConnectedToGoogleCalendar);
                        $('#google-calendar').removeClass('is-visible');
                        location.reload();
                        window.location.reload();
                        document.cookie = "showGoogleCalendar=no";
                    } else {
                        alert(data.error);
                    }

                    console.log(data);
                },
                error : function(request, error) {
                    console.log('error');
                },
                complete: function() {
                    $('#google-calendar .confirm-time .spinner-grow').addClass('display-none')
                }
            });
        } else {
            $('#google-calendar code').show(300);
        }
    });



    $('#trial-google-calendar .connect').click(function () {
        let gmail = $('#trial-google-calendar .google-calendar-input').val().trim();

        if (/@gmail\.com$/.test(gmail) && gmail.length > 12) {
            $('#trial-google-calendar code').hide(300);

            $.ajax({
                url : _googleCalendar,
                type : 'POST',
                data : {'_csrf-frontend': _csrf_frontend, 'action': 'connect', 'gmail': gmail},
                beforeSend: function() {
                    $('#trial-google-calendar .confirm-time .spinner-grow').removeClass('display-none')
                },
                success : function(data) {
                    data = JSON.parse(data);

                    if(data.success) {
                        alert(gmailConnectedToGoogleCalendar);
                        $('#trial-google-calendar').removeClass('is-visible');
                        location.reload();
                        window.location.reload();
                        document.cookie = "showGoogleCalendar=no";
                    } else {
                        alert(data.error);
                    }

                    console.log(data);
                },
                error : function(request, error) {
                    console.log('error');
                },
                complete: function() {
                    $('#trial-google-calendar .confirm-time .spinner-grow').addClass('display-none')
                }
            });
        } else {
            $('#trial-google-calendar code').show(300);
        }
    })



    $('#google-calendar .disconnect').click(function () {
        $.ajax({
            url : _googleCalendar,
            type : 'POST',
            data : {'_csrf-frontend': _csrf_frontend, 'action': 'disconnect', 'gmail': ''},
            beforeSend: function() {
                $('#google-calendar .confirm-time .spinner-grow').removeClass('display-none')
            },
            success : function(data) {
                data = JSON.parse(data);

                if(data.success) {
                    alert(gmailDisconnectedToGoogleCalendar);
                    $('#google-calendar').removeClass('is-visible');
                    location.reload();
                    window.location.reload();
                    document.cookie = "showGoogleCalendar=no";
                } else {
                    alert(data.error);
                }

                console.log(data);
            },
            error : function(request, error) {
                console.log('error');
            },
            complete: function() {
                $('#google-calendar .confirm-time .spinner-grow').addClass('display-none')
            }
        });
    });



    $('#trial-google-calendar .disconnect').click(function () {
        $.ajax({
            url : _googleCalendar,
            type : 'POST',
            data : {'_csrf-frontend': _csrf_frontend, 'action': 'disconnect', 'gmail': ''},
            beforeSend: function() {
                $('#trial-google-calendar .confirm-time .spinner-grow').removeClass('display-none')
            },
            success : function(data) {
                data = JSON.parse(data);

                if(data.success) {
                    alert(gmailDisconnectedToGoogleCalendar);
                    $('#trial-google-calendar').removeClass('is-visible');
                    location.reload();
                    window.location.reload();
                    document.cookie = "showGoogleCalendar=no";
                } else {
                    alert(data.error);
                }

                console.log(data);
            },
            error : function(request, error) {
                console.log('error');
            },
            complete: function() {
                $('#trial-google-calendar .confirm-time .spinner-grow').addClass('display-none')
            }
        });
    });


    /***------------    Pop Up Close    ------------***/
    $('[data-popup-id]').click(function () {
        $('#' + $(this).data('popup-id')).addClass('is-visible')
        window.location.hash = '#pop-up'
        setTimeout(() => { window.location.hash = '#pop-up' }, 500)
    })

    $('.not-now, .cd-popup-close').click(function () {
        $('.cd-popup, .cd-approve').removeClass('is-visible').trigger('close-pop-up')
    })

    $(document).keyup(function(event) {
        if(event.which === 27) {
            $('.cd-popup, .cd-approve').removeClass('is-visible').trigger('close-pop-up')
            $('.nav-button').click()
        }
    })

    $('.cd-popup').on('click', function(event) {
        if($(event.target).is('.cd-popup-close') || $(event.target).is('.cd-popup')) {
            event.preventDefault()
            $(this).removeClass('is-visible').trigger('close-pop-up')
        }
    }).click('close-pop-up', function() {
        window.location.hash = '#...'
        $('#teacher-iframe').each(function(e) {
            $(this).attr('src', $(this).attr('src'))
            return false
        });
    })


    /***------------    Time Availability    ------------***/
    $('[data-check-id]').click(function () {
        $('#' + $(this).data('check-id')).prop('checked', true);
    })

    $('.lesson-graphic .form-check').click(function () {
        $('.lesson-graphic .form-check').removeClass('active')
        $(this).addClass('active')
        $(this).find('.form-check-input').prop('checked', true)
    })

    $('.form-check-input, .form-check').click(function () {
        if ($('.form-check-input').is(":checked")) {
            $('#time-availability .confirm-time').removeClass('disabled')
        }
    })

    $('.pre-form-check-input, .form-check').click(function () {
        if ($('.pre-form-check-input').is(":checked")) {
            $('.level-time-range-select .confirm-pre-boarding-time-range').removeClass('disabled-visually').prop('disabled', false)
        }
    })

    /*$('.confirm-pre-boarding-time-range').click(function () {
        if ($('.pre-form-check-input').is(":checked")) {
            let checkedTime = $('.pre-form-check-input:checked').val().trim()

            $.ajax({
                url : _timeAvailability,
                type : 'POST',
                data : {'_csrf-frontend': _csrf_frontend, 'checkedTime': checkedTime},
                beforeSend: function() {
                    $('.level-time-range-select .confirm-pre-boarding-time-range .spinner-grow').removeClass('display-none')
                },
                success : function(data) {
                    data = JSON.parse(data)
                    console.log(data)

                    if(data.success) {
                        location.reload()
                        window.location.reload()
                    } else {
                        alert(data.error)
                    }
                },
                error : function(request, error) {
                    console.log('error')
                    alert('error')
                },
                complete: function() {
                    $('.level-time-range-select .confirm-pre-boarding-time-range .spinner-grow').addClass('display-none')
                }
            })
        } else {
            alert('Please choose a schedule')
        }
    })*/

    /*$('#time-availability .confirm-time').click(function () {
        if ($('.form-check-input').is(":checked")) {
            let checkedTime = $('.form-check-input:checked').val().trim()

            $.ajax({
                url : _timeAvailability,
                type : 'POST',
                data : {'_csrf-frontend': _csrf_frontend, 'checkedTime': checkedTime},
                beforeSend: function() {
                    $('#time-availability .confirm-time .spinner-grow').removeClass('display-none')
                },
                success : function(data) {
                    data = JSON.parse(data)
                    console.log(data)

                    if(data.success) {
                        location.reload()
                        window.location.reload()
                    } else {
                        alert(data.error)
                    }
                },
                error : function(request, error) {
                    console.log('error')
                    alert('error')
                },
                complete: function() {
                    $('#time-availability .confirm-time .spinner-grow').addClass('display-none')
                }
            })
        } else {
            alert('Please choose a schedule')
        }
    })*/
})