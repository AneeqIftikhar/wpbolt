<?php

namespace MatthiasMullie\PathConverter;

/**
 * Convert file paths.
 *
 * Please report bugs on https://github.com/matthiasmullie/path-converter/issues
 *
 * @author Matthias Mullie <pathconverter@mullie.eu>
 * @copyright Copyright (c) 2015, Matthias Mullie. All rights reserved
 * @license MIT License
 */
if (!interface_exists('ConverterInterface')) {
    interface ConverterInterface
    {
        public function convert($path);
    }
}
