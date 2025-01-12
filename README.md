Kirby Minify
============

CSS, HTML, JavaScript, JSON, PHP, and XML compressor for [Kirby CMS](https://github.com/getkirby). Just plug and play!
This plugin has been tested with Kirby 5 with responses in the form of regular pages, JSON, and XML.

Notes
-----

Bug reports and feature requests related to Kirby CMS can be submitted [here][bug/kirby-minify]. Bug reports related to
compression (e.g. compression of inline CSS and JavaScript code causes them to break) can be submitted
[here][bug/minify].

 [bug/kirby-minify]: https://github.com/taufik-nurrohman/kirby-minify/issues/new
 [bug/minify]: https://github.com/taufik-nurrohman/minify/issues/new

Installation Methods
--------------------

> [!TIP]
>
> `your-plugin-name` is the name you want to use for the plugin. The recommended plugin name is `minify`, but you may
> want to use a different name for certain purposes. For example, Kirby does not have a hook priority feature, so the
> order in which hooks are executed depends entirely on the name of the plugin folder. This plugin should ideally be
> executed at the very end, so it is a good idea to give it a folder name that allows it to be positioned at the very
> end of the plugin list.

### Download

Download the plugin via the
[download link](https://github.com/taufik-nurrohman/kirby-minify/archive/refs/tags/v1.0.0.zip) and extract. Then put the
extracted folder into the `/site/plugins/` folder of your Kirby project. If the `plugins` folder does not exist yet,
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

License
-------

This library is licensed under the [MIT License](LICENSE). Please consider
[donating 💰](https://github.com/sponsors/taufik-nurrohman) if you benefit financially from this library.