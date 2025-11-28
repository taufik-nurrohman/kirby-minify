<?php

namespace x\minify {
    function j_s_o_n(?string $from): ?string {
        if ("" === ($from = \trim($from ?? ""))) {
            return null;
        }
        if ('""' === $from || '[]' === $from || 'false' === $from || 'null' === $from || 'true' === $from || '{}' === $from || \is_numeric($from)) {
            return $from;
        }
        $c1 = ',:[]{}';
        $c2 = " \n\r\t";
        $to = "";
        while ("" !== $from) {
            if ($n = \strspn($from, $c2)) {
                $from = \substr($from, $n);
                continue;
            }
            if ('"' === ($c = $from[0])) {
                $max = \strlen($from);
                $n = 1;
                while ($n < $max) {
                    if ("\\" === $from[$n] && $n + 1 < $max) {
                        $n += 2;
                        continue;
                    }
                    if ('"' === $from[$n]) {
                        ++$n;
                        break;
                    }
                    ++$n;
                }
                $to .= \substr($from, 0, $n);
                $from = \substr($from, $n);
                continue;
            }
            if (false !== \strpos($c1, $c)) {
                $to .= $c;
                $from = \substr($from, 1);
                continue;
            }
            $to .= \substr($from, 0, $n = \strcspn($from, $c1 . $c2));
            $from = \substr($from, $n);
        }
        return "" !== $to ? $to : null;
    }
}