 <?php

 use unclead\multipleinput\MultipleInput;
 use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\ConversationUsers */
/* @var $form yii\widgets\ActiveForm */

 //$conversationUsers = \backend\models\ConversationUsers::find()->all();

 //debug($conversationUsers);

 /*foreach ($conversationUsers as $key) {
     //debug($key);
     $convoUser = \backend\models\ConversationUsers::findOne(['id' => $key->id]);
     $user = \common\models\User::findOne(['id' => $key->userId]);
     $convoUser->userEmail = $user->email;
     $convoUser->userName = $user->username;
     $convoUser->save();
 }*/

 /*foreach ($conversationUsers as $key) {
     //debug($key);
     $convoUser = \backend\models\ConversationUsers::findOne(['id' => $key->id]);
     $conversation_old = \backend\models\Conversation::findOne(['id' => $key->conversationId]);

     $convoUser->conversationLevel = $conversation_old->level;
     $convoUser->startsAT = $conversation_old->startsAt;
     $convoUser->conversationTopic = $conversation_old->topicName;
     $convoUser->tutorName = $conversation_old->tutorName;
     $convoUser->tutorImage = $conversation_old->tutorImage;

     $convoUser->save();
 }*/

//$user = \common\models\User::find()->where(['currentPacket' => 'kids'])->all();
//debug($user);

 //foreach ($user as $key) {
     //debug($key->currentPacket);
  //   $atomicUser = \backend\models\User::findOne(['id' => $key->id]);
  //   debug($atomicUser);
  //   $atomicUser->currentPacket = 'kidsEnglish';
 //    $atomicUser->save();
 //}

?>

<div class="conversation-users-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'conversationId')->textInput() ?>

    <?= $form->field($model, 'userId')->textInput() ?>

    <?= $form->field($model, 'requestDate')->textInput() ?>

    <?= $form->field($model, 'requestTime')->textInput() ?>

    <?= $form->field($model, 'action')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
