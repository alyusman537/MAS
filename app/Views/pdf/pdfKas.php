<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>pdf</title>
    <style>
        #table {
            font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
            font-weight: 25;
            border-collapse: collapse;
            width: 100%;
        }

        #table td,
        #table th {
            border: 1px solid #ddd;
            padding: 5px;
        }

        #table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        #table tr:hover {
            background-color: #ddd;
        }

        #table th {
            padding-top: 6px;
            padding-bottom: 6px;
            text-align: left;
            background-color: #3085C3;
            color: white;
        }

        .column {
            float: left;
            width: 50%;
            /* padding: 10px; */
            /*height: 300px; Should be removed. Only for demonstration */
        }

        /* Clear floats after the columns */
        .row:after {
            content: "";
            display: table;
            clear: both;
        }

        .nominal {
            text-align: right;
        }

        /* .footer .page-number:after {
            content: counter(page);
        } */
        @page {
            margin: 15px 35px 10px;
        }

        #footer {
            position: fixed;
            left: 50px;
            bottom: 0;
            text-align: center;
        }

        #footer .page:after {
            content: counter(page);
        }
    </style>
</head>

<body>
    </div>
    <h4>Laporan Mutasi Kas Al-Wafa Bi'ahdillah</h4>
    <div class="row">
        <div class="column">
            <p>Periode <?= $tanggal_awal . ' s/d ' . $tanggal_akhir; ?></p>
        </div>
        <div class="column">
            <p class="nominal" style="margin-right: 35px;">Tanggal Cetak <?= $tanggal_cetak; ?></p>
        </div>
    </div>


    <table id="table">
        <thead>
            <tr>
                <th>No.</th>
                <!-- <th>Kode</th> -->
                <th>Tanggal</th>
                <th>Keterangan</th>
                <th>Debet</th>
                <th>Kredit</th>
                <th>Saldo</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td></td>
                <td></td>
                <td>Saldo Awal</td>
                <td></td>
                <td></td>
                <td class="nominal"> <?= $saldo_awal; ?></td>
            </tr>
            <?php $no = 0;
            foreach ($mutasi as $item): ?>
                <tr>
                    <td class="nominal" scope="row" width="5%"><?= $no + 1;
                                                                $no++; ?></td>
                    <td width="12%"><?= $item['tanggal'] ?></td>
                    <td width="27%"><?= $item['keterangan'] ?></td>
                    <td class="nominal" width="15%"><?= $item['debet'] ?></td>
                    <td class="nominal" width="15%"><?= $item['kredit'] ?></td>
                    <td class="nominal" width="15%"><?= $item['saldo'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <!--div class="footer fixed-section">
        <div class="right">
            <span class="nominal page-number">Page </span>
        </div>
    </div-->
    <div id="footer">
    <p class="page">Halaman </p>
</div>
</body>

</html>