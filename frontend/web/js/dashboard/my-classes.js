$(document).ready(function () {
    (window.location.hash !== '#reserved-classes') ? window.location.hash = '#...' : window.location.hash

    if($('#nav-profile').find('.class-column').length > 0) {
        $('#nav-profile-tab .red-dot').removeClass('display-none')
    }


    /*** ____________________ Onboard Start  ____________________ ***/
    $('.next-to-boarding, .next-to-time-range').click(function() {
        $('#boarding-carousel, #pre-boarding-carousel').carousel('next')
    })

    $('#flexCheckDefault').prop('checked', false)

    $('#flexCheckDefault').click(function () {
        if($('#flexCheckDefault').is(':checked')) {
            $('.next-to-time-range').removeClass('disabled-visually').prop('disabled', false)
        }
        if(!$('#flexCheckDefault').is(':checked')) {
            $('.next-to-time-range').addClass('disabled-visually').prop('disabled', true)
        }
    })

    $('#boarding .back').click(function () {
        $('#boarding-carousel').carousel('prev')
        $('#pre-boarding-carousel').carousel('prev')
    })

    $('.boarding-graphic .form-check').click(function () {
        $('#boarding .confirm-boarding-time-range').removeClass('disabled-visually').prop('disabled', false)
    })

    _startClassDate = $('#start-date-select .dropdown-menu[aria-labelledby="dropdownMenuButton3333"] .dropdown-item.active').data('start-class-date')

    $('#start-date-select .dropdown-menu[aria-labelledby="dropdownMenuButton3333"] .dropdown-item').click(function () {
        $('#start-date-select .dropdown-item').removeClass('active')
        $(this).addClass('active')
        $('.selected-start-date').html($(this).find('text').html().trim())

        _startClassDate = $(this).data('start-class-date')
    })

    $('.boarding-finish-button').click(function () {
        location.reload()
        window.location.reload()
    })

    $('.confirm-boarding-time-range').click(function () {
        if(_startClassDate) {
            $.ajax({
                url : _confirmStartDateAndTimeRangeUrl,
                type : 'POST',
                data : {'_csrf-frontend': _csrf_frontend, 'startClassDate': _startClassDate},
                beforeSend: function() {
                    $('.confirm-boarding-time-range .spinner-grow').removeClass('display-none')
                },
                success : function(data) {
                    data = JSON.parse(data)
                    console.log(data)

                    if(!data.success) {
                        alert(data.error)
                    } else {
                        $('#chosenDate').html(data.chosenStartDate)
                        $('#chosenTimeRange').html(data.chosenTimeRange)
                        $('#boarding-carousel').carousel('next')
                    }
                },
                error : function(request, error) {
                    alert('System error')
                    console.log('error')
                },
                complete: function() {
                    $('.confirm-boarding-time-range .spinner-grow').addClass('display-none')
                }
            })
        }
    })
    /*** ____________________ On Boarding End  ____________________ ***/


    $('#date-select .dropdown-menu[aria-labelledby="dropdownMenuButton666"] .dropdown-item').click(function () {
        $('#date-select .dropdown-item').removeClass('active')
        $(this).addClass('active')
        $('.selected-date').html($(this).find('text').html().trim())

        _currentClassDate = $(this).data('class-date')
        document.cookie = "chosen-date=" + _currentClassDate

        $('.topic-group').removeClass('display-none-important').stop().hide()
        $('.topic-group[data-topic-date="' + _currentClassDate + '"]').fadeIn(800)

        let classColumn = $('.class-column[data-class-date="' + _currentClassDate + '"]')
        $('#nav-home .class-column').stop().hide(0)
        classColumn.fadeIn(500)

        if(classColumn.length === 0) {
            $('.testimonial-classes').stop().hide()
            $('.no-class').fadeIn(500)
        } else {
            $('.no-class').stop().hide(0)
            $('.testimonial-classes').fadeIn(500, function () {
                $('#time-select .dropdown-item.active').click()
            })
        }
    })

    $('#date-select .dropdown-item[data-class-date="' + _currentClassDate + '"]').click()

    $('#go-to-classes').click(function (e) {
        $('#nav-home-tab').click()
    })

    $('.live-chat').click(function () {
        Intercom('show');
    })

    $('button[data-bs-toggle="tab"]').click(function (e) {
        if($(this).attr('id') === 'nav-profile-tab') {
            window.location.hash = '#reserved-classes'
            document.cookie = "class-tab=reserved-classes"
        } else {
            document.cookie = "class-tab=available-classes"
            window.location.hash = '#...'
        }
    })

    $('.clickable-story').click(function () {
        window.location.hash = '#pop-up'
        let presentationUrl = $(this).data('presentation').replace('watch?v=', 'embed/')
        $('#teacher-iframe').attr('src', presentationUrl);
        $('.my-modal-content [data-parse="name"]').html($(this).data('name'))
        $('.my-modal-content [data-parse="country"]').html($(this).data('country'))
        $('.my-modal-content [data-parse="experience"]').html($(this).data('experience'))
        $('.my-modal-content [data-parse="description"]').html($(this).data('description'))
    })

    $('#exampleModal5').on('hidden.bs.modal', function () {
        window.location.hash = '#...'
        $('#teacher-iframe').each(function(index) {
            $(this).attr('src', $(this).attr('src'))
            return false
        })
    })
})