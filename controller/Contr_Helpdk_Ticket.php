<?php
if (session_status() == PHP_SESSION_NONE) {
    @session_start();
}
include '../includes/Function.php';
include '../models/Model_P_Floor.php';
include '../models/Model_P_Building.php';
include '../models/Model_P_Room.php';
include '../models/Model_helpdk.php';
include '../models/Model_Helpdk_Ticket.php';
include '../classes/mail_send_in.class.php';
$action = getRequest('action');

if ($action == null) {
} else if ($action == 'getBuildings') {
    $Building_model = new Model_P_Building();
    $BuildingsList = $Building_model->getBuildings();
    $response = array(
        'BuildingsList' => $BuildingsList,
    );
    echo json_encode($response);
} else if ($action == 'getFloors') {
    $floor_model = new Model_P_Floor();
    $floorsList = $floor_model->getFloors(getRequest('buildingId'));
    $response = array(
        'floorsList' => $floorsList,
    );
    echo json_encode($response);
} else if ($action == 'getRooms') {
    $room_model = new Model_P_Room();
    $roomsList = $room_model->getRooms(getRequest('floorID'));
    $response = array(
        'roomsList' => $roomsList,
    );
    echo json_encode($response);
} else if ($action == 'getCategory') {
    $category_model = new Model_helpdk();
    $categories = $category_model->getCategory();
    $response = array(
        'categoryList' => $categories,
    );
    echo json_encode($response);
} else if ($action == 'getSubCategory') {
    $subCategory_model = new Model_helpdk();
    $subCategories = $subCategory_model->getSubCategory(getRequest('Category'));
    $response = array(
        'subCategoryList' => $subCategories,
    );
    echo json_encode($response);
} else if ($action == 'getReasons') {
    $reasons_model = new Model_helpdk();
    $reasons = $reasons_model->getReasons(getRequest('Category'), getRequest('subCategory'));
    $response = array(
        'reasonsList' => $reasons,
    );
    echo json_encode($response);
} else if ($action == 'allassets') {
    $asset_model = new Model_helpdk();
    $asset_list = $asset_model->getAllAssets(getRequest('building'), getRequest('floor'), getRequest('room'));
    $response = array(
        'assetsList' => $asset_list,
    );
    echo json_encode($response);
} else if ($action == 'addnewtkt') {
    addNewTicket();
} else if ($action == 'updatetkt') {
    updateTicket();
} else if ($action == 'getdashboardcounts') {
    getDashboardCounts(getRequest('startdate'),getRequest('enddate'));
}
else if ($action == 'getComplex') {
    getComplex();
}
else if ($action == 'getAllBuildings') {
    $tkt_model = new Model_Helpdk_Ticket();
    $BuildingsList = $tkt_model->getAllBuildings(getRequest('complexid'));
    $response = array(
        'BuildingsList' => $BuildingsList,
    );
    echo json_encode($BuildingsList);
}

function getComplex() {
    $tkt_model = new Model_Helpdk_Ticket();
    $r_complex = $tkt_model->getComplex();
    echo json_encode($r_complex);
}

function getDashboardCounts($startdate,$enddate) {
    $tkt_model = new Model_Helpdk_Ticket();
    $r_tkts = $tkt_model->getDashboardCounts($startdate,$enddate);
    echo $r_tkts;
}


