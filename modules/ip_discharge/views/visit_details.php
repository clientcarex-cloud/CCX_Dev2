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
                <a href="#tab_summary" aria-controls="tab_summary" role="tab" data-toggle="tab">Summary</a>
            </li>
            <li role="presentation">
                <a href="#tab_discharge" aria-controls="tab_discharge" role="tab" data-toggle="tab">Discharge</a>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content">
            <!-- Summary Tab -->
            <div role="tabpanel" class="tab-pane active" id="tab_summary">
                <div class="panel_s">
                    <div class="panel-body">
                        <!-- Patient Demographic Header Block for Editor Context -->
                        <div style="border-bottom: 2px solid #ddd; padding-bottom: 20px; margin-bottom: 20px;">
                            <h2 class="text-center bold">FATIMA HOSPITAL</h2>
                            <p class="text-center">At Kalapathar, On Shamsheergunj to Eidgah New Road, Hyderabad. For
                                Appointments & Enquiries, Call 88 97 38 44 28</p>

                            <table class="table table-bordered table-condensed" style="margin-top: 10px;">
                                <tbody>
                                    <tr>
                                        <td class="bold">PATIENT NAME</td>
                                        <td>:
                                            <?php echo $visit['patient_name']; ?>
                                        </td>
                                        <td class="bold">AGE / SEX</td>
                                        <td>:
                                            <?php echo $visit['age'] . ' / ' . $visit['gender']; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="bold">WIFE / SON OF</td>
                                        <td>:
                                            <?php echo isset($visit['family_head_name']) ? $visit['family_head_name'] : '-'; ?>
                                        </td>
                                        <td class="bold">PHONE NO</td>
                                        <td>:
                                            <?php echo $visit['mobile_number']; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="bold">CONSULTANT</td>
                                        <td>:
                                            <?php echo $visit['doctor_name']; ?>
                                        </td>
                                        <td class="bold">I.P. No</td>
                                        <td>:
                                            <?php echo $visit['patient_user_id'] . ' / ' . $visit['visit_code']; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="bold">D.O.A</td>
                                        <td>:
                                            <?php echo date('d-m-Y h:i A', strtotime($visit['created_at'])); ?>
                                        </td>
                                        <td class="bold">D.O.D</td>
                                        <td>:
                                            <?php echo date('d-m-Y h:i A'); // Current time as discharge time placeholder ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <h4 class="text-center bold"
                                style="border-top: 1px double #999; border-bottom: 1px double #999; padding: 5px;">
                                DISCHARGE SUMMARY</h4>
                        </div>

                        <!-- Editor -->
                        <div class="form-group">
                            <label>Discharge Content</label>
                            <textarea id="discharge_content" name="discharge_content"
                                class="tinymce-editor"><?php echo $visit['summary']; ?></textarea>
                        </div>

                        <div class="btn-bottom-toolbar text-right">
                            <button type="button" class="btn btn-info" id="save_discharge_summary"
                                data-visit-id="<?php echo $visit['id']; ?>"
                                data-patient-id="<?php echo $visit['patient_id']; ?>">
                                Save
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Discharge Tab -->
            <div role="tabpanel" class="tab-pane" id="tab_discharge">
                <div class="alert alert-info">Discharge functionality placeholder.</div>
                <!-- Logic for finalizing discharge would go here -->
            </div>
        </div>
    </div>
</div>