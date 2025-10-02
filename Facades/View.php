<?php


namespace Fmk\Facades;

class View{
    protected $view_file;
    protected $data = [];
    public function __construct($view_file){
        $this->view_file = $view_file;   
    }

    public function __set($name, $value){
        $this->data[$name] = $value;
    }
    //new View('home'); $view->user = 'John'; $view->setData([]);
    public function setData(array $data){
        $this->data = $data;
    }

    public function __get($name){
        return $this->data[$name] ?? NULL;
    }

    public function render(array $data = []){
        ob_start();
        extract(array_merge($this->data,$data));
        include $this->view_file;
        return ob_get_clean();
    }

    public function __toString(){
        return $this->render();
    }
}