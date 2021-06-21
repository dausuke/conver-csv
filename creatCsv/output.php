<?php

namespace output;

require __DIR__ . '/vendor/autoload.php';
require_once 'read.php';

use read\readCsv;

class outputCsv
{
    //スプレッドシート設定
    public function sheetConfig()
    {
        $credentials_path = 'sereal-traction-ddac26584e73.json';
        $client = new \Google_Client();

        $client->setScopes([
            \Google_Service_Sheets::SPREADSHEETS, // スプレッドシート
            \Google_Service_Sheets::DRIVE, // ドライブ
        ]);
        $client->setAuthConfig($credentials_path);
        return $client;
    }

    public function output()
    {
        $client = $this->sheetConfig();
        //$spreadsheet = new creatSheet;

        //スプレッド作成実行
        $spreadsheet_id = '1DWEeUcCBPJJgH-rt72lPbU6nMJNmsLN_oxxY1BLUi4E';
        //$client = $spreadsheet->sheetConfig();

        $spreadsheet_service = new \Google_Service_Sheets($client);

        for ($i = 1; $i < 4; $i++) {
            //シートを追加
            $body = new \Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
                'requests' => [
                    'addSheet' => [
                        'properties' => [
                            'title' => 'シート' . $i
                        ]
                    ]
                ]
            ]);
            $response = $spreadsheet_service->spreadsheets->batchUpdate($spreadsheet_id, $body);
            $response->getReplies()[0]
                ->getAddSheet()
                ->getProperties();

            //シート書き込み
            $values = $this->toArrayCsvData($i);
            $range = "シート{$i}!B1:M60";
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
        return [$spreadsheet_id, $client];
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
