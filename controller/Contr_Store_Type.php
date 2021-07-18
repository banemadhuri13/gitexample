<?php
if (session_status() == PHP_SESSION_NONE) {
    @session_start();
}
include '../includes/Function.php';
include '../models/Model_P_Store_Type.php';
$action = getRequest('action');
 $store_type_model = new Model_P_Store_Type();
if ($action == 'updategrp') {
    updateGroup();
} else if ($action == 'deletegrp') {
    deleteGroup();
} else if ($action == 'addgrp') {
    addGroup();
}else if ($action == 'addstrtyp') {
    addstoretype();
}

function addstoretype() {
    $Store_type_name = $_REQUEST['StoreTypeName'];
	$Description = $_REQUEST['Description'];
	$data="'" . $Store_type_name . "',";
	$data.="'" . $Description . "'";
    
    $r_gm = $store_type_model->addStoreType($Store_type_name,$Description);
    echo $r_gm;
}

function updateGroup() {
    $group_id = $_REQUEST['group_id'];
    $groupname = $_REQUEST['groupname'];
    $gm = new Model_P_UserGroup();
    $r_gm = $gm->updateGroup($group_id, $groupname);
    echo $r_gm;
}

function deleteGroup() {
    $group_id = $_REQUEST['group_id'];
    $gm = new Model_P_UserGroup();
    $r_gm = $gm->deleteGroup($group_id);
    echo $r_gm;
}
?>