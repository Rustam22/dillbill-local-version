<?php


namespace frontend\controllers;


use common\models\User;
use common\models\UserParameters;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupUserForm;
use pctux\recaptcha\InvisibleRecaptchaValidator;
use Yii;
use yii\base\Exception;
use yii\base\InvalidArgumentException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\Response;


class UserController extends AppController {

    public $layout = 'landing';


    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['login', 'logout', 'sign-up', 'confirm-email', 'resend-confirm', 'forgot-password', 'reset-password'],
                'rules' => [
                    [
                        'actions' => ['login', 'sign-up', 'forgot-password', 'reset-password'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout', 'confirm-email', 'resend-confirm'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
                'denyCallback' => function($rule, $action) {
                    Yii::$app->response->redirect(['landing/index']);
                },
            ],

            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],

        ];
    }



    /**
     * {@inheritdoc}
     */
    public function actions(): array
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
                'layout' => 'user'
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }



    /**
     * Displays homepage.
     *
     * @return Response
     */
    public function actionLogin(): Response
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['dashboard/my-classes']);
        }

        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect(['dashboard/my-classes']);
        } else {
            Yii::$app->session->set('authError', true);
            Yii::$app->session->set('authErrorMessage', Yii::$app->devSet->getTranslate('incorrectEmailOrPassword'));

            return $this->goHome();
        }
    }



    /**
     * Logs out the current user.
     *
     * @return Response
     */
    public function actionLogout(): Response
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }


    /**
     * Displays homepage.
     *
     * @throws Exception
     */
    public function actionSignUp()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['dashboard/my-classes']);
        }

        //debug(Yii::$app->request->post());
        //var_dump(InvisibleRecaptchaValidator::POST_ELEMENT);
        //var_dump(Yii::$app->request->post(InvisibleRecaptchaValidator::POST_ELEMENT));
        //var_dump(InvisibleRecaptchaValidator::validate(Yii::$app->request->post(InvisibleRecaptchaValidator::POST_ELEMENT)));

        if(InvisibleRecaptchaValidator::validate(Yii::$app->request->post(InvisibleRecaptchaValidator::POST_ELEMENT))) {

            $loginForm['_csrf-frontend'] =  Yii::$app->request->post('_csrf-frontend');
            $loginForm['LoginForm'] =  array('email' => Yii::$app->request->post('SignupUserForm')['email'],
                                             'password' => Yii::$app->request->post('SignupUserForm')['password'],
                                             'rememberMe' => true);

            $login = new LoginForm();

            if ($login->load($loginForm) and $login->login()) {
                return $this->redirect(['dashboard/my-classes']);
            } else {
                $model = new SignupUserForm();

                if ($model->load(Yii::$app->request->post()) AND $model->signup()) {
                    $loginModel = new LoginForm();

                    $loginModel->email = $model->email;
                    $loginModel->password = $model->password;
                    $loginModel->rememberMe = true;

                    if($loginModel->login()) {
                        if (!Yii::$app->devSet->isLocal()) {
                            try {
                                $curl = curl_init();
                                curl_setopt_array($curl, array(
                                    CURLOPT_URL => 'https://api.customer.io/v1/send/email',
                                    CURLOPT_RETURNTRANSFER => true,
                                    CURLOPT_ENCODING => '',
                                    CURLOPT_MAXREDIRS => 10,
                                    CURLOPT_TIMEOUT => 0,
                                    CURLOPT_FOLLOWLOCATION => true,
                                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                    CURLOPT_CUSTOMREQUEST => 'POST',
                                    CURLOPT_POSTFIELDS =>'
                                    {
                                          "to": "'.Yii::$app->user->identity->email.'",
                                          "transactional_message_id": "'.Yii::$app->acc::EMAIL_MESSAGES_ID[Yii::$app->language]['confirmEmail'].'",
                                          "message_data": {
                                                "emailConfirmationUrl": "'.Url::to(['user/confirm-email'], true).'?token='.Yii::$app->user->identity->verification_token.'",
                                                "userEmailAddress": "'.Yii::$app->user->identity->email.'",
                                                "userName": "'.Yii::$app->user->identity->userProfile->name.'"
                                          },
                                          "identifiers": {
                                                "id": "'.Yii::$app->user->id.'"
                                          }
                                    }',
                                    CURLOPT_HTTPHEADER => array(
                                        'Authorization: Bearer ',
                                        'Content-Type: application/json'
                                    ),
                                ));

                                $response = curl_exec($curl);
                                curl_close($curl);
                            } catch (\Exception $exception) {}
                        }

                        if(Yii::$app->request->post('root') == 'empty') {
                            return $this->redirect(['dashboard/my-classes']);
                        } elseif (Yii::$app->request->post('root') == 'payment') {
                            return $this->redirect(['payment/index']);
                        }
                    } else {
                        $loginModel->password = '';
                        $errorMessage = '';

                        if(isset($loginModel->errors['password'][0])) {
                            $errorMessage = $loginModel->errors['password'][0];
                        }

                        $errorMessage = ($errorMessage == '') ? 'incorrectEmailOrPassword' : $errorMessage;

                        Yii::$app->session->set('authError', true);
                        Yii::$app->session->set('authErrorMessage', $errorMessage);

                        return $this->goHome();
                    }

                } else {
                    $model->email = '';
                    $errorMessage = '';

                    if(isset($model->errors['email'][0])) {
                        $errorMessage = $model->errors['email'][0];
                    }

                    $errorMessage = ($errorMessage == '') ? 'Wrong password' : $errorMessage;

                    Yii::$app->session->set('authError', true);
                    Yii::$app->session->set('authErrorMessage', $errorMessage);

                    return $this->goHome();
                }
            }
        } else {
            Yii::$app->session->set('authError', true);
            Yii::$app->session->set('authErrorMessage', Yii::$app->devSet->getTranslate('googleRecaptchaError'));

            return $this->goHome();
        }
    }




    /**
     * Displays homepage.
     *
     * @param $token
     * @return Response
     * @throws BadRequestHttpException
     */
    public function actionConfirmEmail($token): Response
    {
        if (Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $user = User::findOne(['verification_token' => $token]);
        $userParameters = UserParameters::findOne(['userId' => $user->id]);

        if($user == null) {
            throw new BadRequestHttpException(Yii::$app->devSet->getTranslate('userWasNotFound'));
        }

        if($userParameters->confirmed == 'yes') {
            throw new BadRequestHttpException(Yii::$app->devSet->getTranslate('userAlreadyConfirmed'));
        }

        $userParameters->confirmed = 'yes';

        if(!$userParameters->save(false)) {
            throw new BadRequestHttpException(Yii::$app->devSet->getTranslate('userFailedToSave'));
        }


        if (!Yii::$app->devSet->isLocal()) {
            try {
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://api.customer.io/v1/send/email',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS =>'
                        {
                              "to": "'.Yii::$app->user->identity->email.'",
                              "transactional_message_id": "'.Yii::$app->acc::EMAIL_MESSAGES_ID[Yii::$app->language]['welcome'].'",
                              "message_data": {
                                    "name": "'.Yii::$app->user->identity->userProfile->name.'",
                                    "email": "'.Yii::$app->user->identity->email.'"
                              },
                              "identifiers": {
                                    "id": "'.Yii::$app->user->id.'"
                              }
                        }',
                    CURLOPT_HTTPHEADER => array(
                        'Authorization: Bearer ',
                        'Content-Type: application/json'
                    ),
                ));

                $response = curl_exec($curl);
                curl_close($curl);
            } catch (\Exception $exception) {}
        }

        return $this->redirect(['dashboard/my-classes']);
    }



    /**
     * @throws BadRequestHttpException
     */
    public function actionResendConfirm(): Response
    {
        if (Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $verificationToken = Yii::$app->request->post('verification_token');

        if (Yii::$app->user->identity->verification_token != $verificationToken) {
            throw new BadRequestHttpException(Yii::$app->devSet->getTranslate('wrongVerificationToken'));
        } else {

            if (!Yii::$app->devSet->isLocal()) {
                try {
                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                        CURLOPT_URL => 'https://api.customer.io/v1/send/email',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_POSTFIELDS =>'
                               {
                                      "to": "'.Yii::$app->user->identity->email.'",
                                      "transactional_message_id": "'.Yii::$app->acc::EMAIL_MESSAGES_ID[Yii::$app->language]['confirmEmail'].'",
                                      "message_data": {
                                            "emailConfirmationUrl": "'.Url::to(['user/confirm-email'], true).'?token='.Yii::$app->user->identity->verification_token.'",
                                            "userEmailAddress": "'.Yii::$app->user->identity->email.'",
                                            "userName": "'.Yii::$app->user->identity->userProfile->name.'"
                                      },
                                      "identifiers": {
                                            "id": "'.Yii::$app->user->id.'"
                                      }
                                }',
                        CURLOPT_HTTPHEADER => array(
                            'Authorization: Bearer ',
                            'Content-Type: application/json'
                        ),
                    ));

                    $response = curl_exec($curl);
                    curl_close($curl);
                } catch (\Exception $exception) {}

            }

            Yii::$app->session->set('resendConfirmation', true);

            return $this->redirect(['dashboard/my-classes']);
        }
    }



    /**
     * Displays homepage.
     *
     * @return Response
     * @throws Exception
     */
    public function actionForgotPassword(): Response
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['dashboard/my-classes']);
        }

        $model = new PasswordResetRequestForm();

        if ($model->load(Yii::$app->request->post())) {
            if($model->validate()) {
                if ($model->sendEmail()) {
                    Yii::$app->session->setFlash('resetResponse', true);
                } else {
                    Yii::$app->session->setFlash('resetResponse', false);
                }
            } else {
                Yii::$app->session->setFlash('resetResponse', false);
            }

            return $this->redirect(['landing/index']);
        }

        return $this->redirect(['landing/index']);
    }



    /**
     * Displays homepage.
     *
     * @param $token
     * @return Response|string
     * @throws BadRequestHttpException|Exception
     */
    public function actionResetPassword($token) {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['dashboard/my-classes']);
        }

        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException(Yii::$app->devSet->getTranslate('wrongPasswordResetToken'));
        }

        $context = array();

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            return $this->redirect(['user/login']);
        }

        return $this->render('reset-password', $context);
    }

}