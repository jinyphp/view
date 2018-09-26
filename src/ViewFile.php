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

/**
 * jiny
 * 뷰파일을 읽어 처리를 합니다.
 */
class ViewFile  
{
   
    //protected $_filepath;

    public $_pageType;
 
    // protected $_tempFile;
    public $_path;

    // 실제 뷰파일이 존재하는 디렉토리
    protected $_viewDir;

    public $Cache;
    public $View;

    /**
     * 
     */
    public function __construct($view)
    {
        $this->View = $view;

        // 뷰 리소스의 경로를 초기화 합니다.
        $this->getPath();

        // 캐쉬 인스턴스 생성
        $this->Cache = new \Jiny\View\Cache;

    }

    
    /**
     * 뷰의 리소스 경로를 확인합니다.
     */
    public function getPath()
    {
        if ($this->_path = conf("site.view_path")) {
            // site 설정의 값을 우선 처리합니다.
        } else {
            // 기본값는 환경변수 설정값 입니다.
            $this->_path = conf("ENV.path.pages");
        }

        // 경로를 운영체제에 맞게 수정합니다.
        $this->_path = ROOT.str_replace("/", DIRECTORY_SEPARATOR, $this->_path);
    }


    /**
     * 뷰의 리소스 경로를 설정합니다.
     */
    public function setPath($path)
    {
        // 경로를 운영체제에 맞게 수정합니다.
        $this->_path = ROOT.str_replace("/", DIRECTORY_SEPARATOR, $path);
    }

    
    /**
     * 뷰(view) 파일을 읽어옵니다.
     */
    public function read($viewName)
    {   
        // 리소스 경로를 확인합니다.
        $path = $this->_path;

        // 파일명을 확인합니다.
        $filename = $this->fileCheck($path, $viewName);
        if ($filename) {

            // 템플릿 캐쉬가 활성화 된경우, 캐쉬파일을 확인합니다.
            if ($this->Cache->is()) {
                // 원본 파일이 수정되었는지 확인
                if($this->Cache->isFileUpdate($filename)){
                    $filename = $this->Cache->tempPath();          
                }
            }
             
            return $this->getFile($filename);

        } else {
            //echo "404: 페이지 파일이 없습니다.";
            //exit;
            return NULL;        
        }
    }


    /**
     * URL에 대한 파일명을 확인합니다.
     */
    public function fileCheck($path, $viewFile)
    {
        // Indexs
        // 우선순위 설정으로 반복 검색을 합니다.
        $indexs = conf("ENV.Resource.Indexs");     
    
        // 입력한 URL에 해당하는 index 파일이 있는지를 검사합니다.
        // index 파일은 모든 조건에서 우선 처리됩니다.
        if ($name = $this->isIndex($path.$viewFile, $indexs)) {
            //echo "인덱스 이름을 찾습니다...<br>";
            //$this->_viewDir = $path;       
            $this->_viewDir = $path.$viewFile; 
            return $name;
        }
        // index 파일이 없는 경우, 폴더명을 파일로 변환처리하여 이름을 찾습니다.
        else {
            
            // Indexs를 이용하여 확장자 우선순위를
            // 배열로 생성합니다.
            $exts = $this->getExts($indexs); 

            // 기본 패스서 부터 파일명 찾기
            $filepath = $path.DS;   
            //echo  $filepath;
            $dd = \explode(DS, \trim($viewFile, DS) );  
            foreach ($dd as $value) {
                $filepath .= $value;
                //echo  $filepath."<br>";

                if(is_dir($filepath)){
                    // 디렉토리인 경우 다음 경로를 찾습니다.
                    $filepath .= DS;
                    $this->_viewDir = $filepath;
                    continue;
                } else {
                    // 디렉토리 구분자를 삽입합니다.
                    $filepath .= "_";
                }
            }

            // loop로 생성된 마지막 구분자를 제거해 줍니다.
            $filepath = \rtrim($filepath, "_");
            $filepath = \rtrim($filepath, DS);

            // 디렉토리명과 매칭된 파일이 있는 경우
            // 해당 파일로 정의합니다.
            if ($name = $this->isExt($filepath, $exts)) return $name;
    
            return $name;
        }
    
        return NULL;
    }


