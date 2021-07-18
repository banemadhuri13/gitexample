<?php
/**
* File: ContrActivityWiseDailyLogsheet.php
* Location: /controller
* 
*
* Change Log: 
* 01-11-2020 | Madhuri Faragade & Sneha Mehta | New file created
*
**/

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

    include '../includes/Function.php';
    include '../includes/connect.php';
    include '../models/Model_P_ActivityWiseMaster.php';
$request = $_REQUEST;
$action = $request['action'];

    if($action == 'verifiy') {
          addVeriyComment();
    }/*else if($action == 'assetType') {
        assetType($_SESSION['login_siteid']);
    }*/
    // else if($action == 'assetTypeLogsheet') {
       
    //     assetTypeLogsheet($_REQUEST['service_type_id'],$_REQUEST['service_profile_id']);
    // }
    else if($action == 'assetlistforlogsheet') {
        assetsListforlogsheet($_REQUEST['activity_id']);
    } 
       /* else if($action == 'assetList') {
        assetsList();
    } */
   /* else if($action == 'assetloc') {
        assetLoc($_SESSION['login_siteid']);
    }
       else if($action == 'typename') {
        typename($_REQUEST['asset_id']);
    } */else if($action == 'activitylist') {
        activityList($_REQUEST['service_type_id'],$_REQUEST['service_profile_id']);
    }/*else if($action == 'getallactivities') {

        getallactivities();

    }*/else if($action == 'serviceType') {
        serviceType();
    }
     /*else if($action == 'getAssetsByActivityId') {
        getAssetsByActivityId($_REQUEST['activity_id']);

    }*/
   /* else if($action == 'ppmassettype') {
        ppmassettype($_SESSION['login_siteid']);
    }
     else if($action == 'ppmassetsList') {
        ppmassetsList();
    } 
    else if($action == 'ppmactivitylist') {
        ppmactivityList($_REQUEST['asset_id']);
    } */elseif ($action == 'serviceProfileforlogsheet') {
      //  $servicetype_id = implode(', ', $_REQUEST['service_type_id']);//explode(',',$_REQUEST['service_type_id']);
      //  print_r($_REQUEST['service_type_id']);
       $servicetype_id=$_REQUEST['service_type_id'];
        serviceProfileforlogsheet($servicetype_id);
    } else if($action == 'getcolumnheaders') {
        getcolumnheaders();
    }

    function getcolumnheaders() {
        $activityid = $_REQUEST['activityid'];
        $model_fs  = new Model_P_ActivityWiseMaster();
            $columnheaders = $model_fs->getcolumnheaders($activityid);
            echo json_encode($columnheaders);
    }

    
    function assetsList(){
           $asset_model = new Model_P_AssetSmartPlaceMaster1();
            $asset_list = $asset_model->getAllAssets();
            echo json_encode($asset_list);
    }

    function serviceType(){
            $service_type_model= new Model_P_ActivityWiseMaster();
            $service_type_list=$service_type_model->getTypeName();
            echo json_encode($service_type_list);
    }
        
    function serviceProfileforlogsheet($service_type_id) {
        $usergrp_model= new Model_P_ActivityWiseMaster();
        $service_profile_list=$usergrp_model->serviceProfileforlogsheet($service_type_id);
        echo json_encode($service_profile_list);
    }

 

    // function assetTypeLogsheet($service_type_id, $service_profile_id){
    //         $asset_type_model= new Model_P_ActivityWiseMaster();
    //         $asset_type_list=$asset_type_model->getAssetTypeForLogsheet($service_type_id, $service_profile_id);
    //         echo json_encode($asset_type_list);
    // }

    function ppmassettype($company_id){
            $asset_type_model= new Model_P_AssetType1();
            $asset_type_list=$asset_type_model->getPPMAssetType($company_id);
            echo json_encode($asset_type_list);
    }
            
    function assetsListforlogsheet($activity_id) {
            $asset_model = new Model_P_ActivityWiseMaster();
            $asset_list = $asset_model->getAssetsByType($activity_id);
            echo json_encode($asset_list);
    }
    
    function activityList($service_type_id, $service_profile_id) {
          $asset_activity_link_model = new Model_P_ActivityWiseMaster();
          $activity_id_list = $asset_activity_link_model->getActivityID($service_type_id, $service_profile_id);
      echo json_encode($activity_id_list);
    }
 

function addVeriyComment(){
       $task_ids = $_REQUEST['task'];
       // $task_ids = "'". implode("','", $task_ids) ."'";
     if($task_ids!= '' && $_REQUEST['comment']!=''){
        $site_id=$_SESSION['login_siteid'];
        $date = date("Y/m/d");
     $filess=[];
        $desired_dir = "../android/Images/".$_SESSION['login_db'] . '/' . $date . '/';
        $append_desired_dir = "Images/".$_SESSION['login_db'] . '/' . $date . '/';
            if (!is_dir($desired_dir)) {
                mkdir($desired_dir, 0777, true);
            }
        foreach ($_FILES['files']['tmp_name'] as $key => $tmp_name) {
            $file_name = basename($_FILES['files']['name'][$key]);
            $filename   = "Attached_Image_" . $file_name; 
            $basename   = $filename;
            $file_tmp = $_FILES['files']['tmp_name'][$key];
            $final_nm = "$append_desired_dir". $basename;
                if (move_uploaded_file($file_tmp, "$desired_dir" . $basename)) {
                  array_push($filess, $final_nm);
                }
        }

        $comment = $_REQUEST['comment'];
        $verified_user_id=$_SESSION['login_userid'];
        $verfication_date=date('Y-m-d H:i:s');
            $model_task_verification = new Model_P_ActivityWiseMaster();
            $model_task_image = new Model_P_ActivityWiseMaster();
            for($i=0;$i<count($task_ids);$i++)
            {
                $task_id =$task_ids[$i];
                 $res = $model_task_verification->Addverificationcomment($task_id,$verfication_date,$comment,$verified_user_id);
                if ($filess != ''){
                foreach ($filess as $key => $ImagePath) {
                    $result = $model_task_image->AddVerificationTaskImage($task_id,$ImagePath,$verfication_date,$verified_user_id);
                    }
                }
            }
      
          echo json_encode(array('result'=>1));
      } else {
        echo json_encode(array('result'=>0));
      }
}
?>