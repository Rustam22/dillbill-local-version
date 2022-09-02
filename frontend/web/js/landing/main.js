$(document).ready(function (){

    var myModalEl = document.getElementById('logIn_SignUp')

    myModalEl.addEventListener('show.bs.modal', function (event) {
        if(event.relatedTarget.classList.contains("logIn")) {
            myModalEl.classList.add("LogIn");
            myModalEl.classList.remove("SignUp");
            $(".my-tab-1.logIn").click();
        }
        else {
            myModalEl.classList.add("SignUp");
            myModalEl.classList.remove("LogIn");
            $(".my-tab-1.signUp").click();
        }
    })

    function resetButton(){
        $(".my-tab-1.logIn, .my-tab-1.signUp").css({
            color: "#848FA3"
        })
        $(".my-tab-bottom.logIn, .my-tab-bottom.signUp").css({
            background: "#E0E2E7",
            height: "1px"
        })
    }

    $(".my-tab-1.logIn").click(function (){
        resetButton()
        $(this).css({
            color: "#212327"
        })
        $(".my-tab-bottom.logIn").css({
            background: "#1877F2",
            height: "2px"
        })
        myModalEl.classList.add("LogIn");
        myModalEl.classList.remove("SignUp");
    })

    $(".my-tab-1.signUp").click(function (){
        resetButton()
        $(this).css({color: "#212327"})
        $(".my-tab-bottom.signUp").css({
            background: "#1877F2",
            height: "2px"
        })
        myModalEl.classList.add("SignUp");
        myModalEl.classList.remove("LogIn");
    })

    function password(a) {
        $(a + " a").on('click', function (event) {
            event.preventDefault();
            if ($(a + ' input').attr("type") === "text") {
                $(a + ' input').attr('type', 'password');
                $(a + ' i').addClass("fa-eye-slash");
                $(a + ' i').removeClass("fa-eye");
            } else if ($(a + ' input').attr("type") === "password") {
                $(a + ' input').attr('type', 'text');
                $(a + ' i').removeClass("fa-eye-slash");
                $(a + ' i').addClass("fa-eye");
            }
        });
    }

    password("#show_hide_password_2");
    password("#show_hide_password");

    $('.demo-trigger').click(function () {
        $('.btn-outline-primary[data-bs-target="#logIn_SignUp"]').click()
        $('.signUp').click()
    })

    $('[data-bs-target="#password_send"]').click(function () {
        $('#logIn_SignUp').modal('hide')
    })

    $('.logIn[data-bs-target="#logIn_SignUp"]').click(function () {
        $('#password_send').modal('hide')
    })

});