    /**
     * Index 파일이 있는지를 검사합니다.
     * 지정된 경로에 index 순서를 검사합니다.
     */
    public function isIndex($path, $indexs = [])
    {
        $path = rtrim($path,DS).DS;
        foreach ($indexs as $name) {

            if (file_exists($path.$name)) {
                $key = \explode(".", $name);
                if(isset($key[1])) $this->_pageType = $key[1];
                return $path.$name;
            }
        }
        return NULL;
    }


    /**
     * 확장자가 있는지 검사합니다.
     */
    public function isExt($filepath, $exts)
    {
        foreach ($exts as $ext) {
            if (file_exists($filepath.".".$ext)) {
                $this->_pageType = $ext;   
                return $filepath.".".$ext;
            } 
        }
        return NULL;
    }

    
    /**
     * 확장자를 분리합니다.
     */
    public function getExts($indexs)
    {
        $exts=[];
        foreach ($indexs as $name) {
            $key = \explode(".", $name);
            if (isset($key[1])) array_push($exts, $key[1]);
        }
        return $exts;
    }


    /**
     * 파일을 읽어 옵니다.
     */
    public function getFile($name)
    {
        $language = language();
        if ($this->_pageType == "docx") {
            // 읽어올 문서 형식이 MS-Word일때
            $obj = new \Jiny\View\Drivers\docx($language);
            return $obj->read($name);
        } else if ($this->_pageType == "md") {
            // 읽어올 문서 형식이 markdown일때
            $obj = new \Jiny\View\Drivers\md($language);
            return $obj->read($name);
        } else {
            // 읽어올 문서 형식이 htm일때
            $obj = new \Jiny\View\Drivers\htm($language);
            return $obj->read($name);   
        }

        
    }


    /**
     * 이미지를 복사합니다.
     * public/_
     */
    public function imagesCopy($body)
    {
        //캐쉬저장 폴더
        $baseDir = "/public";
        $tempDir = "_";
        $resourcePath = $this->_viewDir;
        
        // 문서의 돔을 분석합니다.
        $dom = HtmlDomParser::str_get_html( $body );
        if($arr = $dom->find('img')){

            // URL에 맞는 폴더를 생성합니다.
            $urlpath = $this->App->Boot->urlString();            
            $urlpath = \Jiny\Core\Base\File::osPath($urlpath);

            $dir = \Jiny\Core\Base\File::osPath("/public".DS.$tempDir.$urlpath);    
            if(!is_dir($dir)){
                // 디렉토리를 생성합니다.
                \Jiny\Core\Base\File::mkdir($dir);
                if(!is_dir($dir)){
                    //echo "이미지 복사 디렉토리를 생성할 수 없습니다.";
                }
            }
            
            // 돔의 이미지의 갯수많큼 복사합니다.
            foreach($arr as $element){

                if ($element->src[0] == "/") {
                    // 정대경로
                    $src = ROOT.$element->src;
                    
                } else if ($element->src[0] == ".") {
                    //상대경로
                    $src = \Jiny\Core\Base\Path::append($resourcePath, $element->src);

                } else {
                    // 그외 경로는 스킵합니다.
                    continue;
                }


                if (\file_exists($src)) {
                    $info = pathinfo($element->src);
                    $dst = \Jiny\Core\Base\Path::append($dir, $element->src);
                    if (\Jiny\Core\Base\File::copy($src, ROOT.$dst)) {
                        //echo $src."==>".ROOT.$dst."이미지를 복사합니다.<br>";
                    }
                  
                    // 문서의 위치 경로를 변경합니다.
                    if(ROOT == ".."){
                        $imgSrc = str_replace("\public","", $dst); 
                        $body = str_replace($element->src, $imgSrc, $body);
                    } else {
                        $body = str_replace($element->src, $dst, $body); 
                    }

                }
                //                             
                  
            }           

        }  else {
            // 문서에 포함된 이미지가 없습니다.
        }

        unset($dom);
        return $body;
    }


    /**
     * 
     */
}