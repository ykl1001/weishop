<?php
namespace YiZan\Http\Controllers\Install;
use YiZan\Http\Controllers\YiZanViewController;
use YiZan\Utils\String;
use View, Session, Input,Cache,File,Config,Request,Response,Redirect,DB;

//系统安装
class IndexController extends YiZanViewController{
    private $install_lock;
    /**
     * 调用模板
     * @var string
     */
    protected $tpl = 'install';
    public function __construct()
    {
        parent::__construct();
        if(file_exists(base_path()."/install/install.lock")) {
            Session::put('db_config',"");
            return Redirect::to(u('admin#Index/index'))->send();
        }
        $this->install_lock = base_path()."/install/install.lock";

    }
    public function bengin()
    {
        Session::put('agent','bengin');
        Session::save();
        return $this->success('开始执行');
    }
    /**
     * 第一步,安装向导首页
     */
    public function index()
    {
        Session::put('agent','');
        Session::save();
        self::clear_cache();
        View::share("site_name", '安装简介');
        //系统安装
        if(file_exists($this->install_lock)) {
            return Redirect::to(u('admin#Public/login'))->send();
        } else {
            return $this->display();
        }
    }
    /**
     * 第二步,检测
     */
    public function check()
    {
        //系统安装
        if(file_exists($this->install_lock)) {
            View::share("jumpUrl", base_path()."/public/login");
            return Redirect::to(u('admin#Public/login'))->send();
        } else {
            $bengin = Session::get('agent');
            if(ACTION_NAME != 'index' && ACTION_NAME != 'bengin' && $bengin != 'bengin'){
                return Redirect::to(u('Index/index'))->send();
            }
            View::share("is_short_open_tag", ini_get('short_open_tag'));
            Session::put('from_items', "");
            Session::save();
            $rs = self::checkEnv();  //检测系统环境
            View::share("result", $rs);
            View::share("site_name", '检测安装');
            return $this->display();
        }
    }
    /**
     * 第三步,填写数据库
     */
    public function database()
    {
        //系统安装
        if(file_exists($this->install_lock))
        {
            View::share("jumpUrl",base_path()."/admin");
            return Redirect::to(u('admin#Public/login'))->send();
        }
        else
        {
            $rs = $this->checkEnv();  //检测系统环境
            if($rs['status'])
            {
                //isset( Session::get('from_items') ) && !empty(Session::get('from_items'))
                if(Session::get('from_items') != '')
                    $froms = Session::get('from_items');
                else
                {
                    $froms = Config::get("install.FROM_ITEMS");
                }
                View::share("froms",$froms);
                View::share("site_name", '填写数据');
                return $this->display();
            }
            else
            {
                View::share("site_name", '输出检测结果');
                View::share("result",$rs);
                return $this->display();//输出检测结果
            }
        }
    }

