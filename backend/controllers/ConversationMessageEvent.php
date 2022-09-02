<?php


namespace backend\controllers;

use yii\base\Event;

class ConversationMessageEvent extends Event
{
    public $message;
    public $action;
}