<?php

namespace spec\App\Service;

use App\Service\CsvConverter\CsvConverterInterface;
use App\Service\CsvConverterService;
use org\bovigo\vfs\vfsStream;
use PhpSpec\ObjectBehavior;

class CsvConverterServiceSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(CsvConverterService::class);
    }

    function it_converts_csv_string_using_converter(CsvConverterInterface $converter)
    {
        $converter->convert('test,test,test')->shouldBeCalled();

        $this->convert('test,test,test', $converter);
    }

    function it_converts_csv_to_file(CsvConverterInterface $converter)
    {
        $vfs = vfsStream::setup('storage');
        $file = vfsStream::newFile('file.xls')->at($vfs)->url();

        $converter->convert('file,file,file')->willReturn('output file');

        $this->convertToFile('file,file,file', $file, $converter)->shouldReturn($file);

        expect(file_get_contents($file))->toBe('output file');
    }

    function it_throws_exception_if_unable_to_write_file(CsvConverterInterface $converter)
    {
        $vfs = vfsStream::setup('storage')->chmod(0);

        $this->shouldThrow(new \Exception("Unable to write file: vfs://storage/unwritable.xls"))
            ->duringConvertToFile('file', 'vfs://storage/unwritable.xls', $converter);
    }
}
