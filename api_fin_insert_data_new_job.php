<?php
include "config_api.php";
include "../../sysconf/global_func.php";
include "../../sysconf/db_config.php";

// $idName              = $_GET['idName']; //UPDATE_FROM_CRM 
// $idName              = "UPDATE_FROM_CRM"; //UPDATE_FROM_CRM 
// $agrmntid              = $_GET['agrmntid']; //POL000000005
// $distributedDate     = $_GET['distributedDate']; //2021-04-23 04:10:36
// $no_pengajuan        = $_GET['no_pengajuan']; //TUP69020220000002 
// Sanitasi dan validasi untuk 'idName'

$idName = filter_input(INPUT_GET, 'idName', FILTER_SANITIZE_STRING);
if (!$idName) {
        $idName = "";
}
$agrmntid = filter_input(INPUT_GET, 'agrmntid', FILTER_SANITIZE_STRING);
if (!$agrmntid) {
        $agrmntid = "";
}
$distributedDate = filter_input(INPUT_GET, 'distributedDate', FILTER_SANITIZE_STRING);
if (!$distributedDate) {
        $distributedDate = "";
}
$no_pengajuan = filter_input(INPUT_GET, 'no_pengajuan', FILTER_SANITIZE_STRING);
if (!$no_pengajuan) {
        $no_pengajuan = "";
}

$whr_sql = " AGRMNT_ID='$agrmntid' ";
if ($no_pengajuan != "") {
        $whr_sql = " no_pengajuan='$no_pengajuan' ";
}
$condb = connectDB();
//API URL
$url = $url_api_fin . '/api/Pengajuan/InsertUpdateFromCRMRO'; //echo "string $url";

$dateexe = DATE("Y-m-d H:i:s");

//create a new cURL resource

