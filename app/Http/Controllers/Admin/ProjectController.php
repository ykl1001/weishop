<?php 
namespace YiZan\Http\Controllers\Admin;
use View, Input, Lang,Config,Request,Session,Response,ZipArchive;

/**
 * 系统配置
 */
class ProjectController extends AuthController {
    /**
     * API调用类型
     * @var string
     */
    protected $html = '';

    public function index(){
		/*系统信息*/
		$sysVersion = Config::get('app.sys_version','v1.9');
		$data = ['sysVersion' =>$sysVersion];
		
			if(Input::get("index") != "updateSave"){
				$path = base_path()."/releases/";
				if ($dh = opendir($path)){
					while (($file = readdir($dh))!= false){
						$pics = explode('.' , $file);
						$num = count($pics);
						if($pics[$num-1] == 'sql'){
							//文件名的全路径 包含文件名
							$filePath = $path.$file;
							$fileName = $file;
						}
					}
					closedir($dh);
				}
				if($filePath){
					 $data['filePath']    = $filePath;
					 $data['zipName']     = $fileName;
					 $data['sql'] =  file_exists($path.$data['zipName']);
				}
		 
				$data['showUpdate'] = file_exists(base_path()."/releases/"."update.lock") ?  false  :  true;
			}
        View::share('data', $data);
		return $this->display();
	}

    public function get($dir)
    {
        //先判断指定的路径是不是一个文件夹
        if (is_dir($dir)){
            if ($dh = opendir($dir)){
                while (($file = readdir($dh))!= false){
                    $pics = explode('.' , $file);
                    $num = count($pics);
                    if($file <> "." && $file <> ".." && $pics[$num-1] <> 'zip' && $pics[$num-1] <> 'txt' && $pics[$num-1] <> 'sql' && $pics[$num-1] <> 'lock'){
                        //文件名的全路径 包含文件名
                        $filePath = $dir.$file;
                        //获取文件修改时间
                        //.date("Y-m-d H:i:s",$fmt).
                        $fmt = filemtime($filePath);
                        if (is_dir($filePath)){
                            $file = "<span style='color: firebrick'>".$file."</span>";
                        }else{
                            $file = '<span style="color: green">'.$file.'</span>';
                        }
                        $this->html .= "<span style='color:#666;padding-right: 5px'>(更新文件：".date("Y-m-d H:i:s",$fmt).")</span> ".$dir. $file."<br>";
                        if($file <> "." && $file <> ".." && $pics[$num-1] <> 'zip' && $pics[$num-1] <> 'txt'){
                            $newfilePath = $filePath.'/';
                            if (is_dir($newfilePath)){
                                self::get($newfilePath);
                            }
                        }
                    }
                }
                closedir($dh);
            }
        }
        return  $this->html;
    }
    /**
     * 保存版本
     */
    public function save() {
            // 得到上传的文件信息
            $zipFile = Request::file('zipFile');
            // 判断是否上传
            if($zipFile == null){
                return $this->error("请选择升级补丁");
            }
            //验证是否上传和是否有效
            if(!Request::hasFile('zipFile') && $zipFile->isValid()){
                return $this->error("请选择升级补丁SQL文件错误!");
            }
			self::deldir(base_path()."/releases/");
            $path = base_path()."/releases/";
            $path = str_replace('\\', '/', $path);
            mkdir($path,0777,true);
            // 移动zip文件
            $zipFile->move($path,$zipFile->getClientOriginalName());

            $sysVersion = Config::get('app.sys_version','v1.9');
            if ($dh = opendir($path)){
                while (($file = readdir($dh))!= false){
                    $pics = explode('.' , $file);
                    $num = count($pics);
                    if($pics[$num-1] == 'zip'){
                        //文件名的全路径 包含文件名
                        $filePath = $path.$file;
                        $fileName = $file;
                    }
                }
                closedir($dh);
            }
            $data = [
                'sysVersion' =>$sysVersion,
                'filePath'  =>$filePath,
                'zipName'  =>$fileName
            ];
            $data['file'] = self::get($path);
            $data['showUpdate'] = file_exists(base_path()."/releases/"."update.lock") ? true :  false ;
            View::share('upzip', $data['file']);
            View::share('data', $data);
            return $this->display('index');

    }
	
