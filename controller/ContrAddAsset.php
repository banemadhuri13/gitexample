<?php

if (session_status() == PHP_SESSION_NONE) {
    @session_start();
}
ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 error_reporting(E_ALL); 

include '../includes/Function.php';
include '../models/Model_P_AssetActivityLinking.php';
include '../models/Model_P_AssetSmartPlaceMaster.php';
include '../models/Model_P_EmployeeSiteLinking.php';
include '../models/Model_P_ActivityMaster.php';
include '../models/Model_P_AssetType.php';
include '../models/Model_K_Status.php';
include '../models/Model_P_SiteLocationMaster.php';
include '../models/Model_P_AssetCodeGeneration.php';
include '../models/Model_P_Building.php';
include '../models/Model_P_Floor.php';
include '../models/Model_P_Room.php';
include '../models/Model_Asset.php';


$action = getRequest('action');
if ($action == 'addasset') {
    addAssetToActivity(getRequest('activity_id'), getRequest('assets'));
} else if ($action == 'remainingassets') {

    getRemainingAsset(getRequest('activity_id'));
} else if ($action == 'allassets') {

    getAllAsset(getRequest('activity_id'));
} else if ($action == 'AssetTypeAndStatus') {

    getAssetTypeAndStatus();
} else if ($action == 'AssetType') {

    getAssetType();
} else if ($action == 'AssetStatus') {

    getAssetStatus();
} else if ($action == 'addnewasset') {

    addNewAsset();
} else if ($action == 'addassettype') {

    addAssetType();

}
  else if ($action == 'getparameter') {
      getparameter();
} 


  else if ($action == 'AssetCodeGeneration') {
      GenerateAssetCode();
} 
  else if ($action == 'cursite') {
    getSiteName($_SESSION['login_siteid']);
} 
  else if ($action == 'getBuildingCode') {
    getBuildingCode();
} 
  else if ($action == 'floorlist') {
    $SiteId = $_SESSION['login_siteid'];
    getFloorCode($SiteId);
} 
  elseif ($action == 'getFloorListbybuildingId') {
    getFloorListbybuildingId();
} 
  elseif ($action == 'getRoomListbyfloorId') {
    getRoomListbyfloorId();
} elseif ($action == 'delast') {
    deleteAsset();
    displayassetactivitylink();
} else if($action=='getasset'){
    getAsset();
} else if($action=='gettenant'){
    gettenant();
  }else if($action=='getwatermeter'){
    getwatermeter();
  }

  function getwatermeter(){
  // $assetid=$_REQUEST['id'];
   $getassetmodel=new Model_Asset();
   $model=$getassetmodel->getwatermeter();
   echo json_encode($model);
}
function getparameter(){
   $assetid=$_REQUEST['id'];
   $getassetmodel=new Model_Asset();
   $model=$getassetmodel->getparameter($assetid);
   echo json_encode($model);
}

function gettenant(){
   // $assetid=$_REQUEST['id'];
   $gettenant=new Model_Asset();
   $model=$gettenant->gettenant();
   echo json_encode($model);
}



function displayassetactivitylink(){
  $assetid=$_REQUEST['assetid'];
  $asset_model1=new Model_P_AssetSmartPlaceMaster();
  $delete1=$asset_model->displayassetactivitylink($asset_id);
echo json_encode($delete1);
}

function deleteAsset(){
$asset_id=$_REQUEST['auto_id'];
$asset_model=new Model_P_AssetSmartPlaceMaster();
$delete=$asset_model->deleteAsset($asset_id);
echo json_encode($delete);
}

function getFloorListbybuildingId() {
    $buildingid = $_REQUEST['buildingid'];
    $floor_model = new Model_P_Floor();
    $floor_code = $floor_model->getFloorListbybuildingId($buildingid);
    echo json_encode($floor_code);
}
function getAsset(){
  
  
  $getassetmodel=new Model_Asset();
  $model=$getassetmodel->getAssetDetails();
  echo json_encode($model);
}

function getBuildingCode() {
    $building_model = new Model_P_Building();
    echo $BuildingCode = $building_model->getBuildingCode();
}