    /**
     * 最后完成
     */
    public function successOk(){
        return $this->display('success');
    }
    /**
     * 第四步,检测数据库信息
     */
    public function install()
    {
        $bengin = Session::get('agent');
        if(ACTION_NAME != 'index' && ACTION_NAME != 'bengin' && $bengin != 'bengin'){
            return Redirect::to(u('Index/index'))->send();
        }
        $args = Input::all();
        @set_time_limit(3600);
        if(function_exists('ini_set'))
            ini_set('max_execution_time',3600);

        $from_items = Config::get("install.FROM_ITEMS");
        $submit = true;
        //验证用户输入的数据
        foreach($from_items as $key => $items)
        {
            if(isset($args[$key]) && is_array($args[$key]))
            {
                foreach($items as $k => $v)
                {
                    $from_items[$key][$k]['value'] = $args[$key][$k];

                    if(empty($args[$key][$k]) || !preg_match($v['reg'],$args[$key][$k]))
                    {
                        if(empty($args[$key][$k]) && !$v['required'])
                            if($args['upload'] == 1 || $args[$key] == "oss"){
                                if(empty($args[$key][$k])){
                                    $submit = false;
                                    $from_items[$key][$k]['error'] = 1;
                                }
                            }else if($args['upload'] == 2 || $args[$key][$k] == "server"){
                                if(empty($args[$key][$k])){
                                    $submit = false;
                                    $from_items[$key][$k]['error'] = 1;
                                }
                            }else{
                                continue;
                            }
                        else
                        {
                            $submit = false;
                            $from_items[$key][$k]['error'] = 1;
                        }
                    }
                }
            }
        }
        if($from_items['admin']['ADM_PWD']['error'] == 1)
        {
            $from_items['admin']['ADM_PWD2']['error'] = 0;
        }
        else
        {
            $from_items['admin']['ADM_PWD']['notice'] = '';

            if($args['admin']['ADM_PWD'] != $args['admin']['ADM_PWD2'])
            {
                $submit = false;
                $from_items['admin']['ADM_PWD2']['error'] = 1;
            }
        }
        if($args['upload'] == ""){
            $upload = 1;
        }else{
            $upload =      $args['upload'];
        }
        Session::put('from_items',$from_items);
        View::share("upload", $upload);
        if(!$submit)
        {
            View::share("froms",$from_items);
            View::share("site_name", '错误');
            return $this->display("database");
            exit;
        }else{
            $db_configs = $args['dbinfo'];
            @mysql_connect($db_configs['DB_HOST'] . ":" . $db_configs['DB_PORT'], $db_configs['DB_USER'], $db_configs['DB_PWD']);
            if (mysql_error() != "") {
                $outstr = mb_convert_encoding(mysql_error(),'UTF-8','GBK');
                View::share("db_error",$outstr);
                View::share("froms",$from_items);
                View::share("site_name", '错误');
                return $this->display("database");
            }
        }
        $db_config = $args['dbinfo'];
        $user_config = $args['admin'];
        Session::put('db_config',$db_config);
        Session::put('user_config',$user_config);


        if(Session::get('from_app') != '')
            $from_items = Session::get('from_app');
        else
        {
            $from_items = Config::get("install.FROM_APP");
        }
        View::share("froms",$from_items);
        View::share("site_name", '检测数据');
        return $this->display('appconfig');
    }


