<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Infaq</title>
    <style>
        #table {
            font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
            font-weight: 25;
            border-collapse: collapse;
            width: 100%;
        }

        #table td {
            border: 1px solid #dddddd;
            font-size: smaller;
            padding-left: 10px;
            padding-right: 10px;
        }
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
            padding-top: 3px;
            padding-bottom: 3px;
            text-align: left;
            background-color: #3085C3;
            color: white;
        }
/*8888888*/
#meja {
            font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
            font-weight: 25;
            border-collapse: collapse;
            width: 65%;
        }

        #meja td {
            border: 1px solid #dddddd;
            font-size: smaller;
            padding-left: 10px;
            padding-right: 10px;
        }
        #meja th {
            border: 1px solid #ddd;
            padding: 5px;
        }

        #meja th {
            padding-top: 3px;
            padding-bottom: 3px;
            text-align: left;
            background-color: #3085C3;
            color: white;
        }
        /********************* */
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
        .pinggir {
            padding-right: 20px;
            font-weight: bold;
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
    <h4>Kartu Infaq Al-Wafa Bi'ahdillah</h4>

    <table id="meja" style="border: 1px #3085C3;" width="60%">
        <!-- kode: null,
      acara: null,
      keterangan: null,
      tanggal_acara: null,
      nominal: null,
      rutin: null,
      aktif: null, -->
        <tr>
            <td>Kode Infaq</td>
            <td class="nominal pinggir"><?= $infaq['kode']; ?></td>
        <!-- </tr>
        <tr> -->
            <td>Tujuan Infaq</td>
            <td class="nominal pinggir"><?= $infaq['acara']; ?></td>
        </tr>
        <tr>
            <td>Keterangan</td>
            <td style="text-align:right; font-weight: bold; padding-right: 20px" colspan="3"><?= $infaq['keterangan']; ?></td>
        </tr>
        <tr>
            <td>Nominal Infaq</td>
            <td class="nominal pinggir"><?= number_format( (int) $infaq['nominal']); ?></td>
        <!-- </tr>
        <tr> -->
            <td>Batas waktu Pembayaran</td>
            <td class="nominal pinggir"><?= $infaq['tanggal_acara']; ?></td>
        </tr>
        <tr>
            <td>Status Infaq</td>
            <td class="nominal pinggir" colspan="3"><?php $sifat= $infaq['rutin'] ? 'Rutin' : 'Insidentil'; echo($sifat)?></td>
        </tr>
    </table>
    <br>
        
    <table id="table">
        <thead>
            <tr>
                <th>No.</th>
                <!-- <th>Kode</th> -->
                <th>ID</th>
                <th>Nama</th>
                <th>Wilayah</th>
                <th>Bayar</th>
                <th>Tgl. Bayar</th>
                <th>Status</th>
                <th>Penerima</th>
                <th>Tgl. Penerimaan</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 0;
            foreach ($data_bayar as $key => $item): ?>
                <tr>
                    <td style="text-align:right; padding-right: 10;" scope="row" width="5%"><?= $no + 1;
                                                                $no++; ?></td>
                    <td width="10%"><?= $item['nia'] ?></td>
                    <td width="20%"><?= strtoupper($item['nama']) ?></td>
                    <td width="20%"><?= strtoupper($item['wilayah']) ?></td>
                    <td white="15" style="text-align: right;"><?= number_format((int) $item['bayar']) ?></td>
                    <td width="10%"><?= $item['tanggal_bayar'] ?></td>
                    <td width="10%"><?= $item['is_lunas'] ?></td>
                    <td width="10%"><?= $item['validator'] ?></td>
                    <td width="10%"><?= $item['tanggal_validasi'] ?></td>
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
        <table id="table">
            <tr>
                <td width="50%">
                    <p class="page">Halaman </p>
                </td>
                <td width="50%">
                    <p style="text-align: right;">Tanggal cetak <?= date('Y-m-d H:i:s')?></p>
                </td>
            </tr>
        </table>
</div>

</body>

</html>