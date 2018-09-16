<?php
namespace Jiny\View;

/**
 * ViewHTML
 */
class ViewHtml 
{
    public $_body;
    public $_data;

    public function __construct($body=null, $data=null)
    {
        $this->_body = $body;
        $this->_data = $data;
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