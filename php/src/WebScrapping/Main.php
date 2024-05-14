<?php

namespace Chuva\Php\WebScrapping;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Runner for the Webscrapping exercice.
 */
class Main {
  /**
   * Main runner, instantiates a Scrapper and runs.
   */
  public static function run(): void {
    $dom = new \DOMDocument('1.0', 'utf-8');
    @$dom->loadHTMLFile(__DIR__ . '/../../assets/origin.html');
    $data = (new Scrapper())->scrap($dom);
    self::saveDataToExcel($data);
  }

  /** Save data to an Excel file */
  public static function saveDataToExcel(array $data): void {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $headers = [
        'ID', 'Title', 'Type', 'Author 1', 'Author 1 Institution', 'Author 2', 'Author 2 Institution',
        'Author 3', 'Author 3 Institution', 'Author 4', 'Author 4 Institution', 'Author 5', 'Author 5 Institution',
        'Author 6', 'Author 6 Institution', 'Author 7', 'Author 7 Institution', 'Author 8', 'Author 8 Institution',
        'Author 9', 'Author 9 Institution'
    ];

    $columnIndex = 'A';
    foreach ($headers as $header) {
        $sheet->setCellValue($columnIndex++ . '1', $header);
    }

    $rowIndex = 2;
    foreach ($data as $row) {
        $sheet->fromArray([$row['id'], $row['title'], $row['type']], null, 'A' . $rowIndex);
        $authors = explode(';', $row['authors']);
        $columnIndex = 'D';
        foreach ($authors as $author) {
            $details = explode(',', $author);
            $sheet->setCellValue($columnIndex++ . $rowIndex, trim($details[0]));
            $sheet->setCellValue($columnIndex++ . $rowIndex, trim($details[1] ?? ''));
        }
        $rowIndex++;
    }

    $dirPath = __DIR__ . '/../../data/';
    if (!is_dir($dirPath)) mkdir($dirPath, 0777, true);
    $filePath = $dirPath . 'model.xlsx';
    (new Xlsx($spreadsheet))->save($filePath);
    echo "Data has been saved to '{$filePath}'\n";
  }
}
