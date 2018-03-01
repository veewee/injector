<?php

declare(strict_types=1);

namespace Injector\Console\Style;

use Injector\Exception\InputStreamException;
use Symfony\Component\Console\Style\SymfonyStyle;

class ConsoleStyle extends SymfonyStyle
{
    public function readResource($handle)
    {
        if (!is_resource($handle)) {
            throw InputStreamException::fromInvalidResource($handle);
        }

        stream_set_blocking($handle, false);
        $input = stream_get_contents($handle);

        // When the input only consist of white space characters, we assume that there is no input.
        $code = !preg_match_all('/^([\s]*)$/', $input) ? $input : '';
        if (!$code) {
            throw InputStreamException::fromEmptyInputStream();
        }

        // Strip automatically added newline at EOF
        return mb_substr($code, 0, -1);
    }
}
