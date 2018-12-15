<?php
namespace YiZan\Utils;

use YiZan\Models\SystemConfig;
//use Image as ImageHandler;
use Intervention\Image\Facades\Image as ImageHandler;
use Config;

class Image {
    /**
     * 获取表单上传参数
     * @return [type] [description]
     */
    public static function getFormArgs($type){
        $class = 'YiZan\\Utils\\Image\\'.Config::get('app.image_type').'Image';
        $image = new $class();
        $path  = 'images/'.Time::toDate(UTC_TIME, 'Y/m/d').'/'.Helper::getSn().'.jpg';
        return $image->getFormArgs($path,$type);
    }

    public static function upload($data, $path,$type){
        $class = 'YiZan\\Utils\\Image\\'.Config::get('app.image_type').'Image';
        $image = new $class();
        return $image->upload($data, $path,$type);
    }

    /**
     * 删除
     * @param  array $paths 路径数组
     * @return boolean
     */


    public static function remove($paths){
    	$class = 'YiZan\\Utils\\Image\\'.Config::get('app.image_type').'Image';
    	$image = new $class();
    	return $image->remove($paths);
    }

    /**
     * 移动
     * @param  [type] $formPath [description]
     * @param  [type] $dir      [description]
     * @return [type]           [description]
     */
    public static function move($formPath, $dir){
    	$class = 'YiZan\\Utils\\Image\\'.Config::get('app.image_type').'Image';
    	$image = new $class();
    	return $image->move($formPath,$dir);
    }

    /**
     * 水印
     */
    public static function watermark($img){

        $watermark = SystemConfig::getConfig('watermark_logo');
        $clarity = SystemConfig::getConfig('watermark_clarity');

        if(empty($watermark) || empty($clarity) ){
            return $img;
        }

        if(Config::get('app.image_type') == 'Oss'){
            $posint = strpos($watermark,'images/');
            //原来的
//            $watermark = substr($watermark,$posint,strlen($watermark));
//            $img .= '@watermark=1&object='.self::url_safe_base64_encode($watermark).'&t='.$clarity.'&p=5';
            //新的
            $watermark = substr($watermark,$posint,strlen($watermark))."?x-oss-process=image/resize,P_50";
            $img .= '?x-oss-process=image/resize,w_600/watermark,image_'.self::url_safe_base64_encode($watermark).',t='.$clarity.',g=se';

            $data = (string)ImageHandler::make($img)->encode('jpg');
            $path  = 'images/'.Time::toDate(UTC_TIME, 'Y/m/d').'/'.Helper::getSn().'.jpg';
            $class = 'YiZan\\Utils\\Image\\'.Config::get('app.image_type').'Image';
            $image = new $class();
            $img = $image->upload($data, $path,'');
            return $img;
        }else{
            $watermark_img = ImageHandler::make($watermark)->opacity($clarity);
            $img = ImageHandler::make($img)->insert($watermark_img, 'center');

            $data = (string)$img->encode('jpg');

            $path  = 'images/'.Time::toDate(UTC_TIME, 'Y/m/d').'/'.Helper::getSn().'.jpg';
            $class = 'YiZan\\Utils\\Image\\'.Config::get('app.image_type').'Image';
            $image = new $class();
            return $image->upload($data, $path,'');
        }
    }

    public function url_safe_base64_encode($string) {
        $data = base64_encode($string);
        $data = rawurlencode($data);
        return $data;
    }
}