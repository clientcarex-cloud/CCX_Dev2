<!DOCTYPE html>
<html>

<head>
    <title>
        <?php echo $title; ?>
    </title>
    <link href="<?php echo base_url('assets/css/style.min.css'); ?>" rel="stylesheet">
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 12px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .table th,
        .table td {
            border: 1px solid #ddd;
            padding: 5px;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .font-bold {
            font-weight: bold;
        }

        .no-print {
            display: none;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body onload="window.print();">
    <div style="width: 100%; margin: 0 auto;">
        <?php $this->load->view('patient_visit_overall'); ?>
    </div>
</body>

</html>