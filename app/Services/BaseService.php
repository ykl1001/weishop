<?php namespace YiZan\Services;

use YiZan\Utils\Time;
use YiZan\Utils\Image;
use YiZan\Utils\Helper;

class BaseService 
{
    /**
     * 成功
     */
    const SUCCESS = 0;

	public function __construct() {

	}

    /**
     * 移除in语句的不标准字符
     * @param string $ids int,int,int
     */
    public static function replaceIn(&$ids) {
        if($ids != null) {
            $ids = preg_replace("/(^,)|(,$)|([^0-9,])/", "", $ids);
        }
    }

    public static function getRegionFieldName($level) {
    	static $fields = ['', 'province_id', 'city_id', 'area_id'];
    	return ($level == true && array_key_exists($level, $fields)) ? $fields[$level] : "province_id";
    }

	protected static function removeImage($images){
		if (is_array($images)) {
			foreach ($images as $image) {
				Image::remove($images);
			}
		} else {
			Image::remove($images);
		}
	}

	protected static function movePublicImage($path){
		$dir = 'public/'.Time::toDate(UTC_TIME,'Y-m-d');
		return Image::move($path, $dir);
	}

	protected static function moveUserImage($userId, $path){
		$dir = 'user/'.Helper::getDirsById($userId).'/'.Time::toDate(UTC_TIME,'Y-m-d');
		return Image::move($path, $dir);
	}
	
	protected static function moveCarImage($path){
	    $dir = 'static/car/'.Time::toDate(UTC_TIME,'Y-m-d');
	    return Image::move($path, $dir);
	}

	protected static function removeUserImage($userId){
		$dir = 'user/'.Helper::getDirsById($userId);
		Image::remove($dir);
	}

	protected static function moveSellerImage($sellerId, $path){
		$dir = 'seller/'.Helper::getDirsById($sellerId).'/'.Time::toDate(UTC_TIME,'Y-m-d');
		return Image::move($path, $dir);
	}

	protected static function removeSellerImage($sellerId){
		$dir = 'seller/'.Helper::getDirsById($sellerId);
		Image::remove($dir);
	}

	protected static function moveGoodsImage($sellerId, $goodsId, $path){
		$dir = 'seller/'.Helper::getDirsById($sellerId).'/goods/'.Helper::getDirsById($goodsId).'/'.Time::toDate(UTC_TIME,'Y-m-d');
		return Image::move($path, $dir);
	}

	protected static function removeGoodsImage($sellerId, $goodsId){
		$dir = 'seller/'.Helper::getDirsById($sellerId).'/goods/'.Helper::getDirsById($goodsId);
		Image::remove($dir);
	}

	protected static function moveSystemGoodsImage($goodsId, $path){
		$dir = 'public/goods/'.Helper::getDirsById($goodsId).'/'.Time::toDate(UTC_TIME,'Y-m-d');
		return Image::move($path, $dir);
	}

	protected static function removeSystemGoodsImage($goodsId){
		$dir = 'public/goods/'.Helper::getDirsById($goodsId);
		Image::remove($dir);
	}

	protected static function moveOrderImage($orderId, $path){
		$dir = 'order/'.Helper::getDirsById($orderId).'/'.Time::toDate(UTC_TIME,'Y-m-d');
		return Image::move($path, $dir);
	}

	protected static function moveStaffImage($sellerId, $staffId, $path){
		$dir = 'seller/'.Helper::getDirsById($sellerId).'/staff/'.Helper::getDirsById($staffId).'/'.Time::toDate(UTC_TIME,'Y-m-d');
		return Image::move($path, $dir);
	}

	protected static function removeStaffImage($sellerId, $staffId){
		$dir = 'seller/'.Helper::getDirsById($sellerId).'/staff/'.Helper::getDirsById($staffId);
		Image::remove($dir);
	}
}
