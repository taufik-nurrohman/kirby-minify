Kirby Minify
============

CSS, HTML, JavaScript, JSON, PHP, and XML compressor for [Kirby CMS](https://github.com/getkirby). This plugin has been
tested with Kirby 5 with responses in the form of regular pages, JSON, and XML.

_Just plug and play!_

Installation
------------

> [!TIP]
>
> The `your-plugin-name\` is the name you want to use for the plugin. The recommended plugin name is `minify\`, but you
> may want to use a different name for certain purposes. For example, Kirby does not have a hook priority feature, so
> the order in which hooks are executed depends entirely on the name of the plugin folder. This plugin should ideally be
> executed at the very end, so it is a good idea to give it a folder name that allows it to be positioned at the very
> end of the plugin list.

### Download

Download the plugin via the
[download link](https://github.com/taufik-nurrohman/kirby-minify/archive/refs/tags/v1.1.0.zip) and extract. Then put the
extracted folder into the `.\site\plugins\` folder of your Kirby project. If the `plugins\` folder does not exist yet,
create it first. The resulting folder structure should look like this:

~~~ txt
.\
└── site\
    └── plugins\
        └── your-plugin-name\
            ├── index.php
            └── …
~~~

### Git

If you use Git to version control your project, you can install plugins that are available from an online service like
GitHub as a Git sub-module:

~~~ sh
git submodule add https://github.com/taufik-nurrohman/kirby-minify.git site/plugins/your-plugin-name
~~~

### Composer

This installation method works best if your project is managed via Composer as well. Then you can run the following
command from the root of your project:

~~~ sh
composer require taufik-nurrohman/kirby-minify
~~~

Options
-------

Kirby configuration settings for plugins go into `.\site\config\config.php`. The configuration file contains a return
statement with an array of configuration options:

~~~ php
<?php

return [
    'taufik-nurrohman.minify' => [
        'CSS' => [ /* … */ ],
        'HTML' => [
            // This will cause the HTML minifier to be disabled.
            'active' => false
        ],
        'JS' => [
            'extensions' => [
                // Disable JavaScript minification if you have a dynamic JavaScript file that ends with `.mjs` extension.
                // By default, it will be enabled for dynamic files that end with both `.js` and `.mjs` extensions.
                'mjs' => false
            ]
        ],
        'JSON' => [
            'types' => [
                // Disable JSON minification if the current MIME type of the dynamic JSON file is `application/feed+json`.
                // Other dynamic JSON files with other MIME types will continue to be minified.
                'application/feed+json' => false
            ]
        ],
        // PHP minifier does nothing in general, unless you want to use its function (the `x\minify\p_h_p()` function) to
        // make some kind of PHP minifier in the control panel with the click of a button. Configuration options for this
        // minifier will have no effect.
        // 'PHP' => [],
        'XML' => [
            // Provide custom XML minification function to replace the default XML minification function (the `x\minify\x_m_l()` function).
            'f' => function (?string $value): ?string {
                return preg_replace('/>\s*</', '><', (string) $value);
            },
            // Provide custom XML file detection based on its content in case the current dynamic file extension is not
            // detected as XML file and the current dynamic file MIME type is also not detected as XML file.
            'test' => function (string $value) {
                return '<?xml' === strtolower(strtok($value, " \n\r\t?"));
            }
        ]
    ]
];
~~~

Tests
-----

The [@taufik-nurrohman/minify](https://github.com/taufik-nurrohman/minify) project has it’s own tests. Here is an easy
way you can do to run various tests specifically for this plugin:

 1. Create a `.\content\my-content\test.txt` file.
 2. Create a `.\site\templates\test.css.php` file and paste in some random CSS code there to test the CSS minifier in
    action on dynamic CSS file that can be accessed via `http://127.0.0.1/my-content.css`.
 3. Create a `.\site\templates\test.js.php` file and paste in some random JavaScript code there to test the JavaScript
    minifier in action on dynamic JavaScript file that can be accessed via `http://127.0.0.1/my-content.js`.
 4. Create a `.\site\templates\test.json.php` file and paste in some random JSON code there to test the JSON minifier in
    action on dynamic JSON file that can be accessed via `http://127.0.0.1/my-content.json`.
 5. Create a `.\site\templates\test.xml.php` file and paste in some random XML code there to test the XML minifier in
    action on dynamic XML file that can be accessed via `http://127.0.0.1/my-content.xml`.
 6. To test the HTML file minification, there is nothing you have to do. Simply go to your front-end site and view the
    source code! 😉

License
-------

This library is licensed under the [MIT License](LICENSE). Please consider
[donating 💰](https://github.com/sponsors/taufik-nurrohman) if you benefit financially from this library.

Notes
-----

 - Bug reports and feature requests related to Kirby CMS can be submitted [here][bug/kirby-minify]. Bug reports related
   to compression (e.g. compression of inline CSS and JavaScript code causes them to break) can be submitted
   [here][bug/minify].
 - The `minify\` folder in this project contains the contents of the
   [@taufik-nurrohman/minify](https://github.com/taufik-nurrohman/minify) repository. Ideally, it should be set as a
   [Git sub-module](https://git-scm.com/book/en/v2/Git-Tools-Submodules), however I decided to do a manual copy and
   paste of the files and folders to make it easier for users to install this plugin. Not everyone understands how to
   bring Git sub-modules into the project after doing a `git clone`. Git archive also won’t merge sub-modules
   automatically into the package.
 - Don’t confuse the internal plugin name with the GitHub repository name (and also with the Composer package name). As
   explained in [plugin best practices](https://github.com/getkirby/getkirby.com/blob/e54f7c8b5bfe9e53415899e7939b09de03f206b9/content/docs/1_guide/17_plugins/7_best-practices/guide.txt#L94)
   and [custom plugin naming conventions](https://github.com/getkirby/getkirby.com/blob/e54f7c8b5bfe9e53415899e7939b09de03f206b9/content/docs/1_guide/17_plugins/1_custom-plugins/guide.txt#L55-L57),
   it is best to name the plugin without the `kirby-` prefix if you want the plugin to be listed properly in the
   [official plugin repository](https://getkirby.com/plugins) later.

 [bug/kirby-minify]: https://github.com/taufik-nurrohman/kirby-minify/issues/new
 [bug/minify]: https://github.com/taufik-nurrohman/minify/issues/new