<?php

require __DIR__ . DIRECTORY_SEPARATOR . 'minify' . DIRECTORY_SEPARATOR . 'index.php';

// Register a plugin hook to execute after the response body is ready
Kirby::plugin('taufik-nurrohman/minify', [
    'hooks' => [
        'route:after' => function ($route, $path, $method, $result) {
            // Apply the compressor only to the `GET` request
            if ('GET' !== $method) {
                return $result;
            }
            // Do not apply the compressor when we are in the control panel
            $url = kirby()->urls();
            if (0 === strpos($url->current . '/', $url->panel . '/')) {
                return $result;
            }
            // Response body is a simple string
            if (is_string($result)) {
                $result = trim($result);
                // Determine the content type from the extension in URL
                if ($type = pathinfo($path, PATHINFO_EXTENSION)) {
                    if (
                        'htm' === $type ||
                        'html' === $type
                    ) {
                        return x\minify\h_t_m_l($result);
                    }
                    if (
                        'json' === $type ||
                        'jsonp' === $type ||
                        'webmanifest' === $type
                    ) {
                        return x\minify\j_s_o_n($result);
                    }
                    if (
                        'xht' === $type ||
                        'xhtm' === $type ||
                        'xhtml' === $type ||
                        'xml' === $type
                    ) {
                        return x\minify\x_m_l($result);
                    }
                // Determine the content type from the string
                } else {
                    if ('<!doctype' === strtolower(strtok($result, " \n\r\t")) || '</html>' === strtolower(substr($result, -7))) {
                        return x\minify\h_t_m_l($result);
                    }
                    if (isset($result[0]) && false !== strpos('[{', $result[0]) && false !== strpos(']}', substr($result, -1))) {
                        if (function_exists('json_validate') && json_validate($result) || null !== json_decode($result)) {
                            return x\minify\j_s_o_n($result);
                        }
                    }
                    if ('<?xml' === strtolower(strtok($result, " \n\r\t?"))) {
                        return x\minify\x_m_l($result);
                    }
                }
                // Do nothing!
                return $result;
            }
            // Response body is an object, such as `Kirby\Cms\Page` or `Kirby\Cms\Responder`
            if (is_object($result) && (method_exists($result, 'render') || method_exists($result, 'send'))) {
                $raw = method_exists($result, 'send') ? $result->send() : 0;
                $template = method_exists($result, 'template') ? $result->template() : 0;
                $render = (string) ($raw ?: $result->render());
                $type = ($template ? $template->type() : ($raw ? $raw->type() : pathinfo($path, PATHINFO_EXTENSION))) ?: 'html';
                if (
                    'htm' === $type ||
                    'html' === $type ||
                    'text/html' === $type
                ) {
                    return x\minify\h_t_m_l($render);
                }
                if (
                    'application/feed+json' === $type ||
                    'application/geo+json' === $type ||
                    'application/json' === $type ||
                    'application/ld+json' === $type ||
                    'audio/midi' === $type ||
                    'audio/x-midi' === $type ||
                    'json' === $type ||
                    'jsonp' === $type ||
                    'text/json' === $type ||
                    'webmanifest' === $type
                ) {
                    return x\minify\j_s_o_n($render);
                }
                if (
                    'application/atom+xml' === $type ||
                    'application/mathml+xml' === $type ||
                    'application/rdf+xml' === $type ||
                    'application/rss+xml' === $type ||
                    'image/svg+xml' === $type ||
                    'text/xml' === $type ||
                    'xht' === $type ||
                    'xhtm' === $type ||
                    'xhtml' === $type ||
                    'xml' === $type
                ) {
                    return x\minify\x_m_l($render);
                }
            }
            // Do nothing!
            return $result;
        }
    ]
]);