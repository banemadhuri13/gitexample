<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

if (session_status() == PHP_SESSION_NONE) {
    @session_start();
}
include '../includes/Function.php';
include '../models/Model_Request_quotes.php';
include '../classes/mail_send.class.php';

$action = getRequest('action');
if ($action == 'addrequestquote') {
    $quotesitem = $_REQUEST['item'];
    $vendor = $_REQUEST['vendor'];
    addrequestquotes($quotesitem,$vendor);
} else if ($action == 'requestforRFQ'){
  requestforRFQ();
}else if ($action == 'getcode'){
  getcode();
}else if ($action == 'updatequotes'){
  updatequotes();
}else if ($action == 'addvendorforRFQ'){
  addvendorforRFQ();
}



function addvendorforRFQ()
{
  $Q_Id=$_REQUEST['Quotation_Master_Id'];
  $Vendor_Ids=$_REQUEST['vendor'];

  $gm1 = new Model_Request_quotes();
  $res1 = $gm1->addvendorforRFQ($Q_Id,$Vendor_Ids); 
  foreach ($res1 as $maildata) {
  $itm[]=$maildata['Item_Id'];
  $qtyes[]=$maildata['Quantity'];  
  $link_id[]=$maildata['Requisition_ItemLinking_Id'];
  }
  $itmid=implode(",", $itm);
  $qty=implode(",", $qtyes);
  $linking_id1=implode(",", $link_id);

  $code1=$res1[0]['Quotation_Code'];
  $Remark=$res1[0]['Remark'];
  $lastdate=$res1[0]['Last_day_of_submission'];
  $gm3 = new Model_Request_quotes();
  $res3 = $gm3->getmaildata($itmid,$qty,$Vendor_Ids,$code1,$Remark,$lastdate,$Q_Id,$linking_id1);
  $reqlinkid=explode(',', $linking_id1);
  $linkides=implode("','", $reqlinkid);
    $q12="SELECT distinct rm.`Requisition_No` FROM pun_inventory_requisition_master rm 
        LEFT JOIN pun_inventory_requisition_item_linking ril  ON ril.Requisition_Id = rm.Auto_Id
        where ril.`Auto_Id` IN ('".$linkides."') AND rm.Status = 'Approved'";
    $result12 = $GLOBALS['connect']->rawQuery($q12);
    $cod=[];
    foreach ($result12 as $k) {
      $reqno=$k['Requisition_No'];
      array_push($cod, $reqno);
    }
    $imp=implode(',', $cod); 
    
  include_once '../includes/pdf.php';
  $rand= mt_rand(100000, 999999);
  $file_name = '../upload/RFQ_Details/"'.$rand.'".pdf';
                    $pdf = new Pdf();
                    $pdf->load_html($res3['htmlattch']);
                    $pdf->render();
                    $file = $pdf->output();
                    file_put_contents($file_name, $file);
                    foreach ($res3['vendordata'] as $key) {
                      //$touser = [];
                      $touser=$key['Email_Id'];
                      $subjectuser = "Request For Quotation For Requisition Code (".$imp.")";
                      $ccuser =array('krishan.kumar22@cbre.com','facilities.escalations@onehorizoncenter.com','vivek.singh1@cbre.com') ;
                      $msguser = "<b>Dear " . $key['Company_Name'] . " & Team,<b><br>Please Find Attached RFQ for items required by us.<br/>Please Submit your best competitive rate & any other feature list for the items listed in the attached BOQ.<br><br>
                     Krishan.Kumar22@cbre.com Click this Email-Id To Reply.<br><br>
                      Thankyou<br>Purchase Department<br>".$_SESSION['login_companyname']." ";
                      $sendmail_model = new Mail_Send();
                      $mailres = $sendmail_model->SendMailWithAttachment123($file_name,$touser, $ccuser, $subjectuser, $msguser);
                    }
                    unlink($file_name);
  return 1;                   

}

