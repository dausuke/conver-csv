<?php

namespace output;

require_once 'creat.php';
require_once 'read.php';

use creat\creatSheet;
use read\readCsv;

class outputCsv
{
    public function output()
    {
        $spreadsheet = new creatSheet;

        //スプレッド作成実行
        $spreadsheet_id = $spreadsheet->creat();
        $client = $spreadsheet->sheetConfig();

        $spreadsheet_service = new \Google_Service_Sheets($client);

        //シートを追加
        for ($i = 2; $i < 4; $i++) {
            $body = new \Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
                'requests' => [
                    'addSheet' => [
                        'properties' => [
                            'title' => 'Sheet' . $i
                        ]
                    ]
                ]
            ]);
            $response = $spreadsheet_service->spreadsheets->batchUpdate($spreadsheet_id, $body);
            $response->getReplies()[0]
                ->getAddSheet()
                ->getProperties();
        }
        //シート書き込み
        for ($i = 1; $i < 4; $i++) {
            $values = $this->toArrayCsvData($i);
            $range = "Sheet{$i}!B1:M60";
            $body = new \Google_Service_Sheets_ValueRange([
                'values' => $values
            ]);
            $body->setValues($values);
            $params = ['valueInputOption' => 'USER_ENTERED'];
            $result = $spreadsheet_service->spreadsheets_values->update(        //書き込み実行
                $spreadsheet_id,
                $range,
                $body,
                $params
            );
            $result->getUpdatedCells();
        }
        return $spreadsheet_id;
    }

    //csvデータ読み取り実行
    public function toArrayCsvData($i)
    {
        $readCsv = new readCsv;

        //繰り返しのカウントに応じて作成する配列を変更
        switch ($i) {
            case 1:
                $csvArray = $readCsv->readCsvDaily();
                break;
            case 2:
                $csvArray = $readCsv->readCsvWeekly();
                break;
            case 3:
                $csvArray = [];
                $csvArrayTag = $readCsv->readCsvTotal();
                array_push($csvArray, $csvArrayTag);
                break;
        }
        array_unshift($csvArray, ['取引期間', '売上', 'アイス個数', 'コーヒー個数']);
        return $csvArray;
    }
}
