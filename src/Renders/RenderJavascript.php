<?php
/*
 * This file is part of the jinyPHP package.
 *
 * (c) hojinlee <infohojin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Jiny\View\Renders;

use \Jiny\Core\Registry\Registry;
use Liquid\Template;
use Symfony\Component\Filesystem\Filesystem;

class RenderJavascript extends \Jiny\View\Render
{
    // 실제적인 동작
    protected function render($body)
    {
        // 머리말 커스텀 javascript 파일이 있는 경우
        if ($this->is_javascript($body)) {
            $body->appendBody( $this->javascript($body->_data['page']['javascript']) );
        }
    }

    /**
     * 머리말, 자바스크립트를 확인합니다.
     */
    private function is_javascript($body)
    {
        return isset($body->_data['page']['javascript']);
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