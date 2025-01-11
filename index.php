<?php

require __DIR__ . DIRECTORY_SEPARATOR . 'minify' . DIRECTORY_SEPARATOR . 'index.php';

// Create globally reusable function(s) to be used by other(s)
function minify_css(...$v) {
    return x\minify\c_s_s(...$v);
}

function minify_html(...$v) {
    return x\minify\h_t_m_l(...$v);
}

function minify_js(...$v) {
    return x\minify\j_s(...$v);
}

function minify_json(...$v) {
    return x\minify\j_s_o_n(...$v);
}

function minify_php(...$v) {
    return x\minify\p_h_p(...$v);
}

function minify_xml(...$v) {
    return x\minify\x_m_l(...$v);
}

// Register plugin hook that runs after the response body is ready
Kirby::plugin('taufik-nurrohman/kirby-minify', [
    'hooks' => [
        'route:after' => function ($route, $path, $method, $result) {
            // Apply minifier only to the `GET` request
            if ('GET' !== $method) {
                return $result;
            }
            // Disable minifier if we are in the control panel
            if (0 === strpos($path . '/', 'panel/')) {
                return $result;
            }
            // Response body is a plain string
            if (is_string($result)) {
                // TODO: Detect content type from the string
                $result = trim($result);
                if (0 === strpos($result, '{')) {
                    // TODO
                }
                if (0 === strpos($result, '<?xml')) {
                    // TODO
                }
                return minify_html($result);
            }
            // Response body is an object, like `Kirby\Cms\Page`
            if (is_object($result) && method_exists($result, 'render')) {
                $render = $result->render();
                if ($template = $result->template()) {
                    $type = $template->type() ?? 'html';
                    if ('json' === $type) {
                        return minify_json($render);
                    }
                    if ('xml' === $type) {
                        return minify_xml($render);
                    }
                }
                return minify_html($render);
            }
            // Do nothing!
            return $result;
        }
    ]
]);