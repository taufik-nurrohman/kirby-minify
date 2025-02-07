<?php

require __DIR__ . DIRECTORY_SEPARATOR . 'minify' . DIRECTORY_SEPARATOR . 'index.php';

// <https://github.com/taufik-nurrohman/kirby-minify/issues/1>
function __minify(?string $value) {
    $value = x\minify\h_t_m_l($value);
    if (!$value || false === strpos($value, '<')) {
        return $value;
    }
    $options = (array) (option('taufik-nurrohman.minify') ?? []);
    if (empty($options['HTML']['files'])) {
        return $value;
    }
    $assets_root = kirby()->root('assets');
    $assets_url = kirby()->urls()->assets;
    if (false !== strpos($value, '<img ') && (!array_key_exists('active', $options['XML'] ?? []) || !empty($options['XML']['active'])) && !empty($options['HTML']['files']['svg'])) {
        $value = preg_replace_callback('/<img\s(?>"[^"]*"|\'[^\']*\'|[^>]+)+>/', static function ($m) use ($assets_root, $assets_url, $options) {
            if (false === strpos($m[0], 'src=')) {
                return $m[0];
            }
            return preg_replace_callback('/\bsrc=("[^"]+"|\'[^\']+\'|[^\s>]+)/', static function ($m) use ($assets_root, $assets_url, $options) {
                if ('"' === ($q = $m[1][0]) && $q === substr($m[1], -1) || "'" === ($q = $m[1][0]) && $q === substr($m[1], -1)) {
                    $m[1] = \substr($m[1], 1, -1);
                } else {
                    $q = "";
                }
                // Resolve relative URL
                if ('/' === ($m[1][0] ?? 0)) {
                    $m[1] = $assets_url . $m[1];
                }
                // URL does not match with `http://127.0.0.1/assets/**.svg`
                if (0 !== strpos($m[1], $assets_url . '/')) {
                    return $m[0];
                }
                // File `.\srv\http\assets\**.svg` does not exist
                if (!$file = stream_resolve_include_path($assets_root . substr(strtok($m[1], '?&#'), strlen($assets_url)))) {
                    return $m[0];
                }
                // Skip minification if you have already minified the file and then manually include it into the site!
                if ('.min.svg' === substr($file, -8)) {
                    return $m[0];
                }
                $file_new = substr($file, 0, -4) . '.min.svg';
                // Compare the file modification time of the original file with the file modification time of the
                // minified file. If the original file modification time is newer, then update the minified file! If the
                // minified file does not exist yet, then create it!
                if (!is_file($file_new) || filemtime($file) > filemtime($file_new)) {
                    // Create a minified file and then save it along with the original file as `**.min.svg`. Be aware
                    // that this will wipe the existing `**.min.svg` file if you already have it. You probably have
                    // created it before with a better minification script using the Node.js build tool and such, which
                    // is more optimized in every way.
                    file_put_contents($file_new, x\minify\x_m_l(file_get_contents($file)));
                }
                $v = dechex(filemtime($file));
                if ($query = strstr($m[1], '?')) {
                    $a = explode('#', $query, 2);
                    parse_str($a, $r);
                    if (isset($r['v'])) {
                        $r[$v] = 1;
                    } else {
                        $r['v'] = $v;
                    }
                    $query = http_build_query($r) . (isset($a[1]) ? '#' . $a[1] : "");
                } else {
                    $a = explode('#', $m[1], 2);
                    $query = '?v=' . $v . (isset($a[1]) ? '#' . $a[1] : "");
                }
                return 'src=' . $q . substr(strtok($m[1], '?&#'), 0, -4) . '.min.svg' . $query . $q;
            }, $m[0]);
        }, $value);
    }
    if (false !== strpos($value, '<link ') && (!array_key_exists('active', $options['CSS'] ?? []) || !empty($options['CSS']['active'])) && !empty($options['HTML']['files']['css'])) {
        $value = preg_replace_callback('/<link\s(?>"[^"]*"|\'[^\']*\'|[^>]+)+>/', static function ($m) use ($assets_root, $assets_url, $options) {
            if (false === strpos($m[0], 'href=') || false === strpos($m[0], 'rel=')) {
                return $m[0];
            }
            if (!preg_match('/\brel=(?>"stylesheet"|\'stylesheet\'|stylesheet\b)/', $m[0])) {
                return $m[0];
            }
            return preg_replace_callback('/\bhref=("[^"]+"|\'[^\']+\'|[^\s>]+)/', static function ($m) use ($assets_root, $assets_url, $options) {
                if ('"' === ($q = $m[1][0]) && $q === substr($m[1], -1) || "'" === ($q = $m[1][0]) && $q === substr($m[1], -1)) {
                    $m[1] = \substr($m[1], 1, -1);
                } else {
                    $q = "";
                }
                if ('/' === ($m[1][0] ?? 0)) {
                    $m[1] = $assets_url . $m[1];
                }
                if (0 !== strpos($m[1], $assets_url . '/')) {
                    return $m[0];
                }
                if (!$file = stream_resolve_include_path($assets_root . substr(strtok($m[1], '?&#'), strlen($assets_url)))) {
                    return $m[0];
                }
                if ('.min.css' === substr($file, -8)) {
                    return $m[0];
                }
                $file_new = substr($file, 0, -4) . '.min.css';
                if (!is_file($file_new) || filemtime($file) > filemtime($file_new)) {
                    file_put_contents($file_new, x\minify\c_s_s(file_get_contents($file)));
                }
                $v = dechex(filemtime($file));
                if ($query = strstr($m[1], '?')) {
                    $a = explode('#', $query, 2);
                    parse_str($a, $r);
                    if (isset($r['v'])) {
                        $r[$v] = 1;
                    } else {
                        $r['v'] = $v;
                    }
                    $query = http_build_query($r) . (isset($a[1]) ? '#' . $a[1] : "");
                } else {
                    $a = explode('#', $m[1], 2);
                    $query = '?v=' . $v . (isset($a[1]) ? '#' . $a[1] : "");
                }
                return 'href=' . $q . substr(strtok($m[1], '?&#'), 0, -4) . '.min.css' . $query . $q;
            }, $m[0]);
        }, $value);
    }
    if (false !== strpos($value, '<script ') && (!array_key_exists('active', $options['JS'] ?? []) || !empty($options['JS']['active'])) && !empty($options['HTML']['files']['js'])) {
        $value = preg_replace_callback('/<script\s(?>"[^"]*"|\'[^\']*\'|[^>]+)+>/', static function ($m) use ($assets_root, $assets_url, $options) {
            if (false === strpos($m[0], 'src=')) {
                return $m[0];
            }
            return preg_replace_callback('/\bsrc=("[^"]+"|\'[^\']+\'|[^\s>]+)/', static function ($m) use ($assets_root, $assets_url, $options) {
                if ('"' === ($q = $m[1][0]) && $q === substr($m[1], -1) || "'" === ($q = $m[1][0]) && $q === substr($m[1], -1)) {
                    $m[1] = \substr($m[1], 1, -1);
                } else {
                    $q = "";
                }
                if ('/' === ($m[1][0] ?? 0)) {
                    $m[1] = $assets_url . $m[1];
                }
                if (0 !== strpos($m[1], $assets_url . '/')) {
                    return $m[0];
                }
                if (!$file = stream_resolve_include_path($assets_root . substr(strtok($m[1], '?&#'), strlen($assets_url)))) {
                    return $m[0];
                }
                if ('.min.js' === substr($file, -7)) {
                    return $m[0];
                }
                $file_new = substr($file, 0, -3) . '.min.js';
                if (!is_file($file_new) || filemtime($file) > filemtime($file_new)) {
                    file_put_contents($file_new, x\minify\j_s(file_get_contents($file)));
                }
                $v = dechex(filemtime($file));
                if ($query = strstr($m[1], '?')) {
                    $a = explode('#', $query, 2);
                    parse_str($a, $r);
                    if (isset($r['v'])) {
                        $r[$v] = 1;
                    } else {
                        $r['v'] = $v;
                    }
                    $query = http_build_query($r) . (isset($a[1]) ? '#' . $a[1] : "");
                } else {
                    $a = explode('#', $m[1], 2);
                    $query = '?v=' . $v . (isset($a[1]) ? '#' . $a[1] : "");
                }
                return 'src=' . $q . substr(strtok($m[1], '?&#'), 0, -3) . '.min.js' . $query . $q;
            }, $m[0]);
        }, $value);
    }
    return $value;
}

