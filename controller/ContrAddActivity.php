<?php
	@session_start();
   	include '../includes/MysqliDb.php';
	include '../includes/Function.php';
	include '../models/Model_P_Form.php';
	include '../models/Model_P_FormStructure.php';
	include '../models/Model_P_ActivityMaster.php';
	include '../models/Model_P_EmployeeSiteLinking.php';
	include '../models/Model_P_ActivityFrequency.php';
	include '../models/Model_P_AssetActivityLinking.php';
	include '../models/Model_P_AssetSmartPlaceMaster.php';
	include '../models/Model_P_CategoryMaster.php';
	include '../models/Model_K_Form.php';
	include '../models/Model_K_FormStructure.php';

	$action = getRequest('action');
	if($action == null) {
	} else if($action == 'insert') {
	  	insertion();
	} else if($action == 'assetlist') {
		assetsList();
	} else if($action == 'assetlist2') {
		assetsList2();
	} else if($action == 'assetlistforlogsheet') {
		assetsListforlogsheet();
	} else if($action == 'activitylist') {
		activityList($_REQUEST['asset_id']);
	} else if($action == 'frm_list') {
		getFormList();
	} else if($action == 'acttype') {
		getActType();
	} else if($action == 'changeActivity') {
		changeActivitydetails();
	} else if($action == 'typelst') {
		getActtypelist();
	}

	function getActtypelist() {
	    $type_model = new Model_P_CategoryMaster();
	    $types = $type_model->getTypeName();
	    echo json_encode($types);
	}

	function changeActivitydetails(){
            $userID = (isset($_SESSION['login_userid'])? $_SESSION['login_userid'] : '');
            $act_Id=getRequest('activity_id');
            $act_Name=getRequest('Activity_Name');
            $category_id=getRequest('acttype');
            $form_name=getRequest('Form_Name');
            $activity_model = new Model_P_ActivityMaster();
            $act_auto_id=$activity_model->updateActivity($act_Id,$act_Name,$category_id);
            $form_id=$activity_model->getFormForActivity($act_Id);
            //print_r($form_id);
            $form_model= new Model_P_Form();
            $changeform=$form_model->udpFormName($form_id,$form_name);
            $response = array('result' => 1);
            echo json_encode($response);
        }

	function insertion() {
		//print_r($_REQUEST);
	    $userID 	= (isset($_SESSION['login_userid'])? $_SESSION['login_userid'] : '');
		$Activity_data = array(
                            'auto_id'=> 'uuid',
                            'Form_Id'=> getRequest('form_id'),
                            'Company_Customer_Id'=> getRequest('company_id'),
                            'Site_Location_Id'=> getRequest('emp_site_id'),
                            'activity_name'=> getRequest('act_name'),
                            'activity_code'	=> getRequest('act_code'),
                            'activity_type'	=> getRequest('type_id'),
			);
//echo "a1@";
		// To Fetch form name and type from ker_form
		$ker_form_model = new Model_K_Form();
		$form_type = $ker_form_model->getFormType(getRequest('form_id'));
		$form_name = $ker_form_model->getFormName(getRequest('form_id'));
        
		$site_form_model = new Model_P_Form();
		$form = $site_form_model->insert($form_name, $form_type, getRequest('site_id'), getLogger($userID));
//        print_r($form);
        
		$form_model = new Model_K_FormStructure();
		$form_design = $form_model->getDesign(getRequest('form_id'));
        //  print_r($form_design);
        // echo "___________________________";
        
		$site_form_s_model = new Model_P_FormStructure();
		$site_form_s_model->copyStructure($form_design, $form['Auto_Id'], date('Y-m-d H:i:s'));

		$addActivity_data  = "uuid(),";
		$addActivity_data .= "'". $form['Auto_Id']			 						."',";
		$addActivity_data .= "'". $Activity_data['Company_Customer_Id'] 			."',";
		$addActivity_data .= "'". $Activity_data['Site_Location_Id'] 				."',";
		$addActivity_data .= "'". $Activity_data['activity_code'] 					."',";
		$addActivity_data .= "'". $Activity_data['activity_name']   				."',";
		$addActivity_data .= "'". $Activity_data['activity_type']   				."',";
		$addActivity_data .= "'',";
		$addActivity_data .= getLogger($userID);
		$activity_model = new Model_P_ActivityMaster();
		$act_auto_id=$activity_model->addActivity($addActivity_data);
		//exit();
		$asset_linking_model = new Model_P_AssetActivityLinking();
		$asset_linking_model->insert(getRequest('assets'), $act_auto_id['Auto_Id'], date('Y-m-d H:i:s'), getLogger($userID));
			
			$response = array(
						'result'	=> 1,
					);
			echo json_encode($response);
	}



	function assetsList() {
		$asset_model = new Model_P_AssetSmartPlaceMaster();


		$asset_list = $asset_model->getAssets();
		

		echo json_encode($asset_list);
	}

	function assetsList2() {
		$asset_model = new Model_P_AssetSmartPlaceMaster();


		$asset_list = $asset_model->getAllAssets();
		

		echo json_encode($asset_list);
	}
	
	function assetsListforlogsheet() {
		$asset_model = new Model_P_AssetSmartPlaceMaster();
		$asset_list = $asset_model->getLinkedAssets();
		

		echo json_encode($asset_list);
	}

	function activityList($asset_id) {
		$asset_activity_link_model	= new Model_P_AssetActivityLinking();
		$activity_model = new Model_P_ActivityMaster();
		$activityname=array();

		$activity_id_list = $asset_activity_link_model->getActivityID($asset_id);

		foreach ($activity_id_list as $act_id) {
			$activityname[$act_id]= $activity_model->getName($act_id);
		}
			
		echo json_encode($activityname);
	}
    
    
    function getActType() {
		$category_master_model	= new Model_P_CategoryMaster();
		
		$acttype_id_list = $category_master_model->getTypeName();

		echo json_encode($acttype_id_list);
	}
	
	
	function getFormList() {
		$form_model = new Model_K_Form();
		echo json_encode($form_model->getList());
	}




?>