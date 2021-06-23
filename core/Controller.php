<?php

namespace app\core;

class Controller
{
    public string $layout = 'main';

    /**
     * @param $layout
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    /**
     * @param $view
     * @param array $params
     * @return array|false|string|string[]
     */
    public function view($view, $params = [])
    {
        return Application::$app->router->renderView($view, $params);
    }
}