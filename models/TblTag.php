<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Query;

/**
 * This is the model class for table "tbl_tags".
 *
 * @property integer $tag_id
 * @property string $tagname
 *
 * @property TagAssign[] $tagAssigns
 */
class TblTag extends ActiveRecord
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
     * @return ActiveQuery
     */
    public function getTagAssigns()
    {
        return $this->hasMany(TagAssign::className(), ['tag_id' => 'tag_id']);
    }
    
    ////////////////////////////////////////////////
    // Queries for tbl_tag_assign
    ////////////////////////////////////////////////
    
    /**
     * Returns all the tags of a given post_id.
     * 
     * @param type $post_id
     * @return type
     */
    public static function getTags ($post_id)
    {
        $result = TblTag::find()
                ->select(['t.tagname'])
                ->from(['tbl_tags t', 'tbl_tag_assign a'])
                ->where('t.tag_id=a.tag_id')
                ->andWhere(['a.post_id' => $post_id])
                ->all();
        
        // Store in an array
        foreach($result as $tag) {
            $tags[] = $tag->tagname;
        }
        
        if(isset($tags)) {
            return $tags;
        } else {
            return array();
        }
    }
    
    /**
     * Create a tag and assign it to the post.
     * 
     * @param type $post_id
     * @param type $tagname
     */
    public static function createAndAssign ($post_id, $tagname)
    {
        $newtag = new TblTag();
        $newtag->tagname = $tagname;
        $newtag->save();

        self::assign($post_id, $newtag->tag_id);
    }
    
    /**
     * Assign a tag to the post.
     * 
     * @param type $post_id
     * @param type $tag_id
     */
    public static function assign ($post_id, $tag_id)
    {
        (new Query())
        ->createCommand()
        ->insert('tbl_tag_assign', [
            'tag_id' => $tag_id,
            'post_id' => $post_id
        ])->execute();
    }
    
    /**
     * Delete an assignment of a tag to a post.
     * 
     * @param type $tag_id
     */
    public static function deleteAssign ($tag_id)
    {
        (new Query())
        ->createCommand()
        ->delete('tbl_tag_assign', ['tag_id' => $tag_id])
        ->execute();
    }
    
    /**
     * Check if a tag is already assigned.
     * If post not specified, it just queries its existence in tbl_tag_assign.
     * 
     * @param type $tag_id
     * @param type $post_id
     * @return type
     */
    public static function isAssigned ($tag_id, $post_id=null)
    {
        if (isset($post_id)) {
            return (new Query())
                ->select(['t.tagname'])
                ->from(['tbl_tags t', 'tbl_tag_assign a'])
                ->where(['a.tag_id' => $tag_id])
                ->andWhere(['a.post_id' => $post_id])
                ->exists();
        } else {
            return (new Query())
                ->select(['tag_id'])
                ->from(['tbl_tag_assign'])
                ->where(['tag_id' => $tag_id])
                ->exists();
        }
    }
    
    /**
     * Organize the tags comparing old and new ones,
     * creating or deleting assignments and the own tags.
     * 
     * @param type $post_id
     * @param type $oldtags
     * @param type $newtags
     */
    public static function organizeTags ($post_id, $oldtags, $newtags)
    {
        
        // NEW AND EXISTING TAGS
        
        if(!empty($newtags))
        {
            foreach($newtags as $tag) {
                
                // Check if each tag already exists
                $foundtag = TblTag::findOne(["tagname" => trim($tag)]);
                
                // If not, create the new tag and assign to this post
                if (!isset($foundtag)) {
                    
                    self::createAndAssign($post_id, trim($tag));
                    
                // If tag already exists, check if it's already assigned
                // If not, then assign to the post
                } else if (!self::isAssigned($foundtag->tag_id, $post_id)) {
                    
                    self::assign($post_id, $foundtag->tag_id);
                }
            }
        }
        
        // REMOVED TAGS

        foreach ($oldtags as $tagname) {

            $tag = TblTag::findOne(["tagname" => $tagname]);

            // Check if some tag has been removed
            if(!in_array($tagname, $newtags)) {

                // And delete the assignment
                self::deleteAssign($tag->tag_id);
            }

            // Check if there's no post with this tag assigned
            if(!self::isAssigned($tag->tag_id)) {

                // And delete the tag
                $tag->delete();
            }
        }
    }
    
    ////////////////////////////////////////////////
    // Auxiliar
    ////////////////////////////////////////////////
    
    /**
     * Converts a tag array to a string
     * with tags separated by commas (,)
     * 
     * @param type $tags
     * @return string
     */
    public static function turnString ($tags)
    {
        if(isset($tags)) {
            return implode(",", $tags);
        } else {
            return "";
        }
    }
    
    /**
     * Converts an string to array
     * 
     * @param type $tagstring
     * @return type
     */
    public static function turnArray ($tagstring)
    {
        if(strlen($tagstring)!=0) {
            return explode(",", $tagstring);
        } else {
            return array();
        }
    }
}
