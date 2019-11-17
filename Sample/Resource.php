<?php
require "../../../../vendor/autoload.php";

// 리소스 폴더 초기화 및 패스 설정
\Jiny\View\resource()->init()->setPath("Resource/View/");

// 리소스 파일 읽기
echo \Jiny\View\resource("hello.md");