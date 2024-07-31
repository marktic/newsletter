<?php

namespace Marktic\Newsletter\Bundle\Library\View;

class ViewUtility
{
    public static function registerViewPaths($view, $module = null): void
    {
        $modules = [
            $module,
            'base',
        ];
        foreach ($modules as $module) {
            $path = realpath(__DIR__ . '/../../Resources/views/' . $module);
            $view->addPath($path);
            $view->addPath($path, 'MarkticNewsletter');
        }
    }
}
