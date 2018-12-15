<?php
namespace YiZan\Utils\Image;

use YiZan\Utils\Time;
use Config, Request,Helper,File;

class ServerImage{
    public function __construct() {
        if (!defined('SERVER_IMAGE_UPLOAD_URL')) {
            define('SERVER_IMAGE_UPLOAD_URL', Config::get('app.image_config.server.upload_url'));
            define('SERVER_IMAGE_TOKEN', Config::get('app.image_config.server.token'));
            define('SERVER_IMAGE_URL', Config::get('app.image_config.server.url'));
            define('SERVER_IMAGE_SAVE_PATH', Config::get('app.image_config.server.save_path'));
        }
    }

    public function getFormArgs($path,$type='') {
        $data = [
            'save_path' => [
                'name'  => 'key',
                'path'  => SERVER_IMAGE_SAVE_PATH.$path,
            ],
            'file_name' => 'file',
            'action'    => SERVER_IMAGE_UPLOAD_URL,
            'wap_action'    => SERVER_IMAGE_UPLOAD_URL,
            'image_url' => SERVER_IMAGE_URL.$path
        ];

        $token = md5(http_build_query($data).'&'.SERVER_IMAGE_TOKEN.'&'.Request::ip());

        $data['args'] = [
            'token' => $token,
            'success_action_redirect' => u('Resource/callback'),
            'type' => $type
        ];
        return $data;
    }

    public function upload($data, $path , $type = '') {

        if($type === 1){
            $image_url = $path;
            $save_path = base_path().'/'.SERVER_IMAGE_SAVE_PATH.str_replace("http://resource.".Config::get('app.domain')."/upload", '', $path);
        }else{
            $image_url = SERVER_IMAGE_URL.str_replace(SERVER_IMAGE_SAVE_PATH, '', $path);
            $save_path = base_path().'/'.SERVER_IMAGE_SAVE_PATH.'/'.$path;
        }
        $dirs = pathinfo($save_path);
        File::makeDirectory($dirs['dirname'], 0777, true);

        try {
            $this->_removeFiles($image_url);
            $saved = @file_put_contents($save_path, $data);
            return $image_url;
        } catch (Exception $e) {
            return false;
        }
    }

    private function _removeFiles($files) {
        if (!is_array($files)) {
            $files = array($files);
        }
        foreach ($files as $file) {
            if (strpos($file, SERVER_IMAGE_URL) === 0) {
                $file = str_replace(SERVER_IMAGE_URL , base_path().'/'.SERVER_IMAGE_SAVE_PATH, $file);
                $paths = pathinfo($file);

                $path = $paths['dirname'];
                $dir = dir($path);
                while (false !== ($entry = $dir->read())) {
                    if ($entry != '.' && $entry != '..') {
                        if(is_file($path.'/'.$entry)) {
                            if (strpos($entry, $paths['basename']) === 0) {
                                @unlink($path.'/'.$entry);
                            }
                        }
                    }
                }
            }
        }
    }

    public function remove($paths) {
        return true;
        if (!is_array($paths)) {
            $paths = array($paths);
        }
        foreach ($paths as $key => $path) {
            $url = parse_url($path);
            $paths[$key] = $url['path'];
        }
    }

    public function move($formPath, $dir) {
        return $formPath;
    }
}