    public function saveapp(){
        $bengin = Session::get('agent');
        if(ACTION_NAME != 'index' && ACTION_NAME != 'bengin' && $bengin != 'bengin'){
            return Redirect::to(u('Index/index'))->send();
        }
        $args = Input::all();
        @set_time_limit(3600);
        if(function_exists('ini_set'))
            ini_set('max_execution_time',3600);

        $from_items = Config::get("install.FROM_APP");
        $submit = true;
        //验证用户输入的数据
        foreach($from_items as $key => $items)
        {
            if(isset($args[$key]) && is_array($args[$key]))
            {
                foreach($items as $k => $v)
                {
                    $from_items[$key][$k]['value'] = $args[$key][$k];

                    if(empty($args[$key][$k]) || !preg_match($v['reg'],$args[$key][$k]))
                    {
                        if(empty($args[$key][$k]) && !$v['required']){
                            continue;
                        }
                        else
                        {
                            $submit = false;
                            $from_items[$key][$k]['error'] = 1;
                        }
                    }
                }
            }
        }
        if($args['upload'] == ""){
            $upload = 1;
        }else{
            $upload =      $args['upload'];
        } if($upload == 1){
            if($args['oss']['host'] == ""){
                $submit = false;
                $from_items['oss']['host']['error'] = 1;
            }
            if($args['oss']['access_id'] == ""){
                $submit = false;
                $from_items['oss']['access_id']['error'] = 1;
            }
            if($args['oss']['access_key'] == ""){
                $submit = false;
                $from_items['oss']['access_key']['error'] = 1;
            }
            if($args['oss']['bucket'] == ""){
                $submit = false;
                $from_items['oss']['bucket']['error'] = 1;
            }
            if($args['oss']['url'] == ""){
                $submit = false;
                $from_items['oss']['url']['error'] = 1;
            }
        }
        else if($upload == 2){
        }
        View::share("upload",$upload);
        Session::put('from_app',$from_items);
        if(!$submit)
        {
            View::share("froms",$from_items);
            return $this->display("appconfig");
            exit;
        }
        Session::put('oss',$args['oss']);
        Session::put('url',$args['url']);
        Session::put('sms',$args['sms']);
        Session::put('map',$args['map']);
        Session::put('server',$args['server']);
        Session::put('upload',$upload);
        View::share("site_name", '组装数据');
        return $this->display('install');
    }
    /**
     * 第五步,链接数据库
     */
    public function insert(){
        $status = Input::get('status');
        if($status) {
            if($this->app_configs(base_path()."/config/app.php")){
                $db_config = Session::get('db_config');
                $connect = @mysql_connect($db_config['DB_HOST'] . ":" . $db_config['DB_PORT'], $db_config['DB_USER'], $db_config['DB_PWD']);
                if (mysql_error() == "") {
                    $rs = mysql_select_db($db_config['DB_NAME'], $connect);
                    if (!$rs) {
                        $db_rs = mysql_query("CREATE DATABASE IF NOT EXISTS `" . $db_config['DB_NAME'] . "` DEFAULT CHARACTER SET utf8");
                        if (!$db_rs) {
                            return $this->error('创建数据库失败错误原因:密码或用户不正确');
                        }
                    }
                } else {
                    return $this->error('链接数据库失败错误原因:密码或用户不正确');
                }
                flush();
                ob_flush();
                $db_config_str = "APP_ENV=local\r\nAPP_DEBUG=true\r\nAPP_KEY=pK3MZTLg5KMUmq4oWN8CTYYk5SwV4ZEg\r\n\r\nDB_HOST=" . $db_config['DB_HOST'] . "\r\nDB_DATABASE=" . $db_config['DB_NAME'] . "\r\nDB_USERNAME=" . $db_config['DB_USER'] . "\r\nDB_PASSWORD=" . $db_config['DB_PWD'] . "\r\nDB_PREFIX=" . $db_config['DB_PREFIX'] . "\r\n\r\nCACHE_DRIVER=file\r\nSESSION_DRIVER=file";
                @file_put_contents(base_path() . "/.env", $db_config_str); // FILE_APPEND 追加到字符串
                $datas['status'] = 1;
                return $this->success("开始安装程序",'',$datas);
            }else{
                return $this->error("写入配置文件失败，请检查权限！");
            }
        }else{
            return $this->error("参数错误");
        }
    }
    /**
     * 第六步,创建数据表
     */
    public function runsql() {
        $res = [
            'msg'    => "安装成功",
            'url'    => false,
            'status' => 0,
            'index'  => 0
        ];
        @set_time_limit(3600);
        if(function_exists('ini_set')){
            ini_set('max_execution_time',3600);
        }

        $db_config = Session::get('db_config');
        $index     = Input::get('index') != null ? (int)Input::get('index') : 0;
        $sql_file  = str_replace('\\', '/', base_path()."/install/sql/install_".$index.".sql");
        flush();
        ob_flush();
        if(file_exists($sql_file)) {
            $res['msg']   = "正在执行SQL install_".$index.".sql";
            $result['status'] = true;
            $result =  self::restore($sql_file,$db_config);
            if($result['bln']) {
                return Response::json($result);
            } else{
                $res['msg']    = "执行SQL install_".$index.".sql 发生错误";
                $res['status'] = 1;
                return Response::json($res);
            }
        } else {
            $host = $db_config['DB_HOST'];
            if(!empty($db_config['DB_PORT'])){
                $host = $db_config['DB_HOST'].':'.$db_config['DB_PORT'];
            }
            Vendor("mysql");
            $db = new \mysqldb(array('dbhost'=>$host,'dbuser'=>$db_config['DB_USER'],'dbpwd'=>$db_config['DB_PWD'],'dbname'=>$db_config['DB_NAME'],'dbcharset'=>'utf8','pconnect'=>0));
            $user_config = Session::get('user_config');
            if($user_config['ADM_NAME'] != "admin" || $user_config['123123'] != md5(md5("123123")))
            {
                $crypt 	= String::randString(6);
                $sql   = "UPDATE ".$db_config['DB_PREFIX']."admin_user SET name = '".$user_config['ADM_NAME']."',crypt ='".$crypt."' ,pwd = '".md5(md5($user_config['ADM_PWD']).$crypt)."' WHERE id = 1";
                $db->query($sql);
            }
            @file_put_contents($this->install_lock,"");
            $res['msg'] ="安装成功";
            $res['status'] = true;
            return Response::json($res);
        }
    }
    /**
     * 验证当前操作系统环境下是否支持本系统
     */
    private function checkEnv()
    {
        $rs['status'] = 1;

        $systems[0]['name'] = '操作系统';
        $systems[0]['maxask'] = '不限制';
        $systems[0]['minask'] = '不限制';
        $systems[0]['msg'] = PHP_OS;
        $systems[0]['status'] = 1;

        $systems[1]['name'] = 'PHP 版本';
        $systems[1]['minask'] = '5.5';
        $systems[1]['maxask'] = '5.6+';
        $systems[1]['msg'] = PHP_VERSION;
        $systems[1]['status'] = (substr(PHP_VERSION, 0, 1) < 5) ? 0 : 1;
        $rs['status'] = $systems[1]['status'];

        $systems[2]['name'] = '附件上传';
        $systems[2]['minask'] = '需开启';
        $systems[2]['maxask'] = '需开启';
        $systems[2]['msg'] = @ini_get('file_uploads') ? ini_get('upload_max_filesize') : 'unknow';
        $systems[2]['status'] = @ini_get('file_uploads') ? 1 : 0;
        $rs['status'] = $systems[2]['status'];

        $tmp = function_exists('gd_info') ? gd_info() : array();

        $systems[3]['name'] = 'GD 库';
        $systems[3]['minask'] = '需开启';
        $systems[3]['maxask'] = '需开启';
        $systems[3]['msg'] = empty($tmp['GD Version']) ? 'noext' : $tmp['GD Version'];
        $systems[3]['status'] = empty($tmp['GD Version']) ? 0 : 1;
        $rs['status'] = $systems[3]['status'];
        $mysql = @mysql_get_server_info();
        if($mysql){
            $systems[4]['name'] = 'My Sql 版本';
            $systems[4]['minask'] = '5.0';
            $systems[4]['maxask'] = '5.6+';
            $systems[4]['msg'] = $mysql;
            $systems[4]['status'] = ( $mysql < $systems[4]['minask']) ? 0 : 1;
        }
        $rs['status'] = $systems[1]['status'];
        $rs['systems'] = $systems;
        //本地目录权限验证
        $dirs = Config::get("install.DIRS_CHECK");
        foreach($dirs as $item)
        {
            $item_path = $item['path'];
            $file['name'] = $item_path;
            $file['ask'] = '可读写';

            if($item['type'] == 'dir')
            {
                if(file_exists(base_path().$item_path))
                {
                    if(!dirWriteable(base_path().$item_path))
                    {
                        if(in_array('sql',explode('.',$item_path))|| in_array('bak',explode('.',$item_path))){
                            $file['status'] = 1;
                            $rs['status'] = 1;
                            $file['ask'] = '只读';
                        }else{
                            $rs['status'] = 0;
                            $file['status'] = 0;
                        }
                        $file['msg'] = '只读';
                    }
                    else
                    {
                        $file['status'] = 1;
                        $file['msg'] = '可读写';
                    }
                }
                else
                {
                    $file['status'] = 0;
                    $file['msg'] = '没有目录';
                    $rs['status'] = 0;
                }
            }
            else
            {
                if(file_exists(base_path().$item_path))
                {
                    if(is_writable(base_path().$item_path))
                    {
                        $file['status'] = 1;
                        $file['msg'] = '可读写';
                    }
                    else
                    {
                        if(in_array('sql',explode('.',$item_path))|| in_array('bak',explode('.',$item_path))){
                            $file['status'] = 1;
                            $rs['status'] = 1;
                            $file['ask'] = '只读';
                        }else{
                            $rs['status'] = 0;
                            $file['status'] = 0;
                        }
                        $file['msg'] = '只读';
                    }
                }
                else
                {
                    $file['status'] = 0;
                    $file['ask'] = '没有文件';
                    $rs['status'] = 0;
                }
            }
            $rs['files'][] = $file;
        }

        $funs = Config::get("install.FUNCTiON_CHECK");
        foreach($funs as $fun)
        {
            $item['name'] = $fun;
            $item['ask'] = '支持';

            if(function_exists($fun))
            {
                $item['status'] = 1;
                $item['msg'] = '支持';
            }
            else
            {
                $item['status'] = 0;
                $item['msg'] = "不支持";
                $rs['status'] = 0;
            }
            $rs['funs'][] = $item;
        }

        return $rs;
    }

