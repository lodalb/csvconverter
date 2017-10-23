<?php

require __DIR__ . '/../vendor/autoload.php';

use Phalcon\Mvc\Micro;
use App\Service\CsvConverterService;
use App\Service\CsvConverter\CsvToExcelConverter;

define('STORAGE_DIR', __DIR__ . '/../storage');

// Services

$csvConverterService = new CsvConverterService();

$phpExcel = new PHPExcel();

$csvToExcelConverter = new CsvToExcelConverter(
    $phpExcel,
    new PHPExcel_Reader_CSV(),
    new PHPExcel_Writer_Excel5($phpExcel)
);


// App Routes

$app = new Micro();

$app->post(
    '/',
    function () use ($app, $csvConverterService, $csvToExcelConverter) {
        $postData = $app->request->getPost();

        if (empty($postData['csv'])) {
            throw new \Exception("Missing csv parameter");
        }

        $csvConverterService->convertToFile(
            $postData['csv'],
            STORAGE_DIR . '/' . uniqid() . '.xls',
            $csvToExcelConverter
        );

        return $app->response->redirect("/");
    }
);

$app->get(
    '/{id}',
    function ($id) use ($app) {
        if (empty($id)) {
            $dir = glob(STORAGE_DIR . '/*');
            $files = [];
            foreach ($dir as $filepath) {
                $files[] = [
                    'file' =>  substr(strrchr($filepath, '/'), 1),
                    'date' => date('Y-m-d H:i:s', filemtime($filepath))
                ];
            }

            $app->response->setContentType('application/json');
            $app->response->sendHeaders();
            return json_encode($files);
        }

        $file = STORAGE_DIR . '/' . $id;
        if (!file_exists($file)) {
            throw new \Exception('File not found');
        }

        $app->response->setContentType('application/xls');
        $app->response->sendHeaders();
        readfile($file);
    }
);

try {
    $app->handle();
} catch (\Exception $e) {
    header("Content-type: application/json");
    echo json_encode(['error' => $e->getMessage()]);
}
