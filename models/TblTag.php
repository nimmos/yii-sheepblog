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
            [['tagname'], 'unique'],
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
     * Obtain an array of post_id which are assigned
     * the tags specified in "tagstring".
     *
     * @param type $tagstring
     * @return type
     */
    public static function getPostsByTags ($tagstring)
    {

        foreach($tagstring as $tag) {
            $where[] = ['like', 'tagname', $tag];
        }

        $return = (new Query())
            ->select(['a.post_id'])
            ->from(['tbl_tags t', 'tbl_tag_assign a'])
            ->where(array_merge(['or'], $where))
            ->andWhere('a.tag_id=t.tag_id')
            ->all();

        if(!empty($return)) {

            foreach($return as $post) {
                $posts[] = array_values($post)[0];
            }
            return $posts;
        } else {
            return array();
        }
    }

    /**
     * Obtain an array of tags matching "searchstring"
     *
     * @param type $searchstring
     * @return type
     */
    public static function searchTags ($searchstring)
    {
        if(empty($searchstring))
        {
            // Perform query for all tags
            return (new Query())
                ->select(['tagname'])
                ->from(['tbl_tags'])
                ->orderBy('usage DESC')
                ->limit(30)
                ->all();
        } else {

            // Building WHERE clause

            foreach($searchstring as $string) {
                $where[] = ['like', 'tagname', $string];
            }

            // Perform query
            return (new Query())
                ->select(['tagname'])
                ->from(['tbl_tags'])
                ->where(array_merge(['or'], $where))
                ->orderBy('usage DESC')
                ->limit(30)
                ->all();
        }
    }

    ////////////////////////////////////////////////
    // Logic for organizing tags
    ////////////////////////////////////////////////

    /**
     * Organize the tags comparing old and new ones,
     * creating or deleting assignments and the own tags.
     *
     * @param type $post_id
     * @param type $oldtags
     * @param type $newtags
     */
    public static function organizeTags ($post_id, $oldtags=array(), $newtags)
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
     * @param type $array
     * @return string
     */
    public static function turnString ($array, $separator=",")
    {
        if(isset($array)) {
            return implode($separator, $array);
        } else {
            return "";
        }
    }

    /**
     * Converts an string to array
     *
     * @param type $string
     * @return type
     */
    public static function turnArray ($string)
    {
        if(strlen($string)!=0) {
            return array_filter(explode(",", $string));
        } else {
            return array();
        }
    }

    /**
     * Cleans a string with spaces and
     * returns an array with unique and non-empty items.
     *
     * @param type $string
     * @return string
     */
    public static function cleanStringToArray ($string)
    {
        if(strlen($string)!=0) {

            $array = explode(",", $string);
            $string = implode(" ", $array);
            $array = explode(" ", $string);
            $array = array_filter($array);
            $array = array_unique($array);
            return $array;
        } else {
            return array();
        }
    }
}
