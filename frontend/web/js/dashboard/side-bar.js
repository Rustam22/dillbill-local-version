$(document).ready(function() {

    $('.navbar-hamburger').click(function() {
        if($('.overlay').hasClass('display-none')) {
            $('.overlay').removeClass('display-none')
            $('.side-bar').addClass('reveal-side-bar')
            window.location.hash = '#side-bar'
        } else {
            $('.overlay').addClass('display-none')
            $('.side-bar').removeClass('reveal-side-bar')
            window.location.hash = '#..'
        }
    })
    $('.overlay').click(function () {
        $(this).addClass('display-none')
        $('.side-bar').removeClass('reveal-side-bar')
        window.location.hash = '#...'
    })


    /***------------    Level Test    ------------***/
    $('#level-test').click(function () {
        $.ajax({
            url : _levelTest,
            type : 'POST',
            data : {'_csrf-frontend': _csrf_frontend},
            beforeSend: function() {
                $('.inside-menu .spinner-grow').removeClass('display-none')
                $('#level-test.inside-menu').addClass('disabled')
            },
            success : function(data) {
                data = JSON.parse(data)
                console.log(data)
                let redirectUrl = 'https://test.dillbill.com/dillbill-log-in/'

                if (typeof data.status !== 'undefined') {
                    if(data.status === 'success') {
                        redirectUrl += data.response.user_id
                        // Simulate a mouse click:
                        window.location.href = redirectUrl
                        // Simulate an HTTP redirect:
                        window.location.replace(redirectUrl)
                    } else {
                        if(data.message !== 'undefined') {
                            alert(data.message)
                        } else {
                            alert('error')
                        }
                    }
                } else {
                    if ((typeof data.message !== 'undefined') && (data.message !== null)) {
                        alert(data.message)
                    } else {
                        if (typeof data.error_class !== 'undefined') {
                            alert(data.error_class)
                        } else {
                            alert('error')
                        }
                    }
                }
            },
            error : function(request, error) {
                console.log('error')
                alert('error')
            },
            complete: function() {
                $('.inside-menu .spinner-grow').addClass('display-none')
                $('#level-test.inside-menu').removeClass('disabled')
            }
        })
    })


    /***------------    My Schedule    ------------***/
    $('.tooltip-my').click(function (event) {
        $(this).find('.top').addClass('display-block-important')
    })

    $(window).click(function (e) {
        let container = $('.tooltip-my')

        // if the target of the click isn't the container nor a descendant of the container
        if (!container.is(e.target) && container.has(e.target).length === 0) {
            container.find('.top').removeClass('display-block-important')
        }
    })
})