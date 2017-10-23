<?php

namespace App\Service;

use App\Service\CsvConverter\CsvConverterInterface;

class CsvConverterService
{
    /**
     * @param $csv
     * @param CsvConverterInterface $converter
     * @return string
     */
    public function convert($csv, CsvConverterInterface $converter)
    {
        return $converter->convert($csv);
    }

    /**
     * @param $csv
     * @param $outputFile
     * @param CsvConverterInterface $converter
     * @return string
     */
    public function convertToFile($csv, $outputFile, CsvConverterInterface $converter)
    {
        if (!is_writable(dirname($outputFile))) {
            throw new \Exception("Unable to write file: $outputFile");
        }

        $output = $this->convert($csv, $converter);

        file_put_contents($outputFile, $output);

        return $outputFile;
    }
}
