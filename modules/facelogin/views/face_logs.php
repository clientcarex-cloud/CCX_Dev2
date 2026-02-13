<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .face-logs-card {
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(15,23,42,0.05);
        overflow: hidden;
    }
    .face-logs-header {
        background: linear-gradient(135deg, #f8fafc, #eef2ff);
        padding: 18px 20px;
        border-bottom: 1px solid #e5e7eb;
    }
    .face-logs-header h4 {
        margin: 0;
        font-weight: 600;
        color: #0f172a;
    }
    .face-logs-header p {
        margin: 4px 0 0 0;
        color: #475569;
    }
    .table-face-logs th {
        background: #f8fafc;
        border-bottom: 1px solid #e5e7eb;
        color: #0f172a;
        font-weight: 600;
    }
    .table-face-logs td {
        vertical-align: middle;
    }
    .pill {
        display: inline-block;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: .2px;
    }
    .pill-success { background: #ecfdf3; color: #16a34a; border: 1px solid #bbf7d0; }
    .pill-danger  { background: #fef2f2; color: #dc2626; border: 1px solid #fecdd3; }
</style>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="face-logs-card">
                    <div class="face-logs-header">
                        <div class="row">
                            <div class="col-md-8 col-sm-7">
                                <h4><?php echo _l('facelogin_face_logs'); ?></h4>
                                <p>Recent FaceLogin attempts with status, user, IP, and device info.</p>
                            </div>
                            <div class="col-md-4 col-sm-5 text-right mtop15-xs">
                                <?php if (is_admin()) { ?>
                                    <?php echo form_open(admin_url('facelogin/clear_logs'), ['id' => 'clear-face-logs-form']); ?>
                                    <button type="submit" class="btn btn-danger" id="btn-clear-logs"><i class="fa fa-trash"></i> Clear History</button>
                                    <?php echo form_close(); ?>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-face-logs">
                            <thead>
                                <tr>
                                    <th style="width:60px;">#</th>
                                    <th>Time</th>
                                    <th>Staff</th>
                                    <th>FaceLogin Status</th>
                                    <th>Message</th>
                                    <th>IP</th>
                                    <th>User Agent</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($logs)) { $i = 1; foreach ($logs as $log) { ?>
                                    <tr>
                                        <td><?php echo $i++; ?></td>
                                        <td><?php echo _dt($log['created_at']); ?></td>
                                        <td>
                                            <?php if (!empty($log['staff_name'])) { ?>
                                                <span class="bold"><?php echo html_escape($log['staff_name']); ?></span><br>
                                                <small class="text-muted"><?php echo html_escape($log['email']); ?></small>
                                            <?php } else { ?>
                                                <span class="text-muted">Unknown/Guest</span>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <?php if ($log['status'] === 'success') { ?>
                                                <span class="pill pill-success">Success</span>
                                            <?php } else { ?>
                                                <span class="pill pill-danger">Failed</span>
                                            <?php } ?>
                                        </td>
                                        <td><?php echo html_escape($log['message']); ?></td>
                                        <td><?php echo html_escape($log['ip_address']); ?></td>
                                        <td style="max-width:260px; white-space: normal;"><?php echo html_escape($log['user_agent']); ?></td>
                                    </tr>
                                <?php } } else { ?>
                                    <tr><td colspan="7" class="text-center text-muted p-3">No face login activity recorded yet.</td></tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    $(function(){
        $('#clear-face-logs-form').on('submit', function(e){
            if(!confirm('Clear all face login history?')) {
                e.preventDefault();
            }
        });
    });
</script>
</html>
