<?php

foreach (glob(substr(__FILE__, 0, -4) . DIRECTORY_SEPARATOR . '*.php', GLOB_NOSORT) as $v) {
    require $v;
}