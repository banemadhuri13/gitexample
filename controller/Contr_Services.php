<?php
if (session_status() == PHP_SESSION_NONE) {
    @session_start();
}
include '../includes/Function.php';
include '../models/Model_P_Services.php';
$action = getRequest('action');

if ($action == 'getServicesByUserId') {
    getServicesByUserId();
} else if ($action == 'updateservice') {
    updateServices();
} else if ($action == 'deleteservice') {
    deleteServices();
} else if ($action == 'addservice') {
    addServices();
} else if ($action == 'servicenames') {
    listServices();
}

function getServicesByUserId() {
    $EmployeeId = $_REQUEST['EmployeeId'];
    $gm = new Model_P_Services();
    $res = $gm->getServicesByUserId($EmployeeId);
    echo json_encode($res);
}


function addServices() {
    $service_name = $_REQUEST['servicename'];
    $service_id = $_REQUEST['services_list'];
    $gm = new Model_P_Services();
    $r_gm = $gm->addServices($service_id, $service_name);
    echo $r_gm;
}

function updateServices() {
    $service_id = $_REQUEST['service_id'];
    $servicename = $_REQUEST['servicename'];
    $parentidchk = $_REQUEST['parent_chk'];
	$parentid = $_REQUEST['parent_id'];

    $gm = new Model_P_Services();
    $r_gm = $gm->updateServices($parentidchk, $parentid, $service_id, $servicename);
    echo $r_gm;
}

function deleteServices() {
    $service_id = $_REQUEST['service_id'];
    $gm = new Model_P_Services();
    $r_gm = $gm->deleteServices($service_id);
    echo $r_gm;
}

function listServices() {
    $service_model = new Model_P_Services();
    $services_list = $service_model->getServiceList();
    echo json_encode($services_list);
}