    public function unzip(){
        /*系统信息*/
        $oldsysVersion =  str_replace('v','',Config::get('app.sys_version','v1.9'));
        $newsysVersion = str_replace('v','',Input::get('v'));
        if($oldsysVersion >=  $newsysVersion){
			self::deldir(base_path()."/releases/");
            return Response::json(['status'=> -1]);
        }else{
            $path = base_path()."/releases/";
            if ($dh = opendir($path)){
                while (($file = readdir($dh))!= false){
                    $pics = explode('.' , $file);
                    $num = count($pics);
                    if($pics[$num-1] == 'zip'){
                        //文件名的全路径 包含文件名
                        $filePath = $path.$file;
                    }
                }
                closedir($dh);
            }
            $upzip = self::jieyaZip($filePath,$path."/");
            return Response::json($upzip);
        }
    }

    public function unzipProject(){
        /*系统信息*/
        $oldsysVersion =  str_replace('v','',Config::get('app.sys_version','v1.9'));
        $newsysVersion = str_replace('v','',Input::get('v'));
        if($oldsysVersion >=  $newsysVersion){
			self::deldir(base_path()."/releases/");
            return Response::json(['status'=> -1]);
        }else{
            return Response::json(['status' => 2 ]);
        }
    }
    public function zipProjectbak(){
        // if(self::unzipProjectBf(base_path()."/app/",'app')) // 备份项目App
        // {
            // self::unzipProjectBf(base_path()."/resources",'resources'); // 备份项目视图
        // }
        return Response::json(['status'=>3]);

    }
	/**
     * 解压zip包
     */
    public function jieyaZip($filename, $path) {
        //需开启配置 php_zip.dll
        header("Content-type:text/html;charset=utf-8");
        //先判断待解压的文件是否存在
        if(!file_exists($filename)){
            return false;
        }
        $starttime = explode(' ',microtime()); //解压开始的时间
        //将文件名和路径转成windows系统默认的gb2312编码，否则将会读取不到
        //长度大于1，是WINNT操作系统
        if (count(explode('WIN', strtoupper(PHP_OS))) > 1) {
            $filename = iconv("utf-8","gb2312", $filename);
            $path = iconv("utf-8","gb2312", $path);
        }
        //打开压缩包
        $resource = zip_open($filename);
        //遍历读取压缩包里面的一个个文件
        while ($dir_resource = zip_read($resource)) {
            //如果能打开则继续
            if (zip_entry_open($resource,$dir_resource)) {
                //获取当前项目的名称,即压缩包里面当前对应的文件名
                $file_name = $path.zip_entry_name($dir_resource);
                //以最后一个“/”分割,再用字符串截取出路径部分
                $file_path = substr($file_name,0,strrpos($file_name, "/"));
                //如果路径不存在，则创建一个目录，true表示可以创建多级目录
                if(!is_dir($file_path)){
                    mkdir($file_path,0777,true);
                }
                //如果不是目录，则写入文件
                if(!is_dir($file_name)){
                    //读取这个文件
                    $file_size = zip_entry_filesize($dir_resource);
                    //最大读取6M，如果文件过大，跳过解压，继续下一个
                    if($file_size<(1024*1024*200)){
                        $file_content = zip_entry_read($dir_resource,$file_size);
                        @file_put_contents($file_name,$file_content);
                    }
                }
                //关闭当前
                zip_entry_close($dir_resource);
            }
        }
        //关闭压缩包
        zip_close($resource);
        $endtime = explode(' ',microtime()); //解压结束的时间
        $thistime = $endtime[0]+$endtime[1]-($starttime[0]+$starttime[1]);
        $thistime = round($thistime,3); //保留3为小数
        return $thistime;
    }

