<?php
namespace app\models;

use yii\base\Model;
use yii\helpers\BaseFileHelper;
use yii\imagine\Image;

class TblImage extends Model {
    
    const UPLOADSROOT = 'uploads/';
    const IMAGESROOT = 'rfmimages/';
    const THUMBSROOT = 'rfmthumbs/';
    
    const POSTROOT = '/images/post/';
    const USERROOT = '/images/user/';
    
    const HEADER = 'header';
    const PROFILE = 'profile';
    
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
            'imageFile' => 'Image',
        ];
    }
    
    ////////////////////////////////////////////////
    // Route generators
    ////////////////////////////////////////////////
        
    /**
     * Generates the route for the user profile image folder
     * based on user_id
     * 
     * @param type $user_id
     * @return type
     */
    public static function routeUserImageDir($user_id)
    {
        return TblImage::UPLOADSROOT . $user_id
                . TblImage::USERROOT;
    }
    
    /**
     * Generates the route for the post image folder
     * based on user_id
     * 
     * @param type $user_id
     * @return type
     */
    public static function routeRFMImageDir($user_id)
    {
        return TblImage::IMAGESROOT . $user_id . '/';
    }
    
    /**
     * Generates the route for the post thumbnail folder
     * based on user_id
     * 
     * @param type $user_id
     * @return type
     */
    public static function routeRFMThumbDir($user_id)
    {
        return TblImage::THUMBSROOT . $user_id . '/';
    }
    
    /**
     * Generates the route for the post header image folder
     * based on user_id and post_id
     * 
     * @param type $user_id
     * @param type $post_id
     * @return type
     */
    public static function routePostHeaderDir($user_id, $post_id)
    {
        return TblImage::UPLOADSROOT . $user_id . '/'
                . TblImage::POSTROOT . $post_id . '/';
    }
    
    /**
     * Searches for the "src=" attribute value from "img" tags
     * and stores them in a return array.
     * 
     * @param type $content
     * @param type $thumbs if true, it also obtains thumbnail routes
     * @return array
     */
    public static function routesImageFromContent ($content, $thumbs = false) {
        
        $pattern = '/src\="([^"]*)\?/';
        preg_match_all($pattern, $content, $matches, PREG_OFFSET_CAPTURE);
        
        foreach($matches[1] as $array) {    
            $result[] = $array[0];
            if($thumbs) {
                $result[] = str_replace(TblImage::IMAGESROOT, TblImage::THUMBSROOT, $array[0]);
            }
        }
        
        if(empty($result)) {
            $result = array();
        }
        return $result;
    }
    
    /**
     * Saves the uploaded profile image in the db.
     * 
     * @param type $user
     * @return boolean
     */
    public function saveProfileImage($user)
    {
        if ($this->validate()) {
            
            // Profile image name
            $imagename = self::PROFILE . self::ORIGINAL . $user->userimage;
            
            // Profile image path
            $this->imageRoute = self::routeUserImageDir($user->user_id) . $imagename;
                
            // Save profile image
            $this->imageFile->saveAs($this->imageRoute);
            
            return true;
        } else { return false; }
    }
    
    /**
     * Saves the uploaded image in the db.
     * 
     * @return boolean
     */
    public function saveHeaderImage($post)
    {
        
        // Image name
        $imagename = TblImage::HEADER . TblImage::ORIGINAL . $post->headerimage;

        // Create the folder in which the image will be saved, and set route
        
        $directory = TblImage::routePostHeaderDir($post->user_id, $post->post_id);

        if(!file_exists($directory))
        {
            BaseFileHelper::createDirectory($directory);
        }

        // Establish image path
        $this->imageRoute = $directory . $imagename;

        if ($this->validate()) {
            
            // Save image in directory
            $this->imageFile->saveAs($this->imageRoute);
        
            // Thumbnail image name
            $imagename = TblImage::HEADER . TblImage::THUMBNAIL . $post->headerimage;

            // Create and save the thumbnail
            Image::thumbnail($this->imageRoute, TblImage::THUMBNAIL_W, TblImage::THUMBNAIL_W)
                ->save(($directory . $imagename), ['quality' => 50]);

            return true;    
        } else { return false; }
    }
}
