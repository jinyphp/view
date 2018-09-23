<?php

namespace Jiny\View;
/**
 * 뷰 추상클래스
 *
 */
abstract class Process {

    /**
     * 템플릿 메소드패턴
     * 복잡한 뷰 동작의 알고리즘을 처리합니다.
     */
    final public function process($viewName=NULL)
    {
        
        if ($viewHtml = $this->create($viewName) ) {
            // 뷰를 출력합니다.
            // $viewHtml = new ViewHtml($this->_body, $this->_data);      
            return $this->show($viewHtml);
     
        } else {
            // 뷰 화면을 생성하지 못한경우 NULL 반환
            return NULL;
        }
    }

    abstract public function create($viewName, $data=[]);
    abstract public function show($viewHtml);


    /**
     * 뷰 설정처리 동작설정
     */
    public $_conf = [];

    /**
     * 뷰 처리테마를 설정합니다.
     */
    public function setTheme($name)
    {
        $this->_conf['theme'] = $name;
    }

    /**
     * 뷰 처리테마를 확인합니다.
     */
    public function getTheme()
    {
        return $this->_conf['theme'];
    }

    



    /**
     * 
     */
}