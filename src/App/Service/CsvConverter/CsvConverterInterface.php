<?php

namespace App\Service\CsvConverter;

interface CsvConverterInterface
{
    /**
     * @param string $csv
     * @return string
     */
    public function convert($csv);
}