function updatequotes() 
{

  $itmid=$_REQUEST['itemids'];
  //$GSTRate=$_REQUEST['Gst'];
  $rate=$_REQUEST['unitrate'];
  $vndr=$_REQUEST['vendor'];  
  $Amt=$_REQUEST['Amo'];
  $inirate=$_REQUEST['Init_rates'];
  $finamt=$_REQUEST['Final_Amt'];
   $gm11= new Model_Request_quotes();
   $res11 = $gm11->updatequotes($itmid,$rate,$vndr,$Amt,$inirate,$finamt);
   echo $res11;
}

function addrequestquotes($quotesitem,$vendor) 
{
   $gm = new Model_Request_quotes();
   $res = $gm->addrequestquotes($quotesitem,$vendor);
   //echo json_encode($res);
  // print_r($res);
   echo $res;
  //echo json_encode($quotes);
}
function requestforRFQ()
{
  $linking_id1=$_REQUEST['linking_id'];
  $itmid=$_REQUEST['itemids'];
  $qty=$_REQUEST['quantity'];
  // $rate=$_REQUEST['unitrate'];
  $vndr=$_REQUEST['vendor'];  
  $code1=$_REQUEST['code'];
  $Remark=$_REQUEST['remark']; 
  $lastdate=$_REQUEST['lastsubdate'];

  $gm1 = new Model_Request_quotes();
  $res1 = $gm1->addRFQ($itmid,$qty,$vndr,$code1,$Remark,$lastdate,$linking_id1); 
  // $rate,
  $gm3 = new Model_Request_quotes();
  $res3 = $gm3->getmaildata($itmid,$qty,$vndr,$code1,$Remark,$lastdate,$res1,$linking_id1);
  // print_r($res3);
  // $rate,

  $reqlinkid=explode(',', $linking_id1);
    $linkides=implode("','", $reqlinkid);
    $q12="SELECT distinct rm.`Requisition_No` FROM pun_inventory_requisition_master rm 
        LEFT JOIN pun_inventory_requisition_item_linking ril  ON ril.Requisition_Id = rm.Auto_Id
        where ril.`Auto_Id` IN ('".$linkides."') AND rm.Status = 'Approved'";
    $result12 = $GLOBALS['connect']->rawQuery($q12);
    $cod=[];
    foreach ($result12 as $k) {
      $reqno=$k['Requisition_No'];
      array_push($cod, $reqno);
    }
    $imp=implode(',', $cod); 
    
  include_once '../includes/pdf.php';
  $rand= mt_rand(100000, 999999);
  $file_name = '../upload/RFQ_Details/"'.$rand.'".pdf';
                    $pdf = new Pdf();
                    $pdf->load_html($res3['htmlattch']);
                    $pdf->render();
                    $file = $pdf->output();
                    file_put_contents($file_name, $file);
                    foreach ($res3['vendordata'] as $key) {
                     // $touser = [];
                      $touser=$key['Email_Id'];
                      $subjectuser = "Request For Quotation For Requisition Code (".$imp.")";
                      $ccuser = array('krishan.kumar22@cbre.com','facilities.escalations@onehorizoncenter.com','vivek.singh1@cbre.com');
                      $msguser = "<b>Dear " . $key['Company_Name'] . " & Team,<b><br>Please Find Attached RFQ for items required by us.<br/>Please Submit your best competitive rate & any other feature list for the items listed in the attached BOQ.<br><br>
                      Krishan.Kumar22@cbre.com Click this Email-Id To Reply.<br><br>
                      Thankyou<br>Purchase Department<br>".$_SESSION['login_companyname']."<br>";
                      $sendmail_model = new Mail_Send();
                      $mailres = $sendmail_model->SendMailWithAttachment123($file_name,$touser, $ccuser, $subjectuser, $msguser);
                    }
                    unlink($file_name);
  return 1;                   

}
function getcode()
{
   $gm2 = new Model_Request_quotes();
  $res2 = $gm2->getcode1();
  echo json_encode($res2); 
}

