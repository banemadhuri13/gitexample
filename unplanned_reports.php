<?php
include('./includes/session.php');
include 'includes/connect.php';
date_default_timezone_set('Asia/Kolkata');
@session_start();
if (isset($_SESSION['login_db'])) {
    ?>
    <ul class="page-breadcrumb breadcrumb">
        <li>
            <i class="fa fa-home"></i>
            <a onclick="refresh();">Home</a>
        </li>
        <li>
            Status Wise Report
        </li>
    </ul>
     <div class="row">
        <div class="col-md-12">
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption">
                        <span class="caption-subject bold uppercase">
                            <i class="fa fa-list-ol" aria-hidden="true"></i> Status Wise Report
                        </span>
                    </div>
                </div>
                <div class="portlet-body">
                    <form action="" id="unplannedfrm" name="unplannedfrm" method="post">
                        <div class="row">
						    <div class="form-group col-md-3">
						      <label for="inputFrom">From</label>
						      <input type="text" id="dateFrom" name="dateFrom" autocomplete="off" class="date form-control mandatory">
                              <div class="form-control-focus"></div>
						    </div>
						    <div class="form-group col-md-3">
						      <label for="inputTo">To</label>
						      <input type="text" id="dateTo" name="dateTo" autocomplete="off" class="date form-control mandatory">
                              <div class="form-control-focus"></div>
						    </div>
				        </div>
						<div class="row">
						    <div class="form-group col-md-6">
						      
						    </div>
				         </div>
						  <div class="form-group">
						    <span class="md-checkbox">
                                <input type="checkbox" id="status1" name="status1" value="Open" class="md-check">
                                <label>Open</label>
                                <label for="status1">
                                    <span class="inc"></span>
                                    <span class="check"></span>
                                    <span class="box"></span>
                                </label>
                            </span>
                            <span class="md-checkbox">
                                <input type="checkbox" id="status3" name="status3" value="Hold" class="md-check">
                                <label>Hold</label>
                                <label for="status3">
                                    <span class="inc"></span>
                                    <span class="check"></span>
                                    <span class="box"></span>
                                </label>
                            </span>
                            <span class="md-checkbox">
                                <input type="checkbox" id="status4" name="status4" value="Closed" class="md-check">
                                <label>Closed</label>
                                <label for="status4">
                                    <span class="inc"></span>
                                    <span class="check"></span>
                                    <span class="box"></span>
                                </label>
                            </span>
                            <span class="md-checkbox">
                                <input type="checkbox" id="status5" name="status5" value="Rejected" class="md-check">
                                <label>Rejected</label>
                                <label for="status5">
                                    <span class="inc"></span>
                                    <span class="check"></span>
                                    <span class="box"></span>
                                </label>
                            </span>  
                            <span class="md-checkbox">
                                <input type="checkbox" id="status2" name="status2" value="In Progress" class="md-check">
                                <label>In Progress</label>
                                <label for="status2">
                                    <span class="inc"></span>
                                    <span class="check"></span>
                                    <span class="box"></span>
                                </label>
                            </span>
		                   </div>
                         </div>
                            <input type="button" onclick="getUnplannedReport()" class="btn btn-primary" value="Generate">
                    </form>
                </div>
            </div>
            <div class="panel panel-default" id="UnplannedReport" style="display:block">
                <div class="panel-body" id="UnplannedReport1">
                    <table class="table table-bordered table-responsive sync" id="sync" >
                        <thead>
                            <tr>
                                <th>Sr No.</th>
                                <th>Ticket Id</th>
                                <th>Location</th>
                                <th>Services</th>
                                <th>Service Area</th>
                                <th>Service Issues</th>
                                <th>Created On </th>
                                <th>Assigned To</th>
                                <th>Requestor</th>
                                <th>Levels</th>
                                <th>Att.</th>
                                <th>Status</th>
                               
                            </tr>
                        </thead>

                        <tbody id="synctbody">
                        </tbody>
                        <tfoot>
                            <tr>
                                <td></td>
                                <th>Ticket Id</th>
                                <th>Location</th>
                                <th>Services</th>
                                <th>Service Area</th>
                                <th>Service Issues</th>
                                <th>Created On</th>
                                <th>Assigned To</th>
                                <th>Requestor</th>
                                <th>Levels</th>
                                <td></td>
                                <th>Status</th>
                                
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        function getUnplannedReport() {
            var startdate = $("#dateFrom").val();
            var enddate = $("#dateTo").val();
            var status1 = $("input[name='status1']:checked").val();
            var status2 = $("input[name='status2']:checked").val();
            var status3 = $("input[name='status3']:checked").val();
            var status4 = $("input[name='status4']:checked").val();
            var status5 = $("input[name='status5']:checked").val();
            if (status1 == "Open")
            {
                var status11 = status1;
            } else
            {
                var status11 = "";
            }
            if (status2 == "In Progress")
            {
                var status22 = status2;
            } else
            {
                var status22 = "";
            }
            if (status3 == "Hold")
            {
                var status33 = status3;
            } else
            {
                var status33 = "";
            }
            if (status4 == "Closed")
            {
                var status44 = status4;
            } else
            {
                var status44 = "";
            }
            if (status5 == "Rejected")
            {
                var status55 = status5;
            } else
            {
                var status55 = "";
            }
            $("#sync").dataTable().fnDestroy();
            table = $('.sync').DataTable({});
            table.destroy();
            ajaxdataload(startdate, enddate, status11, status22, status33, status44, status55);
            //            $.ajax({
            //                type: 'POST',
            //                data: $("#unplannedfrm").serialize(),
            //                url: "ajax/getUnplannedReport.php", // + dataString,
            //                cache: false,
            //                success: function (data) {
            //                    document.getElementById("synctbody").innerHTML = data;
            //                    $('#UnplannedReport').show();
            //                }
            //            });


        }
        /*$(document).ready(function () {
         // getUnplannedReport();
         /*/


        var dNow = new Date();
        var currentdate = dNow.getFullYear() + '' + (dNow.getMonth() + 1) + '' + dNow.getDate() + '' + dNow.getHours() + '' + dNow.getMinutes() + '' + dNow.getSeconds();
        var filenm = "UnplannedReport" + currentdate;

        $('#sync tfoot th').each(function (i) {

            var title = $('#sync tfoot th').eq($(this).index()).text();
            if ($(this).index() == 9) {
                $(this).html('<input type="text" id="date" class=" form-control form-filter input-sm txt" style="width:70px !important"  placeholder="Search ' + title + '" data-index="' + i + '" />');
            } else {
                $(this).html('<input type="text" class="form-control form-filter input-sm txt"   placeholder="Search ' + title + '" data-index="' + i + '" />');
            }
        });

        function ajaxdataload(startdate, enddate, status11, status22, status33, status44, status55)
        {
            $('#sync').DataTable({
                "ajax": {
                    url: "ajax/getUnplannedReport.php?dateFrom=" + startdate + "&dateTo=" + enddate
                            + "&status1=" + status11 + "&status2=" + status22 + "&status3=" + status33 + "&status4=" + status44 + "&status5=" + status55,
                    type: 'GET', 
                },
                "autoWidth": true,
                "lengthChange": false,
                "paging": true,
                "pageLength": 10,
                "ordering": false,
                "language": {
                    "paginate": {
                        "previous": "<",
                        "next": ">",
                    },
                    "zeroRecords": "No Records Found"
                },

                "pagingType": "simple_numbers",
                "dom": 'Brtip',
                buttons: [{
                        extend: 'collection',
                        text: 'Export',
                        buttons: [
                            {
                                extend: 'excel',
                                title: filenm,
                            },
                            {
                                extend: 'csv',
                                title: filenm
                            },
                            {
                                extend: 'pdf',
                                title: filenm,
                                extend: 'pdfHtml5',
                                orientation: 'landscape',
                                pageSize: 'LEGAL'
                            },
                            {
                                extend: 'print',
                                title: filenm
                            },
                        ]
                    }],
                "createdRow": function (row, data, dataIndex) {

                    if (data[10] == `L1`) {
                        //   $(row).addClass('success');
                    } else if (data[10] == 'L2') {
                        //$(row).addClass('red-pink tbltext');
                        $(row).find('td:eq(10)').css({"background-color": "#E69A8DFF","color":"#5F4B8BFF","font-size":"11px"});
                    } else if (data[10] == 'L3') {
                        //$(row).addClass('red-pink tbltext');
                        $(row).find('td:eq(10)').css({"background-color": "#5F4B8BFF","color":"#E69A8DFF","font-size":"11px"});
                    }
                }
            });

            //reload datatable to check acknowleged or not

            // Filter event handler
            table.columns().eq(0).each(function (t) {
                $('input', table.column(t).footer()).on('keyup change', function () {
                    table.column(t)
                            .search(this.value.replace(/(;|,)\s?/g, "|"), true, false)
                            .draw();
                });
            });

        }

        $(document).ready(function () {
            $('#dateFrom').datetimepicker({
                format: 'd-m-Y H:i:00',
                formatTime: 'H:i',
                formatDate: 'd-m-Y',
                step: 1,
                maxDate: new Date(),
                maxTime: new Date(),
                disabledTimeIntervals: [
                    [moment(), moment().hour(24).minutes(0).seconds(0)]
                ],
                defaultTime: '00:00:00'
            });

            $('#dateTo').datetimepicker({
                format: 'd-m-Y H:i:59',
                formatTime: 'H:i',
                formatDate: 'd-m-Y',
                step: 1,
                maxDate: new Date(),
                maxTime: new Date(),
                disabledTimeIntervals: [
                    [moment(), moment().hour(24).minutes(0).seconds(0)]
                ],
                defaultTime: '23:59:59'
            });
        });

        function updateTicket(id) {

            $('.modal-body').load('ajax/ticket_details.php?id=' + id, function () {


                $('#modal-default').modal({show: true});

            });
        }


    </script>
    <div class="modal fade" id="modal-default">
        <div class="modal-lg modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" style="color:white !important;margin-top:10px !important;opacity:1 !important;width:15px !important;height:15px !important;" aria-hidden="true"></button>
                <h4 class="modal-title" id="gridSystemModalLabel">Ticket Details</h4>
            </div>
                <div class="modal-body">
                </div>
            </div>
        </div>
    </div>
    <?php
} else {
    header('Location:Login.php');
}
?>