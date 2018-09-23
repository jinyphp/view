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

class htm extends \Jiny\View\Driver
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
    
    public function resource($name)
    {
        $body = $this->read($name);
            
        $Front = new \Jiny\View\FrontMatter($this);
        $Front->frontMatter($body);
        print_r($Front->_data);
        exit;
    }

    /**
     * 리소스 읽기
     */
    public function read($name)
    {
        if ($this->_lang) {
            $multi = str_replace(".htm", ".".$this->_lang.".htm", $name);
            if(file_exists($multi)){
                return file_get_contents($multi);
            }
        }
        return file_get_contents($name);
    }

}