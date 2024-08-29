<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= $title_pdf;?></title>
        <style>
            #table {
                font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
                border-collapse: collapse;
                width: 100%;
            }

            #table td, #table th {
                border: 1px solid #ddd;
                padding: 5px;
            }

            #table tr:nth-child(even){background-color: #f2f2f2;}

            #table tr:hover {background-color: #ddd;}

            #table th {
                padding-top: 6px;
                padding-bottom: 6px;
                text-align: left;
                background-color: #3085C3;
                color: white;
            }
        </style>
    </head>
    <body>
        <!-- <div style="text-align:center">
            <h4> <?= $title_pdf?></h4>
            <h5> <?= $title_pdf1?></h5>
        </div> -->
        <table>
            <tr>
                <td><h4> <?= $title_pdf?></h4></td>
                <td style="text-align: right;"><h4> <?= $title_pdf1?></h4></td>
            </tr>
        </table>
        <table id="table">
            <thead>
                <tr>
                    <th>No.</th>
                    <!-- <th>Kode</th> -->
                    <th>Tanggal</th>
                    <th>Aktifitas</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
            <?php $no = 0; foreach ($aktifitas as $item):?>
                <tr>
                    <td scope="row" width="5%"><?= $no+1; $no++;?></td>
                    <!-- <td><?= $item->kode?></td> -->
                    <td width="15%"><?= $item->tanggal?></td>
                    <td width="30%"><?= $item->aktifitas?></td>
                    <td width="50%"><?= $item->keterangan?></td>
                </tr>
                <?php endforeach;?>
            </tbody>
        </table>
    </body>
</html>