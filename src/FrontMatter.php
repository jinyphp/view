<?php

namespace Jiny\Views;

use \Jiny\Core\Registry\Registry;
/**
 * 본문에서 머리말 데이터를 분리합니다.
 */
trait FrontMatter
{
    /**
     * 분리합니다.
     */
    public function frontMatter($doc)
    {
        // \TimeLog::set(__METHOD__);

        // 문서의 데이터를 분리합니다.
        $document = Registry::get("FrontMatter")->parse($doc);
        $datakey = "page";

        if($data = $this->isDataFile()){      
            // $this->view_data['page'] = $data;
            $this->appendViewData($datakey, $data);
        } 
        else {
            // echo "ymal 데이터가 없습니다.<br>";
            // 머리말 데이터를 글로벌 설정으로 저장
            // $this->view_data['page'] = $document->getData();
            if ($data=$document->getData()) {
                $this->appendViewData($datakey, $document->getData());
            }            
        }
        
        // Registry::get("CONFIG")->append("page", $data);
        // $this->_body = $document->getContent();

        // return $this;

        return $document->getContent();
    }


    public function frontParser($doc)
    {
        return Registry::get("FrontMatter")->parse($doc);
    }

    /**
     * 별도의 파일 데이터가 있는 경우
     */
    public function isDataFile()
    {
        $path = ROOT.$this->conf->data('ENV.path.view');
        $dataYMAL = $path. DS. $this->view_file."index.yml";
        if (file_exists($dataYMAL)){
            $str = file_get_contents($dataYMAL);         
            // return $this->conf->Drivers['Yaml']->parser($str);
            return \Jiny\Config\Yaml\Yaml::parse($str);
        }
    }

    /**
     * 
     */

}