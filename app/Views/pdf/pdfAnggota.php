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

        #table td {
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
    <h4>Data Anggota Al-Wafa Bi'ahdillah</h4>
        
    <table id="table" style="margin-bottom: 70px;">
        <thead>
            <tr>
                <th>No.</th>
                <!-- <th>Kode</th> -->
                <th>NIA</th>
                <th>Nama</th>
                <th>WA</th>
                <th>Wilayah</th>
                <th>Alamat</th>
                <th>Level</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 0;
            foreach ($anggota as $item): ?>
                <tr>
                    <td style="text-align:right; padding-right: 10;" scope="row" width="5%"><?= $no + 1;
                                                                $no++; ?></td>
                    <td width="10%"><?= $item->nia ?></td>
                    <td width="20%"><?= $item->nama ?></td>
                    <td white="15"><?= $item->wa ?></td>
                    <td width="10%"><?= $item->wilayah ?></td>
                    <td width="25%"><?= $item->alamat ?></td>
                    <td width="10%"><?= $item->level ?></td>
                    <td width="10%"><?= $item->aktif ?></td>
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

<script type="text/php">
    if ( isset($pdf) ) {
        $font = Font_Metrics::get_font("helvetica", "bold");
        $pdf->page_text(72, 18, "Header: {PAGE_NUM} of {PAGE_COUNT}", $font, 6, array(0,0,0));
    }
</script> 

</body>

</html>