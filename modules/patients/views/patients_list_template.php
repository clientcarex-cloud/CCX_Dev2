<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<table class="table dt-table" data-order-col="0" data-order-type="desc">
    <thead>
        <tr>
            <th>S.no</th>
            <th>Patient Name & Date</th>
            <th>MR No & Visits</th>
            <th>Contact Info</th>
            <th>Age / DOB</th>
            <th>Since Patient</th>
            <th>Branch</th>

        </tr>
    </thead>
    <tbody>
        <?php
        $i = 1;
        foreach ($patients as $patient) {
            // Calculate Since
            $reg_date = $patient->datecreated;
            $since = '';
            if ($reg_date) {
                $d1 = new DateTime($reg_date);
                $d2 = new DateTime();
                $diff = $d2->diff($d1);
                if ($diff->y > 0) {
                    $since .= $diff->y . ' Yrs ';
                }
                $since .= $diff->m . ' Months';
            }

            // Masking Logic
            $mobile = $patient->extra_mobile ? $patient->extra_mobile : $patient->phonenumber;
            $mobile = (string) $mobile;
            $masked_mobile = (strlen($mobile) > 4) ? substr($mobile, 0, 2) . '******' . substr($mobile, -2) : $mobile;

            $email = $patient->email;
            $masked_email = '';
            if ($email) {
                $parts = explode('@', $email);
                if (count($parts) == 2) {
                    $masked_email = substr($parts[0], 0, 2) . '****@' . $parts[1];
                } else {
                    $masked_email = $email;
                }
            }
            ?>
            <tr>
                <td>
                    <?php echo $i++; ?>
                </td>
                <td>
                    <a href="#" onclick="open_action_modal(this, null); return false;"
                        data-patient-id="<?php echo $patient->patientid; ?>" data-name="<?php echo $patient->full_name; ?>"
                        data-mr="<?php echo $patient->mr_number; ?>" data-gender="<?php echo $patient->gender; ?>"
                        data-next="" data-days="" data-invert="" data-title="<?php echo $patient->title; ?>"
                        style="font-weight:bold; font-size:14px; color:#333;">
                        <?php echo ($patient->title ? $patient->title . ' ' : '') . $patient->full_name; ?>
                    </a><br>
                    <small class="text-muted">
                        <?php echo _d($patient->datecreated); ?>
                    </small>
                </td>
                <td>
                    <span style="color:#333;">MR No:
                        <?php echo $patient->mr_number; ?>
                    </span><br>
                    <small class="text-muted">Total Visits:
                        <b>
                            <?php echo $patient->visits_count; ?>
                        </b></small>
                </td>
                <td>
                    <div class="contact-info-wrapper">
                        <i class="fa fa-phone text-success"></i>
                        <span class="masked-content" data-real="<?php echo $mobile; ?>">
                            <?php echo $masked_mobile; ?>
                        </span>
                        <a href="#" class="toggle-mask text-muted mleft5"><i class="fa fa-eye"></i></a>
                        <br>
                        <?php if ($email) { ?>
                            <i class="fa fa-envelope text-info"></i>
                            <span class="masked-content" data-real="<?php echo $email; ?>">
                                <?php echo $masked_email; ?>
                            </span>
                            <a href="#" class="toggle-mask text-muted mleft5"><i class="fa fa-eye"></i></a>
                        <?php } ?>
                    </div>
                </td>
                <td>
                    <?php
                    echo $patient->age . ' ' . $patient->age_unit;
                    if ($patient->dob) {
                        echo '<br><small class="text-muted">DOB: ' . _d($patient->dob) . '</small>';
                    }
                    ?>
                </td>
                <td>
                    <?php echo $since; ?>
                </td>
                <td>
                    <?php echo $patient->customer_groups; ?>
                </td>

            </tr>
        <?php } ?>
    </tbody>
</table>