function getFloorCode($SiteId) {
    $floor_model = new Model_P_Floor();
    $floor_code = $floor_model->getFloorlist($SiteId);
    echo json_encode($floor_code);
}

function getRoomListbyfloorId() {
    $floorid = $_REQUEST['floorid'];
    $room_model = new Model_P_Room();
    $room_code = $room_model->getRoomListbyfloorId($floorid);
    echo json_encode($room_code);
}

function GenerateAssetCode() {
    $asset_code_gen_model = new Model_P_Code_Generation();
    echo $newCode = $asset_code_gen_model->generateCode('pun_asset_smart_place_master');
}

function getSiteName($site_id) {
    $site_model = new Model_P_SiteLocationMaster();
    $site_name = $site_model->getSiteName($site_id);
    echo json_encode(array('data' => array($site_id => $site_name)));
}

function addAssetToActivity($activity_id, $asset_ids) {
    $user_id = (isset($_SESSION['login_userid']) ? $_SESSION['login_userid'] : '');
    $asset_act_link_model = new Model_P_AssetActivityLinking();
    $asset_act_link_model->insert($asset_ids, $activity_id, date('Y-m-d H:i:s'), getLogger($user_id));
    echo json_encode(array('result' => 1));
}

function getRemainingAsset($activity_id) {
    $activity_model = new Model_P_ActivityMaster();
    $act_name = $activity_model->getName($activity_id);
    $asset_model = new Model_P_AssetSmartPlaceMaster();
    $assets_list = $asset_model->getAssets();
    $response = array(
        'name' => $act_name,
        'asset' => $assets_list
    );
    echo json_encode($response);
}

function getAllAsset($activity_id) {
    $activity_model = new Model_P_ActivityMaster();
    $act_name = $activity_model->getName($activity_id);
    $asset_model = new Model_P_AssetSmartPlaceMaster();
    $assets_list = $asset_model->getAllAssets();
    $response = array(
        'name' => $act_name,
        'asset' => $assets_list
    );
    echo json_encode($response);
}

/*  function addNewAsset() {

  if(getRequest('parent_asset_code')=='')
  {$parent_asset_code='NA';}
  else{$parent_asset_code=getRequest('parent_asset_code');}

  if(getRequest('capacity')=='')
  {$capacity='NA';}
  else{$capacity=getRequest('capacity');}

  if(getRequest('make')=='')
  {$make='NA';}
  else{$make=getRequest('make');}

  if(getRequest('model_number')=='')
  {$model_number='NA';}
  else{$model_number=getRequest('model_number');}

  if(getRequest('Serial_no')=='')
  {$Serial_no='NA';}
  else{$Serial_no=getRequest('Serial_no');}

  if(getRequest('client_asset_code')=='')
  {$client_asset_code='NA';}
  else{$client_asset_code=getRequest('client_asset_code');}

  if(getRequest('compass_asset_code')=='')
  {$compass_asset_code='NA';}
  else{$compass_asset_code=getRequest('compass_asset_code');}

  if(getRequest('remarks')=='')
  {$remarks='NA';}
  else{$remarks=getRequest('remarks');}


  $user_id = (isset($_SESSION['login_userid']) ? $_SESSION['login_userid'] : '');
  $addNewActivity_data = "uuid(),";
  $addNewActivity_data .= "'" . $_SESSION['login_siteid'] . "',";
  $addNewActivity_data .= "'" . getRequest('asset_code') . "',";
  $addNewActivity_data .= "'" . getRequest('asset_owner') . "',";
  $addNewActivity_data .= "'" . getRequest('asset_name') . "',";
  $addNewActivity_data .= "'" . $parent_asset_code . "',";
  // $addNewActivity_data .= "'" . $building.'-'.$floor.'-'.$room . "',";
  $addNewActivity_data .= "'" . getRequest('building_code') . "',";
  $addNewActivity_data .= "'" . getRequest('floor_code') . "',";
  $addNewActivity_data .= "'" . getRequest('room') . "',";
  $addNewActivity_data .= "'" . getRequest('critical') . "',";
  $addNewActivity_data .= "'" . getRequest('maintenance_po') . "',";
  $addNewActivity_data .= "'" . $capacity . "',";
  $addNewActivity_data .= "'" . getRequest('uom') . "',";
  $addNewActivity_data .= "'" . $make . "',";
  $addNewActivity_data .= "'" . $model_number . "',";
  $addNewActivity_data .= "'" . getRequest('asset_status') . "',";
  $addNewActivity_data .= "'" . getRequest('asset_type') . "',";
  $addNewActivity_data .= "'" . $Serial_no . "',";
  $addNewActivity_data .= "'" . getRequest('service_type') . "',";
  $addNewActivity_data .= "'" . getRequest('category') . "',";
  $addNewActivity_data .= "'" . $client_asset_code . "',";
  $addNewActivity_data .= "'" . $compass_asset_code . "',";
  $addNewActivity_data .= "'" . getRequest('warranty_app') . "',";
  $addNewActivity_data .= "'" . getRequest('warranty_date') . "',";
  $addNewActivity_data .= "'" . getRequest('installation_date') . "',";
  $addNewActivity_data .= "'" . $remarks . "',";
  $addNewActivity_data .= "'" . date('Y-m-d H:i:s') . "',";
  $addNewActivity_data .= getLogger($user_id);
  $Asset_model = new Model_P_AssetSmartPlaceMaster();
  $Asset_model->insert($addNewActivity_data);
  $Assetcode_model = new Model_P_Code_Generation();
  $res = $Assetcode_model->updateCode('pun_asset_smart_place_master');

  echo json_encode(array('result'=>1));
  } */

