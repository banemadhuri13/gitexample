<?php
include '../includes/Function.php';
include '../includes/session.php';
include '../models/Model_Tenants.php';
include '../models/Model_Tenant_Users.php';


$action = getRequest('action');
if ($action == "loadtenant") {
    loadTenants();
}

else if ($action == "adduser") {
    addNewUser();
}

else if ($action == "loadtenant1") {
    loadTenants1($_REQUEST['value']);
}
else if ($action == "approveuser") 
{
    approveuser($_REQUEST['id']);
}

else if ($action == "blockuser") 
{
    blockuser($_REQUEST['id']);
}



function loadTenants() {
    $tenantmodel = new Model_Tenants();
    $res = $tenantmodel->getAllTenants();  
    echo json_encode($res);
}


function loadTenants1($id) {
    $tenantmodel = new Model_Tenants();
    $res = $tenantmodel->getAllTenants1($id);  
    echo json_encode($res);
}

function addNewUser() 
{	

	$Site_Id=$_SESSION['login_siteid'];
	$Company= $_POST['Company'];
    $name = $_REQUEST['name'];
    $mailid = $_REQUEST['mailid'];
    $contact = $_REQUEST['contact'];
    $pwd = $_REQUEST['pwd'];
    $cpwd = $_REQUEST['cpwd'];
    $section = $_REQUEST['section'];
    $user_model = new Model_Tenant_Users();
	$res1 = $user_model->addNewUser($name,$mailid,$contact,$pwd,$cpwd,$Company,$Site_Id,$section);
	echo json_encode($res1);
					
	
}

function approveuser($id) 
{	
    $user_model = new Model_Tenant_Users();
	$res1 = $user_model->approveuser($id);
	echo json_encode($res1);
}

function blockuser($id) 
{	
    $user_model = new Model_Tenant_Users();
	$res1 = $user_model->blockuser($id);
	echo json_encode($res1);
}



?>