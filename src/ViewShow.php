<?php

namespace Jiny\Views;

use \Jiny\Core\Registry\Registry;
use Sunra\PhpSimple\HtmlDomParser;
use Symfony\Component\Filesystem\Filesystem;

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
    public function show($data)
    {
        // \TimeLog::set(__METHOD__);

        // 테마기능 등록
        // Registry pool에 등록합니다.  
        if (Registry::get("Packages")->isPackage("jiny/theme")) {
            // 테마 의존성 주입(view)
            // 뷰 객체가 생성되기 이전이기 때문에, 테마에 $this로 주입을 합니다.
            $this->Theme = Registry::create(\Jiny\Theme\Theme::class, "Theme", $this);
            
            if ($this->Theme->isTheme()) {
                /*
                 // 해더를 생성합니다.
                $this->_header = $this->Theme->header();

                // 푸터를 생성합니다.
                $this->_footer = $this->Theme->footer();

                // 레이아웃을 체크합니다.
                $this->_body = $this->Layout($this->Theme->_env['layout']);
                */
                $this->_body = $this->Theme->render($this->_body);
              

            }
        }

        // 템플릿 처리
        // 템플릿 인스턴스를 생성합니다.       
        //$this->Template = new \Jiny\Template\Template($this);
        $this->Template = Registry::create(\Jiny\Template\Template::class, "Template", $this);

        if ($this->Template) {
            // Liquid 엔진처리
            $this->_body = $this->Template->process($this->_body);
       
            $this->_body = str_replace("/assets", ROOT_PUBLIC."/assets", $this->_body);
        }   


        return $this->_body;
    }
    
    /**
     * 테마 레이아웃을 결합합니다.
     */
    public function Layout($layout=NULL)
    {
        // \TimeLog::set(__METHOD__);
        
    }
    

    /**
     * 
     */
}