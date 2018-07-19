<?php

namespace Jiny\View;

use \Jiny\Core\Registry\Registry;
use Sunra\PhpSimple\HtmlDomParser;
use Symfony\Component\Filesystem\Filesystem;

/**
 * jiny
 * 뷰파일을 읽어 처리를 합니다.
 */
class ViewFile extends AbstractView 
{
   
    protected $_filepath;

    protected $_pageType;
    protected $DOCX;

    protected $_tempFile;

    // 실제 뷰파일이 존재하는 디렉토리
    protected $_viewDir;

    protected $_isUpdate = false;

    
    
    /**
     * 뷰(view) 파일을 읽어옵니다.
     */
    public function loadViewFile($viewName)
    {     
        // 리소스 경로를 확인합니다.
        $path = ROOT.str_replace("/", DIRECTORY_SEPARATOR, conf("ENV.path.pages"));
        
        // 파일명을 확인합니다.
        $filename = $this->fileCheck($path, $viewName);
        //echo "파일명 = ".$filename;
        if ($filename) {

            // 템플릿 캐쉬가 활성화 된경우, 캐쉬파일을 확인합니다.
            if (conf("ENV.Tamplate.Cache")) {
                // 원본 파일이 수정되었는지 확인
                if($this->isFileUpdate($filename)){
                    $filename = $this->tempPath();          
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
     * 뷰파일을 읽어올 경로를 확인합니다.
     */
    /*
    public $_path = NULL;

    public function path()
    {
        // 환경설정 경로를 확인합니다.
        if($this->_path){
            return $this->_path;
        } else {
            // directory 구분자를 변경처리 합니다.
            $path = str_replace("/", DIRECTORY_SEPARATOR, conf("ENV.path.pages"));

            // 시작 위치의 포인터를 설정합니다.
            // 프로퍼티에 저장을 합니다.
            $this->_filepath = ROOT.$path;

            return $this->_filepath;
        }
        
    }
    */

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
        //echo "인덱스 = ".$path.$viewFile."<br>";
        if ($name = $this->isIndex($path.$viewFile, $indexs)) {
            //echo "인덱스 이름을 찾습니다...<br>";
            $this->_viewDir = $path;       
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
            //echo  $filepath."<br>";

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
        //echo "인덱스 파일 확인합니다...<br>";
        $path = rtrim($path,DS).DS;
        //echo $path."<br>";
        foreach ($indexs as $name) {
            //echo $path.$name."<br>";
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
     * Indexs 순서에 맞에 파일을 읽어옵니다.
     * .env.php 설정을 참고합니다.
     */
    public function viewFile($path)
    {
        // \TimeLog::set(__METHOD__);
        /*
        $indexs = $this->Config->data("ENV.Resource.Indexs");
        foreach ($indexs as $name) {
            if (file_exists($path.$name)) {
                $arr = \explode(".",$name);

                $this->_pageType = isset($arr[1])? $arr[1]: NULL;

                if ($this->isFileUpdate($path.$name)) {
                    //echo "원본처리<br>";
                    if ($this->_pageType == "docx") {
                        $body = $this->getDocx($path.$name);
                    } else {
                        $body = $this->getFile($path.$name);
                    }

                    $this->tempFile($path.$name, $body);
                    return $body;

                } else {
                    //echo "캐쉬로 대체합니다.<br>";
                    return $this->getFile($path.$name.".tmp");
                }
                
            }
        }
        */
    }

    /**
     * 파일 갱신여부 체크
     */
    public function isFileUpdate($name)
    {
        // echo "캐쉬를 확인합니다.<br>";
        // echo $name."<br>";
        $origin = filemtime($name);
        // echo $origin."\n";
        $name = $this->tempPath();
        if (file_exists($name)) {            
            $temp = filemtime($name);
            //echo "케쉬=".$temp."<br>";

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
     * 워드 문서를 읽어 옵니다.
     */
    public function getDocx($name)
    {
        $this->DOCX = new \Docx_reader\Docx_reader();
        $this->DOCX->setFile($name);

        if(!$this->DOCX->get_errors()) {
            $html = $this->DOCX->to_html();
            $plain_text = $this->DOCX->to_plain_text();

            unset($this->DOCX);
            return $html;
        } else {
            // echo implode(', ',$doc->get_errors());
        }
    }

    /**
     * 파일을 읽어 옵니다.
     */
    public function getFile($name)
    {
        if ($this->_pageType == "docx") {
            $body = $this->getDocx($name);
        } else {
            $body = file_get_contents($name);;
        }
        return $body;
    }

    /**
     * 이미지를 복사합니다.
     */
    public function imagesCopy()
    {
        //
        $dom = HtmlDomParser::str_get_html( $this->_body );
        if($arr = $dom->find('img')){

            $tempDir = "_";
            $urlpath = $this->App->Boot->urlString();            
            $urlpath = \Jiny\Core\Base\File::osPath($urlpath);
            echo "urlpath=".$urlpath."<br>";

            $dir = \Jiny\Core\Base\File::osPath("/public".DS.$tempDir.$urlpath);    
            \Jiny\Core\Base\File::mkdir($dir);
            
            foreach($arr as $element){
                echo $element->src."<br>";

                if ($element->src[0] == "/") {
                    // 정대경로
                    $src = ROOT.$element->src;

                } else if ($element->src[0] == ".") {
                    //상대경로
                    $src = $this->_viewDir.$urlpath.DS.ltrim($element->src,"./");

                } else {
                    // 그외 경로는 스킵합니다.
                    continue;
                }

                $src = \Jiny\Core\Base\File::osPath($src);                
                $info = pathinfo($element->src);
    
                \Jiny\Core\Base\File::copy($src, ROOT.$dir.DS.$info['basename']);
                //echo $src."==>".ROOT.$dir.DS.$info['basename']."이미지를 복사합니다.<br>";

                if(ROOT == ".."){
                    $imgSrc = str_replace("\public","",$dir.DS.$info['basename']); 
                    $this->_body = str_replace($element->src, $imgSrc, $this->_body);
                } else {
                    $this->_body = str_replace($element->src, $dir.DS.$info['basename'], $this->_body); 
                }
                  
            }           

        }
    }

    /**
     * 임시파일 경로
     */
    public function tempPath()
    {
        $tempDir = "_";
        $urlpath = $this->App->Boot->urlString();
        $dir = ROOT."/public".DS.$tempDir .$urlpath;
      
        \Jiny\Core\Base\File::mkdir($dir);

        $filename = $dir.DS."temp.htm";        
        return \Jiny\Core\Base\File::osPath($filename);       
    }

    /**
     * 
     */
}