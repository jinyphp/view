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

/**
 * 본문에서 머리말 데이터를 분리합니다.
 */
class FrontMatter
{
    public $FM;

    /**
     * 초기화
     */
    public function __construct()
    {
        $this->FM = Registry::get("FrontMatter");
    }

    public function parser($doc)
    {
        // 문서의 데이터를 분리합니다.
        $document = $this->FM->parse($doc);
        $datakey = "page";
        
        // 커스텀 view데이터
        /*      
        if($data = $this->isDataFile()){
            $this->appendViewData($datakey, $data);
        } 
        else {
            // 문서의 데이터를 추가합니다.
            if ($data = $document->getData()) {
                $this->appendViewData($datakey, $data);
            }            
        }*/
       
        //return $document->getContent();
        return [
            'data' => $document->getData(),
            'content' => $document->getContent()
        ];
    }


    /**
     * 별도의 파일 데이터가 있는 경우
     */
    public function isDataFile()
    {
        $path = ROOT.conf('ENV.path.pages');
      
        $dataYMAL = \Jiny\Core\Base\Path::append($path, $this->view_file);
        if(file_exists($dataYMAL.".yaml")) {
            $str = file_get_contents($dataYMAL.".yaml");

        } else {
            if(file_exists($dataYMAL.DS."index.yaml")) {
                $str = file_get_contents($dataYMAL.DS."index.yaml");
            }
        }
    }

    /**
     * 
     */
}