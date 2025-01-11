<?php

require __DIR__ . DIRECTORY_SEPARATOR . 'module' . DIRECTORY_SEPARATOR . 'index.php';

Kirby::plugin('taufik-nurrohman/kirby-minify', [
    'hooks' => [
        'route:after' => function ($route, $path, $method, $result) {
            if ('GET' !== $method) {
                return $result;
            }
            if (0 === strpos($path . '/', 'panel/')) {
                return $result;
            }
            if (is_string($result)) {
                return x\minify\h_t_m_l($result);
            }
            if (is_object($result) && method_exists($result, 'render')) {
                return x\minify\h_t_m_l($result->render());
            }
            return $result;
        }
    ]
]);