function addNewTicket() {
    $building = explode("|", $_REQUEST['building']);
    $building_id = $building[0];
    $Site_Location_Id = $building[1];
	$building_name = $building[2];
	
        $floor_id = 0;
        $floor_name = "";		
    if (isset($_REQUEST['floor'])) {
        $floor = explode("|", $_REQUEST['floor']);
		$floor_id = $floor[0];
        @$floor_name = $floor[1] ;		
    }

        $room_id = 0;
		$room_name = "";
    if (isset($_REQUEST['room'])) {
        $room = explode("|", $_REQUEST['room']);
		$room_id = $room[0];
        @$room_name = $room[1];		
    }

    $Area = $_REQUEST['txtArea'];
	
        $assets_id = 0;
		$assets_name = "";
	if (isset($_REQUEST['astid-1'])) {
        $assets = explode("|", $_REQUEST['astid-1']);
		$assets_id = $assets[0];
		@$assets_name = $assets[1];
		
    }
	
	if (isset($_REQUEST['Services'])) {
        $services = explode("|", $_REQUEST['Services']);
		$services_id = $services[0];
		$services_name = $services[1];
	}
	
	if (isset($_REQUEST['ServiceArea'])) {
        $servicearea = explode("|", $_REQUEST['ServiceArea']);
	    $servicearea_id = $servicearea[0];
		$servicearea_name = $servicearea[1];
	}
	
	if (isset($_REQUEST['serviceIssue'])) {
        $issues = explode("|", $_REQUEST['serviceIssue']);
		$issues_id = $issues[0];
		$issues_name = $issues[1];
	}
	
    $mailonclsr = $_REQUEST['mailonclsr'];
    $description = $_REQUEST['Description'];
    $client_name = $_REQUEST['txtName'];
    $client_contact_no = $_REQUEST['txtContactNo'];
    $client_email = $_REQUEST['txtEmail'];

    if ($mailonclsr == true) {
        $mailonclosure = 1;
    } else {
        $mailonclosure = 0;
    }
	
	$date = date('d-m-Y h:i A', time());
	
    $tkt_model = new Model_Helpdk_Ticket();
    $tkt_arr = $tkt_model->getCode();
//    print_r($tkt_arr);
    if ($tkt_arr) {
        $tkt_code = $tkt_arr['ticket_code'];
        $tkt_id = $tkt_arr['id'];
        $tktlstcode = $tkt_arr['last_code'];
        $userid = $_SESSION['login_userid'];
        $selected_main_db = $_SESSION['login_masterdb'];
        $selected_site_db = $_SESSION['login_db'];

        $user_type = 'FM';
        // add ticket
        $data = "'$userid','$Site_Location_Id','$building_id','$floor_id','$room_id','$assets_id','$services_id','$servicearea_id','$issues_id','Open','$mailonclosure','$description','$tkt_code','I','$selected_main_db','$selected_site_db','L0','$user_type','$Area','$client_name','$client_contact_no','$client_email',''";


        $last_inserted_tktid = $tkt_model->addNewTicket($data);
        // echo $data;
        //upload images 
        $desired_dir = "../../HelpdeskImages/";
//            $res = 1;
        $errors = array();
        foreach ($_FILES['files']['tmp_name'] as $key => $tmp_name) {
//                $file_ext = substr(strrchr(basename($_FILES['files']['name']), '.'), 1);
            $file_name = $key . $_FILES['files']['name'][$key];
            $file_size = $_FILES['files']['size'][$key];
            $file_tmp = $_FILES['files']['tmp_name'][$key];
            $file_type = $_FILES['files']['type'][$key];
            if ($file_size > 2097152) {
                $errors[] = 'File size must be less than 2 MB';
            }
            $final_nm = "$desired_dir" . "helpdk_@" . "" . $tkt_code . "" . $file_name;
            if (empty($errors) == true) {
                if (move_uploaded_file($file_tmp, "$desired_dir" . "helpdk_@" . "" . $tkt_code . "" . $file_name)) {
                    $imgdata = "'$final_nm','$file_type','$file_size','$last_inserted_tktid','$userid'";
                    $res = $tkt_model->addTicketImages($imgdata);
                }
            }
        }
		
		
        //send mail to requester  
        $from = "support@punctualiti.in"; 
        //$touser = $_SESSION['login_usermail'];

           if ($client_email == "")
           {
              $touser = "jubernkhan@yahoo.com";
           } 
            else
           {
              $touser = $client_email;    
           }
        
        $ccuser = array("");
        $subjectuser =  "One Horizon Center Helpdesk - Ticket ID : " . $tkt_code . " - " .$services_name ." - ". $servicearea_name ." - ".$issues_name;
		
					// location of create_ticket_email_template file
                     $template_file = "../email_template/helpdesk/create_internal_ticket_email_template.php";
					 
					 // create a swap varibles array
						$swap_var = array(
						   "{CLIENTNAME}" => $client_name,
						   "{TICKET_CODE}" => $tkt_code,
						   "{DATETIME}" => $date,
						   "{BUILDINGNAME}" => $building_name,
						   "{FLOORNAME}" => $floor_name,
						   "{ROOMNAME}" => $room_name,
						   "{CATEGORY}" => $services_name,
						   "{SUBCATEGORY}" => $servicearea_name,
						   "{ISSUE}" => $issues_name,
						   "{STATUS}" => "Open",
						   "{DESCRIPTION}" => $description 
						   
						);
						
						if (file_exists($template_file))
							$content = file_get_contents($template_file);
						else 
							die("Unable to locate the create ticket template file");

						// search replace all swap variables
						foreach(array_keys($swap_var) as $key){
							if(strlen($key) > 2 && trim($key) != "")
							$content = str_replace($key, $swap_var[$key], $content);
						}
		
		

        $sendmail_model = new Mail_Send();
        $mailres = $sendmail_model->SendMail($touser, $ccuser, $subjectuser, $content, $from);
		
		/* Get L1 email for notifiying the responser (profile)*/
		$tkt_model = new Model_Helpdk_Ticket();		
		$l1_email =  $tkt_model->getL1Email($services_id, $servicearea_id, $issues_id);
		$cc = array("facilities.helpdesk@onehorizoncenter.com","khan322@gmail.com");
		
		// location of create_ticket_email_template file
        $template_file = "../email_template/helpdesk/create_internal_ticket_assigned_to_email_template.php";
		
		// create a swap varibles array
			$swap_var = array(
			   "{CLIENTNAME}" => "",
			   "{TICKET_CODE}" => $tkt_code,
			   "{DATETIME}" => $date,
			   "{BUILDINGNAME}" => $building_name,
			   "{FLOORNAME}" => $floor_name,
			   "{ROOMNAME}" => $room_name,
			   "{CATEGORY}" => $services_name,
			   "{SUBCATEGORY}" => $servicearea_name,
			   "{ISSUE}" => $issues_name,
			   "{STATUS}" => "Open",
			   "{DESCRIPTION}" => $description,
			   "{REQUESTER}" => $client_name,
			   "{REQ_EMAIL}" => $client_email			   
			   
			);
		
		if (file_exists($template_file))
			$content = file_get_contents($template_file);
		else 
			die("Unable to locate the email_template file");

		// search replace all swap variables
		foreach(array_keys($swap_var) as $key){
			if(strlen($key) > 2 && trim($key) != "")
			$content = str_replace($key, $swap_var[$key], $content);
		}
		
		$sendmail_model = new Mail_Send();
        $mailres = $sendmail_model->SendMail($l1_email, $cc, $subjectuser, $content, $from);

        $res2 = $tkt_model->updatetktCode($tkt_id, $tktlstcode);
        echo urldecode($tkt_code);
    } else {
        echo "0";
    }
}


