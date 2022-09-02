$(document).ready(function () {

    /*$('.reserve-room-button').click(function () {
        $([document.documentElement, document.body]).animate({
            scrollTop: $('.panel').offset().top
        }, 1000, () => {
            $('.panel').addClass('my-shake')
            setTimeout(() => { $('.panel').removeClass('my-shake') }, 1720)
        })
    })*/

    $('#date-select .dropdown-item').click(function () {
        $('#date-select .dropdown-item').removeClass('active')
        $(this).addClass('active')
        $('.selected-date').html($(this).find('text').html().trim())

        _currentClassDate = $(this).data('class-date')
        document.cookie = "selected-date=" + _currentClassDate

        let classColumn = $('.class-column[data-class-date="' + _currentClassDate + '"]')
        $('.class-column').stop().hide(0)
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

    $('.testimonial-classes').removeClass('display-none')
    $('#date-select .dropdown-item[data-class-date="' + _currentClassDate + '"]').click()

    $('#time-select .dropdown-item').click(function () {
        $('#time-select .time-dropdown').removeClass('active')
        $(this).addClass('active')
        $('#time-select .selected-time').html($(this).find('text').html().trim())

        let rangeStart = new Date(_userDateTime + ' ' + $(this).data('start-time'))
        let rangeEnd = new Date(_userDateTime + ' ' + $(this).data('end-time'))
        let visibleClasses = 0
        let currentClasses = $('.class-column[data-class-date="' + _currentClassDate + '"]')

        let checkedStartTime = $('.time-schedule .form-check-input:checked').data('start-value')
        //console.log(checkedStartTime + ' - ' + $(this).data('start-time'))

        /*if(checkedStartTime !== $(this).data('start-time') && checkedStartTime !== undefined) {
            $('.time-schedule .form-check-input[data-start-value="' + $(this).data('start-time') + '"]').click()
        }*/

        currentClasses.each(function(e) {
            let classStartTime = new Date(_userDateTime + ' ' + $(this).data('start-time'))
            let classEndTime = new Date(_userDateTime + ' ' + $(this).data('end-time'))

            //if(classStartTime < rangeStart || classStartTime > rangeEnd) {
            $(this).stop().hide()
            //}

            if(classStartTime >= rangeStart && classStartTime < rangeEnd) {
                $(this).stop().fadeIn(300)
                visibleClasses++
            }
        })

        if(visibleClasses === 0) {
            $('.no-class').fadeIn(300)
        } else {
            visibleClasses = 0
            $('.no-class').stop().hide()
        }
    })
})