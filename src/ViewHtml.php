<?php
namespace Jiny\View;

/**
 * ViewHTML
 */
class ViewHtml 
{
    public $_body;
    public $_data;

    /**
     * 초기화
     */
    public function __construct($body=null, $data=null)
    {
        $this->_body = $body;
        $this->_data = $data;
    }

    /**
     * 본문을 설정합니다.
     */
    public function setBody($body)
    {
        $this->_body = $body;
    }

    /**
     * 본문을 반환합니다.
     */
    public function getBody()
    {
        return $this->_body;
    }


    /**
     * 확장 레이아웃이 있는지 확인합니다.
     */
    public function isLayout()
    {
        if (isset($this->_data['page']['layout'])) {
            return $this->_data['page']['layout'];
        }
    }

    public function clearLayout()
    {
        unset($this->_data['page']['layout']);
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

    /**
     * 코드를 치환합니다.
     */
    public function replace($src, $dst)
    {
        $this->_body = str_replace($src, $dst, $this->_body);
    }

    /**
     * 해더 부분 스트링을 추가합니다.
     */
    public function appendHead($str)
    {
        $this->_body = str_replace("</head>", $str."</head>", $this->_body);
    }

    /**
     * 바디 부분 스트링을 추가합니다.
    */
    public function appendBody($str)
    {
        $this->_body = str_replace("</body>", $str."</body>", $this->_body);
    }
}