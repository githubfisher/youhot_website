<?php

/**
 * Layout management library based on:
 * http://codeigniter.com/wiki/layout_library/
 *
 * Extended layout placeholders and javascript and css files inclussion.
 * Author: mbo
 */
class Layout {
    var $obj;
    var $layout;
    var $js;
    var $css;
    var $placeholder;

    function __construct($layout = "layout")
    {
        $this->obj =& get_instance();
        $this->layout = $layout;
        $this->js = $this->css = $this->placeholder = array();
    }

    function set_layout($layout)
    {
      $this->layout = $layout;
    }

    function view($view, $data=null, $return=false)
    {
        $loadedData = array();
        $loadedData['content'] = $this->obj->load->view($view,$data,true);

        if($return)
        {
            $output = $this->obj->load->view($this->layout, $loadedData, true);
            return $output;
        }
        else
        {
            $this->obj->load->view($this->layout, $loadedData, false);
        }
    }

    function load_js($file=null)
    {
        if(is_array($file)){
            foreach($file as $f)
                $this->js[] = $f;
        }else $this->js[] = $file;
    }

    function js()
    {
        $stream = "";
        foreach($this->js as $js){
            if(strpos($js,'http://') === 0){ //不处理绝对路径
                $stream .= '<script	type="text/javascript" src="'. $js .'"></script>' . "\n";

            }else{

                $stream .= '<script	type="text/javascript" src="'. $this->obj->config->item('static_file_path') . $js .'"></script>' . "\n";

            }
        }

        return $stream;
    }

    function load_css($file=null)
    {
        if(is_array($file)){
            foreach($file as $f)
                $this->css[] = $f;
        }else $this->css[] = $file;
    }

    function css()
    {
        $stream = "";
        foreach($this->css as $css){

            if(strpos($css,'http://') === 0 ){ //不处理绝对路径
                $stream .= '<link href="'. $css .'" rel="stylesheet" type="text/css" media="screen" />' . "\n";
            }else{
                $stream .= '<link href="'. $this->obj->config->item('static_file_path') . $css .'" rel="stylesheet" type="text/css" media="screen" />' . "\n";
            }
        }

        return $stream;
    }

    function placeholder($key, $value=null){
        if($value==null)
            return array_key_exists($key, $this->placeholder)?$this->placeholder[$key]:FALSE;
        else
            $this->placeholder[$key] = $value;
    }



}
