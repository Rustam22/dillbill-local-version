<?php

use frontend\assets\BasicAppAsset;

BasicAppAsset::register($this);

?>


<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">
    <head>
        <title><?= Yii::$app->devSet->getTranslate('payment') ?> | DillBill</title>

        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" href="<?=Yii::getAlias('@web');?>/img/favicon.ico" type="image/ico">

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
                analytics.identify('<?= Yii::$app->user->id ?>',
                    {
                        name: '<?= Yii::$app->user->identity->username ?>',
                        email: '<?= Yii::$app->user->identity->email ?>'
                    },
                    {
                        integrations: {
                            Intercom : {
                                user_hash : "<?= hash_hmac("sha256", Yii::$app->user->id, "omlykWOLoM0Ty0TPXVBRToMRZQGHPWZyh0Sof9Jc"); ?>"
                            }
                        }
                    }
                );
            }}();
        </script>

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">

        <!-- Optional JavaScript -->
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>

        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

        <style>
            body {
                background-color: #FAFAFA;
                font-family: 'Inter', serif !important;
            }
        </style>
    </head>

    <body>

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

<?php $this->endPage() ?>