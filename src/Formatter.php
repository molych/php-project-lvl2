<?php

namespace Gendiff\Formatter;

use function GenDiff\Formatters\Pretty\renderPretty;
use function GenDiff\Formatters\Plain\renderPlain;
use function GenDiff\Formatters\Json\renderJson;

function format($format, $astTree)
{
    switch ($format) {
        case 'pretty':
            return renderPretty($astTree);
        case 'plain':
            return renderPlain($astTree);
        case 'json':
            return renderJson($astTree);
        default:
            throw new \Exception("Unknown fotmat $format");
    }
}
