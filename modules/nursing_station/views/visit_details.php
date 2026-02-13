<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row">
    <div class="col-md-12">
        <h4 class="bold no-mtop text-info pull-left">
            ::
            <?php echo isset($visit['created_at']) ? date('D, M d, Y', strtotime($visit['created_at'])) : ''; ?>
        </h4>
        <h4 class="bold no-mtop text-danger pull-right">
            Name :
            <?php echo $visit['patient_name']; ?>, MR No :
            <?php echo $visit['mr_number']; ?>, Visit ID :
            <?php echo $visit['visit_code']; ?>
        </h4>
        <div class="clearfix"></div>
        <hr class="no-mtop" />
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <!-- Tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active">
                <a href="#tab_billing" aria-controls="tab_billing" role="tab" data-toggle="tab">Billing</a>
            </li>
            <li role="presentation">
                <a href="#tab_monitoring" aria-controls="tab_monitoring" role="tab" data-toggle="tab">Monitoring</a>
            </li>
            <li role="presentation">
                <a href="#tab_attachments" aria-controls="tab_attachments" role="tab" data-toggle="tab">Attachments</a>
            </li>
            <li role="presentation">
                <a href="#tab_tasks" aria-controls="tab_tasks" role="tab" data-toggle="tab">Tasks</a>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content">
            <!-- Billing Tab -->
            <div role="tabpanel" class="tab-pane active" id="tab_billing">
                <div class="alert alert-info">Billing information placeholder.</div>
            </div>

            <!-- Monitoring Tab -->
            <div role="tabpanel" class="tab-pane" id="tab_monitoring">
                <div class="alert alert-info">Patient monitoring placeholder.</div>
            </div>

            <!-- Attachments Tab -->
            <div role="tabpanel" class="tab-pane" id="tab_attachments">
                <div class="alert alert-info">Attachments placeholder.</div>
            </div>

            <!-- Tasks Tab -->
            <div role="tabpanel" class="tab-pane" id="tab_tasks">
                <div class="alert alert-info">Tasks placeholder.</div>
            </div>
        </div>
    </div>
</div>