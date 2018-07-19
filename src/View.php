<?php
namespace Jiny\View;

use \Jiny\Core\Registry\Registry;
use Liquid\Template;
use Symfony\Component\Filesystem\Filesystem;



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
        // \TimeLog::set(__CLASS__."가 생성이 되었습니다.");
        $this->Controller = Registry::get("controller");
        // $this->App = $this->Controller->getApp();
        $this->App = Registry::get("App");
        
        // 객체참조 개선을 위해서 임시저장합니다.        
        //$this->conf = Registry::get("CONFIG");
        //$this->Config = $this->conf;   

        $this->FileSystem = new Filesystem();
    }

    /**
     * 뷰 동작을 처리합니다.
     */
    public function process($viewName=NULL)
    {
        
        

        if ($this->create($viewName) ) {
            // 뷰를 출력합니다.          
            return $this->show($this->_data);            
        } else {
            // 뷰 화면을 생성하지 못한경우 NULL 반환
            return NULL;
        }
    }

}