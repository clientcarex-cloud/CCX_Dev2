<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade" id="patient_details_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo _l('patient_details'); ?></h4>
            </div>
            <div class="modal-body">
                <?php if ($type == 'client') { ?>
                    <div class="row">
                        <div class="col-md-12">
                            <h4 class="bold"><?php echo $patient->company; ?></h4>
                            <hr />
                        </div>
                        <div class="col-md-6">
                            <p class="bold"><?php echo _l('mr_no'); ?>: <span class="text-muted"><?php echo isset($patient->mr_no) ? $patient->mr_no : ''; ?></span></p>
                            <p class="bold"><?php echo _l('mobile_number'); ?>: <span class="text-muted"><?php echo isset($patient->phonenumber) ? $patient->phonenumber : ''; ?></span></p>
                            <p class="bold"><?php echo _l('gender'); ?>: <span class="text-muted"><?php echo isset($patient->gender) ? ucfirst($patient->gender) : ''; ?></span></p>
                            <p class="bold"><?php echo _l('dob'); ?>: <span class="text-muted"><?php echo isset($patient->dob) ? _d($patient->dob) : ''; ?></span></p>
                        </div>
                        <div class="col-md-6">
                             <p class="bold"><?php echo _l('email'); ?>: <span class="text-muted"><?php echo isset($patient->email) ? $patient->email : ''; ?></span></p>
                             <p class="bold"><?php echo _l('address'); ?>: <span class="text-muted"><?php echo isset($patient->address) ? $patient->address : ''; ?></span></p>
                             <p class="bold"><?php echo _l('city'); ?>: <span class="text-muted"><?php echo isset($patient->city) ? $patient->city : ''; ?></span></p>
                        </div>
                    </div>
                <?php } elseif ($type == 'guest') { ?>
                    <div class="row">
                        <div class="col-md-12">
                            <h4 class="bold"><?php echo $patient->name; ?> <span class="label label-warning pull-right">Guest</span></h4>
                            <hr />
                        </div>
                        <div class="col-md-6">
                            <p class="bold"><?php echo _l('mobile_number'); ?>: <span class="text-muted"><?php echo isset($patient->phone) ? $patient->phone : ''; ?></span></p>
                            <p class="bold"><?php echo _l('gender'); ?>: <span class="text-muted"><?php echo isset($patient->gender) ? ucfirst($patient->gender) : ''; ?></span></p>
                        </div>
                        <div class="col-md-6">
                            <p class="bold"><?php echo _l('dob'); ?>: <span class="text-muted"><?php echo isset($patient->dob) ? _d($patient->dob) : ''; ?></span></p>
                            <p class="bold"><?php echo _l('title'); ?>: <span class="text-muted"><?php echo isset($patient->title) ? ucfirst($patient->title) : ''; ?></span></p>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            </div>
        </div>
    </div>
</div>
