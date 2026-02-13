<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
    .facelink-card { border:1px solid #e5e7eb; border-radius:12px; box-shadow:0 8px 22px rgba(15,23,42,0.06); }
    .facelink-card .card-header { padding:18px 20px; border-bottom:1px solid #e5e7eb; background:#f8fafc; }
    .facelink-card h4 { margin:0; font-weight:600; color:#0f172a; }
    .pill { display:inline-block; padding:6px 10px; border-radius:999px; font-size:12px; font-weight:600; letter-spacing:.2px; }
    .pill-success { background:#ecfdf3; color:#16a34a; border:1px solid #bbf7d0; }
    .pill-muted { background:#f3f4f6; color:#4b5563; border:1px solid #e5e7eb; }
    .link-url { font-family: Menlo, Consolas, monospace; font-size:12px; word-break: break-all; }
</style>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="facelink-card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-sm-8">
                                <h4><?php echo _l('facelogin_links'); ?></h4>
                                <p class="text-muted m-b-0">Generate shareable face punch links for staff to check in/out without CRM login.</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-20">
                        <div class="row m-b-20">
                            <div class="col-md-8">
                                <?php echo form_open(admin_url('facelogin/links')); ?>
                                <div class="row">
                                    <div class="col-md-6 m-b-10">
                                        <label class="control-label">Link name</label>
                                        <input type="text" class="form-control" name="link_name" placeholder="e.g. Morning shift">
                                    </div>
                                    <div class="col-md-6 m-b-10">
                                        <label class="control-label">Staff</label>
                                        <select class="form-control selectpicker" name="staff_id" data-width="100%" data-live-search="true" title="Choose staff">
                                            <?php foreach ($staff as $s) { ?>
                                                <option value="<?php echo $s['staffid']; ?>">
                                                    <?php echo html_escape(trim($s['firstname'] . ' ' . $s['lastname'])); ?> (<?php echo html_escape($s['email']); ?>)
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary m-t-10"><i class="fa fa-plus"></i> Create staff link</button>
                                <?php echo form_close(); ?>
                            </div>
                            <div class="col-md-4">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-xs-12">
                                                <p class="text-muted m-b-5"><strong>One link for all staff</strong></p>
                                                <p class="text-muted m-b-10">Share a single URL that matches any enrolled employee face.</p>
                                            </div>
                                        </div>
                                        <?php echo form_open(admin_url('facelogin/links')); ?>
                                            <input type="hidden" name="mode" value="global">
                                            <div class="form-group m-b-10">
                                                <input type="text" class="form-control" name="global_link_name" placeholder="Global link name" value="<?php echo html_escape($global_link->name ?? 'All Staff'); ?>">
                                            </div>
                                            <button type="submit" class="btn btn-info btn-block"><?php echo $global_link ? 'Regenerate global link' : 'Create global link'; ?></button>
                                        <?php echo form_close(); ?>
                                        <?php if ($global_link) { 
                                            $gurl = site_url('facelogin/facelink/' . $global_link->token);
                                        ?>
                                            <div class="m-t-10 link-url"><?php echo html_escape($gurl); ?></div>
                                            <button type="button" class="btn btn-default btn-xs m-t-5 copy-link" data-link="<?php echo html_escape($gurl); ?>"><i class="fa fa-copy"></i> Copy</button>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th style="width:60px;">#</th>
                                        <th>Name</th>
                                        <th>Staff</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                        <th>Public URL</th>
                                        <th>Created</th>
                                        <th>Last Used</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($links)) { $i=1; foreach ($links as $link) { 
                                        $url = site_url('facelogin/facelink/' . $link['token']);
                                        $active = (int)$link['is_active'] === 1;
                                    ?>
                                        <tr>
                                            <td><?php echo $i++; ?></td>
                                            <td>
                                                <?php echo html_escape($link['name']); ?>
                                                <?php if (!empty($link['is_global'])) { ?>
                                                    <span class="pill pill-muted m-l-5" style="text-transform:uppercase;">Global</span>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <span class="bold"><?php echo html_escape(trim(($link['firstname'] ?? '') . ' ' . ($link['lastname'] ?? ''))); ?></span><br>
                                                <small class="text-muted"><?php echo html_escape($link['email']); ?></small>
                                            </td>
                                            <td><?php echo html_escape($link['role_name']); ?></td>
                                            <td>
                                                <?php if ($active) { ?>
                                                    <span class="pill pill-success">Active</span>
                                                <?php } else { ?>
                                                    <span class="pill pill-muted">Inactive</span>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <div class="link-url"><?php echo html_escape($url); ?></div>
                                                <button type="button" class="btn btn-default btn-xs m-t-5 copy-link" data-link="<?php echo html_escape($url); ?>"><i class="fa fa-copy"></i> Copy</button>
                                            </td>
                                            <td><?php echo _dt($link['created_at']); ?></td>
                                            <td><?php echo $link['last_used_at'] ? _dt($link['last_used_at']) : '<span class="text-muted">Never</span>'; ?></td>
                                            <td>
                                                <div class="btn-group">
                                                    <a class="btn btn-default btn-xs" href="<?php echo admin_url('facelogin/toggle_link/' . $link['id'] . '?active=' . ($active ? '0' : '1')); ?>">
                                                        <?php echo $active ? 'Deactivate' : 'Activate'; ?>
                                                    </a>
                                                    <a class="btn btn-danger btn-xs _delete" href="<?php echo admin_url('facelogin/delete_link/' . $link['id']); ?>"><i class="fa fa-trash"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php } } else { ?>
                                        <tr><td colspan="9" class="text-center text-muted p-3">No links created yet.</td></tr>
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
<script>
    $(function(){
        $('.selectpicker').selectpicker();
        $('body').on('click', '.copy-link', function(){
            var text = $(this).data('link');
            navigator.clipboard.writeText(text).then(function(){
                alert_float('success', 'Link copied');
            }).catch(function(){
                alert_float('warning', 'Could not copy link');
            });
        });
    });
</script>
</html>
