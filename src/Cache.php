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

use \Jiny\Core\Registry\Registry;
use Sunra\PhpSimple\HtmlDomParser;
use Symfony\Component\Filesystem\Filesystem;

class Cache
{
    public $_isUpdate = false;

    const TEMP = "_";

    public function __construct()
    {
        // 캐시 초기화
    }

    /**
     * 캐쉬 활성화를 체크합니다.
     */
    public function is()
    {
        return conf("ENV.Tamplate.Cache");
    }

    /**
     * 갱신여부를 확인합니다.
     */
    public function isUpdate()
    {
        return $this->_isUpdate;
    }

    /**
     * 파일 갱신여부 체크
     */
    public function isFileUpdate($name)
    {
        $origin = filemtime($name);

        $name = $this->tempPath();
        if (file_exists($name)) {            
            $temp = filemtime($name);
            if( $temp > $origin ) {
                // echo "최신";
                $this->_isUpdate = true;
                
            } else {
                // echo "원본갱신";
                $this->_isUpdate = false;
               
            }
        } else {
            $this->_isUpdate = false;           
        }
      

        return $this->_isUpdate;
    }

    /**
     * 임시파일 경로
     */
    public function tempPath()
    {
        $tempDir = "_";
        $temFile = "temp.htm";

        $urlpath = urlString();
        $dir = ROOT."/public".DS. $tempDir. $urlpath;

        // 캐쉬 디렉토리 생성
        \Jiny\Core\Base\File::mkdir($dir);
     
        return \Jiny\Core\Base\File::osPath($dir.DS.$temFile);       
    }

    /**
     * 
     */
}