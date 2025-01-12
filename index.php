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
            $extensions = $tests = $types = [];
            $options = option('taufik-nurrohman.' . basename(__DIR__));
            foreach ($options as $k => $v) {
                // Assume configuration without `active` key as active by default
                if (array_key_exists('active', $v) && !$v['active']) {
                    continue;
                }
                if (!empty($v['extensions'])) {
                    foreach ($v['extensions'] as $kk => $vv) {
                        if (!$vv) {
                            continue;
                        }
                        $extensions[$kk] = $k;
                    }
                }
                if (!empty($v['test']) && is_callable($v['test'])) {
                    $tests[$k] = $v['test'];
                }
                if (!empty($v['types'])) {
                    foreach ($v['types'] as $kk => $vv) {
                        if (!$vv) {
                            continue;
                        }
                        $types[$kk] = $k;
                    }
                }
            }
            // Response body is a simple string
            if (is_string($result)) {
                $result = trim($result);
                // Determine the content type from the extension in URL
                if ($extension = pathinfo($path, PATHINFO_EXTENSION)) {
                    if (isset($extensions[$extension]) && is_callable($f = $options[$extensions[$extension]]['f'] ?? 0)) {
                        return call_user_func($f, $result);
                    }
                // Determine the content type from the string
                } else {
                    foreach ($tests as $k => $v) {
                        if (call_user_func($v, $result) && is_callable($f = $options[$k]['f'] ?? 0)) {
                            return call_user_func($f, $result);
                        }
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
                if (false !== strpos($type, '/')) {
                    if (isset($types[$type]) && is_callable($f = $options[$types[$type]]['f'] ?? 0)) {
                        return call_user_func($f, $render);
                    }
                } else {
                    if (isset($extensions[$type]) && is_callable($f = $options[$extensions[$type]]['f'] ?? 0)) {
                        return call_user_func($f, $render);
                    }
                }
            }
            // Do nothing!
            return $result;
        }
    ],
    'options' => [
        'CSS' => [
            'active' => true,
            'extensions' => [
                'css' => true
            ],
            'f' => "x\\minify\\c_s_s",
            'types' => [
                'text/css' => true
            ]
        ],
        'HTML' => [
            'active' => true,
            'extensions' => [
                'htm' => true,
                'html' => true
            ],
            'f' => "x\\minify\\h_t_m_l",
            'test' => function ($v) {
                return '<!doctype' === strtolower(strtok($v, " \n\r\t")) || '</html>' === strtolower(substr($v, -7));
            },
            'types' => [
                'text/html' => true
            ],
        ],
        'JS' => [
            'active' => true,
            'extensions' => [
                'js' => true,
                'mjs' => true
            ],
            'f' => "x\\minify\\j_s",
            'types' => [
                'application/javascript' => true,
                'application/x-javascript' => true,
                'text/javascript' => true
            ]
        ],
        'JSON' => [
            'active' => true,
            'extensions' => [
                'json' => true,
                'jsonp' => true,
                'webmanifest' => true
            ],
            'f' => "x\\minify\\j_s_o_n",
            'test' => function ($v) {
                if (isset($v[0]) && false !== strpos('[{', $v[0]) && false !== strpos(']}', substr($v, -1))) {
                    if (function_exists('json_validate') && json_validate($v) || null !== json_decode($v)) {
                        return true;
                    }
                }
                return false;
            },
            'types' => [
                'application/feed+json' => true,
                'application/geo+json' => true,
                'application/json' => true,
                'application/ld+json' => true,
                'audio/midi' => true,
                'audio/x-midi' => true,
                'text/json' => true
            ]
        ],
        'PHP' => [
            'active' => true,
            'extensions' => [
                'php' => true,
                'phtml' => true
            ],
            'f' => "x\\minify\\p_h_p",
            'types' => [
                'application/php' => true,
                'application/x-httpd-php' => true,
                'application/x-httpd-php-source' => true,
                'text/php' => true,
                'text/x-php' => true
            ]
        ],
        'XML' => [
            'active' => true,
            'extensions' => [
                'xht' => true,
                'xhtm' => true,
                'xhtml' => true,
                'xml' => true
            ],
            'f' => "x\\minify\\x_m_l",
            'test' => function ($v) {
                return '<?xml' === strtolower(strtok($v, " \n\r\t?"));
            },
            'types' => [
                'application/atom+xml' => true,
                'application/mathml+xml' => true,
                'application/rdf+xml' => true,
                'application/rss+xml' => true,
                'image/svg+xml' => true,
                'text/xml' => true
            ]
        ]
    ]
]);