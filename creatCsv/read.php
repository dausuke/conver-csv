<?php

namespace read;

require_once 'processingSheet.php';

use Google\Service\AIPlatformNotebooks\Location;
use processing\processingCsv;

ini_set('display_errors', 1);

//--------読み込み--------
class readCsv extends calculate
{
    static $saveCsvPath;

    //CSV読み取りと保存
    public function saveFile()
    {
        $file = $_FILES['upcsv'];
        if (!isset($file) && $file['error'] != 0) {
            exit('Error:ファイルが送信されていません');
        } else {
            $temp_path = $file['tmp_name'];
            $directory_path = '../input/';  //画像保存ファイル
            $unique_name = date('YmdHis') . ".csv";   //保存した日付とランダム文字列.拡張子
            $filename_to_save = $directory_path . $unique_name;     //保存先のパス

            if (!is_uploaded_file($temp_path)) {
                exit('Error:ファイルがありません');
            } elseif (!move_uploaded_file($temp_path, $filename_to_save)) {
                exit('Error:アップロードできませんでした');
            } else {
                chmod($filename_to_save, 0644); // 権限の変更
            }
            self::$saveCsvPath = $filename_to_save;
        }
    }
    //合計
    public function readCsvTotal()
    {
        $readCsv = fopen(self::$saveCsvPath, 'r');
        flock($readCsv, LOCK_EX);

        if ($readCsv) {
            $readData = [];
            while ($line = fgets($readCsv)) {
                $arrayData = explode(",", $line);
                array_push($readData, $arrayData);
            }
        }

        flock($readCsv, LOCK_UN);
        fclose($readCsv);

        $length = count($readData);
        $month = substr($readData[1][7], 1, 7);

        $icePrice = 200;
        $coffeePrice = 290;

        $iceTotal = 0;
        $coffeeTotal = 0;
        $iceSales = 0;
        $coffeeSales = 0;

        for ($i = 1; $i < $length; $i++) {
            $sales = (int)$readData[$i][8];
            //var_dump($length);
            if ($sales % $icePrice === 0) {
                $cnt = $sales / $icePrice;
                $iceTotal += $cnt;
                $iceSales += $sales;
            } elseif ($sales >= $coffeePrice  && $sales % $coffeePrice === 0) {
                $cnt = $sales / $coffeePrice;
                $coffeeTotal += $cnt;
                $coffeeSales += $sales;
            } else {
                $ice = $sales % $coffeePrice;
                $coffee = $sales - $ice;
                $iceCnt = $ice / $icePrice;
                $coffeeCnt = $coffee / $coffeePrice;

                $iceTotal += $iceCnt;
                $coffeeTotal += $coffeeCnt;
                $iceSales += $ice;
                $coffeeSales += $coffee;
            }
        }
        $totalData = [
            $month,
            $iceSales + $coffeeSales,
            $iceTotal,
            $coffeeTotal
        ];

        return $totalData;
    }
    //週ごと
    public function readCsvWeekly()
    {
        $readCsv = fopen(self::$saveCsvPath, 'r');
        flock($readCsv, LOCK_EX);

        if ($readCsv) {
            $readData = [];
            while ($line = fgets($readCsv)) {
                $arrayData = explode(",", $line);
                array_push($readData, $arrayData);
            }
        }
        flock($readCsv, LOCK_UN);
        fclose($readCsv);

        $weeklyData = [];
        $dailyData = parent::calculate($readData, 1);
        // var_dump($dailyData);
        // exit();

        $year = substr($readData[1][7], 1, 4);
        $month = substr($readData[1][7], 6, 2);
        $last_day = date('t', strtotime($year . '-' . $month));

        $manthDay = array();

        for ($i = 1; $i < $last_day + 1; $i++) {
            $week = date('w', mktime(0, 0, 0, (int)$month, $i, (int)$year));

            if ($i == 1) {
                for ($j = 1; $j <= $week; $j++) {
                    array_push($manthDay, false);
                }
            }

            array_push($manthDay, (string)$i);

            if ($i == $last_day) {
                for ($s = 1; $s <= 6 - $week; $s++) {
                    array_push($manthDay, false);
                }
            }
        }
        $manthDay_chunk = array_chunk($manthDay, 7);
        // var_dump($manthDay_chunk);
        // exit();
        foreach ($manthDay_chunk as $key => $value) {
            $salse = 0;
            $ice = 0;
            $coffee = 0;
            foreach ($dailyData as $daily) {
                $date = substr($daily[0], -2);
                if (in_array($date, $value)) {
                    $salse += $daily[1];
                    $ice += $daily[2];
                    $coffee += $daily[3];
                }
                for ($i = 0; $i < 7; $i++) {
                    if ($value[$i]) {
                        $startDay = $value[$i];
                        break;
                    }
                }
                if ($value == end($manthDay_chunk)) {
                    for ($i = 0; $i < 7; $i++) {
                        if (!$value[$i]) {
                            $endDay = $value[$i - 1];
                            break;
                        }
                    }
                } else {
                    $endDay = $value[6];
                }
                $weeklyData[$key][0] = "{$year}/{$month}/{$startDay} ~ {$year}/{$month}/{$endDay}";
                $weeklyData[$key][1] = $salse;
                $weeklyData[$key][2] = $ice;
                $weeklyData[$key][3] = $coffee;
            }
        }
        return $weeklyData;
    }

