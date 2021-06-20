<?php
namespace creat;

require __DIR__ . '/vendor/autoload.php';
//設定
class creatSheet
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

    //ドライブ設定
    public function driveConfig()
    {
        $email = 'ksye616@gmail.com';
        $drive_permission = new \Google_Service_Drive_Permission();
        $drive_permission->setEmailAddress($email);
        $drive_permission->setType('user');
        $drive_permission->setRole('owner');

        return $drive_permission;
    }
    //スプレッド作成
    public function creat()
    {
        //設定情報の使用
        $client = $this->sheetConfig();
        $drive_permission = $this->driveConfig();

        //シートの作成
        $spreadsheet_service = new \Google_Service_Sheets($client);
        $requestBody = new \Google_Service_Sheets_Spreadsheet([
            'properties' => [
                'title' => "PayPay取引データ"
            ]
        ]);

        $response = $spreadsheet_service->spreadsheets->create($requestBody);       //スプレッド作成
        $spreadsheet_id = $response->spreadsheetId;         //作成したスプレッドのID

        //作成したシートのオーナー権限渡す
        $drive_service = new \Google_Service_Drive($client);
        $drive_service->permissions->create($spreadsheet_id, $drive_permission, [
            'transferOwnership' => 'true' ,  // コピー等の権限あり
            'sendNotificationEmail' => 'false'
        ]);
        return $spreadsheet_id;
    }
}
