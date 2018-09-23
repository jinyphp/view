<?php

namespace Jiny\View;
/**
 *  뷰 추상클래스
 *  체인패턴
 */
abstract class Render {
    private $Next;

    public function setNext($obj)
    {
        $this->Next = $obj;
    }

    public function process($body)
    {
        // 뷰 랜더링을 처리합니다.
        $this->render($body);

        // 다음 처리를 연속합니다.
        if ($this->Next) {
            $this->Next->process($body);
        }

    }

    // 실제적인 동작
    abstract protected function render($body);
}