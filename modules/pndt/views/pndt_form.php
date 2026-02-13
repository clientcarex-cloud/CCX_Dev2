<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade" id="pndt_form_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document" style="width: 90%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <h4 class="modal-title">Pre-Natal Diagnostic Techniques Form</h4>
                    <button type="button" class="btn btn-success" onclick="save_pndt_form()">Save & Print</button>
                </div>
            </div>

            <?php
            $form_data = isset($request['pndt_form_data']) ? $request['pndt_form_data'] : [];

            // Helper to get value
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

            <form id="pndtForm">
                <input type="hidden" name="patient_test_id" value="<?php echo $request['id']; ?>">
                <div class="modal-body">

                    <div class="text-center">
                        <h5><b>FORM F</b></h5>
                        <h5><b>FORM FOR MAINTENANCE OF RECORD IN RESPECT OF PREGNANT WOMAN BY ULTRASOUND CLINIC</b></h5>
                        <p class="text-muted">[See Provision to Section 4(3), Rule 9(4) and Rule 10(1A)]</p>
                    </div>

                    <div class="text-right">
                        <b>Date: <?php echo date('d-m-Y'); ?></b>
                    </div>

                    <hr>

                    <!-- Section 1 & 2 -->
                    <div class="row">
                        <div class="col-md-12">
                            <label>1. Name and Address of the Ultrasound Clinic</label>
                            <div class="form-horizontal">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Clinic Name :</label>
                                    <div class="col-sm-10">
                                        <p class="form-control-static"><b>Prime Diagnostics</b></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Address :</label>
                                    <div class="col-sm-10">
                                        <select class="form-control" name="clinic_address">
                                            <option
                                                value="18-1-423/1/3/60, PHOOLBAGH, CHANDRAYANGUTTA ROAD, HYDERABAD - 500005"
                                                selected>18-1-423/1/3/60, PHOOLBAGH, CHANDRAYANGUTTA ROAD, HYDERABAD -
                                                500005</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label>2. Clinic Registration No: &nbsp;&nbsp; <b>0116A975</b></label>
                        </div>
                    </div>

                    <br>

                    <!-- Section 3 -->
                    <div class="row">
                        <div class="col-md-12">
                            <label>3. Patient's Name : <span class="text-danger">*</span></label>
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="patient_name"
                                value="<?php echo get_val($form_data, 'patient_name', $request['patient_name']); ?>"
                                readonly>
                        </div>
                        <div class="col-md-2 text-right">
                            <label>Patient Age : <span class="text-danger">*</span></label>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="patient_age"
                                value="<?php echo get_val($form_data, 'patient_age', $request['age']); ?>" readonly>
                        </div>
                    </div>

                    <br>

                    <!-- Section 4 -->
                    <div class="row">
                        <div class="col-md-12">
                            <label>4. Number of children with sex of each child :</label>
                        </div>
                        <div class="col-md-2 text-right"><label>Male :</label></div>
                        <div class="col-md-3">
                            <input type="number" class="form-control" name="num_children_male"
                                value="<?php echo get_val($form_data, 'num_children_male', '0'); ?>">
                        </div>
                        <div class="col-md-2 text-right"><label>Female :</label></div>
                        <div class="col-md-3">
                            <input type="number" class="form-control" name="num_children_female"
                                value="<?php echo get_val($form_data, 'num_children_female', '0'); ?>">
                        </div>
                    </div>

                    <br>

                    <!-- Section 5 -->
                    <div class="row">
                        <div class="col-md-12">
                            <label>5. Husband's/Father's name :</label>
                        </div>
                        <div class="col-md-12">
                            <div class="form-horizontal">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Husband's name <span
                                            class="text-danger">*</span> :</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" name="husband_name"
                                            placeholder="Enter Husband's Name"
                                            value="<?php echo get_val($form_data, 'husband_name', $request['father_husband_name']); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Father's name <span
                                            class="text-danger">*</span> :</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" name="father_name"
                                            placeholder="Enter Father Name"
                                            value="<?php echo get_val($form_data, 'father_name'); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <br>

                    <!-- Section 6 -->
                    <div class="row">
                        <div class="col-md-12">
                            <label>6. Patient Full Address with Tel. No., if any :</label>
                        </div>
                        <div class="col-md-12">
                            <div class="form-horizontal">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Address :</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" name="patient_address"
                                            placeholder="Enter Address"
                                            value="<?php echo get_val($form_data, 'patient_address', $request['address'] . ', ' . $request['city']); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Mobile No :</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" name="mobile_no"
                                            value="<?php echo get_val($form_data, 'mobile_no', $request['mobile_no']); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Telephone No :</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" name="telephone_no"
                                            placeholder="Enter Telephone Number"
                                            value="<?php echo get_val($form_data, 'telephone_no'); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <br>

                    <!-- Section 7 -->
                    <div class="row">
                        <div class="col-md-12">
                            <label>7. Referred by. (full name and address of Doctor(s) / Genetic Counseling Centre.
                                Referral Note, and Case papers in case of self Referral, to be preserved carefully)
                                :</label>
                            <div class="form-horizontal">
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Ref.Doc Name :</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="ref_doc_name"
                                            placeholder="Enter Doctor's Name"
                                            value="<?php echo get_val($form_data, 'ref_doc_name', $request['ref_doc_name']); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Ref Doc Address :</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="ref_doc_address"
                                            placeholder="Enter Doctor's Address"
                                            value="<?php echo get_val($form_data, 'ref_doc_address'); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Genetic Counseling Centre :</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="genetic_center"
                                            value="<?php echo get_val($form_data, 'genetic_center', 'Prime Diagnostics'); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Referral Note :</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="referral_note"
                                            placeholder="Enter Referral Note"
                                            value="<?php echo get_val($form_data, 'referral_note'); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <br>

                    <!-- Section 8 -->
                    <div class="row">
                        <div class="col-md-5">
                            <label>8. Last menstrual period / weeks of Pregnancy :</label>
                        </div>
                        <div class="col-md-7">
                            <input type="text" class="form-control" name="lmp"
                                value="<?php echo get_val($form_data, 'lmp', 'NOT APPLICABLE'); ?>">
                        </div>
                    </div>

                    <br>

                    <!-- Section 9 -->
                    <div class="row">
                        <div class="col-md-12">
                            <label>9. History of genetic/medical disease in the family (Specify) :</label>
                            <p>Basis of diagnosis :</p>
                            <div class="form-horizontal">
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">(A) Clinical :</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="history_clinical"
                                            value="<?php echo get_val($form_data, 'history_clinical', 'NOT APPLICABLE'); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">(B) Bio-Chemical :</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="history_biochemical"
                                            value="<?php echo get_val($form_data, 'history_biochemical', 'NOT APPLICABLE'); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">(C) Cytogenetic :</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="history_cytogenetic"
                                            value="<?php echo get_val($form_data, 'history_cytogenetic', 'NOT APPLICABLE'); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">(D) Other (e.g. radiological, ultrasonography
                                        etc.specify) :</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="history_other"
                                            value="<?php echo get_val($form_data, 'history_other', 'NOT APPLICABLE'); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <br>

                    <!-- Section 10 -->
                    <div class="row">
                        <div class="col-md-12">
                            <label>10. Indication for prenatal diagnosis :</label>
                            <p>(A) Previous child/children with :</p>
                            <div class="form-horizontal">
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">(i) Chromosomal disorder :</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="indi_chromosomal"
                                            value="<?php echo get_val($form_data, 'indi_chromosomal', 'NOT APPLICABLE'); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">(ii) Metabolic disorders :</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="indi_metabolic"
                                            value="<?php echo get_val($form_data, 'indi_metabolic', 'NOT APPLICABLE'); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">(iii) Congenital anomaly :</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="indi_congenital"
                                            value="<?php echo get_val($form_data, 'indi_congenital', 'NOT APPLICABLE'); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">(iv) Mental retardation :</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="indi_mental"
                                            value="<?php echo get_val($form_data, 'indi_mental', 'NOT APPLICABLE'); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">(v) Haemoglobinopathy :</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="indi_haemo"
                                            value="<?php echo get_val($form_data, 'indi_haemo', 'NOT APPLICABLE'); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">(vi) Sex linked disorders :</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="indi_sex_linked"
                                            value="<?php echo get_val($form_data, 'indi_sex_linked', 'NOT APPLICABLE'); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">(vii) Single gene disorders :</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="indi_single_gene"
                                            value="<?php echo get_val($form_data, 'indi_single_gene', 'NOT APPLICABLE'); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">(viii) Any other (specify) :</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="indi_other_child"
                                            value="<?php echo get_val($form_data, 'indi_other_child', 'NOT APPLICABLE'); ?>">
                                    </div>
                                </div>

                                <br>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">(B) Advanced maternal age (35 years) :</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="indi_maternal_age"
                                            value="<?php echo get_val($form_data, 'indi_maternal_age', 'NOT APPLICABLE'); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">(C) Mother / father / sibling has genetic
                                        disease(specify) :</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="indi_genetic_disease"
                                            value="<?php echo get_val($form_data, 'indi_genetic_disease', 'NOT APPLICABLE'); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">(D)Other (specify) :</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="indi_other_general"
                                            value="<?php echo get_val($form_data, 'indi_other_general', 'NOT APPLICABLE'); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <br>

                    <!-- Section 11 -->
                    <div class="row">
                        <div class="col-md-12">
                            <label>11. Procedures carried out (with name and Registration No.of Gynecologist,
                                Radiologist/ Registered Medical Practitioner) With performed it :</label>
                            <div class="form-horizontal">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Doctor Name <span
                                            class="text-danger">*</span></label>
                                    <div class="col-sm-10">
                                        <p class="form-control-static" style="text-align: right;">
                                            <b><?php echo $doc_names_str; ?></b></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">REG.NO</label>
                                    <div class="col-sm-10">
                                        <p class="form-control-static" style="text-align: right;">
                                            <b><?php echo $doc_licenses_str; ?></b>
                                        </p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-6 control-label">Non - Invasive(I) Ultrasound (Specify purpose
                                        for which ultrasound is done during pregnancy):</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" name="ultrasound_purpose"
                                            value="<?php echo get_val($form_data, 'ultrasound_purpose', 'NOT APPLICABLE'); ?>">
                                        <small class="text-center center-block" style="margin-top: 5px;"><b>TO DIAGNOSE
                                                INTRAUTERINE VIABLE PREGNANCY</b></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <br>

                    <!-- Section 12 -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-horizontal">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">12. Any complication of the procedure - please
                                        specify :</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="complication"
                                            value="<?php echo get_val($form_data, 'complication', 'NOT APPLICABLE'); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <br>

                    <!-- Section 13 -->
                    <div class="row">
                        <div class="col-md-12">
                            <label>13. Laboratory tests recommended :</label>
                            <div class="form-horizontal">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">(i) Chromosomal studies :</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="lab_chromosomal"
                                            value="<?php echo get_val($form_data, 'lab_chromosomal', 'NOT APPLICABLE'); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">(ii) Biochemical studies :</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="lab_biochemical"
                                            value="<?php echo get_val($form_data, 'lab_biochemical', 'NOT APPLICABLE'); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">(iii) Molecular studies :</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="lab_molecular"
                                            value="<?php echo get_val($form_data, 'lab_molecular', 'NOT APPLICABLE'); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">(iv) Pre implantation genetic diagnosis
                                        :</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="lab_pre_implantation"
                                            value="<?php echo get_val($form_data, 'lab_pre_implantation', 'NOT APPLICABLE'); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <br>

                    <!-- Section 14 -->
                    <div class="row">
                        <div class="col-md-12">
                            <label>14. Result of :</label>
                            <div class="form-horizontal">
                                <div class="form-group">
                                    <label class="col-sm-6 control-label">(a) Pre-natal diagnostic procedure (give
                                        details) :</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" name="result_procedure"
                                            value="<?php echo get_val($form_data, 'result_procedure', 'NOT APPLICABLE'); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-6 control-label">(b) Ultrasonography Normal / Abnormal (Specify
                                        abnormality detected,if any) :</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" name="result_ultrasound"
                                            value="<?php echo get_val($form_data, 'result_ultrasound', 'NOT APPLICABLE'); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <br>

                    <!-- Section 15, 16, 17 -->
                    <div class="row">
                        <div class="col-md-12 form-horizontal">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">15. Date(s) on which procedures carried out
                                    :</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name="date_procedures"
                                        value="<?php echo get_val($form_data, 'date_procedures', 'NOT APPLICABLE'); ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">16. Date on consent obtained (In case of invasive)
                                    :</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name="date_consent"
                                        value="<?php echo get_val($form_data, 'date_consent', 'NOT APPLICABLE'); ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-12 text-left">17. The Result of Pre-natal diagnostic Procedure was
                                    conveyed to Results Conveyed</label>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-1 control-label">To :</label>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control" name="result_conveyed_to"
                                        value="<?php echo get_val($form_data, 'result_conveyed_to', 'NOT APPLICABLE'); ?>">
                                </div>
                                <label class="col-sm-1 control-label">On :</label>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control" name="result_conveyed_on"
                                        value="<?php echo get_val($form_data, 'result_conveyed_on', 'NOT APPLICABLE'); ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <br>

                    <!-- Section 18, 19 -->
                    <div class="row">
                        <div class="col-md-12 form-horizontal">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">18. Was MTP advised / conducted ?</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name="mtp_advised"
                                        value="<?php echo get_val($form_data, 'mtp_advised', 'NOT APPLICABLE'); ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">19. Date of which MTP carried out :</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name="mtp_date"
                                        value="<?php echo get_val($form_data, 'mtp_date', 'NOT APPLICABLE'); ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-1 control-label">Date :</label>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control" name="mtp_detail_date"
                                        value="<?php echo get_val($form_data, 'mtp_detail_date', 'NOT APPLICABLE'); ?>">
                                </div>
                                <label class="col-sm-2 control-label">MTP Done Doctor :</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" name="mtp_doctor"
                                        value="<?php echo get_val($form_data, 'mtp_doctor', 'NOT APPLICABLE'); ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-1 control-label">Place :</label>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control" name="mtp_place"
                                        value="<?php echo get_val($form_data, 'mtp_place', 'NOT APPLICABLE'); ?>">
                                </div>
                                <label class="col-sm-2 control-label">REG.NO :</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" name="mtp_reg_no"
                                        value="<?php echo get_val($form_data, 'mtp_reg_no', 'NOT APPLICABLE'); ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-1 control-label">PNDT.NO :</label>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control" name="mtp_pndt_no"
                                        value="<?php echo get_val($form_data, 'mtp_pndt_no', 'NOT APPLICABLE'); ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <br>
                    <hr>
                    <br>

                    <!-- Declarations -->
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <h5><b>DECLARATION OF PREGNANT WOMAN</b></h5>
                            <p class="text-justify">
                                I, <b>Mrs. <?php echo $request['patient_name']; ?></b> (name of the pregnant woman)
                                declare that by undergoing Ultrasonography / image scanning etc. I do not want to know
                                the sex of my fetus.
                            </p>
                            <br><br>
                            <p class="text-right"
                                style="border-top: 1px solid #ddd; width: 300px; float: right; padding-top: 5px;">
                                Signature / Thumb impression of pregnant woman</p>
                        </div>
                    </div>

                    <br><br><br>

                    <div class="row">
                        <div class="col-md-12 text-center">
                            <h5><b>DECLARATION OF DOCTOR / PERSON CONDUCTING THE ULTRASONOGRAPHY / IMAGE SCANNING</b>
                            </h5>
                            <p class="text-justify">
                                I, <b><?php echo $doc_names_str; ?></b> (Name of the person conducting
                                Ultrasonography / Image Scanning) declare that while conducting Ultrasonography / image
                                scan on <b>Mrs. <?php echo $request['patient_name']; ?></b> (Name of the pregnantwoman),
                                have neither detected nor disclosed the sex of her fetus to any body, in any manner.
                            </p>
                            <br><br>
                            <div class="text-right">
                                <p><b><?php echo $doc_names_str; ?></b></p>
                                <p>Name and signature of the Radiologist conducting Ultrasonography.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" onclick="save_pndt_form()">Save & Print</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function save_pndt_form() {
        console.log('Save button clicked');

        // Basic Validation
        var required_fields = ['patient_name', 'patient_age', 'husband_name', 'father_name'];
        var has_error = false;

        required_fields.forEach(function (field) {
            var val = $('input[name="' + field + '"]').val();
            if (!val) {
                $('input[name="' + field + '"]').addClass('has-error').css('border-color', 'red');
                has_error = true;
            } else {
                $('input[name="' + field + '"]').removeClass('has-error').css('border-color', '');
            }
        });

        if (has_error) {
            alert_float('warning', 'Please fill all required fields');
            return;
        }

        var formData = $('#pndtForm').serialize();

        // Append CSRF token if available
        if (typeof csrfData !== 'undefined') {
            formData += '&' + csrfData['token_name'] + '=' + csrfData['hash'];
        }

        console.log('Sending AJAX request...');

        $.post(admin_url + 'pndt/save', formData, function (response) {
            console.log('Response received:', response);
            try {
                var res = JSON.parse(response);
                if (res.success) {
                    alert_float('success', res.message);
                    $('#pndt_form_modal').modal('hide');
                    if (typeof pndt_table !== 'undefined') {
                        pndt_table.ajax.reload(); // Reload table if exists
                    }

                    // Open Print Window
                    var patient_test_id = $('input[name="patient_test_id"]').val();
                    setTimeout(function () {
                        window.open(admin_url + 'pndt/print_form/' + patient_test_id, '_blank');
                    }, 500);

                } else {
                    alert_float('danger', res.message);
                }
            } catch (e) {
                console.error('JSON Parse Error:', e);
                alert_float('danger', 'Server Error: ' + response);
            }
        }).fail(function (xhr, status, error) {
            console.error('AJAX Error:', error);
            alert_float('danger', 'Request Failed: ' + error);
        });
    }
</script>