PHP Minify
==========

![c-s-s.php] ![h-t-m-l.php] ![j-s.php] ![j-s-o-n.php] ![p-h-p.php] ![x-m-l.php]

[c-s-s.php]: https://img.shields.io/github/size/taufik-nurrohman/minify/index/c-s-s.php?branch=main&color=%234f5d95&label=c-s-s.php&labelColor=%231f2328&style=flat-square
[h-t-m-l.php]: https://img.shields.io/github/size/taufik-nurrohman/minify/index/h-t-m-l.php?branch=main&color=%234f5d95&label=h-t-m-l.php&labelColor=%231f2328&style=flat-square
[j-s.php]: https://img.shields.io/github/size/taufik-nurrohman/minify/index/j-s.php?branch=main&color=%234f5d95&label=j-s.php&labelColor=%231f2328&style=flat-square
[j-s-o-n.php]: https://img.shields.io/github/size/taufik-nurrohman/minify/index/j-s-o-n.php?branch=main&color=%234f5d95&label=j-s-o-n.php&labelColor=%231f2328&style=flat-square
[p-h-p.php]: https://img.shields.io/github/size/taufik-nurrohman/minify/index/p-h-p.php?branch=main&color=%234f5d95&label=p-h-p.php&labelColor=%231f2328&style=flat-square
[x-m-l.php]: https://img.shields.io/github/size/taufik-nurrohman/minify/index/x-m-l.php?branch=main&color=%234f5d95&label=x-m-l.php&labelColor=%231f2328&style=flat-square

Motivation
----------

This project was started as a [gist][gist/minify], which now has more stars than [the copy of it][mecha-cms/x.minify],
that was actually made to abandon the gist. It was inspired by [a code snippet][ideone], that will probably get lost in
the future, so I decided to make [a copy of it][gist/ideone].

I once got an e-mail from someone who wanted to use some parts of [my extension’s code][mecha-cms/x.minify], for him to
use in a proprietary application, hoped not to be bound by [the GPL restrictions][article/gpl]. It was not possible
legally at first, due to the nature that an extension will always be a part of its core application. And its core
application, in this case, were using the GPL license from the start. And so, its extensions have to be licensed under
the GPL license too.

I then decided to completely re-write this project under the MIT license because I no longer view this implementation as
a “complex thing” that it takes a lot of effort to program it. I also want to make my implementation generally available
to a wide range of people (including those who develop proprietary applications), so it will be easier for me to get
financial support from them.

[article/gpl]: https://mecha-cms.com/article/general-public-license
[gist/ideone]: https://gist.github.com/taufik-nurrohman/db723da29e69065a1130
[gist/minify]: https://gist.github.com/taufik-nurrohman/d7b310dea3b33e4732c0/804ae266c30664e7dcdf1d7d544628f7790bdad8
[ideone]: https://ideone.com/Q5USEF
[mecha-cms/x.minify]: https://github.com/mecha-cms/x.minify

Usage
-----

This converter can be installed using [Composer](https://packagist.org/packages/taufik-nurrohman/minify), but it doesn’t
need any other dependencies and just uses Composer’s ability to automatically include files. Those of you who don’t use
Composer should be able to include the `index.php` file directly into your application without any problems.

### Using Composer

From the command line interface, navigate to your project folder then run this command:

~~~ sh
composer require taufik-nurrohman/minify
~~~

Require the generated auto-loader file in your application:

~~~ php
<?php

use function x\minify\c_s_s as minify_css;
use function x\minify\h_t_m_l as minify_html;
use function x\minify\j_s as minify_js;
use function x\minify\j_s_o_n as minify_json;
use function x\minify\p_h_p as minify_php;
use function x\minify\x_m_l as minify_xml;

require 'vendor/autoload.php';

echo minify_css('asdf { asdf: 0px; } asdf { /* asdf */ }'); // Returns `'asdf{asdf:0}'`
~~~

### Using File

Require the `index.php` file in your application:

~~~ php
<?php

use function x\minify\c_s_s as minify_css;
use function x\minify\h_t_m_l as minify_html;
use function x\minify\j_s as minify_js;
use function x\minify\j_s_o_n as minify_json;
use function x\minify\p_h_p as minify_php;
use function x\minify\x_m_l as minify_xml;

require 'index.php';

echo minify_css('asdf { asdf: 0px; } asdf { /* asdf */ }'); // Returns `'asdf{asdf:0}'`
~~~

Notes
-----

My choice of naming the functions may irritate a lot of people, but it is part of the standard rules that Mecha has
decided to keep them [reversible][article/strict-convert]. You can always make
[a list of function aliases][tweak/functions] in a particular file that you can store in some place, to be included
later in your application. That way, you can use only the function aliases that you find more pleasant to type.

[article/strict-convert]: https://mecha-cms.com/article/strict-convert
[tweak/functions]: #globally-reusable-functions

This converter focuses only on white-space removal. Other optimizations are considered as a bonus. It can’t read your
code and only does generic tokenization, like grouping comments and strings into a single token. It won’t fix your code,
like adding an optional semi-colon at the end of a line because it was followed by a line-break, so the semi-colon was
optional in that situation. But then your JavaScript code will probably break after the minification, because the
required line-break is now gone:

~~~ js
// Before
a()
b()
c()

// After
a()b()c()
~~~

I still don’t quite understand why this way of writing JavaScript has become so popular these days. Someone who
initiated [this coding style][standard/standard] is probably into [Python][python], but he/she doesn’t want to admit it.

[python]: https://github.com/python
[standard/standard]: https://github.com/standard/standard

This CSS code is not valid because a space is required after the `and` token, and the CSS compressor will not insert a
space after it, even when it is possible to do so:

~~~ css
/* Before */
@media (min-width: 1280px)and(max-width: 1919px) {
  color: #f00;
}

/* After */
@media (min-width:1280px)and(max-width:1919px){color:#f00}
~~~

The idea is that **you are responsible for the code you are going to compress**. If the original code works well, then
it is likely that it will work well too after it has been compressed.

Options
-------

### CSS

~~~ php
<?php

c_s_s(?string $from): ?string;
~~~

### HTML

~~~ php
<?php

h_t_m_l(?string $from): ?string;
~~~

### JS

~~~ php
j_s(?string $from): ?string;
~~~

### JSON

~~~ php
<?php

j_s_o_n(?string $from): ?string;
~~~

### PHP

~~~ php
<?php

p_h_p(?string $from): ?string;
~~~

### XML

~~~ php
<?php

x_m_l(?string $from): ?string;
~~~

Tests
-----

Clone this repository into the root of your web server that supports PHP and then you can open the `test.php` file with
your browser to see the result and the performance of this converter in various cases.

Tweaks
------

### Globally Reusable Functions

You can use this method to shorten function names globally:

~~~ php
<?php

require 'index.php';

// Or, if you are using Composer…
// require 'vendor/autoload.php';

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
~~~

License
-------

This library is licensed under the [MIT License](LICENSE). Please consider
[donating 💰](https://github.com/sponsors/taufik-nurrohman) if you benefit financially from this library.