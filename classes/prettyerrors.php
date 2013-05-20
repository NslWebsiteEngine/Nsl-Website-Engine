<?php
include_once __DIR__."/../vendor/autoload.php";
use Whoops\Handler\PrettyPageHandler;
use Whoops\Handler\JsonResponseHandler;
class prettyerrors extends base {
    function __construct(&$main) {
        parent::__construct($main);
        $this->run = new Whoops\Run;
        $this->handler = new PrettyPageHandler;
    }
    
    function setArgs($name = "NSL args", $args = []) {
        if(!is_array($args))
            $args = [];
        $this->handler->addDataTable($name, $args);
        return $this;
    }
    
    function setTitle($title = "We're all going to be fired =)") {
        $this->handler->setPageTitle($title);
        return $this;
    }
    function setEditor($editor) {
        $this->handler->setEditor($editor);
        return $this;
    }
    
    function register() {
        $this->run->pushHandler($this->handler);
        $this->run->pushHandler(function($exception, $inspector, $run) {
            $frames = $inspector->getFrames();
            foreach($frames as $i => $frame)
                if($function = $frame->getFunction())
                    $frame->addComment("You got this error from '$function'", 'error-advice');
        });
        $this->run->register();
        return $this;
    }
}