    //日ごと
    public function readCsvDaily()
    {
        $readCsv = fopen(self::$saveCsvPath, 'r');
        flock($readCsv, LOCK_EX);

        if ($readCsv) {
            $readData = [];
            while ($line = fgets($readCsv)) {
                $arrayData = explode(",", $line);
                array_push($readData, $arrayData);
            }
        }

        flock($readCsv, LOCK_UN);
        fclose($readCsv);

        $dailyData = parent::calculate($readData, 1);
        // var_dump($dailyData);
        // exit();
        return $dailyData;
    }
}

//--------計算--------
class calculate
{
    public function calculate($readData, $min)
    {
        $length = count($readData);

        $icePrice = 200;
        $coffeePrice = 290;

        $recordData = [];

        for ($i = $min; $i < $length; $i++) {
            $sales = (int)$readData[$i][8];
            $date = substr($readData[$i][7], 1, 10);
            if ($sales % $icePrice === 0) {
                $cnt = $sales / $icePrice;
                $dailyTag = [
                    '取引日時' => $date,
                    '販売金額' => $sales,
                    'アイス個数' => $cnt,
                    'コーヒー個数' => '0'
                ];
                array_push($recordData, $dailyTag);
            } elseif ($sales >= $coffeePrice  && $sales % $coffeePrice === 0) {
                $cnt = $sales / $coffeePrice;
                $dailyTag = [
                    '取引日時' => $date,
                    '販売金額' => $sales,
                    'アイス個数' => '0',
                    'コーヒー個数' => $cnt
                ];
                array_push($recordData, $dailyTag);
            } else {
                $ice = $sales % $coffeePrice;
                $coffee = $sales - $ice;
                $iceCnt = $ice / $icePrice;
                $coffeeCnt = $coffee / $coffeePrice;
                $dailyTag = [
                    '取引日時' => $date,
                    '販売金額' => $sales,
                    'アイス個数' => $iceCnt,
                    'コーヒー個数' => $coffeeCnt
                ];
                array_push($recordData, $dailyTag);
            }
        }
        return $this->dateSum($recordData);
    }
    public function dateSum($array)
    {
        $year = substr($array[1]['取引日時'], 0, 4);
        $month = substr($array[1]['取引日時'], 5, 2);
        $last_day = date('t', strtotime($year . '-' . $month));

        $start = substr($array[1]['取引日時'], 0, 8) . '01';
        $end = substr($array[1]['取引日時'], 0, 8) . $last_day;

        $dateArray = [];
        $dailyData = [];

        for ($i = new \DateTime($start); $i <= new \DateTimeImmutable($end); $i->modify('+1 day')) {
            array_push($dateArray, $i->format('Y/m/d'));
        }

        foreach ($dateArray as $date) {
            $salse = 0;
            $ice = 0;
            $coffee = 0;
            foreach ($array as $value) {
                if ($date == $value['取引日時']) {
                    $dailyData[$date] = [
                        $date,
                        $salse += $value['販売金額'],
                        $ice += $value['アイス個数'],
                        $coffee += $value['コーヒー個数']
                    ];
                }
            }
        }
        return array_values($dailyData);
    }
}

//送信チェックとスプレッド作成実行
// if (!isset($_FILES['upcsv'])) {
$extension = pathinfo($_FILES['upcsv']['name'], PATHINFO_EXTENSION);
if ($extension !== 'csv') {
    exit('拡張子が無効です');
} else {
    $execution = new processingCsv;
    $self = new readCsv;
    $self->saveFile();
    $execution->changeSpreadName();
    echo "
            <script>
                alert('スプレッドシートへの変換が完了しました');
                window.location.href = '../../index.php';
            </script>
        ";
    // header('Location: index.php');
}
//}