    /**
     * 执行SQL脚本文件
     *
     * @param array $filelist
     * @return string
     */
    private function restore($file,$db_config)
    {
        @set_time_limit(0);
        @ini_set('memory_limit', '128M');

        $host = $db_config['DB_HOST'];
        if(!empty($db_config['DB_PORT']))
            $host = $db_config['DB_HOST'].':'.$db_config['DB_PORT'];
        Vendor("mysql");
        $db = new \mysqldb(['dbhost'=>$host,'dbuser'=>$db_config['DB_USER'],'dbpwd'=>$db_config['DB_PWD'],'dbname'=>$db_config['DB_NAME'],'dbcharset'=>'utf8','pconnect'=>0]);

        $sql = file_get_contents($file);
        $sql = $this->remove_comment($sql);
        $sql = trim($sql);
        $tables = [];
        $sql = str_replace("\r", '', $sql);
        $segmentSql = explode(";\n", $sql);
        $table = "";
        $msg = '';
        $res =[];
        foreach($segmentSql as $k=>$itemSql) {
            $itemSql = trim(str_replace("%DB_PREFIX%",$db_config['DB_PREFIX'],$itemSql));
            if(strtoupper(substr($itemSql, 0, 12)) == 'CREATE TABLE')
            {
                $table = preg_replace("/CREATE TABLE (?:IF NOT EXISTS |)(?:`|)([a-z0-9_]+)(?:`|).*/is", "\\1", $itemSql);

                if(!in_array($table,$tables)){
                    $tables[] = $table;
                }

                if($db->query($itemSql) === false) {
                    $res['bln'] = false;
                    $res['msg'] .= "建立数据表 ".$table." 失败,";
                    $res['status'] = -1;
                    break;
                } else {
                    $res['bln'] = true;
                    $res['status'] = ture;
                    $msg .= "建立数据表 ".$table." 成功,";
                    $res['msg'] = $msg;// "建立数据表 ".$table." 成功<br>";
                }
            } else {
                if($db->query($itemSql) === false) {
                    $res['bln'] = false;
                    $res['msg'] .= "添加数据表 ".$table." 数据失败";
                    $res['status'] = -1;
                    break;
                }
            }
        }
        return $res;
    }