function updateTicket() {
    $desc = $_REQUEST['description_txt'];
    $status = $_REQUEST['drpstatus'];
    $id = $_REQUEST['ticket_id'];
    $prevstatus = $_REQUEST['curr_status'];
    $tkt_code = $_REQUEST['ticket_code'];
    $mail_on_closure = $_REQUEST['mail_on_closure'];
    $client_name = $_REQUEST['client_name'];
    $guest_name = $_REQUEST['guest_name'];
    $client_email = $_REQUEST['client_email'];
    $guest_email = $_REQUEST['guest_email'];
    $user_type = $_REQUEST['user_type'];
	$Tenant = $_REQUEST['Tenant'];
    $onhold_till = '';

    if ($status == 'Hold') {
        $onhold_till1 = $_REQUEST['starton'];
        $onhold_till = date("Y-m-d H:i:s", strtotime($onhold_till1));
    }

    $tkt_model = new Model_Helpdk_Ticket();
    $r_tkts = $tkt_model->updateTicket($id, $desc, $status, $prevstatus, $onhold_till, $Tenant);
	
	//upload images 
        $desired_dir = "../../HelpdeskImages/";
		$userid = $_SESSION['login_userid'];
//            $res = 1;
        $errors = array();
        foreach ($_FILES['files']['tmp_name'] as $key => $tmp_name) {
//                $file_ext = substr(strrchr(basename($_FILES['files']['name']), '.'), 1);
            $file_name = $key . $_FILES['files']['name'][$key];
            $file_size = $_FILES['files']['size'][$key];
            $file_tmp = $_FILES['files']['tmp_name'][$key];
            $file_type = $_FILES['files']['type'][$key];
            if ($file_size > 2097152) {
                $errors[] = 'File size must be less than 2 MB';
            }
            $final_nm = "$desired_dir" . "helpdk_@" . "" . $tkt_code . "" . $file_name;
            if (empty($errors) == true) {
                if (move_uploaded_file($file_tmp, "$desired_dir" . "helpdk_@" . "" . $tkt_code . "" . $file_name)) {
                    $imgdata = "'$final_nm','$file_type','$file_size','$id','$userid'";
                    $res = $tkt_model->addTicketImages($imgdata);
                }
            }
        }
    
    if ($r_tkts) {
        $from = "support@punctualiti.in";
        //$touser = $_SESSION['login_usermail'];

           if ($user_type == 'FM')
           {
            $to = $client_email;
            $name = $client_name;
           } 
            else
           {
            $to = $guest_email;
            $name = $guest_name;
           }
 
        
        $cc = array("");
        //if ($touser != '') {
           //if ($mail_on_closure != "1") {
                if ($status == "Closed") {
                    $subject =  " One Horizon Center Helpdesk - Ticket ID : " . $tkt_code . " Your ticket has been closed";
                    
					// location of closed_ticket_email_template file
                     $template_file = "../email_template/helpdesk/closed_ticket_email_template.php";
					 
					 SwapAndSend($name, $tkt_code, $desc, $to, $cc, $from, $template_file, $subject, '');               

                    
                } 
				
				else if ($status == "Rejected") {
                    $subject =  "One Horizon Center Helpdesk - Ticket ID : " . $tkt_code . " Your ticket has been rejected";
                    
					// location of rejected_ticket_email_template file
                     $template_file = "../email_template/helpdesk/rejected_ticket_email_template.php";
					 SwapAndSend($name, $tkt_code, $desc, $to, $cc, $from, $template_file, $subject, '');
					 
                }
				
				else if ($status == "Hold") {
                    
					$subject = "One Horizon Center Helpdesk - Ticket ID : " . $tkt_code . " Your ticket has been put on hold";
                    
                    // location of hold_ticket_email_template file
                     $template_file = "../email_template/helpdesk/hold_ticket_email_template.php";
					 SwapAndSend($name, $tkt_code, $desc, $to, $cc, $from, $template_file, $subject, $onhold_till);
					 
                }
           // }
      //  }
        echo "1";
    } else {
        echo "0";
    }
}

function SwapAndSend($name, $tkt_code, $desc, $to, $cc, $from, $template_file, $subject, $onhold_till) {
	
		// create a swap varibles array
		$swap_var = array(
		   "{CLIENTNAME}" => $name,
		   "{TICKETCODE}" => $tkt_code,
		   "{HOLDTILL}" => $onhold_till,
		   "{DESCRIPTION}" => $desc 
		   
		);
		
		if (file_exists($template_file))
			$content = file_get_contents($template_file);
		else 
			die("Unable to locate the email_template file");

		// search replace all swap variables
		foreach(array_keys($swap_var) as $key){
			if(strlen($key) > 2 && trim($key) != "")
			$content = str_replace($key, $swap_var[$key], $content);
		}

			
		$sendmail_model = new Mail_Send();
		$mailres = $sendmail_model->SendMail($to, $cc, $subject, $content, $from);

}




?>