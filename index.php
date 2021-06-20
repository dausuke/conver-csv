<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <style>
        body {
            background-color: #EEEEEE;
            height: 100vh;
        }
        label {
            border: dashed 3px;
        }
        .border{
            border: thick double #6c757d!important;
        }
        span{
            border-radius: 0!important;
        }
    </style>
</head>

<body>
    <div class="container pt-5 bg-white h-100">
        <header class="row">
            <h1 class="col-12 text-center">CSV変換</h1>
        </header>
        <main class="row justify-content-center mt-5">
            <form class="col-8" action="creatCsv/read.php" method="post" enctype="multipart/form-data">
                <div class="form-group d-flex justify-content-center align-items-center flex-column">
                    <label for="formGroupFileInput" class=" d-flex justify-content-center align-items-center flex-column border-secondary rounded  p-5 m-0 h-100 w-100">
                    <div class="border">
                        <span class="btn btn-secondary">ファイルを選択</span>
                    </div>
                        <input type="file" name="upcsv" accept="text/csv" class="form-control" id="formGroupFileInput" style="display: none;">
                    </label>
                    <button type="submit" class="btn btn-secondary w-50 mt-5">アップロード</button>
                </div>
            </form>
        </main>
    </div>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>

</html>