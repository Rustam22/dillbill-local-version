<?php

use frontend\assets\BasicAppAsset;

BasicAppAsset::register($this);

?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">

<head>
    <title>Dill Bill Payment</title>

    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-WWXHM4Z');</script>
    <!-- End Google Tag Manager -->


    <script>
        !function(){var analytics=window.analytics=window.analytics||[];if(!analytics.initialize)if(analytics.invoked)window.console&&console.error&&console.error("Segment snippet included twice.");else{analytics.invoked=!0;analytics.methods=["trackSubmit","trackClick","trackLink","trackForm","pageview","identify","reset","group","track","ready","alias","debug","page","once","off","on","addSourceMiddleware","addIntegrationMiddleware","setAnonymousId","addDestinationMiddleware"];analytics.factory=function(e){return function(){var t=Array.prototype.slice.call(arguments);t.unshift(e);analytics.push(t);return analytics}};for(var e=0;e<analytics.methods.length;e++){var key=analytics.methods[e];analytics[key]=analytics.factory(key)}analytics.load=function(key,e){var t=document.createElement("script");t.type="text/javascript";t.async=!0;t.src="https://cdn.segment.com/analytics.js/v1/" + key + "/analytics.min.js";var n=document.getElementsByTagName("script")[0];n.parentNode.insertBefore(t,n);analytics._loadOptions=e};analytics._writeKey="0x8AxE1IIioq3mJgxEGDOKeuM8ewbV6Z";analytics.SNIPPET_VERSION="4.13.2";
            analytics.load("0x8AxE1IIioq3mJgxEGDOKeuM8ewbV6Z");
            analytics.page();
        }}();
    </script>


    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="<?=Yii::getAlias('@web');?>/img/favicon.ico" type="image/ico">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">

    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
</head>


<body>


<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-WWXHM4Z"
                  height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->


<?php $this->beginBody() ?>

    <?= $content ?>

<?php $this->endBody() ?>

<!--
<script src="https://snippets.freshchat.com/js/fc-pre-chat-form-v2.js"></script>
<script>
    var preChatTemplate = {
        //Form header color and Submit button color.
        mainbgColor: '#0aa4db',
        //Form Header Text and Submit button text color.
        maintxColor: '#fff',
        //Chat Form Title
        heading: 'DillBill',
        //Chat form Welcome Message
        textBanner: 'Zəhmət olmasa Ad Soyad və email adresinizi qeyd edin.',
        //Submit Button Label.
        SubmitLabel: 'Start Chat',
        //Fields List - Maximum is 5
        //All the values are mandatory and the script will not work if not available.
        fields : {
            field2 : {
                //Type for Name - Do not Change
                type: "name",
                //Label for Field Name, can be in any language
                label: "Name",
                //Default - Field ID for Name - Do Not Change
                fieldId: "name",
                //Required "yes" or "no"
                required: "yes",
                //Error text to be displayed
                error: "Please Enter a valid name"
            },
            field3 : {
                //Type for Email - Do Not Change
                type: "email",
                //Label for Field Email, can be in any language
                label: "Email",
                //Default - Field ID for Email - Do Not Change
                fieldId: "email",
                //Required "yes" or "no"
                required: "yes",
                //Error text to be displayed
                error: "Please Enter a valid Email"
            },
        }
    };
    window.fcSettings = {
        token: "5f5687f9-ccae-485d-b82b-78f98d5d92a2",
        host: "https://wchat.freshchat.com",
        config: {
            cssNames: {
                //The below element is mandatory. Please add any custom class or leave the default.
                widget: 'custom_fc_frame',
                //The below element is mandatory. Please add any custom class or leave the default.
                expanded: 'custom_fc_expanded'
            }
        },
        onInit: function() {
            console.log('widget init');
            fcPreChatform.fcWidgetInit(preChatTemplate);
        }
    };
</script>
<script src="https://wchat.freshchat.com/js/widget.js" async></script>-->


</body>


</html>
<?php $this->endPage() ?>