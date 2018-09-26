<?php
/*
 * This file is part of the jinyPHP package.
 *
 * (c) hojinlee <infohojin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Jiny\View\Drivers;

class docx extends \Jiny\View\Driver
{
    private $_lang;

    /**
     * 의존성 주입
     */
    public function __construct($lang=null)
    {
        //
        $this->_lang = $lang;
    }

    
    /**
     * 워드 문서를 읽어 옵니다.
     */
    public function read($name)
    {
        $docx = new \Docx_reader\Docx_reader();
        $docx->setFile($name);

        if(!$docx->get_errors()) {
            $html = $docx->to_html();
            $plain_text = $docx->to_plain_text();

            unset($docx);
            return $html;
        } else {
            echo implode(', ',$docx->get_errors());
            exit;
        }
    }

    /**
     * 
     */
}