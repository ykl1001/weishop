<?php
function u($url, $args = array()){
    if (strpos($url, '#') !== false) {
		$urls = explode('#', $url, 2);
		$url = $urls[1];
		URL::forceRootUrl(Request::getScheme().'://'.$urls[0].'.'.Config::get('app.domain'));
	}
	$url = URL::to($url) . (count($args) > 0 ? '?' : '') . http_build_query($args);
	URL::forceRootUrl('');
    return $url;
}

function formatImage($url,$withd = 0,$height = 0, $isCat = 1){
	if(Config::get('app.image_type') == 'Server') {
        $url .= '@';
        if($withd > 0){
            $url .= $withd.'w_';
        }
        if($height > 0){
            $url .= $height.'h_';
        }
        if($isCat == 2){
            $url .= '4e_';
        }elseif($isCat > 0){
            $url .= '1e_1c_';
        }
        return $url.'1o.jpg';
    } else {
        $url .= '?x-oss-process=image/resize,';
        if($withd > 0){
            $url .= ',w_' . $withd;
        }
        if($height > 0){
            $url .= ',h_' . $height;
        }
        if($isCat == 2){
            $url .= ',m_pad,color_FFFFFF';
        }elseif($isCat > 0){
            $url .= ',m_fill';
        }
        return $url;
    }
}

function yzday($time){
	return YiZan\Utils\Time::toDate($time, 'Y-m-d');
}

function yzhour($time){
	return YiZan\Utils\Time::toDate($time, 'Y-m-d H:i');
}

function yztime($time, $format = 'Y-m-d H:i:s'){
	return YiZan\Utils\Time::toDate($time, $format);
}

function formatTime($time){
	$sub = UTC_TIME - $time;
	if($sub < 60){
    	$timestr = $sub . '秒钟';
    } else if($sub < 3600){
    	$timestr = (int)($sub/60) . '分钟';
    } else if($sub < 3600 * 24){
    	$timestr = (int)($sub/3600) . '小时';
    } else {
    	$timestr = (int)($sub/(3600 * 24)) . '天';
    }
    return $timestr;
}

//格式化html
function yzHtmlSpecialchars($string, $flags = NULL){
	if(is_array($string)){
		foreach($string as $key => $val){
			$string[$key] = yzHtmlSpecialchars($val, $flags);
		}
	}else{
		if($flags === NULL){
			$string = str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $string);
			if(strpos($string, '&amp;#') !== false){
				$string = preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4}));)/', '&\\1', $string);
			}
		}else{
			if(PHP_VERSION < '5.4.0'){
				$string = htmlspecialchars($string, $flags);
			}else{
				$string = htmlspecialchars($string, $flags,'UTF-8');
			}
		}
	}
	return $string;
}

//还原html格式化后的字符串
function yzHtmlSpecialcharsDecode($string){ 
	if(is_array($string)){
		foreach($string as $key => $val){
			$string[$key] = yzHtmlSpecialcharsDecode($val, $flags);
		}
	}else{
		$string = htmlspecialchars_decode($string);
	}
	return $string; 
}

/**
 * 快速导入第三方框架类库 所有第三方框架的类库文件统一放到 系统的Vendor目录下面
 * @param string $class 类库
 * @param string $baseUrl 基础目录
 * @param string $ext 类库后缀
 * @return boolean
 */
function Vendor($class, $baseUrl = '', $ext='.php') {
    if (empty($baseUrl))
        $baseUrl = VENDOR_PATH;
    $url = str_replace('\\', '/',$baseUrl.$class.$ext);
    return require_once $url;
}

function dblogstart(){
	if(class_exists('DB')){
		DB::connection()->enableQueryLog();
	}
}
function dblogsave(){
	if(class_exists('DB')){
		@file_put_contents(base_path().'/storage/logs/sql-'.time().'.log', print_r(DB::getQueryLog(),1));
	}
}
function dblogprint($terminate = false){
	if(class_exists('DB')) {
		echo "<pre>";
		print_r(DB::getQueryLog());
		if ($terminate) exit;
	}
}

function removeComment($sql)
{
    /* 删除SQL行注释，行注释不匹配换行符 */
    $sql = preg_replace('/^\s*(?:--|#).*/m', '', $sql);

    /* 删除SQL块注释，匹配换行符，且为非贪婪匹配 */
    //$sql = preg_replace('/^\s*\/\*(?:.|\n)*\*\//m', '', $sql);
    $sql = preg_replace('/^\s*\/\*.*?\*\//ms', '', $sql);

    return $sql;
}

function arraySort($arr, $keys, $type = 'asc') {
    $keysvalue = $new_array = array();
    foreach ($arr as $k => $v){
        $keysvalue[$k] = $v[$keys];
    }
    $type == 'asc' ? asort($keysvalue) : arsort($keysvalue);
    reset($keysvalue);
    foreach ($keysvalue as $k => $v) {
        $new_array[$k] = $arr[$k];
    }
    return $new_array;
}


function getClientIp($type = 0) {
    $type       =  $type ? 1 : 0;
    static $ip  =   NULL;
    if ($ip !== NULL) return $ip[$type];
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $pos    =   array_search('unknown',$arr);
        if(false !== $pos) unset($arr[$pos]);
        $ip     =   trim($arr[0]);
    }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ip     =   $_SERVER['HTTP_CLIENT_IP'];
    }elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip     =   $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = sprintf("%u",ip2long($ip));
    $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}