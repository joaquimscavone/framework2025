<?php

use Fmk\Facades\View;

if (!function_exists('view_path')) {
    function view_path($view_file)
    {
        $view_path = defined('VIEW_PATH') ? constant('VIEW_PATH') : constant('APP_PATH') . '/views/';
        $view_file = str_replace('.', DIRECTORY_SEPARATOR, $view_file);
        $view_ext = defined('VIEW_EXT') ? constant('VIEW_EXT') : '.view.php';
        $view_file = $view_path . $view_file . $view_ext;
        if (!file_exists($view_file)) {
            throw new Exception("A View $view_file não foi encontrada.");
        }
        return $view_file;
    }
}
if (!function_exists('template_path')) {
    function template_path($template_file)
    {
        $template_path = defined('VIEW_PATH') ? constant('VIEW_PATH') : constant('APP_PATH') . '/views/templates/';
        $template_path = defined('TEMPLATE_PATH') ? constant('TEMPLATE_PATH') : $template_path;
        $template_file = str_replace('.', DIRECTORY_SEPARATOR, $template_file);
        $template_ext = defined('TEMPLATE_EXT') ? constant('TEMPLATE_EXT') : '.template.php';
        $template_file = $template_path . $template_file . $template_ext;
        return $template_file;
    }
}

if (!function_exists('view')) {
    function view($view_file, array $data = [], $template = 'default')
    {

        if ($template === 'default') {
            $template = defined('TEMPLATE_DEFAULT') ? constant('TEMPLATE_DEFAULT') : 'default';
            if (!file_exists(template_path($template))) {
                $template = false;
            }
        }
        $view = new View(view_path($view_file));
        if ($template) {
            $template = template_path($template);
            if (!file_exists($template)) {
                throw new Exception("O Template $template não foi encontrado.");
            }
            $data['template'] = new stdClass();
            $view = $view->render($data);
            $template = new View($template);
            $template = $template->render($data);
            return str_replace('{{$VIEW}}', $view, $template);
        }
        return $view->setData($data);
    }
}