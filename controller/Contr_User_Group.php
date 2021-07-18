<?php
if (session_status() == PHP_SESSION_NONE) {
    @session_start();
}
include '../includes/Function.php';
include '../models/Model_P_UserGroup.php';
$action = getRequest('action');

if ($action == 'updategrp') {
    updateGroup();
} else if ($action == 'deletegrp') {
    deleteGroup();
} else if ($action == 'addgrp') {
    addGroup();
}

function addGroup() {
    $group_name = $_REQUEST['groupname'];
    $gm = new Model_P_UserGroup();
    $r_gm = $gm->addGroup($group_name);
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