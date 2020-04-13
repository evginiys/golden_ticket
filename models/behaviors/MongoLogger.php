<?php

namespace app\models\behaviors;

use MongoDB\BSON\ObjectID;
use Yii;
use yii\base\Behavior;
use yii\base\Event;
use yii\db\ActiveRecord;
use yii\mongodb\Collection;
use yii\mongodb\Exception;

class MongoLogger extends Behavior
{
    private const LOG_COLLECTION_NAME = 'log';

    /**
     * @var Collection
     */
    private $logCollection;

    /**
     *
     */
    public function init()
    {
        $this->logCollection = Yii::$app->mongodb->getCollection(self::LOG_COLLECTION_NAME);

        parent::init();
    }

    /**
     * @return array
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterSave',
            ActiveRecord::EVENT_AFTER_INSERT => 'afterSave',
            ActiveRecord::EVENT_AFTER_DELETE => 'afterSave',

            ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeSave',
            ActiveRecord::EVENT_BEFORE_INSERT => 'beforeSave',
            ActiveRecord::EVENT_BEFORE_DELETE => 'beforeSave',
        ];
    }

    /**
     * @param $event
     *
     * @return bool|int
     * @throws Exception
     */
    public function afterSave($event)
    {
        $data = $event->sender->getAttributes();
        $loggerData = $data;
        unset($loggerData['id']);

        return $this->logCollection->update(['logger_id' => md5(json_encode($loggerData))], ['success' => true]);
    }

    /**
     * @param Event $event
     *
     * @return ObjectID
     * @throws Exception
     */
    public function beforeSave($event)
    {
        $data = $event->sender->getAttributes();
        unset($data['id']);
        $data['logger_id'] = md5(json_encode($data));
        $data['model'] = get_class($event->sender);
        $data['oldAttributes'] = $event->sender->getOldAttributes();

        return $this->logCollection->insert($data);
    }

}