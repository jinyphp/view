<?php
/*
 * This file is part of the jinyPHP package.
 *
 * (c) hojinlee <infohojin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Jiny\View;

class Resource
{
    private $path = "";
    public function setPath($path)
    {
        $this->path = $path;
    }

    private $resourceExist;
    public function init($resource="Resource", $view="View")
    {
        if (!is_dir($resource)) {
            mkdir($resource);
        }

        if(!is_dir($resource.DIRECTORY_SEPARATOR.$view)) {
            mkdir($resource.DIRECTORY_SEPARATOR.$view);
        }
        
        $this->resourceExist = true;
        return $this;
    }

    public function get($name)
    {
        $filename = $this->path.$name;
        $filename = str_replace(["\\","/"],DIRECTORY_SEPARATOR, $filename);
        if (file_exists($filename)) {
            return file_get_contents($filename); 
        } else {
            echo "리소스 파일을 읽을 수 없습니다.";
            echo __CLASS__;
            echo __LINE__;
            exit;
        }
    }
    
    /**
     * 싱글턴 인스턴스를 생성합니다.
     */
    private static $instance;
    public static function instance()
    {
        if (!isset(self::$instance)) {
            // 인스턴스 생성                    
            self::$instance = new self();
            return self::$instance;

        } else {
            // 인스턴스가 중복
            return self::$instance; 
        }
    }

    private function __construct()
    {
        // 생성자 제한
    }

    private function __clone()
    {
        // 복제 제한
    }
}