<?php


/*$responseComposeLessons = Yii::$app->acc->composeLessons(
        ['beginner', 'elementary', 'pre-intermediate', 'intermediate', 'upper-intermediate', 'advanced'],
        //['elementary'],
        [135, 246, 123456],
        //[123456],
        ['09:00-12:00', '18:00-21:00', '18:00-24:00', '21:00-00:00', '21:00-24:00'],
        //['18:00-21:00'],
        //['rustam.atakisiev@gmail.com', 'sanan@ibrahimov@gmail.com']  // Exceptions users
);*/

//debug(Yii::$app->acc->createClassesForNewUser('intermediate', '18:00-21:00', 123456, '2022-03-08'));

//debug(DateTimeZone::listIdentifiers());

//debug(Yii::$app->acc->createClassForSpecificSegment('pre-intermediate', '18:00-21:00', '246', ['2022-05-25', '2022-05-26', '2022-05-27', '2022-05-28', '2022-05-29', '2022-05-30']));

//Yii::$app->acc->deleteRedundantLessons(['beginner', 'elementary', 'pre-intermediate', 'intermediate', 'upper-intermediate', 'advanced'])

?>


<?php use backend\models\Packets;
use common\models\User;
use Segment\Segment;

if (Yii::$app->user->identity->email == 'rustam.atakisiev@gmail.com') { ?>
    <?php

    ?>
    <script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-messaging.js"></script>

    <script type="module">
        const firebaseConfig = {
            apiKey: "AIzaSyBt5HbqCNFFxbEPy88LRD4oLMWmw_dLkSY",
            authDomain: "fcm-web-dillbill.firebaseapp.com",
            projectId: "fcm-web-dillbill",
            storageBucket: "fcm-web-dillbill.appspot.com",
            messagingSenderId: "1031148163457",
            appId: "1:1031148163457:web:73f2ae24803f557a1b1880"
        };

        firebase.initializeApp(firebaseConfig);
        const messaging = firebase.messaging();

        Notification.requestPermission().then(permission => {
            console.log(permission)
            if (permission === 'granted') {
                messaging.getToken({
                    vapidKey: "BHex_MMPcABxrxzAtbSTQSy7-Yu_d6EivaoMuSdBq2INtlT4DfG84VwBlaPFuej8BR0auTyyaqG2WnRSbMkjg9o"
                }).then(currentToken => {
                    console.log(currentToken)
                    document.getElementById('token').innerHTML = currentToken;
                })
            }
        })
    </script>

    <script>
        function sendNotification() {
            const token = document.getElementById('token').innerText;
            const title = 'Title';
            const msg = 'Hello World';

            let body = {
                to: token,
                notification: {
                    title: title,
                    body: msg,
                    icon: "",
                    click_action: "https://blog.phusion.nl/2020/12/22/future_of_macos_apache_modules/"
                }
            }

            let options = {
                method: "POST",
                headers: new Headers({
                    Authorization: "key=AAAA8BU4UYE:APA91bH6HVfSREdWeU4GCGxnO9jYJaUU46HDncsnwos1t8vbvmTO_UQOc7kfemCLSZS9L1UojDnaX_P6yR2dYvDhSlXSuOuw6LRg7cPc_SFB5rLMyuUg29jjzGF02Rpdxhzjyewa2qlF",
                    "Content-Type": "application/json"
                }),
                body: JSON.stringify(body)
            }

            fetch("https://fcm.googleapis.com/fcm/send", options).then(result => {
                console.log(result)
                console.log('SENT')
            }).catch(e => console.log(e))

            console.log(body)
        }
    </script>

    <!--<div id="token"></div>
    <button onClick="sendNotification()">Send</button>-->
<?php } ?>



<div class="container w-100" style="max-width: 944px;">
    <?php if(Yii::$app->user->identity->userParameters->currentLevel == 'empty') { ?>
        <?= Yii::$app->view->renderFile('@app/views/dashboard/new-user.php') ?>
    <?php } ?>

    <?php if(Yii::$app->user->identity->userParameters->currentLevel != 'empty') { ?>
        <?= Yii::$app->view->renderFile('@app/views/dashboard/class-card.php') ?>
    <?php } ?>

    <br><br><br><br>
</div>