function addNewAsset() {
    if (getRequest('parent_asset_code') == '') {
        $parent_asset_code = 'NA';
    } else {
        $parent_asset_code = getRequest('parent_asset_code');
    }

    if (getRequest('capacity') == '') {
        $capacity = 'NA';
    } else {
        $capacity = getRequest('capacity');
    }

    if (getRequest('make') == '') {
        $make = 'NA';
    } else {
        $make = getRequest('make');
    }

    if (getRequest('model_number') == '') {
        $model = 'NA';
    } else {
        $model = getRequest('model_number');
    }

    if (getRequest('Serial_no') == '') {
        $serial = 'NA';
    } else {
        $serial = getRequest('Serial_no');
    }

    if (getRequest('client_asset_code') == '') {
        $client_asset_code = 'NA';
    } else {
        $client_asset_code = getRequest('client_asset_code');
    }

    if (getRequest('company_asset_code') == '') {
        $company_asset_code = 'NA';
    } else {
        $company_asset_code = getRequest('company_asset_code');
    }

    if (getRequest('remarks') == '') {
        $remarks = 'NA';
    } else {
        $remarks = getRequest('remarks');
    }

    //  if (getRequest('AssetType') == '') {
    //     $AssetType = 'NA';
    // } else {
    //     $AssetType = getRequest('AssetType');
    // }



    $user_id = (isset($_SESSION['login_userid']) ? $_SESSION['login_userid'] : '');
    //$uuid = $GLOBALS['connect']->rawQuery("Select UUID()");
    //$auto_id=$uuid[0]['UUID()'];
    $company_customer_id = "";
    $site_id = $_SESSION['login_siteid'];
    $asset_code = getRequest('asset_code');
    $asset_owner = getRequest('asset_owner');
    $asset_name = getRequest('asset_name');
    $client_qr_code = "";
    //  $parent_asset_code = $parent_asset_code;
    $asset_location = "";
    $building_code = getRequest('building_code');
    $floor_code = getRequest('floor_code');
    $room = getRequest('room');
    $critical = getRequest('critical');
    $maintenance = getRequest('maintenance_po');
    //  $capacity = $capacity;
    $uom = getRequest('uom');
    //  $make = $make;
    //  $model =$model_number;
    $asset_status = getRequest('asset_status');
    $asset_type = getRequest('asset_type');

    //$serial = $Serial_no ;
    $service_type = getRequest('service_type');
    $category = getRequest('category');
    // $client_asset_code = $client_asset_code ;
    // $compass_asset_code =$compass_asset_code ;
    $warranty_app = getRequest('warranty_app');
    $warranty_date = getRequest('warranty_date');
    $installation_date = getRequest('installation_date');
    //$remarks = $remarks;
    $sub_site_location_id = "";
    $wing = "";
    $geo_long = "";
    $amc = "";
    $brand_id = "";
    $supplier_id = "";
    $mapun_id = "";
    $purchase_code = "";
    $purchase_date = "";
    $depreciation_percent = "";
    $insurance_company = "";
    $insurance_agent = "";
    $insurance_date = "";
    $asset_sub_type = "";
    $verified_date = "";
    $verified_by_id = "";
    $manual_time = "";
    $updated_time = "";
    $deleted_date_time = "";
    $geo_lang = "";
    $sub_category_id = "";
    $alternate_part_no = "";
    $logger = getLogger($user_id);



    $datastring = "'$company_customer_id', '$site_id', '$asset_code', '$asset_owner','$asset_name', '$client_qr_code',"
        . "'$parent_asset_code','$asset_location', '$building_code','$floor_code', '$room', '$critical','$maintenance', "
        . "'$capacity', '$uom', '$make', '$model','$asset_status', '$asset_type', '$serial', '$service_type',"
        . "'$category', '$client_asset_code','$company_asset_code', '$warranty_app', '$warranty_date',"
        . " '$installation_date','$remarks', '$sub_site_location_id', '$wing', '$geo_long','$amc', "
        . "'$brand_id', '$supplier_id','$mapun_id', '$purchase_code', '$purchase_date', '$depreciation_percent',"
        . "'$insurance_company','$insurance_agent','$insurance_date', '$asset_sub_type','$verified_date', '$verified_by_id', '$manual_time', '$updated_time','$deleted_date_time', '$geo_lang', '$sub_category_id','$alternate_part_no',$logger";

    $image_for=$_REQUEST['image_for'];
    $filenm=$_FILES['filenm'];
 
    $Asset_model = new Model_P_AssetSmartPlaceMaster();
    $Asset_model->addNewAsset($datastring,$image_for,$filenm);
    $Assetcode_model = new Model_P_Code_Generation();
    $res = $Assetcode_model->updateCode('pun_asset_smart_place_master');

}

