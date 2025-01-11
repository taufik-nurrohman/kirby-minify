Kirby Minify
============

CSS, HTML, JavaScript, JSON, PHP, and XML compressor for Kirby CMS.

Installation Methods
--------------------

### Download

Download the plugin via the [download link](#) and extract. Then put the extracted folder into the `/site/plugins/` folder of your Kirby project. If the `plugins` folder does not exist yet, create it first. The resulting folder structure should look like this:

~~~ txt
.\
  site\
    plugins\
      your-plugin-name
        index.php
~~~

### Git

If you use Git to version control your project, you can install plugins that are available from an online service like GitHub as a Git sub-module:

~~~ sh
git submodule add https://github.com/taufik-nurrohman/kirby-minify.git site/plugins/your-plugin-name
~~~

### Composer

This installation method works best if your project is managed via Composer as well. Then you can run the following command from the root of your project:

~~~ sh
composer require taufik-nurrohman/kirby-minify
~~~

License
-------

MIT