    /**
     * 删除解压的文件
     */
    public function deldir($dir) {   
        //先判断指定的路径是不是一个文件夹
        if (is_dir($dir)){
            if ($dh = opendir($dir)){
                while (($file = readdir($dh))!= false){
                    $pics = explode('.' , $file);
                    if($file <> "." && $file <> ".."){
                        //文件名的全路径 包含文件名
                        $filePath = $dir.$file;             
                        //排除文件夹
                        if (!is_dir($filePath)){   
                            unlink($filePath);
                        }else{
                            if($filePath){
                                self::deldir($filePath.'/');
                                if(count(scandir($filePath))==2){//目录为空,=2是因为.和..存在
                                    rmdir($filePath);// 删除空目录 
                                } 
                            }
                        }
                    }
                }
                closedir($dh);
                //删除当前文件夹：
                if(rmdir($dir)) {
                    return true;
                } else {
                    return false;
                }
            }
        }
    }
    function addFileToZip($path,$zip) {
        $handler = opendir($path);
        while (($filename = readdir($handler)) !== false) {
            if($filename != "." && $filename != ".." ){//文件夹文件名字为'.'和‘..’，不要对他们进行操作
                if(is_dir($path."/".$filename)){// 如果读取的某个对象是文件夹，则递归
                    self::addFileToZip($path . "/" . $filename, $zip);
                } else { //将文件加入zip对象
                    $zip->addFile($path . "/" . $filename);
                }
            }
        }
        @closedir($path);
    }
    //备份
    public function unzipProjectBf($path,$name){
        $zip = new ZipArchive();
        if ($zip->open(base_path().'/patch_update-'.Input::get("v").'-'.$name.'.zip', ZipArchive::OVERWRITE) === TRUE) {
            self::addFileToZip($path, $zip); //调用方法，对要打包的根目录进行操作，并将ZipArchive的对象传递给方法
            $zip->close(); //关闭处理的zip文件
        }
        return true;
    }
    //执行
    public function updatezip(){
        /*系统信息*/
        $oldsysVersion =  str_replace('v','',Config::get('app.sys_version','v1.9'));
        $newsysVersion = str_replace('update_v','',Input::get('v'));
        if($oldsysVersion >  $newsysVersion){
			self::deldir(base_path()."/releases/");
            return Response::json(['status'=> -1]);
        }else{
            $sql = base_path()."/releases/update_".$newsysVersion.'.sql';
			if(file_exists($sql)){ //执行sql
				$sql  = $this->requestApi('admin.user.updatesql',['sysVersion'=>Input::get('v')]);
				if($sql['code'] == 0){
					if(!Config::get('app.sys_version')){
						$oPVersion = "=> '".Config::get('app.operation_version');
						$new= $oPVersion."',\n\n        'sys_version' => '".Input::get('v')."";
						$newApp = str_replace($oPVersion,$new,file_get_contents(config_path('app.php')));
					}else{
						$newApp = str_replace(Config::get('app.sys_version','v1.9'),Input::get('v'),file_get_contents(config_path('app.php')));
					}
                    file_put_contents(base_path()."/releases/"."update.lock","");
					file_put_contents(config_path('app.php'),$newApp);
					return Response::json(['status'=>5]);
				}else{
                    if($sql['code'] == 1){
                        return Response::json(['status'=>1]);
                    }else{
                        return Response::json(['status'=>3]);
                    }
				}
            }else{
				return Response::json(['status'=>2]);					
			}
			return Response::json(['status'=>0]);
            /*if(self::copDir($path,base_path().'/')){
                if(file_exists($sql)){ //执行sql
				$sql  = $this->requestApi('admin.user.updatesql',['sysVersion'=>Input::get('v')]);
                    if($sql){
						
					}
                }
                if(self::deldir(base_path()."/releases/".Config::get('app.sys_version','v1.9')."/")){
                    if(!Config::get('app.sys_version')){
                        $oPVersion = "=> '".Config::get('app.operation_version');
                        $new= $oPVersion."',\n\n        'sys_version' => '".Input::get('v')."";
                        $newApp = str_replace($oPVersion,$new,file_get_contents(config_path('app.php')));
                    }else{
                        $newApp = str_replace(Config::get('app.sys_version','v1.9'),Input::get('v'),file_get_contents(config_path('app.php')));
                    }
                    file_put_contents(config_path('app.php'),$newApp);
                }
                return Response::json(['status'=>1]);
            }else{
                return Response::json(['status'=>0]);
            }*/
        }
    }

    //执行拷贝
    function copDir($src,$dst) {
        $dir = opendir($src);
        @mkdir($dst);
        while(false !== ( $file = readdir($dir)) ) {
            if (( $file != '.' ) && ( $file != '..' )) {
                if ( is_dir($src . '/' . $file) ) {
                    self::copDir($src . '/' . $file,$dst . '/' . $file);
                    continue;
                }
                else {
                    copy($src . '/' . $file,$dst . '/' . $file);
                }
            }
        }
        closedir($dir);
        return true;
    }

}
