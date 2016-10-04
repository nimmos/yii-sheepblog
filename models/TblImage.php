<?php
namespace app\models;

use Yii;
use yii\base\Model;

class TblImage extends Model {
    
    const UPLOADSROOT = '/uploads/';
    const POSTROOT = 'images/post/';
    const USERROOT = 'images/user/';
    
    const HEADER = 'header';
    
    const ORIGINAL = '.original';
    const THUMBNAIL = '.90x90';

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
    
    public static function getRoutePostImageFolder($post_id)
    {
        return TblImage::UPLOADSROOT . TblImage::POSTROOT . $post_id . '/';
    }
    
    /**
     * Gets the complete path of the imageFile.
     * 
     * @param type $path
     * @param type $imagename
     * @return type
     */
    public static function getRoute($path, $imagename)
    {
        return $path . $imagename;
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
    
}
