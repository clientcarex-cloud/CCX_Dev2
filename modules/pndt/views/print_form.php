<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title><?php echo $title; ?></title>
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 14px;
            line-height: 1.5;
            color: #000;
            background-color: #fff;
            margin: 0;
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        /* List styles */
        .section {
            margin-bottom: 15px;
        }

        .section-title {
            font-weight: normal;
        }

        .sub-item {
            margin-left: 30px;
        }

        .sub-sub-item {
            margin-left: 60px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 2px 0;
            vertical-align: top;
        }

        .value {
            font-weight: bold;
            padding-left: 5px;
        }

        @media print {
            body {
                padding: 0;
                margin: 20px;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body onload="window.print()">
    <div class="container">
        <?php
        $form_data = isset($request['pndt_form_data']) ? $request['pndt_form_data'] : [];
        function get_val($arr, $key, $default = '')
        {
            return isset($arr[$key]) ? $arr[$key] : $default;
        }

        // Get Dynamic Doctors
        $pndt_doctors_opt = get_option('pndt_selected_doctors');
        $doc_names_str = 'DR. S. ALTHAF ALI / DR. S. NAZIYA'; // Fallback
        $doc_licenses_str = '52935 / 82575'; // Fallback
        $doc_names_arr = [];
        $doc_licenses_arr = [];

        if ($pndt_doctors_opt) {
            $doctors = json_decode($pndt_doctors_opt, true);
            if (is_array($doctors) && count($doctors) > 0) {
                foreach ($doctors as $d) {
                    $doc_names_arr[] = 'DR. ' . strtoupper($d['name']);
                    $doc_licenses_arr[] = $d['license'];
                }
                $doc_names_str = implode(' / ', $doc_names_arr);
                $doc_licenses_str = implode(' / ', $doc_licenses_arr);
            }
        }
        ?>

        <div class="text-center" style="margin-bottom: 30px;">
            <div style="font-weight: bold; font-size: 16px;">FORM F</div>
            <div style="font-weight: bold; font-size: 16px;">FORM FOR MAINTENANCE OF RECORD IN RESPECT OF PREGNANT WOMAN
                BY ULTRASOUND CLINIC</div>
            <div style="font-size: 13px;">[See Provision to Section 4(3), Rule 9(4) and Rule 10(1A)]</div>
        </div>

        <div class="text-right" style="margin-bottom: 20px;">
            <b>Date: <?php echo date('d-m-Y h:i A'); ?></b>
        </div>

        <div class="section">
            1. Name and address of the Ultrasound Clinic :
            <div class="sub-item">
                1. Clinic Name : <span class="value">Prime Diagnostics</span>
            </div>
            <div class="sub-item">
                2. Address : <span class="value">18-1-423/1/3/60, PHOOLBAGH, CHANDRAYANGUTTA ROAD, HYDERABAD -
                    500005</span>
            </div>
            <div class="sub-item">
                3. Phone No : <span class="value">+91 9581342424</span>
            </div>
        </div>

        <div class="section">
            2. Clinic Registration No : <span class="value">0116A975</span>
        </div>

        <div class="section">
            <table>
                <tr>
                    <td width="60%">3. Patient's name and her age : <span
                            class="value"><?php echo strtoupper(get_val($form_data, 'patient_name', $request['patient_name'])); ?></span>
                    </td>
                    <td width="40%">Patient Age : <span
                            class="value"><?php echo get_val($form_data, 'patient_age', $request['age']); ?>
                            Years</span></td>
                </tr>
            </table>
        </div>

        <div class="section">
            4. Number of children with sex of each child :
            <div class="sub-item">
                1. Male : <span class="value"><?php echo get_val($form_data, 'num_children_male', '0'); ?></span>
            </div>
            <div class="sub-item">
                2. Female : <span class="value"><?php echo get_val($form_data, 'num_children_female', '0'); ?></span>
            </div>
        </div>

        <div class="section">
            5. Husband's/Father's name :
            <div class="sub-item">
                1. Husband's name : <span
                    class="value"><?php echo get_val($form_data, 'husband_name', $request['father_husband_name']); ?></span>
            </div>
            <div class="sub-item">
                2. Father's name : <span class="value"><?php echo get_val($form_data, 'father_name'); ?></span>
            </div>
        </div>

        <div class="section">
            6. Patient Full Address with Tel. No., if any :
            <div class="sub-item">
                1. Address :
                <div class="value" style="display:inline;">
                    <?php echo get_val($form_data, 'patient_address', $request['address'] . ', ' . $request['city']); ?>
                </div>
            </div>
            <div class="sub-item">
                2. Mobile No : <span
                    class="value"><?php echo get_val($form_data, 'mobile_no', $request['mobile_no']); ?></span>
            </div>
            <div class="sub-item">
                3. Telephone No : <span class="value"><?php echo get_val($form_data, 'telephone_no'); ?></span>
            </div>
        </div>

        <div class="section">
            7. Referred by. (full name and address of Doctor(s) / Genetic Counseling Centre. Referral Note, and Case
            papers in case of self Referral, to be preserved carefully) :
            <div class="sub-item">
                1. Ref.Doc Name : <span
                    class="value"><?php echo get_val($form_data, 'ref_doc_name', $request['ref_doc_name']); ?></span>
            </div>
            <div class="sub-item">
                2. Ref Doc Address : <span class="value"><?php echo get_val($form_data, 'ref_doc_address'); ?></span>
            </div>
            <div class="sub-item">
                3. Genetic Counseling Centre : <span
                    class="value"><?php echo get_val($form_data, 'genetic_center', 'Prime Diagnostics'); ?></span>
            </div>
            <div class="sub-item">
                4. Referral note : <span class="value"><?php echo get_val($form_data, 'referral_note'); ?></span>
            </div>
        </div>

        <div class="section">
            8. Last menstrual period / weeks of Pregnancy : <span
                class="value"><?php echo get_val($form_data, 'lmp', 'NOT APPLICABLE'); ?></span>
        </div>

        <div class="section">
            9. History of genetic/medical disease in the family (Specify) :
            <div class="sub-item">* Basis of diagnosis :</div>
            <div class="sub-sub-item">
                1. Clinical : <span
                    class="value"><?php echo get_val($form_data, 'history_clinical', 'NOT APPLICABLE'); ?></span>
            </div>
            <div class="sub-sub-item">
                2. Bio-Chemical : <span
                    class="value"><?php echo get_val($form_data, 'history_biochemical', 'NOT APPLICABLE'); ?></span>
            </div>
            <div class="sub-sub-item">
                3. Cytogenetic : <span
                    class="value"><?php echo get_val($form_data, 'history_cytogenetic', 'NOT APPLICABLE'); ?></span>
            </div>
            <div class="sub-sub-item">
                4. Other (e.g. radiological, ultrasonography etc.specify) : <span
                    class="value"><?php echo get_val($form_data, 'history_other', 'NOT APPLICABLE'); ?></span>
            </div>
        </div>

        <div class="section" style="page-break-inside: avoid;">
            10. Indication for prenatal diagnosis :
            <div class="sub-item">* Previous child/children with :</div>
            <div class="sub-sub-item">
                1. Chromosomal disorder : <span
                    class="value"><?php echo get_val($form_data, 'indi_chromosomal', 'NOT APPLICABLE'); ?></span>
            </div>
            <div class="sub-sub-item">
                2. Metabolic disorders : <span
                    class="value"><?php echo get_val($form_data, 'indi_metabolic', 'NOT APPLICABLE'); ?></span>
            </div>
            <div class="sub-sub-item">
                3. Congenital anomaly : <span
                    class="value"><?php echo get_val($form_data, 'indi_congenital', 'NOT APPLICABLE'); ?></span>
            </div>
            <div class="sub-sub-item">
                4. Mental retardation : <span
                    class="value"><?php echo get_val($form_data, 'indi_mental', 'NOT APPLICABLE'); ?></span>
            </div>
            <div class="sub-sub-item">
                5. Haemoglobinopathy : <span
                    class="value"><?php echo get_val($form_data, 'indi_haemo', 'NOT APPLICABLE'); ?></span>
            </div>
            <div class="sub-sub-item">
                6. Sex linked disorders : <span
                    class="value"><?php echo get_val($form_data, 'indi_sex_linked', 'NOT APPLICABLE'); ?></span>
            </div>
            <div class="sub-sub-item">
                7. Single gene disorders : <span
                    class="value"><?php echo get_val($form_data, 'indi_single_gene', 'NOT APPLICABLE'); ?></span>
            </div>
            <div class="sub-sub-item">
                8. Any other (specify) : <span
                    class="value"><?php echo get_val($form_data, 'indi_other_child', 'NOT APPLICABLE'); ?></span>
            </div>

            <div class="sub-item">
                1. Advanced maternal age (35 years) : <span
                    class="value"><?php echo get_val($form_data, 'indi_maternal_age', 'NOT APPLICABLE'); ?></span>
            </div>
            <div class="sub-item">
                2. Mother / father / sibling has genetic disease (specify) : <span
                    class="value"><?php echo get_val($form_data, 'indi_genetic_disease', 'NOT APPLICABLE'); ?></span>
            </div>
            <div class="sub-item">
                3. Other (specify) : <span
                    class="value"><?php echo get_val($form_data, 'indi_other_general', 'NOT APPLICABLE'); ?></span>
            </div>
        </div>

        <div class="section">
            11. Procedures carried out (with name and Registration No.of Gynecologist, Radiologist/ Registered Medical
            Practitioner) With performed it :
            <div class="sub-item">
                1. Doctor Name : <span class="value"><?php echo $doc_names_str; ?></span>
            </div>
            <div class="sub-item">
                2. REG.NO : <span class="value"><?php echo $doc_licenses_str; ?></span>
            </div>
            <div class="sub-item">
                3. PNDT.NO : <span class="value">0116A975</span>
            </div>
            <div class="sub-item">
                4. Non - Invasive(I) Ultrasound (Specify purpose for which ultrasound is done during pregnancy): <span
                    class="value"><?php echo get_val($form_data, 'ultrasound_purpose', 'TO DIAGNOSE INTRAUTERINE VIABLE PREGNANCY'); ?></span>
            </div>
        </div>

        <div class="section">
            12. Any complication of the procedure - please specify : <span
                class="value"><?php echo get_val($form_data, 'complication', 'NOT APPLICABLE'); ?></span>
        </div>

        <div class="section">
            13. Laboratory tests recommended :
            <div class="sub-item">
                1. Chromosomal studies : <span
                    class="value"><?php echo get_val($form_data, 'lab_chromosomal', 'NOT APPLICABLE'); ?></span>
            </div>
            <div class="sub-item">
                2. Biochemical studies : <span
                    class="value"><?php echo get_val($form_data, 'lab_biochemical', 'NOT APPLICABLE'); ?></span>
            </div>
            <div class="sub-item">
                3. Molecular studies : <span
                    class="value"><?php echo get_val($form_data, 'lab_molecular', 'NOT APPLICABLE'); ?></span>
            </div>
            <div class="sub-item">
                4. Pre implantation genetic diagnosis : <span
                    class="value"><?php echo get_val($form_data, 'lab_pre_implantation', 'NOT APPLICABLE'); ?></span>
            </div>
        </div>

        <div class="section">
            14. Result of :
            <div class="sub-item">
                1. Pre-natal diagnostic procedure (give details) : <span
                    class="value"><?php echo get_val($form_data, 'result_procedure', 'NOT APPLICABLE'); ?></span>
            </div>
            <div class="sub-item">
                2. Ultrasonography Normal / Abnormal (Specify abnormality detected,if any) : <span
                    class="value"><?php echo get_val($form_data, 'result_ultrasound', 'NORMAL INTRAUTERINE PREGNANCY'); ?></span>
            </div>
        </div>

        <div class="section">
            15. Date(s) on which procedures carried out : <span
                class="value"><?php echo get_val($form_data, 'date_procedures', 'NOT APPLICABLE'); ?></span>
        </div>

        <div class="section">
            16. Date on consent obtained. (In case of invasive) : <span
                class="value"><?php echo get_val($form_data, 'date_consent', 'NA : NOT APPLICABLE'); ?></span>
        </div>

        <div class="section">
            17. The Result of Pre-natal diagnostic Procedure was conveyed : <span class="value">NOT APPLICABLE</span>
        </div>

        <div class="section">
            18. Was MTP advised / conducted ? : <span
                class="value"><?php echo get_val($form_data, 'mtp_advised', 'NOT APPLICABLE'); ?></span>
        </div>

        <div class="section">
            19. Date of which MTP carried out : <span
                class="value"><?php echo get_val($form_data, 'mtp_date', 'NOT APPLICABLE'); ?></span>
            <table style="width: 100%; margin-top: 5px;">
                <tr>
                    <td width="50%">Date: <span
                            class="value"><?php echo get_val($form_data, 'mtp_detail_date', 'NOT APPLICABLE'); ?></span>
                    </td>
                    <td width="50%">MTP Done Doctor: <span
                            class="value"><?php echo get_val($form_data, 'mtp_doctor', 'NOT APPLICABLE'); ?></span></td>
                </tr>
                <tr>
                    <td>Place: <span
                            class="value"><?php echo get_val($form_data, 'mtp_place', 'NOT APPLICABLE'); ?></span></td>
                    <td>REG.NO: <span
                            class="value"><?php echo get_val($form_data, 'mtp_reg_no', 'NOT APPLICABLE'); ?></span></td>
                </tr>
                <tr>
                    <td colspan="2">PNDT.NO: <span
                            class="value"><?php echo get_val($form_data, 'mtp_pndt_no', 'NOT APPLICABLE'); ?></span>
                    </td>
                </tr>
            </table>
        </div>

        <br><br>

        <div class="text-center" style="margin-bottom: 30px;">
            <div style="font-weight: bold; margin-bottom: 10px;">DECLARATION OF PREGNANT WOMAN</div>
            <p class="text-justify" style="margin: 0;">
                I <b>Mrs. <?php echo strtoupper($request['patient_name']); ?></b> (name of the pregnant woman) declare
                that by undergoing Ultrasonography / image scanning etc. I do not want to know the sex of my fetus.
            </p>
        </div>

        <br>

        <div class="text-right">
            <span style="border-top: 1px solid #000; padding-top: 5px;">Signature / Thumb impression of pregnant
                woman</span>
        </div>

        <br><br>

        <div class="text-center">
            <div style="font-weight: bold; margin-bottom: 10px;">DECLARATION OF DOCTOR / PERSON CONDUCTING THE
                ULTRASONOGRAPHY / IMAGE SCANNING</div>
            <div class="text-justify" style="margin-bottom: 30px;">
                I, <b><?php echo $doc_names_str; ?></b> (Name of the person conducting
                Ultrasonography / Image Scanning) declare that while conducting Ultrasonography / image scan on Patient
                Title not found! Patient Name not found! (Name of the pregnant woman), have neither detected nor
                disclosed the sex of her fetus to any body, in any manner.
            </div>

            <div class="text-right">
                <div style="font-weight: bold;"><?php echo $doc_names_str; ?></div>
                <div>Name and signature of the Radiologist conducting Ultrasonography.</div>
            </div>
        </div>

    </div>
</body>

</html>