function getAssetTypeAndStatus() {
  $company_id='';
    $asset_type_model = new Model_P_AssetType();
    $assets_type = $asset_type_model->getAssetType($company_id);

    $status_model = new Model_K_Status();
    $status = $status_model->getStatus();
    $response = array(
        'assettype' => $assets_type,
        'status' => $status
    );
    echo json_encode($response);
}

function getAssetType() {
    $asset_type_model = new Model_P_AssetType();
    $assets_type = $asset_type_model->getAssetType();
 

    echo json_encode($assets_type);
}

function getAssetStatus() {

    $status_model = new Model_K_Status();
    $status = $status_model->getStatus();

    echo json_encode($status);
}

function addAssetType() 
{
    $user_cid = $_SESSION['login_cid'];
    $user_id = (isset($_SESSION['login_userid']) ? $_SESSION['login_userid'] : '');
    $main_type = '';
    if (getRequest('main_type')) {
        $main_type = 'Meter';
    } else {
        $main_type = 'NonMeter';
    }
    $assetcode=getRequest('asset_code');
  // print_r($assetcode);
     $assettype=getRequest('assettype');
    // print_r($assettype);
    // die;
     
    $asset_type_model = new Model_P_AssetType();
    $assetresult = $asset_type_model->addNewAssetType($user_cid, $assetcode, $assettype, $main_type, $user_id);
    echo json_encode($assetresult);
}

// function addAssetTypee()
// {
//   $assetcode=$_REQUEST['asset_code'];
//   $asset_type=$_REQUEST['assettype'];
//   $asset_type_model = new Model_P_AssetType();
//   $assetresult = $asset_type_model->addNewAssetTypee($autoid,$assetype);
//   echo json_encode($assetresult);
// }

?>