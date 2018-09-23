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

class RenderCss extends \Jiny\View\Render
{
    // 실제적인 동작
    protected function render($body)
    {
        //echo "CSS를 랜더링 합니다.<br>";
        // 머리말 커스텀 css 파일이 있는 경우
        if ($this->is_css($body)) {
            $body->appendHead( $this->css($body->_data['page']['css']) );
        }
    }

    /**
     * 머리말, 커스텀 CSS확인
     */
    private function is_css($body)
    {
        return isset($body->_data['page']['css']);
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

    /**
     * 
     */
}