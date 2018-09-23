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

class RenderTheme extends \Jiny\View\Render
{
    // 실제적인 동작
    protected function render($body)
    {
        //echo "테마를 랜더링 합니다.<br>";
        // 테마처리
        if($theme = conf("site.theme")) {
            if (Registry::get("Packages")->isPackage("jiny/theme")) {

                // 테마 의존성 주입(view)
                // 뷰 객체가 생성되기 이전이기 때문에, 테마에 $this로 주입을 합니다.
                $this->Theme = Registry::create(\Jiny\Theme\Theme::class, "Theme", $this);
                
                if ($this->Theme->isTheme($body)) {
                    $this->Theme->render($body);        
                }
            }
        } else {
            // echo "테마가 지정되지 않았습니다.";
        }
    }

    /**
     * 
     */
}