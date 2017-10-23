<?php

namespace App\Service\CsvConverter;

class CsvToExcelConverter implements CsvConverterInterface
{
    private $tempDirectory = __DIR__ . '/../../../../tmp';

    /**
     * @var \PHPExcel
     */
    private $phpExcel;

    /**
     * @var \PHPExcel_Reader_CSV
     */
    private $reader;

    /**
     * @var \PHPExcel_Writer_Excel5
     */
    private $writer;

    /**
     * @param \PHPExcel $phpExcel
     * @param \PHPExcel_Reader_CSV $reader
     * @param \PHPExcel_Writer_Excel5 $writer
     */
    public function __construct(
        \PHPExcel $phpExcel,
        \PHPExcel_Reader_CSV $reader,
        \PHPExcel_Writer_Excel5 $writer
    )
    {
        $this->phpExcel = $phpExcel;
        $this->reader = $reader;
        $this->writer = $writer;
    }

    public function setTempDirectory($tempDirectory)
    {
        $this->tempDirectory = $tempDirectory;
    }

    /**
     * @param string $csv
     * @return bool|string
     */
    public function convert($csv)
    {
        $tempCsv = $this->tempDirectory . '/csv.csv';
        $tempExcel = $this->tempDirectory . '/excel.xls';

        if (!is_writable($this->tempDirectory)) {
            throw new \Exception("Unable to write temporary csv file");
        }

        // PHPExcel can't read csv from a string :(
        // So we have to create a temporary file
        file_put_contents($tempCsv, $csv);

        $this->phpExcel = $this->reader->loadIntoExisting($tempCsv, $this->phpExcel);

        $this->writer->save($tempExcel);

        // Return raw output instead of filename so future CSVConverters can be more flexible with output.
        return file_get_contents($tempExcel);
    }
}
