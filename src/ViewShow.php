<?php

namespace Jiny\View;

use \Jiny\Core\Registry\Registry;
//use Sunra\PhpSimple\HtmlDomParser;
//use Symfony\Component\Filesystem\Filesystem;

/**
 * 뷰의 페이지를 처리합니다.
 */

trait ViewShow
{

    protected $Theme;

    /**
     * 뷰 화면을 출력합니다.
     * 데이터를 처리합니다.
     */
    public function show($viewHtml)
    {

        // 테마처리
        if($theme = conf("site.theme")) {
            if (Registry::get("Packages")->isPackage("jiny/theme")) {

                // 테마 의존성 주입(view)
                // 뷰 객체가 생성되기 이전이기 때문에, 테마에 $this로 주입을 합니다.
                $this->Theme = Registry::create(\Jiny\Theme\Theme::class, "Theme", $this);
                
                if ($this->Theme->isTheme()) {
                    $this->Theme->render($viewHtml);        
                }
            }
        } else {
            // echo "테마가 지정되지 않았습니다.";
        }
        


        // 템플릿 인스턴스를 레지스트리에 추가합니다.       
        $this->Template = Registry::create(\Jiny\Template\Template::class, "Template", $this);
        if ($this->Template) {
            // 템플릿을 엔진처리
            $this->Template->process($viewHtml); 
        }


        // 머리말 커스텀 css 파일이 있는 경우
        if ($this->is_css($viewHtml)) {
            $viewHtml->appendHead( $this->css($viewHtml->_data['page']['css']) );
        }

        // 머리말 커스텀 javascript 파일이 있는 경우
        if ($this->is_javascript($viewHtml)) {
            $viewHtml->appendBody( $this->javascript($viewHtml->_data['page']['javascript']) );
        }


        // Asset 경로를 변경합니다.
        $viewHtml->replace("=\"/assets", "=\"".ROOT_PUBLIC."/assets");

        return $viewHtml->_body;
    }

    /**
     * 커스텀 CSS확인
     */
    private function is_css($viewHtml)
    {
        return isset($viewHtml->_data['page']['css']);
    }

    /**
     * 커스텀 스타일시트 문자열을 생성합니다.
     */
    private function css($style)
    {
        // 배열여부를 확인합니다.
        // 배열인 경우에는 복수의 링크를 생성합니다.
        if(is_array($style)) {
            // 배열인 경우에만 
            // 스타일시트 HTML코드를 담는 메모리를 할당합니다.
            $stylesheet = "";
            foreach ( $style as $value) {
                $stylesheet .= "<link href=\"".$value."\" rel=\"stylesheet\">";
            }
            return $stylesheet;

        } else {
            return "<link href=\"".$style."\" rel=\"stylesheet\">";
        }        
    }

    private function is_javascript($viewHtml)
    {
        return isset($viewHtml->_data['page']['javascript']);
    }

    /**
     * 커스텀 자바스크립트 문자열을 생성합니다.
     */
    private function javascript($code)
    {
        // 배열여부를 확인합니다.
        // 배열인 경우에는 복수의 링크를 생성합니다.
        if(is_array($code)) {
            // 배열인 경우에만 
            // 스타일시트 HTML코드를 담는 메모리를 할당합니다.
            $script = "";
            foreach ( $code as $value) {
                $script .= "<script src=\"".$value."\"></script>";
            }
            return $script;

        } else {
            return "<script src=\"".$code."\"></script>";
        }        
    }
    
    /**
     * 
     */
}