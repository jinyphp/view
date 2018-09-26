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

class RenderTemplate extends \Jiny\View\Render
{
    // 실제적인 동작
    protected function render($body)
    {
        //echo "템플릿을 랜더링 합니다.<br>";
        // 템플릿 인스턴스를 레지스트리에 추가합니다.
        $this->Template = Registry::create(\Jiny\Template\Template::class, "Template", $this);
        if ($this->Template) {
            // 템플릿을 엔진처리
            $this->Template->process($body); 
        }

    }

    /**
     * 
     */
}