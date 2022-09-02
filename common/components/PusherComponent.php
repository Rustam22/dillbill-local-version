<?php

namespace common\components;

use Pusher\PusherException;
use yii\base\Component;
use yii\base\InvalidConfigException;
use Pusher\Pusher;

class PusherComponent extends Component
{
    private $_pusher = null;

    public $appId = null;
    public $appKey = null;
    public $appSecret = null;

    public $options = [];


    public function init()
    {
        parent::init();

        // Mandatory config parameters.

        /**
         * @throws InvalidConfigException
         */
        if (!$this->appId) {
            throw new InvalidConfigException('AppId cannot be empty!');
        }

        /**
         * @throws InvalidConfigException
         */
        if (!$this->appKey) {
            throw new InvalidConfigException('AppKey cannot be empty!');
        }

        /**
         * @throws InvalidConfigException
         */
        if (!$this->appSecret) {
            throw new InvalidConfigException('AppSecret cannot be empty!');
        }

        /**
         * Create a new Pusher object if it hasn't already been created.
         * @throws InvalidConfigException
         */
        if ($this->_pusher === null) {
            try {
                $this->_pusher = new Pusher(
                    $this->appKey,
                    $this->appSecret,
                    $this->appId,
                    $this->options
                );
            } catch (PusherException $e) {
                throw new InvalidConfigException($e);
            }
        }
    }

    /**
     * @param array $channel An array of channel names to publish the event on.
     * @param string $event
     * @param mixed $data Event data
     */
    public function push($channel, $event, $data) {
        $this->_pusher->trigger($channel, $event, $data);
    }
}