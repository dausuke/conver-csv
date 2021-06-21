<?php

namespace processing;

require_once 'output.php';

use output\outputCsv;

class processingCsv
{
    public $spreadsheet_id;
    public $spreadsheet_service;

    //インスタンスの初期化とシートを作成
    public function init()
    {
        $outputSheet = new outputCsv;
        list($spreadsheet_id, $client)  = $outputSheet->output();
        $spreadsheet_service = new \Google_Service_Sheets($client);

        $this->spreadsheet_id = $spreadsheet_id;
        $this->spreadsheet_service = $spreadsheet_service;
    }
    //シート名の変更
    public function changeSheetName()
    {
        $this->init();
        $spreadsheet_service = $this->spreadsheet_service;
        $spreadsheet_id = $this->spreadsheet_id;

        //シートから何月のデータなのか取得
        $range = 'シート1!B2';
        $response = $spreadsheet_service->spreadsheets_values->get($spreadsheet_id, $range);
        $values = $response->getValues();
        $month = substr($values[0][0], 0, 7);

        $response = $spreadsheet_service->spreadsheets->get($spreadsheet_id);
        $sheets = $response->getSheets();

        foreach ($sheets as $sheet) {

            $properties = $sheet->getProperties();
            $sheet_id = $properties->getSheetId();
            $sheet_index = $properties->getIndex();
            switch ($sheet_index) {
                case 1:
                    $request_data = [
                        'updateSheetProperties' => [
                            'properties' => [
                                'sheetId' => $sheet_id,
                                'title' => $month . '日割売上'
                            ],
                            'fields' => 'title'
                        ],
                    ];
                    $requests = [new \Google_Service_Sheets_Request($request_data)];
                    $batchUpdateRequest = new \Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
                        'requests' => $requests
                    ]);
                    $response = $spreadsheet_service->spreadsheets->batchUpdate($spreadsheet_id, $batchUpdateRequest);
                    $response->getReplies();
                    break;
                case 2:
                    $request_data = [
                        'updateSheetProperties' => [
                            'properties' => [
                                'sheetId' => $sheet_id,
                                'title' => $month .'週間売上'
                            ],
                            'fields' => 'title'
                        ],
                    ];
                    $requests = [new \Google_Service_Sheets_Request($request_data)];
                    $batchUpdateRequest = new \Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
                        'requests' => $requests
                    ]);
                    $response = $spreadsheet_service->spreadsheets->batchUpdate($spreadsheet_id, $batchUpdateRequest);
                    $response->getReplies();
                    break;
                case 3:
                    $request_data = [
                        'updateSheetProperties' => [
                            'properties' => [
                                'sheetId' => $sheet_id,
                                'title' => $month .'月間売上'
                            ],
                            'fields' => 'title'
                        ],
                    ];
                    $requests = [new \Google_Service_Sheets_Request($request_data)];
                    $batchUpdateRequest = new \Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
                        'requests' => $requests
                    ]);
                    $response = $spreadsheet_service->spreadsheets->batchUpdate($spreadsheet_id, $batchUpdateRequest);
                    $response->getReplies();
                    break;
            }
        }
        $this->processingSheets();
    }
    public function processingSheets()
    {
        $spreadsheet_service = $this->spreadsheet_service;
        $spreadsheet_id = $this->spreadsheet_id;

        $response = $spreadsheet_service->spreadsheets->get($spreadsheet_id);
        $sheets = $response->getSheets();

        foreach ($sheets as $sheet) {
            $properties = $sheet->getProperties();
            $sheet_id = $properties->getSheetId();

            $request_data = [
                'repeatCell' => [
                    'fields' => 'userEnteredFormat(backgroundColor)',
                    'range' => [
                        'sheetId' => $sheet_id,
                        'startRowIndex' => 0,
                        'endRowIndex' => 1,
                        'startColumnIndex' => 1,
                        'endColumnIndex' => 5,
                    ],
                    'cell' => [
                        'userEnteredFormat' => [
                            'backgroundColor' => [
                                'red' => 229 / 255,
                                'green' => 229 / 255,
                                'blue' => 229 / 255
                            ]
                        ],
                    ],
                ],
            ];
            $requests = [new \Google_Service_Sheets_Request($request_data)];
            $batchUpdateRequest = new \Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
                'requests' => $requests
            ]);
            $response = $spreadsheet_service->spreadsheets->batchUpdate($spreadsheet_id, $batchUpdateRequest);
            $response->getReplies();
            $request_data = [
                'repeatCell' => [
                    'fields' => 'userEnteredFormat(horizontalAlignment)',
                    'range' => [
                        'sheetId' => $sheet_id,
                        'startRowIndex' => 0,
                        'endRowIndex' => 1,
                        'startColumnIndex' => 1,
                        'endColumnIndex' => 5,
                    ],
                    'cell' => [
                        'userEnteredFormat' => [
                            'horizontalAlignment' => 'CENTER'   // 位置のパラメータ ※
                        ],
                    ],
                ],
            ];
            $requests = [new \Google_Service_Sheets_Request($request_data)];
            $batchUpdateRequest = new \Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
                'requests' => $requests
            ]);
            $response = $spreadsheet_service->spreadsheets->batchUpdate($spreadsheet_id, $batchUpdateRequest);
            $response->getReplies();
        }
    }
}