    /**
     * 过滤SQL查询串中的注释。该方法只过滤SQL文件中独占一行或一块的那些注释。
     *
     * @access  public
     * @param   string      $sql        SQL查询串
     * @return  string      返回已过滤掉注释的SQL查询串。
     */
    private function remove_comment($sql)
    {
        /* 删除SQL行注释，行注释不匹配换行符 */
        $sql = preg_replace('/^\s*(?:--|#).*/m', '', $sql);

        /* 删除SQL块注释，匹配换行符，且为非贪婪匹配 */
        //$sql = preg_replace('/^\s*\/\*(?:.|\n)*\*\//m', '', $sql);
        $sql = preg_replace('/^\s*\/\*.*?\*\//ms', '', $sql);

        return $sql;
    }

    public function clear_cache() {
        Session::flush();
        Cache::flush();
        File::deleteDirectory(storage_path('framework/cache'), true);
        File::deleteDirectory(storage_path('framework/sessions'), true);
        File::deleteDirectory(storage_path('framework/views'), true);
    }

    public function app_configs() {
        $host = explode('.', Request::server('HTTP_HOST'));
        array_shift($host);
        $url   = implode('.', $host);
        $oss = Session::get('oss');
        $sms = Session::get('sms');
        $upload = Session::get('upload');
        $app_config  = 	"<?php\r\n";
        $app_config .=	"return [\r\n";
        $app_config .=" /**
    * 运营版本
    * common       通用版
    * personal     个人加盟版
    * organization 机构加盟版
    * oneself		自营版
    */
   'operation_version' => 'common',

   'tpl_version' => '1.0',//模板版本

    'sys_version' => 'v2.0',//系统版本
    
    'is_open_property' => false,//是否开通物业

    'seller_type_is_all' => true,//是否是商家经营类型多选
	
	'is_local_request' => true,//是否本地请求
	
	

    'oneself_seller_id' => 1,
	
	'is_open_fx' => false,//分销主开关     dsy
	
	'is_wx_version' => false,//是否是微信版本 zxs

    /**
     * 是否使用方维分销
     */
    'fanwefx_system' => false, //true:调用方维分销平台 false:调用本地分销平台
   /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */


    'url' => 'http://wap.".$url."',
    'domain' => '".$url."',

    'callback_url' => 'http://callback.".$url."/',

	'lock_sdk_api' => [
		'install_lock_url_test' => 'http://121.40.204.191:8180/mdserver/service/installLock',
		'install_lock_url' => 'http://121.40.204.191:8180/mdserver/service/installLock',
		'qry_keys_url_test' => 'http://121.40.204.191:8180/mdserver/service/qryKeys',
		'qry_keys_url' => 'http://121.40.204.191:8180/mdserver/service/qryKeys',
		'get_shequ_url' => 'http://121.40.204.191:8180/mdserver/service/getCommunity',
		'add_shequ_url' => 'http://121.40.204.191:8180/mdserver/service/addCommunity',
		'app_key' => '4bb16b42b9c0354682f6eb4943abbe9a',//服务端认证key
		'agt_num' => '10043',//服务端认证编号
		// 'departid' => '10043001',//安装组织机构编号
	],

    'api_url' => [
        'buyer' => 'http://api.".$url."/buyer/v1/',
        'system' => 'http://api.".$url."/system/v1/',
        'seller' => 'http://api.".$url."/sellerweb/v1/',
        'staff' => 'http://api.".$url."/staff/v1/',
    ],";
    $app_config .=    "\r\n
    /**
     * 短信默认配置
     */
    'sms' => [\r\n          'url'=> 'http://sms.fanwe.com/post',\r\n";
     $app_config .=$this->getConfig($sms);
     $app_config .="        ],

    'security_ips' => [
        '127.0.0.1'
    ],\r\n\r\n";
        if($upload == 1){
            $typs = "Oss";
        }else{
            $typs = "Server";
        }
    $app_config .=     "    'image_type' => '".$typs."',
    'image_config' => [\r\n";
        $app_config .=  "
           'oss' => [
                'host' 			=> 'oss-cn-".$oss['host'].".aliyuncs.com',
                'access_id'		=> '".$oss['access_id']."',
                'access_key'	=> '".$oss['access_key']."',
                'bucket' 		=> '".$oss['bucket']."',
                'url' 			=> '".$oss['url']."',
                'callback'		=> 'http://admin.".$url."/Resource/callback',
            ],";
        $app_config .=  "
        'server' =>[
                'upload_url'	=> 'http://resource.".$url."/image/upload', //上传地址
                'remove_url'	=> 'http://resource.".$url."/image/remove', //删除地址
                'token'			=> 'yn2CisXgPjf8',//授权TOKEN
                'url'			=> 'http://resource.".$url."/upload/',//图片访问路径
                'max_size'		=> '5242880', //最大图片上传 5 M
                'save_path'		=> 'public/upload/',//图片保存路径\r\n";
        $app_config .="         ],\r\n        ],\r\n";
        $app_config .="     'image_url'	=> '".$url."',\r\n";
        $app_config .="     'qq_map' => [
            'key' => '2N2BZ-KKZA4-ZG4UB-XAOJU-HX2ZE-HYB4O',
            'address' => '北京市',
            'point' => '39.916527,116.397128',
            ],\r\n";
        $app = @file_get_contents(base_path()."/install/app_config.bak");
        $app_config .= $app."\r\n\r\n";
        $app_config .= "    ];\r\n";

        @file_put_contents(base_path()."/config/app.php",$app_config);
        return true;
    }
    public function getConfig($configs,$url=''){
        if($url != ""){
            $url = 'resource.'.$url.'/';
        }else{
            $url = '';
        }
        $config = '';
        foreach($configs as $key=>$v) {
            $config .="             '".$key."'=>'".$url.$v."',\r\n";
        }
        return $config;
    }
}
?>