<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\live\components;

/**
 * LiveEvent implements a message which can be send via live communication
 *
 * @since 1.2
 * @author Luke
 */
abstract class LiveEvent extends \yii\base\BaseObject
{

    /**
     * @see \humhub\modules\content\components\ContentContainerActiveRecord
     * @var int
     */
    public $contentContainerId;

    /**
     * @see \humhub\modules\content\models\Content::VISIBILITY_*
     * @var int
     */
    public $visibility;
    
    public $pol_op;

    /**
     * Returns the data of this event as array
     * 
     * @return array the live event data
     */
    public function getData()
    {
        $data = get_object_vars($this);
        unset($data['visibility']);
        unset($data['pol_op']);
        unset($data['contentContainerId']);

        return [
            'type' => str_replace('\\', '.', $this->className()),
            'contentContainerId' => $this->contentContainerId,
            'visibility' => $this->visibility,
            'pol_op' =>$this->pol_op,
            'data' => $data
        ];
    }

}
