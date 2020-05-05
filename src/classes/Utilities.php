<?php


namespace UpAssist\WordPress;


class Utilities extends Singleton
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param string $name
     * @param integer $width
     * @param integer $height
     * @param boolean $crop
     */
    public function addImageSize($name, $width, $height, $crop = false){
        add_image_size($name, $width, $height, $crop);
    }
}
