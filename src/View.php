<?php
/*
 * This file is part of the jinyPHP package.
 *
 * (c) hojinlee <infohojin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Jiny\View;

use \Jiny\Core\Registry\Registry;
use Liquid\Template;
use Symfony\Component\Filesystem\Filesystem;

/**
 * 뷰를 처리합니다.
 */
class View extends ViewFile 
{
    public $App;

    protected $_header;
    protected $_footer;
    protected $Menu;
    
    private $_themeENV;    
    private $Controller;
    
    public $FileSystem;
    

    // trait...
    use ViewCreate, ViewShow;

    /**
     * 뷰 클래스를 초기화 합니다.
     */
    public function __construct()
    {
        $this->Controller = Registry::get("controller");
        $this->App = Registry::get("App");
        
        $this->FileSystem = new Filesystem();
    }

    /**
     * 템플릿 메소드패턴
     * 복잡한 뷰 동작을 처리합니다.
     */
    public function process($viewName=NULL)
    {
        
        if ($this->create($viewName) ) {
            // 뷰를 출력합니다.
            $viewHtml = new ViewHtml($this->_body, $this->_data);      
            return $this->show($viewHtml);
     
        } else {
            // 뷰 화면을 생성하지 못한경우 NULL 반환
            return NULL;
        }
    }

}