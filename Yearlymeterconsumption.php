<?php
include('./includes/session.php');
?>
<script type="text/javascript" src="assets/global/plugins/jquery.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
 <script type="text/javascript" src="js/amcharts.js"></script>
<script src="https://www.amcharts.com/lib/3/serial.js"></script>
<script src="https://www.amcharts.com/lib/3/plugins/export/export.js"></script>
<link rel="stylesheet" href='https://www.amcharts.com/lib/3/plugins/export/export.css' type='text/css' media='all'/>


<ul class="page-breadcrumb breadcrumb">
    <li>
        <i class="fa fa-home"></i>
        <a onclick="refresh();">Home</a>
    </li>
    <li>
        <span> Yearly Consumption Report </span>
    </li>
</ul>

<style>
    .center {
  display: block;
  margin-left: 50%;
  margin-right: 50%;
  width: 5%;
}
</style>
<div class="row">
    <div class="col-lg-12">
        <div class="row">
            <div class="col-md-12">
                 <div class="portlet light">
                  <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-file"></i>
                            <span class="caption-subject  bold uppercase">Yearly Meter Consumption
                            </span>
                        </div>
                    </div>

                    <div class="panel-body">
                        <form action="#" class="form-horizontal" id="frm" name="frmlogsheet" method="post">  
                            <div class=" form-group form-md-line-input col-md-12">
                                <div class="row">
                                    <div class="col-md-3half">
                                    <label class="control-label col-sm-5">Select Year:
                                                </label>
                                    <div class="col-sm-7">
                                        <select value="year" id="selectyear" multiple class="form-control input-sm" name="year[]" size="4">
                                        </select>
                                    </div>
                                    </div>
                                

                                    <div class="col-md-3half">
                                
                                    <label class="control-label col-sm-5">Select Meter:
                                                </label>
                                    <div class="col-sm-7">
                                        <select id="Asset_Name" class="form-control select1" name="assetname" onchange="getparameter(this.value);">
                                            <option hidden>Select Asset</option>
                                        </select>
                                    </div>
                                    </div>
                                    <div class="col-md-3">
                                
                                    <label class="control-label col-sm-4">Parameter:
                                                </label>
                                    <div class="col-sm-8">
                                        <select name="parameter" id="parameterid" class="form-control">
                                            </select>
                                    </div>
                                    </div>
                           
                                    <div class="col-md-2">
                                    <div class="col-sm-12">
                                       <div class="md-checkbox"><label> Show With Grid</label><span>
                                                        <input type="checkbox" id="check" name="grid" value="grid" class="md-check" checked>
                                                        <label for="check">
                                                            <span class="inc"></span>
                                                            <span class="check"></span>
                                                            <span class="box"></span></label>
                                                            </span>
                                                    </div>
                                    </div>                             
                                    </div>
                             
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="col-md-5"></div>
                                        <div class="col-md-2">
                                            <input type="submit" name="submit" value="GO" class="btn btn-primary show">
                                        </div>
                                        <div class="col-md-5"></div>
                                        <div id="load"></div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<div class="row" id="row_chart" style="display:none;">
    <div class="col-md-12">
        <div class="portlet light">
            <div class="portlet-body">

                 <div id="d4" style="overflow: auto"></div>
                 <div id="chartdiv" name="chartdiv" style="height: 500px;overflow:auto;">

        </div>
    </div>
</div>
</div>
</div>
<script type="text/javascript">

    $(document).ready(function () {
        getAsset();
        year();
        $('#check').change(function () {
            if (this.checked) {
                document.getElementById("d4").style.display = 'block';
                
            } else {
                document.getElementById("d4").style.display = 'none';
                
            }
        });
    });
    function year() {
        $.ajax({
            cache: false,
            url: "ajax/getYear.php",
            type: 'POST',
            contentType: false,
            processData: false,
            success: function (result)
            {
                var parts = $("#selectyear").empty();
                var currentYear = new Date().getFullYear();
                var prev_year = result.trim();

                for (var i = prev_year; i <= currentYear; i++) {

                    parts.append("<option value='" + i + "'>" + i + "</option>");
                }

            }
        });
    }

    function getAsset() {

        $.ajax({
            cache: false,
            url: 'controller/ContrAddAsset.php?action=getasset',
            type: 'POST',
            contentType: false,
            processData: false,
            success: function (result) {


                var d = JSON.parse(result);
                var len = d.length;

                for (var o = 0; o < len; o++) {

                    $('#Asset_Name').append("<option value='" + d[o].Auto_Id + "|" + d[o].Asset_Name + "'>" + d[o].Asset_Name + "</option>");
                }
            }

        });
    }


    function getparameter(assetid)
      {
         var asset_id=assetid.split("|");
        var id=asset_id[0];
        
        

        $.ajax({
            cache: false,
            url: 'controller/ContrAddAsset.php?action=getparameter&id='+id,
            type: 'POST',
            contentType: false,
            processData: false,
            success: function (result) {
                var d = JSON.parse(result);
                var len = d.length;
                $('#parameterid').children().remove().end();
                for (var o = 0; o < len; o++) { 
                    var a=d[o].Field_Label;
                    var parameter= a.split("|");
                    var para_name=parameter[0];


                    $('#parameterid').append("<option value='" +a+ "'>" + para_name + "</option>");
                }
            }

        });
      }

    $("#frm").on('submit', (function (e) {
        event.preventDefault();
         $('#load').show();
         $('#load').html('<img src="Images/default.gif" class="center">');
        $.ajax({
            type: 'POST',
            cache: false,
            data: new FormData(this),
            url: 'ajax/getyearlyconsumption.php',
            contentType: false,
            processData: false,
            success: function (data)
            {

                
                 $('#load').hide();
                 $('#row_chart').show();
                var d = JSON.parse(data);
                document.getElementById("d4").innerHTML = d.table_data;
                generateGraph("chartdiv", d.graph_data, d.js_data, d.title_data);
            }
        });

    }));

    function generateGraph(divid, dataprovider_data, graphjson, title) {


        AmCharts.makeChart(divid,
                {
                    "type": "serial",
                    "categoryField": "category",
                    "angle": 30,
                    "depth3D": 30,
                    "startDuration": 1,
                    "categoryAxis": {
                        "gridPosition": "start"
                    },
                    "trendLines": [],
                    "graphs": graphjson,
                    "guides": [],
                    "valueAxes": [
                        {
                            "id": "ValueAxis-1",
                            "stackType": "3d",
                            "title": "Meter Reading "
                        }
                    ],
                    "allLabels": [],
                    "balloon": {},
                    "legend": {
                        "enabled": true,
                        "useGraphSettings": true
                    },
                    "titles": [
                        {
                            "id": "Title-1",
                            "size": 15,
                            "text": title
                        }
                    ],
                    "export": {
  "enabled": true,
  "libs": {
    "path": "assets/libs/"
  },
  "menu": [ {
    "class": "export-main",
    "menu": [ "PNG", "JPG", "CSV" ]
  } ]
},
                    "dataProvider": dataprovider_data
                }
        );
    }
</script>




