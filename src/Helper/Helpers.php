<?php
use \Jiny\Core\Registry\Registry;

if (! function_exists('view')) {
    /**
     * 뷰를 생성하고 출력합니다.
     */
    function view($viewName=Null, $data=[], $path=NULL) {

        $view = Registry::create(\Jiny\View\View::class, "view");

        if (func_num_args()) {
            $viewName = str_replace("/", DS, $viewName);
            $view->setFile($viewName);
            $view->setData($data);
            
            // 뷰를 처리합니다.            
            return $view->process($viewName);
        } else {
            return $view;
        }
       
    }
}