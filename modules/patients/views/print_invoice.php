<!DOCTYPE html>
<html>

<head>
    <title>
        <?php echo $title ?? 'Print Invoice'; ?>
    </title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        }

        @media print {
            @page {
                margin: 0;
            }

            body {
                margin: 1.6cm;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print();">Print</button>
        <a href="<?php echo admin_url('patients/visits'); ?>">Back to Visits</a>
    </div>

    <?php echo $content; ?>

    <script>
        window.onload = function () {
            window.print();
        }
    </script>
</body>

</html>