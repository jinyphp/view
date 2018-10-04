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
use Liquid\Template;
use Symfony\Component\Filesystem\Filesystem;

/**
 * 뷰를 처리합니다.
 */
class View extends \Jiny\View\Process
{
    public $App;

    // 인스턴스
    private $Controller;    
    public $FileSystem;
    public $File;
    public $FrontMatter;
    public $Template;

    // 컨덴츠
    public $view_file;
    public $_body;
    public $_data=[];


    /**
     * 뷰 클래스를 초기화 합니다.
     */
    public function __construct()
    {
        $this->Controller = Registry::get("controller");
        $this->App = Registry::get("App");
        
        $this->FileSystem = new Filesystem();

        $this->File = new ViewFile($this);

        $this->FrontMatter = new FrontMatter;
    }


    /**
     * >> 템플릿 메소드 패턴
     * View HTML파일을 로드합니다.
     * 읽어온 내용은 viewHtml에 저장을 합니다.
     * HTMLS 경로디렉토리
     */
    public function create($viewName, $data=[])
    {   
        $viewHtml = new ViewHtml($this->_body, $this->_data); 

        // 리소스 뷰파일을 읽어 옵니다.
        if ($body = $this->File->read($viewName)) {

            // 문서에서 머리말을 분리합니다.    
            $f = $this->FrontMatter->parser($body);
            $this->_body = $f['content'];
            $viewHtml->setBody($f['content']);

            $viewHtml->appendViewData("page", $f['data']);

            // 문서를 변환합니다.
            // 문서 타입
            $this->convert($this->File->_pageType, $viewHtml);

            
            // 컨트롤러에서 넘어온 content 결합
            /*
            if ( isset($this->_data['datas']['content']) ) {
                $this->_body = str_replace("{{ content }}",  $this->_data['datas']['content'], $this->_body);
            }
            */


            // 레이아웃 결합
            // 머리말 layout 설정값에 따라서 재귀적으로 결합합니다.
            $this->extendLayout($viewHtml);           


            // 케쉬 작업을 체크합니다.          
            if(!$this->File->Cache->isUpdate()){

                // 이미지복사.           
                $viewHtml->_body = $this->File->imagesCopy($viewHtml->_body);

                // 케쉬를 생성 저장합니다.
                if (isset($this->_data['page'])) {
                    $filename = $this->File->Cache->tempPath();
                    $yaml = \Jiny\Config\Yaml\Yaml::dump($this->_data['page']);
                    file_put_contents($filename, "---\n".$yaml."\n---\n".$viewHtml->_body);
                }
            }

            return $viewHtml;

        } else {
            // 페이지를 읽을 수 없습니다.        
            return NULL;
        }         
    }


    /**
     * 레이아웃을 확장합니다.
     * yaml layout 설정
     */
    public function extendLayout($html)
    {
        // 레아아웃 설정이 있는지를 확인합니다.
        if ($layout = $html->isLayout()) {

            // 테마에서 레이아웃을 읽어 옵니다.
            if ($body = $this->viewLayout($layout)) {
                
                $html->clearLayout(); // 재귀호출 방지를 위해서 삭제합니다.

                // 레이아웃의 머리말을 분리합니다.
                $doc = $this->FrontMatter->parser($body);
                $html->_body = str_replace("{{ content }}", $html->_body, $doc['content']);

                $html->appendViewData("page", $doc['data']);                

                // 재확장 여부 검사.
                if ($html->isLayout()) $this->extendLayout($html);
                
            }
        }

    }


    /**
     * 뷰 Extends 레이아웃
     * 환경설정에 따라 값을 읽어 옵니다.
     */
    public function viewLayout($name)
    {
        $layout = conf("ENV.path.layout");
        $path = ROOT.DS.$layout.DS;
        $filename = $name.".htm";
        if (file_exists($path.$filename)) {
            return file_get_contents($path.$filename);
        }
        return NULL;
    }


    /**
     * >> 상태패턴 적용
     * 문서를 변환합니다.
     * markdown, word 파일일때 변환처리르 합니다.
     */
    public function convert($type, $viewHtml)
    {
        switch ($type) {
            case 'htm':
                //echo "html 파일을 출력합니다.<br>";             
                break;

            case 'md':
                //echo "md 파일을 출력합니다.<br>";
                // 내용을 마크다운 -> html로 변환합니다.
                $this->markDown($viewHtml);
                break;

            case 'docx':
                // 워드 문서는 파일을 읽을때 자동 변경됩니다.
                break;    
        }

        return $this;
    }


    // _Body 마크다운 변환을 처리합니다.
    public function markDown($viewHtml)
    {
        // 마크다운 변환
        // 컴포저 패키지 참고
        $Parsedown = new \Parsedown();
        $viewHtml->_body = $Parsedown->text($viewHtml->_body);
        return $this;
    }


    /**
     * >> 체인패턴
     * 뷰 화면을 출력합니다.
     * 데이터를 처리합니다.
     */
    public function show($viewHtml)
    {
        // 체인 의존 패턴 적용
        $Chain = [
            new \Jiny\View\Renders\RenderTheme,
            new \Jiny\View\Renders\RenderTemplate,
            new \Jiny\View\Renders\RenderCss,
            new \Jiny\View\Renders\RenderJavascript
        ];

        // 체인결합
        for ($i=count($Chain)-1; $i>0; $i--) {
            $Chain[$i-1]->setNext($Chain[$i]);            
        }

        // 첫번째 체인 실행
        $Chain[0]->process($viewHtml);

        // Asset 경로를 변경합니다.
        $viewHtml->replace("=\"/assets", "=\"".ROOT_PUBLIC."/assets");

        return $viewHtml->_body;
    }

    
    /**
     * 뷰 파일을 설정합니다.
     */
    public function setFile($file)
    {
        $this->view_file = $file;
    }

    
    /**
     * 뷰로 전달되는 데이터를 초기화 합니다.
     */
    public function setData($data)
    {   
        if(isset($data)) {
            $this->_data = $data;
        }

        // 설정파일을 뷰데이터에 결합합니다.
        $cfgData = conf();
        foreach ($cfgData as $key => $value) {
            $this->_data[$key] = $value;
        }

        // 현재 url
        if ($this->_data['url'] = urlString()) {
        } else {
            $this->_data['url'] = "/";
        }
    }

    /**
     * 
     */
}