<?php
namespace Jiny\View;

function resource($name=null)
{
    $rs = \Jiny\View\Resource::instance();
    if ($name) {
        return $rs->get($name);
    }
    return $rs;
}