// Register a plugin hook to execute after the response body is ready
Kirby::plugin('taufik-nurrohman/minify', [
    'hooks' => [
        'route:after' => function ($route, $path, $method, $result) {
            // Apply the compressor only to the `GET` request
            if ('GET' !== $method) {
                return $result;
            }
            $url = kirby()->urls();
            // Do not apply the compressor to the Kirby’s API
            if (0 === strpos($url->current . '/', $url->api . '/')) {
                return $result;
            }
            // Do not apply the compressor to the Kirby’s Media
            if (0 === strpos($url->current . '/', $url->media . '/')) {
                return $result;
            }
            // Do not apply the compressor to the Kirby’s Panel
            if (0 === strpos($url->current . '/', $url->panel . '/')) {
                return $result;
            }
            $extensions = $tests = $types = [];
            $options = (array) (option('taufik-nurrohman.minify') ?? []);
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
                        if (call_user_func($v, (string) $result) && is_callable($f = $options[$k]['f'] ?? 0)) {
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
            'f' => '__minify',
            'files' => [
                'css' => true,
                'js' => true,
                'svg' => true
            ],
            'test' => function (string $v) {
                $v = trim($v);
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
            'test' => function (string $v) {
                $v = trim($v);
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
                'svg' => true,
                'xht' => true,
                'xhtm' => true,
                'xhtml' => true,
                'xml' => true
            ],
            'f' => "x\\minify\\x_m_l",
            'test' => function (string $v) {
                $v = trim($v);
                return '<?xml' === trim(strtolower(strtok($v, " \n\r\t")), '?');
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