<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Display Access Required</title>
    <link href="<?php echo base_url('assets/css/reset.min.css'); ?>" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Roboto:400,300,700' rel='stylesheet' type='text/css'>
    <link href="<?php echo base_url('assets/plugins/bootstrap/css/bootstrap.min.css'); ?>" rel="stylesheet">
    <style>
        body {
            background: #f0f2f5;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Roboto', sans-serif;
        }

        .login-box {
            background: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .btn-primary {
            background: #2563eb;
            border: none;
            padding: 10px 20px;
            width: 100%;
            font-size: 16px;
            border-radius: 4px;
        }

        .btn-primary:hover {
            background: #1d4ed8;
        }

        .form-control {
            height: 45px;
            font-size: 16px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="login-box">
        <h3>Protected Display</h3>
        <p class="text-muted">Enter passcode to access this display</p>
        <br>
        <?php echo form_open(site_url('token_system/token_display/login/' . $display_id)); ?>
        <?php if ($this->session->flashdata('error')) { ?>
            <div class="alert alert-danger"><?php echo $this->session->flashdata('error'); ?></div>
        <?php } ?>
        <input type="password" name="passcode" class="form-control" placeholder="Passcode" required autofocus>
        <button type="submit" class="btn btn-primary">Access Display</button>
        <?php echo form_close(); ?>
    </div>
</body>

</html>