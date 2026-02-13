<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            <?php echo $title; ?>
                        </h4>
                        <hr class="hr-panel-heading" />
                        <div class="table-responsive">
                            <table class="table dt-table" data-order-col="0" data-order-type="desc">
                                <thead>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Mobile</th>
                                    <th>Treatment</th>
                                    <th>Doctor</th>
                                    <th>Consultation</th>
                                    <th>Branch</th>
                                    <th>Msg</th>
                                    <th>Rating</th>
                                    <th>Date</th>
                                    <th>Attachment</th>
                                </thead>
                                <tbody>
                                    <?php foreach ($entries as $entry) { ?>
                                        <tr>
                                            <td>
                                                <?php echo $entry['id']; ?>
                                            </td>
                                            <td>
                                                <?php echo $entry['name']; ?>
                                            </td>
                                            <td>
                                                <?php echo $entry['mobile_number']; ?>
                                            </td>
                                            <td>
                                                <?php echo $entry['treatment']; ?>
                                            </td>
                                            <td>
                                                <?php echo $entry['doctor']; ?>
                                            </td>
                                            <td>
                                                <?php echo _dt($entry['consultation_datetime']); ?>
                                            </td>
                                            <td>
                                                <?php echo $entry['branch']; ?>
                                            </td>
                                            <td>
                                                <?php echo mb_substr(strip_tags($entry['message'] ?? ''), 0, 20) . '...'; ?>
                                            </td>
                                            <td>
                                                <?php echo $entry['rating']; ?>
                                            </td>
                                            <td>
                                                <?php echo _dt($entry['created_at']); ?>
                                            </td>
                                            <td>
                                                <?php if ($entry['attachment']) { ?>
                                                    <a href="<?php echo base_url('uploads/web_integration/' . $entry['form_id'] . '/' . $entry['attachment']); ?>"
                                                        target="_blank">View</a>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>