$payload2 = "";
$total = 0;
$sukses = 0;
$result2 = "";
// $sqla = "SELECT * FROM cc_ts_penawaran a WHERE a.back_flag=0 AND assign_to>0";
$sqla = " SELECT * FROM cc_ts_penawaran a WHERE a.back_flag IN (0, 3) AND a.assign_to > '0' AND a.total_course < 5 ";
//task_id='$taskId'
$resa = mysqli_query($condb, $sqla);
while ($reca = mysqli_fetch_array($resa)) {
        @extract($reca, EXTR_OVERWRITE);

        $sql99 = " UPDATE cc_ts_penawaran SET back_flag = '99' WHERE id = '" . $id . "' ";
        mysqli_query($condb, $sql99);

        if ($no_pengajuan != "") {
                $taskId = $task_id;
        }
        $url = $url_api_fin . '/api/Pengajuan/InsertUpdateFromCRMRO';
        if ($flag_wise == 1) {
                $url = $url_api_fin . '/api/Pengajuan/UpdateNonWiseCRM';
        }

        $sqlcs = "SELECT a.agent_name FROM cc_agent_profile a WHERE a.id='$assign_to' ";
        $rescs = mysqli_query($condb, $sqlcs);
        if ($reccs = mysqli_fetch_array($rescs)) {
                $agent_name         = $reccs['agent_name'];
        }

        //     $ch = curl_init($url);

        $monthly_instalment = str_replace(".00", "", $monthly_instalment);
        $plafond        = str_replace(".00", "", $plafond);
        $otr_price    = str_replace(".00", "", $otr_price);
        $emp_position = "TELESALES";

        $path = '../../public/konfirm/cust_photo/' . $cust_photo;
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $cust_photo = base64_encode($data);

        $path = '../../public/konfirm/id_photo/' . $id_photo;
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $id_photo = base64_encode($data);

        $monthly_income    = str_replace(".", "", $monthly_income);

        $asset_desc = $assets_desc;
        if ($no_mesin == "") {
                $no_mesin = $engine_no;
        }
        if ($no_rangka == "") {
                $no_rangka = $chasis_no;
        }

        $data2 = "";

        if ($distributed_date != '' && $distributed_date != "0000-00-00 00:00:00") {
                $data2 .= ',"DistributedDate":"' . $distributed_date . '"';
        } else {
                $data2 .= ',"DistributedDate":"' . $assign_time . '"';
        }
        if ($region_code != '') {
                $data2 .= ',"OfficeRegionCode":"' . $region_code . '"';
        }
        if ($region_name != '') {
                $data2 .= ',"OfficeRegionName":"' . $region_name . '"';
        }
        if ($cabang_code != '') {
                $data2 .= ',"OfficeCode":"' . $cabang_code . '"';
        }
        if ($cabang_name != '') {
                $data2 .= ',"OfficeName":"' . $cabang_name . '"';
        }
        if ($product_offering_code != '') {
                $data2 .= ',"ProdOfferingCode":"' . $product_offering_code . '"';
        }
        if ($customer_id_ro != '') {
                $data2 .= ',"CustNo":"' . $customer_id_ro . '"';
        }
        if ($customer_name != '') {
                $data2 .= ',"CustName":"' . $customer_name . '"';
        }
        if ($nik_ktp != '') {
                $data2 .= ',"IdNo":"' . $nik_ktp . '"';
        }
        if ($religion != '') {
                $data2 .= ',"Religion":"' . $religion . '"';
        }
        if ($tempat_lahir != '') {
                $data2 .= ',"BirthPlace":"' . $tempat_lahir . '"';
        }
        if ($tanggal_lahir != '' && $tanggal_lahir != "0000-00-00 00:00:00") {
                $data2 .= ',"BirthDate":"' . $tanggal_lahir . '"';
        }
        if ($nama_pasangan != '') {
                $data2 .= ',"SpouseName":"' . $nama_pasangan . '"';
        }
        if ($tanggal_lahir_pasangan != '' && $tanggal_lahir_pasangan != "0000-00-00 00:00:00") {
                $data2 .= ',"SpouseBirthDate":"' . $tanggal_lahir_pasangan . '"';
        }
        if ($last_followup_date != '' && $last_followup_date != "0000-00-00 00:00:00") {
                $data2 .= ',"InputDt":"' . $last_followup_date . '"';
        }
        if ($waktu_survey != '' && $waktu_survey != "0000-00-00 00:00:00") {
                $data2 .= ',"SurveyDt":"' . $waktu_survey . '"';
        }
        if ($visit_dt != '' && $visit_dt != "0000-00-00 00:00:00") {
                $data2 .= ',"VisitDt":"' . $visit_dt . '"';
        }
        if ($spouse_id_photo != '') {
                $data2 .= ',"SpouseIdPhoto":"' . $spouse_id_photo . '"';
        }
        if ($legal_alamat != '') {
                $data2 .= ',"LegalAddr":"' . $legal_alamat . '"';
        }
        if ($legal_city != '') {
                $data2 .= ',"LegalCity":"' . $legal_city . '"';
        }
        if ($legal_kecamatan != '') {
                $data2 .= ',"LegalSubDistrict":"' . $legal_kecamatan . '"';
        }
        if ($legal_kelurahan != '') {
                $data2 .= ',"LegalVillage":"' . $legal_kelurahan . '"';
        }
        if ($legal_provinsi != '') {
                $data2 .= ',"LegalProvince":"' . $legal_provinsi . '"';
        }
        if ($legal_kabupaten != '') {
                $data2 .= ',"LegalDistrict":"' . $legal_kabupaten . '"';
        }
        if ($legal_rt != '') {
                $data2 .= ',"LegalRt":"' . $legal_rt . '"';
        }
        if ($legal_rw != '') {
                $data2 .= ',"LegalRw":"' . $legal_rw . '"';
        }
        if ($legal_kodepos != '') {
                $data2 .= ',"LegalZipcode":"' . $legal_kodepos . '"';
        }
        if ($legal_sub_kodepos != '') {
                $data2 .= ',"LegalSubZipcode":"' . $legal_sub_kodepos . '"';
        }
        if ($survey_alamat != '') {
                $data2 .= ',"SurveyAddr":"' . $survey_alamat . '"';
        }
        if ($survey_rt != '') {
                $data2 .= ',"SurveyRt":"' . $survey_rt . '"';
        }
        if ($survey_rw != '') {
                $data2 .= ',"SurveyRw":"' . $survey_rw . '"';
        }
        if ($survey_provinsi != '') {
                $data2 .= ',"SurveyProvince":"' . $survey_provinsi . '"';
        }
        if ($survey_city != '') {
                $data2 .= ',"SurveyCity":"' . $survey_city . '"';
        }
        if ($survey_kecamatan != '') {
                $data2 .= ',"SurveySubDistrict":"' . $survey_kecamatan . '"';
        }
        if ($survey_kelurahan != '') {
                $data2 .= ',"SurveyVillage":"' . $survey_kelurahan . '"';
        }
        if ($survey_kodepos != '') {
                $data2 .= ',"SurveyZipCode":"' . $survey_kodepos . '"';
        }
        if ($survey_sub_kodepos != '') {
                $data2 .= ',"SurveySubZipcode":"' . $survey_sub_kodepos . '"';
        }
        if ($survey_kabupaten != '') {
                $data2 .= ',"SurveyDistrict":"' . $survey_kabupaten . '"';
        }
        if ($SurveySubDistrict != '') {
                $data2 .= ',"SurveySubDistrict":"' . $SurveySubDistrict . '"';
        }
        if ($mobile_1 != '') {
                $data2 .= ',"MobilePhoneNo1":"' . $mobile_1 . '"';
        }
        if ($mobile_2 != '') {
                $data2 .= ',"MobilePhoneNo2":"' . $mobile_2 . '"';
        }
        if ($phone_1 != '') {
                $data2 .= ',"Phone1":"' . $phone_1 . '"';
        }
        if ($phone_2 != '') {
                $data2 .= ',"Phone2":"' . $phone_2 . '"';
        }
        if ($job_phone_1 != '') {
                $data2 .= ',"JobPhone1":"' . $job_phone_1 . '"';
        }
        if ($job_phone_2 != '') {
                $data2 .= ',"JobPhone2":"' . $job_phone_2 . '"';
        }
        if ($profession_name != '') {
                $data2 .= ',"ProfessionName":"' . $profession_name . '"';
        }
        if ($profession_cat_name != '') {
                $data2 .= ',"ProfessionCategoryName":"' . $profession_cat_name . '"';
        }
        if ($job_position != '') {
                $data2 .= ',"JobPosition":"' . $job_position . '"';
        }
        if ($industry_type_name != '') {
                $data2 .= ',"IndustryTypeName":"' . $industry_type_name . '"';
        }
        if ($monthly_income != '') {
                if ($monthly_income == '0') {
                        $monthly_income = null;
                }
                $data2 .= ',"MonthlyIncome":"' . $monthly_income . '"';
        }
        if ($monthly_expense != '') {
                $data2 .= ',"MonthlyExpense":"' . $monthly_expense . '"';
        }
        if ($plafond != '') {
                $data2 .= ',"Plafon":"' . $plafond . '"';
        }
        if ($oth_biz_name != '') {
                $data2 .= ',"OtherBizName":"' . $oth_biz_name . '"';
        }
        if ($customer_rating != '') {
                $data2 .= ',"CustRating":"' . $customer_rating . '"';
        }
        if ($lob == "MGJMTRKON" || $lob == "MGJMBLSYR" || $lob == "MGJMTRSYR" || $lob == "MGJMBLKON" || $lob == "FASDANMBL" || $lob == "SLBINV" || $lob == "FASDANMTR" || $lob == "SLBMBL") {
                $suppl_name = "SUPPLIER";
                $suppl_code = "DUMMY";
        }
        if ($suppl_name != '') {
                $data2 .= ',"SupplBranchName":"' . $suppl_name . '"';
        }
        if ($suppl_code != '') {
                $data2 .= ',"SupplBranchCode":"' . $suppl_code . '"';
        }
        if ($no_mesin != '') {
                $data2 .= ',"MachineNo":"' . $no_mesin . '"';
        }
        if ($no_rangka != '') {
                $data2 .= ',"ChassisNo":"' . $no_rangka . '"';
        }
        if ($asset_desc != '') {
                $data2 .= ',"AssetDescription":"' . $asset_desc . '"';
        }
        if ($assets_name != '') {
                $data2 .= ',"AssetName":"' . $assets_name . '"';
        }
        if ($otr_price != '') {
                $data2 .= ',"OtrPriceAmt":"' . $otr_price . '"';
        }
        if ($item_year != '') {
                $data2 .= ',"ManufacturingYear":"' . $item_year . '"';
        }
        if ($ownership != '') {
                $data2 .= ',"OwnerRelationship":"' . $ownership . '"';
        }
        if ($agrmnt_rating != '') {
                $data2 .= ',"AgrmntRating":"' . $agrmnt_rating . '"';
        }
        if ($contract_stat != '') {
                $data2 .= ',"ContractStat":"' . $contract_stat . '"';
        }
        if ($num_of_dependents != '') {
                $data2 .= ',"NextInstNum":"' . $num_of_dependents . '"';
        }
        if ($sisa_tenor != '' && $sisa_tenor != 0) {
                $data2 .= ',"OsTenor":"' . $sisa_tenor . '"';
        }
        if ($tenor != '') {
                $data2 .= ',"Tenor":"' . $tenor . '"';
        }
        if ($release_date_bpkb != '' && $release_date_bpkb != "0000-00-00 00:00:00") {
                $data2 .= ',"BpkbReleaseDt":"' . $release_date_bpkb . '"';
        }
        if ($max_past_due_date != '') {
                $data2 .= ',"MaxPastDueDt":"' . $max_past_due_date . '"';
        }
        if ($maturity_date != '' && $maturity_date != "0000-00-00 00:00:00") {
                $data2 .= ',"MaturityDt":"' . $maturity_date . '"';
        }
        if ($product_cat != '') {
                $data2 .= ',"ProdCategory":"' . $product_cat . '"';
        }
        if ($jenis_task != '') {
                $data2 .= ',"TaskType":"' . $jenis_task . '"';
        }
        if ($soa != '') {
                $data2 .= ',"SOA":"' . $soa . '"';
        }
        if ($down_payment != '') {
                $data2 .= ',"DownPayment":"' . $down_payment . '"';
        }
        if ($ltv != '') {
                $data2 .= ',"Ltv":"' . $ltv . '"';
        }
        if ($answer_call != '') {
                $data2 .= ',"AnswerCall":"' . $answer_call . '"';
        }
        if ($prospect_stat2 != '') {
                $data2 .= ',"ProspectStat":"' . $prospect_stat2 . '"';
        }
        if ($reason_not_prospect != '') {
                $data2 .= ',"ReasonNotProspect":"' . $reason_not_prospect . '"';
        }
        if ($notes != '') {
                $data2 .= ',"Note":"' . $notes . '"';
        }
        if ($started_date != '' && $started_date != "0000-00-00 00:00:00") {
                $data2 .= ',"StartDt":"' . $started_date . '"';
        }
        if ($emp_position != '') {
                $data2 .= ',"EmpPosition":"' . $emp_position . '"';
        }
        if ($ia_app != '') {
                $data2 .= ',"IaApp":"' . $ia_app . '"';
        }
        if ($cust_photo != '') {
                $data2 .= ',"CustPhoto":"' . $cust_photo . '"';
        }
        if ($id_photo != '') {
                $data2 .= ',"IdPhoto":"' . $id_photo . '"';
        }
        if ($f_card_photo != '') {
                $data2 .= ',"FCardPhoto":"' . $f_card_photo . '"';
        }
        if ($priority_level != '') {
                $data2 .= ',"PriorityLvl":"' . $priority_level . '"';
        }
        if ($dukcapil_stat != '') {
                $data2 .= ',"DukcapilStat":"' . $dukcapil_stat . '"';
        }
        if ($agent_name != '') {
                $data2 .= ',"FieldPersonName":"' . $agent_name . '"';
        }
        if ($referantor_code != '') {
                $data2 .= ',"ReferantorCode":"' . $referantor_code . '"';
        }
        if ($referantor_name != '') {
                $data2 .= ',"ReferantorName":"' . $referantor_name . '"';
        }
        if ($pos_dealer != '') {
                $data2 .= ',"PosDealer":"' . $pos_dealer . '"';
        }
        if ($nama_ibukandung != '') {
                $data2 .= ',"MotherName":"' . $nama_ibukandung . '"';
        }
        if ($kepemilikan_rumah != '') {
                $data2 .= ',"HomeStat":"' . $kepemilikan_rumah . '"';
        }
        if ($monthly_instalment != '') {
                $data2 .= ',"MonthlyInstallment":"' . $monthly_instalment . '"';
        }
        if ($marital_status != '') {
                $data2 .= ',"MaritalStat":"' . $marital_status . '"';
        }
        if ($education != '') {
                $data2 .= ',"Education":"' . $education . '"';
        }
        if ($length_of_domicile != '') {
                $data2 .= ',"StayLength":"' . $length_of_domicile . '"';
        }
        if ($length_of_work != '') {
                $data2 .= ',"LengthOfWork":"' . $length_of_work . '"';
        }
        if ($duplicate_result != '') {
                $data2 .= ',"IsDuplicate":"' . $duplicate_result . '"';
        }
        if ($duplicate_ke != '') {
                $data2 .= ',"DuplicateNum":"' . $duplicate_ke . '"';
        }
        if ($asset_code != '') {
                $data2 .= ',"AssetCode":"' . $asset_code . '"';
        } else {
                $data2 .= ',"AssetCode":"' . $item_type . '"';
        }
        if ($due_date != '') {
                $data2 .= ',"DueDt":"' . $due_date . '"';
        }
        if ($os_installment_amt != '') {
                $data2 .= ',"OsInstallmentAmt":"' . $os_installment_amt . '"';
        }
        if ($status_call != '') {
                $data2 .= ',"StatusCall":"' . $status_call . '"';
        }
        if ($spouse_nik != '') {
                $data2 .= ',"SpouseNIK":"' . $spouse_nik . '"';
        }
        if ($spouse_birth_place != '') {
                $data2 .= ',"SpouseBirthPlace":"' . $spouse_birth_place . '"';
        }
        if ($guarantor_name != '') {
                $data2 .= ',"GuarantorName":"' . $guarantor_name . '"';
        }
        if ($guarantor_nik != '') {
                $data2 .= ',"GuarantorNIK":"' . $guarantor_nik . '"';
        }
        if ($guarantor_phone != '') {
                $data2 .= ',"GuarantorMobilePhoneNo":"' . $guarantor_phone . '"';
        }
        if ($guarantor_address != '') {
                $data2 .= ',"GuarantorAddr":"' . $guarantor_address . '"';
        }
        if ($guarantor_rt != '') {
                $data2 .= ',"GuarantorRt":"' . $guarantor_rt . '"';
        }
        if ($guarantor_rw != '') {
                $data2 .= ',"GuarantorRw":"' . $guarantor_rw . '"';
        }
        if ($guarantor_provinsi != '') {
                $data2 .= ',"GuarantorProvince":"' . $guarantor_provinsi . '"';
        }

        if ($guarantor_kabupaten != '') {
                $data2 .= ',"GuarantorCity":"' . $guarantor_kabupaten . '"';
        }

        if ($guarantor_kecamatan != '') {
                $data2 .= ',"GuarantorKecamatan":"' . $guarantor_kecamatan . '"';
        }

        if ($guarantor_kelurahan != '') {
                $data2 .= ',"GuarantorKelurahan":"' . $guarantor_kelurahan . '"';
        }

        if ($guarantor_zipcode != '') {
                $data2 .= ',"GuarantorZipcode":"' . $guarantor_zipcode . '"';
        }

        if ($GuarantorSubZipcode != '') {
                $data2 .= ',"GuarantorSubZipcode":"' . $GuarantorSubZipcode . '"';
        }

        if ($GuarantorRelationship != '') {
                $data2 .= ',"GuarantorRelationship":"' . $GuarantorRelationship . '"';
        }

        if ($customer_model != '') {
                $data2 .= ',"CustModel":"' . $customer_model . '"';
        }

        if ($notes_other_vehicle != '') {
                $data2 .= ',"NotesOtherVehicle":"' . $notes_other_vehicle . '"';
        }

        if ($notes_phone_alternative != '') {
                $data2 .= ',"NotesMobilePhoneNo":"' . $notes_phone_alternative . '"';
        }

        if ($agrmnt_no != '') {
                $data2 .= ',"AgrmntNo":"' . $agrmnt_no . '"';
        }

        if ($source_data != '') {
                $data2 .= ',"SourceData":"' . $source_data . '"';
        }

        if ($lob != '') {
                $data2 .= ',"Lob":"' . $lob . '"';
        }
        //new    
        if ($is_pre_approval != '') {
                $param_approv = 'PRE APPROVAL';
                $data2 .= ',"IsPreApproval":"' . $is_pre_approval . '"';
        }

        if ($flag_wise == 1) {
                $data = '{"TaskId":"' . $task_id . '","StartDt":"' . $dateexe . '"' . $data2 . '}';
        } else {
                $data = '{"TaskId":"0","StartDt":"' . $dateexe . '"' . $data2 . '}';
        }

        $data = str_replace('"TaskId":"0",', "", $data);

        $payload = $data;


        $params = array(
                'dataid' => $id,
                'postfield' => $payload,
                'url' => $url
        );

        $body = json_encode($params);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://10.1.49.250:8766/pengajuan");
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json'
        ));
        $exec = curl_exec($ch);
        $msg = json_decode($exec, true);

        curl_close($ch);
}

disconnectDB($condb);
