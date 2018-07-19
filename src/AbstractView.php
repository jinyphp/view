<?php
namespace Jiny\View;

use \Jiny\Core\Registry\Registry;

class AbstractView
{
    public $view_file;
    public $_data = [];
    

    // 인스턴스
    protected $conf;
    protected $Config;

    // 뷰 설정
    public function setFile($file)
    {
        // \TimeLog::set(__METHOD__);
        $this->view_file = $file;
    }

    /**
     * 뷰로 전달되는 데이터를 초기화 합니다.
     */
    public function setData($data)
    {
        // \TimeLog::set(__METHOD__);
   
        if(isset($data)) {
            $this->_data = $data;
        }

        // 설정파일을 뷰데이터에 결합합니다.
        //$cfgData = $this->Config->data();
        $cfgData = conf();
        foreach ($cfgData as $key => $value) {
            $this->_data[$key] = $value;
        }

        // 현재 url
        if ($this->_data['url'] = $this->App->Boot->urlString()) {
        } else {
            $this->_data['url'] = "/";
        }
    }

    /**
     * 뷰 데이터의 값을 추가합니다.
     */
    public function appendViewData(string $key, array $arr)
    {
    
        foreach ($arr as $k => $value) {
            $this->_data[$key][$k] = $value;
        } 
    }



/*
    public function mergeViewData($arr)
    {
        // \TimeLog::set(__METHOD__);

        //echo __METHOD__."를 호출합니다.<br>";
        if (\is_array($this->_data)){
            //echo "배열을 병합합니다.<br>";
            //print_r($arr);
            array_merge($this->_data, $arr);
            //echo "<br><br>";
        } else {
            $this->_data = $data; 
        }        
    }

    public function getViewData()
    {
        // \TimeLog::set(__METHOD__);

        return $this->_data;
    }
    */



    /**
     * 필요한 인스턴스를 재설정합니다.
     * 메소드 호출 빈도를 줄여 줍니다.
     */
    protected function instanceInit()
    {

    }

    /**
     * 뷰 설정처리 동작설정
     */
    public $_conf = [];

    /**
     * 뷰 처리테마를 설정합니다.
     */
    public function setTheme($name)
    {
        $this->_conf['theme'] = $name;
    }

    /**
     * 뷰 처리테마를 확인합니다.
     */
    public function getTheme()
    {
        return $this->_conf['theme'];
    }




}