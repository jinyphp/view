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
        // 테마처리
        if($theme = conf("site.theme")) {
            \jiny\theme($body);
        } 
    }

    /**
     * 
     */
}