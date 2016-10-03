<?php
namespace app\models;

use yii\base\Model;
use yii\web\UploadedFile;
use yii\imagine\Image;

class TblImage extends Model {
    
    /*
     * Notes:
     * - By default, it will save files inside the web folder.
     * - If there's '../' at the start, it will save it in the
     *   parent folder, which is the webapp protected root folder.
     */
    const POSTIMAGE_ROOT = '../uploads/images/post/';
    const USERIMAGE_ROOT = '../uploads/images/user/';
    
    const ORIGINAL = 'original';
    const THUMBNAIL = '90x90';

    public $imageFile;
    public $imageRoute;

    /**
     * @inheritdoc
     */
    public function rules () {
        return [
            ['imageFile', 'image', 'extensions' => 'png, jpg',
                'minWidth' => 100, //'maxWidth' => 1000,
                'minHeight' => 100, //'maxHeight' => 1000,
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
     * Returns the folder route based on post_id.
     * 
     * @param type $post_id
     * @return type
     */
    public static function getFolderRoute ($post_id) {
        return TblImage::POSTIMAGE_ROOT . $post_id . '/';
    }
    
    /**
     * Gets the complete path of the imageFile.
     * 
     * @param type $post_id
     * @param type $imagename
     * @return type
     */
    public static function getRoute ($post_id, $imagename) {
        return TblImage::getFolderRoute($post_id) . $imagename;
    }
    
    /**
     * Sets the complete path of the imageFile
     * based on post_id and the image name (includes its size).
     * 
     * @param type $post_id
     * @param type $imagename
     */
    public function setRoute ($post_id, $imagename) {
        $this->imageRoute = TblImage::getFolderRoute($post_id) . $imagename;
    }
    
    /**
     * Saves the uploaded image in the server.
     * 
     * @return boolean
     */
    public function saveImage () {
        if ($this->validate()) {
            $this->imageFile->saveAs($this->imageRoute);
            return true;
        } else { return false; }
    }
    
    public function resize () {
        
    }
}
