<?php

namespace Jiny\View;

use \Jiny\Core\Registry\Registry;

/**
 * 뷰의 페이지를 처리합니다.
 */
trait ViewCreate
{
    public $Template;
    /**
     * 머리말 처리
     */
    use FrontMatter;
    use MarkDown;
    use Prefix;

    public $_body;
    public $_data=[];

    /**
     * View HTML파일을 로드합니다.
     * 읽어온 내용은 _body에 저장을 합니다.
     * HTMLS 경로디렉토리
     */
    public function create($viewName, $data=[])
    {
    
        //echo $viewName."<br>";
        
        // 리소스 뷰파일을 읽어 옵니다.
        if ($body = $this->loadViewFile($viewName)) {

            // 머리말을 체크합니다.
            // 문서에서 머리말을 분리합니다.     
            $this->_body = $this->frontMatter($body);

            // 문서를 변환합니다.
            // 문서 타입
            $this->convert($this->_pageType);

            // 컨트롤러에서 넘어온 content 결합
            if ( isset($this->_data['datas']['content']) )
            $this->_body = str_replace("{{ content }}",  $this->_data['datas']['content'], $this->_body);
           
            

            // 레이아웃 결합
            // 머리말 layout 설정값에 따라서 재귀적으로 결합합니다.
            $this->extendLayout();

           
            

            // 케쉬 작업을 체크합니다.
            if(!$this->_isUpdate){            
                // 이미지복사.           
                $this->imagesCopy();

                // 케쉬를 생성 저장합니다.
                if (isset($this->_data['page'])) {
                    $filename = $this->tempPath();
                    $yaml = \Jiny\Config\Yaml\Yaml::dump($this->_data['page']);
                    file_put_contents($filename, "---\n".$yaml."\n---\n".$this->_body);
                }
            }

            return $this;

        } else {
            // 페이지를 읽을 수 없습니다.
            // NULL
            
            return NULL;
        }         
    }

    /**
     * 레이아웃을 확장합니다.
     * yamal layout 설정
     */
    public function extendLayout()
    {
        // 레아아웃 설정이 있는지를 확인합니다.
        if (isset($this->_data['page']['layout'])) {

            // 테마에서 레이아웃을 읽어 옵니다.
            $body = $this->viewLayout( $this->_data['page']['layout'] );
            if ($body) {
                // 재귀호출 방지를 위해서 삭제합니다.
                unset($this->_data['page']['layout']);

                // 레이아웃의 머리말을 분리합니다.
                $layout = $this->frontMatter($body);
                $this->_body = str_replace("{{ content }}", $this->_body, $layout);

                // 재확장 여부 검사.
                if (isset($this->_data['page']['layout'])) {
                    $this->extendLayout();
                }
                
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
     * 문서를 변환합니다.
     * markdown, word 파일일때 변환처리르 합니다.
     */
    public function convert($type)
    {
        // \TimeLog::set(__METHOD__);
        switch ($type) {
            case 'htm':
                //echo "html 파일을 출력합니다.<br>";             
                break;

            case 'md':
                //echo "md 파일을 출력합니다.<br>";
                // 내용을 마크다운 -> html로 변환합니다.
                $this->markDown();
                break;

            case 'docx':
                // 워드 문서는 파일을 읽을때 자동 변경됩니다.
                break;    
        }

        return $this;
    }

    /**
     * 
     */
}