<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_tags".
 *
 * @property integer $tag_id
 * @property string $tagname
 *
 * @property TagAssign[] $tagAssigns
 */
class TblTag extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_tags';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tagname'], 'required'],
            [['tagname'], 'string', 'max' => 30],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'tag_id' => 'Tag ID',
            'tagname' => 'Tagname',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTagAssigns()
    {
        return $this->hasMany(TagAssign::className(), ['tag_id' => 'tag_id']);
    }
}
