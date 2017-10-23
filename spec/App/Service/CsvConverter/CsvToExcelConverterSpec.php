<?php

namespace spec\App\Service\CsvConverter;

use App\Service\CsvConverter\CsvToExcelConverter;
use org\bovigo\vfs\vfsStream;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CsvToExcelConverterSpec extends ObjectBehavior
{
    private $phpExcel;
    private $reader;
    private $writer;

    function let(
        \PHPExcel $phpExcel,
        \PHPExcel_Reader_CSV $reader,
        \PHPExcel_Writer_Excel5 $writer
    )
    {
        $this->phpExcel = $phpExcel;
        $this->reader = $reader;
        $this->writer = $writer;

        $this->beConstructedWith(
            $phpExcel,
            $reader,
            $writer
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CsvToExcelConverter::class);
    }

    function it_converts_csv_to_excel()
    {
        $temp = vfsStream::setup('temp');
        $csv = vfsStream::newFile('csv.csv')->at($temp)->url();
        $excel = vfsStream::newFile('excel.xls')->at($temp)->url();

        $this->reader->loadIntoExisting($csv, $this->phpExcel)->shouldBeCalled();
        $this->writer->save($excel)->shouldBeCalled();

        $this->setTempDirectory($temp->url());
        $this->convert('test,test,test');

        expect(file_get_contents($csv))->toBe('test,test,test');
    }
}
