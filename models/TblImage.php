<?php
namespace app\models;

use yii\base\Model;

class TblImage extends Model {
    
    const UPLOADSROOT = 'uploads/';
    const THUMBSROOT = 'rfmthumbs/';
    
    const POSTROOT = '/images/post/';
    const USERROOT = '/images/user/';
    
    const HEADER = 'header';
    
    const ORIGINAL = '.original';
    const THUMBNAIL = '.thumbnail';
    
    const THUMBNAIL_W = 150;
    const THUMBNAIL_H = 150;

    public $imageFile;
    public $imageRoute;

    /**
     * @inheritdoc
     */
    public function rules () {
        return [
            ['imageFile', 'image', 'extensions' => 'png, jpg',
                //'minWidth' => THUMBNAIL_W, 'maxWidth' => 1000,
                //'minHeight' => THUMBNAIL_H, 'maxHeight' => 1000,
            ],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'imageFile' => 'Post header image',
        ];
    }
    
    /**
     * Generates the route for the post image folder
     * based on user_id and post_id
     * 
     * @param type $user_id
     * @param type $post_id
     * @return type
     */
    public static function getRoutePostImageFolder($user_id, $post_id)
    {
        return TblImage::UPLOADSROOT
                . $user_id . '/'
                . TblImage::POSTROOT
                . $post_id . '/';
    }
    
    /**
     * Generates the route for the post thumbnail folder
     * based on user_id and post_id
     * 
     * @param type $user_id
     * @param type $post_id
     * @return type
     */
    public static function getRoutePostRFMThumbFolder($user_id, $post_id)
    {
        return TblImage::THUMBSROOT
                . $user_id . '/'
                . TblImage::POSTROOT
                . $post_id . '/';
    }
    
    /**
     * Saves the uploaded image in the server.
     * 
     * @return boolean
     */
    public function saveImage()
    {
        if ($this->validate()) {
            $this->imageFile->saveAs($this->imageRoute);
            return true;
        } else { return false; }
    }
    
    /**
     * Searches for the "src=" attribute value from "img" tags
     * and stores them in a return array
     * 
     * @param type $content
     * @return type
     */
    public static function getImageRoutesFromContent ($content) {
        
        $pattern = '/src\="([^"]*)"/';
        preg_match_all($pattern, $content, $matches, PREG_OFFSET_CAPTURE);
        foreach($matches[1] as $array) {
            $result[] = $array[0];
        }
        
        if(empty($result)) {
            $result = array();
        }
        return $result;
    }
}
