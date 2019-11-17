<?php
/*
 * This file is part of the jinyPHP package.
 *
 * (c) hojinlee <infohojin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Jiny;
use \Jiny\Core\Registry\Registry;

require "Resource.php";





if (! function_exists('view')) {
    /**
     * 뷰를 생성하고 출력합니다.
     */
    function view($viewName=Null, $data=[], $path=NULL) {

        $view = Registry::create(\Jiny\View\View::class, "View");

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

/**
 * 데이터 기반의 출력뷰
 */
if (! function_exists('dataview')) {
    function dataview()
    {

    }
}

/**
 * 데이터 목록뷰
 */
if (! function_exists('listview')) {
    function listview()
    {

    }
}

/**
 * 폼 입력뷰
 */
if (! function_exists('formview')) {
    function formview()
    {
        
    }
}





