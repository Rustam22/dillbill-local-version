$(document).ready(function () {
    let form = $("#step-wizard")

    if(_chosenAvailableTime) {
        $('input[data-check-value="' + _chosenAvailableTime + '"]').prop('checked', true).parent().addClass('active')
    }

    let rangeChecked = ($('.time-schedule .form-check-input:checked').length > 0)

    form.steps({
        onInit: function(event, currentIndex) {
            //console.log('currentIndex: ' + currentIndex)
            //console.log($('.time-schedule .form-check-input:checked').length)
            if(currentIndex === 0 && !_chosenAvailableTime) {
                $('a[href="#next"]').addClass('disabled').addClass('disabled-visually')
            }
        },

        onStepChanging: function (event, currentIndex, newIndex) {
            //console.log(currentIndex + ' - ' + newIndex)
            //console.log($('.plan-section .form-check-input:checked').length)
            if(currentIndex === 2 && newIndex === 3 && $('.plan-section .form-check-input:checked').length === 1) {
                let chosenPacket = '#' + $('.plan-section input:checked').val().trim()
                window.location.href = _paymentUrl + chosenPacket
                window.location.replace(_paymentUrl + chosenPacket)
            }

            return true
        },

        onStepChanged: function(e, Dest, Src) {
            //console.log($('.time-schedule .form-check-input:checked').length)
            document.cookie = "stepWizard=" + Dest
            console.log('Coming from ' + Src + ' going to ' + Dest)
            console.log(form.steps('getCurrentIndex'))
            rangeChecked = ($('.time-schedule .form-check-input:checked').length > 0)
            //console.log(rangeChecked + ' - ' + Src)

            if(rangeChecked === false) {
                $('#step-wizard-p-0').addClass('my-shake')
                setTimeout(() => { $('#step-wizard-p-0').removeClass('my-shake') }, 1000)
                form.steps('previous')
            }

            $('.steps .current').nextAll().removeClass('done').removeClass('disabled').addClass('disabled')

            if(form.steps('getCurrentIndex') === 3 && ($('.plan-section .form-check-input:checked').length === 0) && (_cp < 1)) {
                $('#step-wizard-p-2').addClass('my-shake')
                setTimeout(() => { $('#step-wizard-p-2').removeClass('my-shake') }, 1000)
                form.steps('previous')
            }

            if(Src === 1 && Dest === 2 && $('.plan-section .form-check-input:checked').length < 1) {
                $('a[href="#next"]').addClass('disabled').addClass('disabled-visually')
            } else {
                $('a[href="#next"]').removeClass('disabled').removeClass('disabled-visually')
            }
        },

        labels: {
            finish: _proceed,
            next: _next,
            previous: _back,
        },

        autoFocus: false,
        headerTag: "h6",
        bodyTag: "section",
        transitionEffect: "fade",
        titleTemplate: '<span class="step">' + '<svg class="check-mark" viewBox="0 0 15 12" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M4.49987 9.25992L1.6082 6.19981C1.2832 5.85588 0.758203 5.85588 0.433203 6.19981C0.108203 6.54374 0.108203 7.09933 0.433203 7.44326L3.91654 11.1295C4.24154 11.4734 4.76654 11.4734 5.09154 11.1295L13.9082 1.79925C14.2332 1.45531 14.2332 0.899733 13.9082 0.555801C13.5832 0.211869 13.0582 0.211869 12.7332 0.555801L4.49987 9.25992Z" fill="white"/></svg>' +'</span> #title#'
    })

    function setStep(stepIndex) {
        for(let i = 1; i <= stepIndex; i++) {
            form.steps("next")
        }
    }

    setStep(_stepWizard)
    $('.panel').removeClass('display-none')

    if($('.plan-section .form-check-input:checked').length === 0 && _cp < 1) {

    }

    $('a[href="#finish"]').click(function () {
        // If level test is not held
        if((_cp > 0) && (_userCurrentLevel === 'empty') && (_status === 0)) {
            let redirectUrl = 'https://test.dillbill.com/dillbill-log-in/'
            redirectUrl += _response_user_id
            window.location.href = redirectUrl
            window.location.replace(redirectUrl)
        }

        // If level test not held correctly
        if((_cp > 0) && (_userCurrentLevel === 'empty') && (_status === 2)) {
            let redirectUrl = 'https://test.dillbill.com/dillbill-log-in/'
            redirectUrl += _response_user_id
            window.location.href = redirectUrl
            window.location.replace(redirectUrl)
        }

        // If level test is successful
        if((_cp > 0) && (_userCurrentLevel === 'empty') && (_status === 1)) {
            return true
        }
    })

    // If level test is successful
    if((_cp > 0) && (_userCurrentLevel === 'empty') && (_status === 1)) {
        $('a[href="#finish"]').html(_checking + '...').addClass('checking').addClass('disabled')
    }

    if(_chosenAvailableTime) {
        $('input[data-check-value="' + _chosenAvailableTime + '"]').prop('checked', true).parent().addClass('active')
    }

    $('.time-schedule .form-check').click(function () {
        $('.time-schedule .form-check').removeClass('active')
        $(this).addClass('active')
        $('a[href="#next"]').removeClass('disabled').removeClass('disabled-visually')
        let checkedTime = $(this).find('.form-check-input').prop('checked', true)

        try {
            $('#time-select .dropdown-menu li a[data-start-time="' + checkedTime.data('start-value') +'"]').click();
        } catch (e) {

        }

        let parentIndex = $(this).index() + 1

        if ($('.time-schedule .form-check-input').is(":checked")) {
            let checkedTime = $('.time-schedule .form-check-input:checked').val().trim()

            $.ajax({
                url : _timeAvailability,
                type : 'POST',
                data : {'_csrf-frontend': _csrf_frontend, 'checkedTime': checkedTime},
                beforeSend: function() {
                    $('.time-schedule .form-check:nth-child(' + parentIndex + ') .form-check-label .my-spinner').removeClass('display-none')
                    $('.panel').addClass('disabled-visually')
                },
                success : function(data) {
                    data = JSON.parse(data)
                    console.log(data)
                },
                error : function(request, error) {
                    console.log('error')
                    alert('error')
                },
                complete: function() {
                    $('.time-schedule .form-check:nth-child(' + parentIndex + ') .form-check-label .my-spinner').addClass('display-none')
                    $('.panel').removeClass('disabled-visually')
                }
            })
        } else {
            alert('Please choose a schedule')
        }
    })

    $('.plan-section .form-check').click(function () {
        $('.plan-section .form-check').removeClass('active')
        $(this).addClass('active')
        $(this).find('.form-check-input').prop('checked', true)
        $('a[href="#next"]').removeClass('disabled').removeClass('disabled-visually')
    })
})