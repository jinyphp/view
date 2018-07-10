<?php
namespace Jiny\Views;

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
    

    // 뷰 데이터
    public $_data = [];

    // trait...
    use ViewCreate, ViewShow;

    public function __construct($Controller)
    {
        // \TimeLog::set(__CLASS__."가 생성이 되었습니다.");
        $this->Controller = $Controller;
        $this->App = $Controller->getApp();

        // 뷰 화면을 읽어 옵니다.
        // 인자값: 라우터 설정 뷰
        $this->setViewFile( $this->App->_viewFile );
        
        // 컨트롤러의 데이터를 
        // 뷰로 전달합니다.
        if( isset($this->Controller->viewData) ){
            $this->setViewData( $this->Controller->viewData );
        }
        

        // 객체참조 개선을 위해서 임시저장합니다.        
        $this->conf = Registry::get("CONFIG");
        $this->Config = $this->conf;   

        


        // 메뉴 데이터를 읽어옵니다.
        $this->view_data['menus'] = menu();

        
        


        // 설정파일을 뷰데이터에 결합합니다.
        $cfgData = $this->Controller->getApp()->Config->data();
        foreach ($cfgData as $key => $value) {
            $this->view_data[$key] = $value;
        }

        // 현재 url
        if ($this->view_data['url'] = $this->App->Boot->urlString()) {
        } else {
            $this->view_data['url'] = "/";
        }

        $this->FileSystem = new Filesystem();

    }

    public function process($viewName)
    {
        if ($this->create($viewName) ) {
            $this->_data = $this->view_data;
            // 뷰를 출력합니다.
            return $this->show($this->view_data);
        } else {
            return NULL;
        }
    }

}