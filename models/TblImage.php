<?php
namespace app\models;

use yii\base\Model;
use yii\helpers\BaseFileHelper;
use yii\imagine\Image;

class TblImage extends Model {
    
    ////////////////////////////////////////////////
    // Constants
    ////////////////////////////////////////////////
    
    // Root folders
    
    const ROOT_UPLOAD = 'uploads/';
    const ROOT_RFM_IMG = 'rfmimages/';
    const ROOT_RFM_THUMB = 'rfmthumbs/';
    
    const PATH_POST = '/images/post/';
    const PATH_USER = '/images/user/';
    
    // Image type
    
    const HEADER = 'header';
    const PROFILE = 'profile';
    
    // Image size
    
    const SIZE_ORIG = '.original';
    const SIZE_THUMB = '.thumbnail';
    
    // Image size values
    
    const THUMB_W = 150;
    const THUMB_H = 150;
    
    // Placeholders
    
    const TEMP_ORIG = "/blogheader.original.jpg";
    const TEMP_THUMB = "/blogheader.thumbnail.jpg";
    
    ////////////////////////////////////////////////
    // Properties
    ////////////////////////////////////////////////
    
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
     * It generates paths for:
     * 
     * - Folders for user profile images and the images
     *   that the user uploads to the server.
     * - Those said images.
     * 
     * @param type $user_id
     * @param type $type It uses the model constants
     * @param type $ext Image extension
     * @param type $thumb If it's going to be a thumbnail or not
     * @param type $post_id
     * @return string
     */
    public static function pathGenerator ($user_id, $type=null, $ext=null, $thumb=false, $post_id=null)
    {
        switch ($type) {
            
            // Generate root folders
            
            case self::ROOT_UPLOAD:
                // Path: 'uploads/1/'
                $path = self::ROOT_UPLOAD . $user_id . '/';
                break;
            
            case self::PATH_USER:
                // Path: 'uploads/1/images/user/'
                $path = self::ROOT_UPLOAD . $user_id . self::PATH_USER;
                break;
            
            default:
            case self::ROOT_RFM_IMG:
                // Path: 'rfmimages/1/'
                $path = self::ROOT_RFM_IMG . $user_id . '/';
                break;
            
            case self::ROOT_RFM_THUMB:
                // Path: 'rfmthumbs/1/'
                $path = self::ROOT_RFM_THUMB . $user_id . '/';
                break;
            
            // Generate post folder
            
            case self::PATH_POST:
                // Path: 'uploads/1/images/post/23/'
                $path = self::ROOT_UPLOAD . $user_id . self::PATH_POST . $post_id . '/';
                break;
            
            // Generate user profile image path
            
            case self::PROFILE:
                $path = $thumb ?
                    // Path: 'uploads/1/images/user/profile.thumbnail.jpg'
                    self::ROOT_UPLOAD . $user_id . self::PATH_USER
                    . self::PROFILE . self::SIZE_THUMB . $ext
                    :
                    // Path: 'uploads/1/images/user/profile.original.jpg'
                    self::ROOT_UPLOAD . $user_id . self::PATH_USER
                    . self::PROFILE . self::SIZE_ORIG . $ext;
                break;
            
            // Generate post header image path
            
            case self::HEADER:
                
                $path = $thumb ?
                    // Path: 'uploads/1/images/post/23/header.thumbnail.jpg'
                    self::ROOT_UPLOAD . $user_id . self::PATH_POST . $post_id . '/'
                    . self::HEADER . self::SIZE_THUMB . $ext
                    :
                    // Path: 'uploads/1/images/post/23/header.original.jpg'
                    self::ROOT_UPLOAD . $user_id . self::PATH_POST . $post_id . '/'
                    . self::HEADER . self::SIZE_ORIG . $ext;
                break;
            
        }
        return $path;
    }
    
    /**
     * Searches for the "src=" attribute value from "img" tags
     * and stores them in a return array.
     * 
     * @param type $content
     * @param type $thumbs if true, it also obtains thumbnail routes
     * @return array
     */
    public static function getImagePathsFromContent ($content, $thumbs = false) {
        
        $pattern = '/src\="([^"]*)\?/';
        preg_match_all($pattern, $content, $matches, PREG_OFFSET_CAPTURE);
        
        foreach($matches[1] as $array) {    
            $result[] = $array[0];
            if($thumbs) {
                $result[] = str_replace(TblImage::ROOT_RFM_IMG, TblImage::ROOT_RFM_THUMB, $array[0]);
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
            
            // Generate profile image path
            $imagepath = self::pathGenerator($user->user_id, self::PROFILE, $user->userimage);
            
            // Save profile image
            $this->imageFile->saveAs($imagepath);
            
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

        // Create the folder in which the image will be saved
        
        $path = TblImage::pathGenerator(
                $post->user_id,
                TblImage::PATH_POST,
                null,
                null,
                $post->post_id
        );

        if(!file_exists($path))
        {
            BaseFileHelper::createDirectory($path);
        }

        // Generate image path
        $imagepath = TblImage::pathGenerator(
                $post->user_id,
                TblImage::HEADER,
                $post->headerimage,
                false,
                $post->post_id
        );

        if ($this->validate()) {
            
            // Save image
            $this->imageFile->saveAs($imagepath);
        
            // Generate thumbnail image path
            $thumbpath = TblImage::pathGenerator(
                    $post->user_id,
                    TblImage::HEADER,
                    $post->headerimage,
                    true,
                    $post->post_id
            );

            // Create and save the thumbnail
            Image::thumbnail($imagepath, TblImage::THUMB_W, TblImage::THUMB_H)
                ->save(($thumbpath), ['quality' => 50]);

            return true;    
        } else { return false; }
    }
}
