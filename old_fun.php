<?php

$connection = connection();
$staff_id = $_SESSION["staff_id"];
$profile = profile($staff_id);
$zone_id = 
$zone_id2 = ""
$statusc= 0;
$getExhibitscount = $connection->prepare("select * from table_name where exhibit_accept_status = ? and (zone_id = ? or zone_id = ?) and (aftiu is  null)");
$getExhibitscount->bind_param("iss", $statusc,$zone_id,$zone_id2);
$getExhibitscount->execute();
$exhibitsc = $getExhibitscount->get_result();
$total_record = $exhibitsc->num_rows;
$limit = 20;
$page = 1;
$tpost = isset($_POST['page'])?$_POST['page']:"";
if($tpost > 1)
{
  $start = (($_POST['page'] - 1) * $limit);
  $page = $_POST['page'];
}
else
{
  $start = 0;
}

$status = 0;
$qry = isset($_POST["query"])?"%".$_POST["query"]."%":"";
if($qry == ""){
    $getExhibits = $connection->prepare("select * from `table name` where exhibit_accept_status = ? and (zone_id = ? or zone_id = ?) and (aftiu is  null)
     order by date_received desc LIMIT ".$start.", ".$limit);
    $getExhibits->bind_param("iss", $status,$zone_id,$zone_id2);

}else{
    $getExhibits = $connection->prepare("select * from `table name` where exhibit_accept_status = ? and (zone_id = ? or zone_id = ?) and (aftiu is  null)
    and (description LIKE ?)  order by date_received desc LIMIT ".$start.", ".$limit);
    $getExhibits->bind_param("isss",$status,$zone_id,$zone_id2,$qry);
}


$getExhibits->execute();
$exhibits = $getExhibits->get_result();
$total_data = $exhibits->num_rows;
$output = '
<label>Total Records   '.$total_record.'  showing   '.$limit.'  of  '.$total_record.'</label><br/><br/>
<table class="table table-striped table-bordered">
<thead>
<tr>
    <th>CR No </th>
    <th>Category</th>
    <th style="width:20%">Description</th>
    <th>Registered By</th>
    <th>Date Registered</th>
    <th>Action</th>
</tr>
</thead>';
$sn=1;

    while($exhibit = $exhibits->fetch_object()) {
    $output .= '
    <tbody>
    <tr>
     
      <td>'.getCaseNumber($exhibit->case_id).'</td>
      <td>'.ucwords(strtolower(assetCategory($exhibit->category))).'</td>
      <td style="width: 40px; height: 20px;">'.nl2br($exhibit->description).'</td>
      <td>'.name($exhibit->staff_id).'</td>
      <td>'.$exhibit->date_received.'</td>
      <td><a style="cursor:pointer" target="_blank" data-toggle="modal"
      data-target="#bd-example-modal-xl'.$sn.'"><i
          style="color:red" class="fa fa-ellipsis-h"></i> </a>
    

    <div class="modal fade bd-example-modal-xl" id="bd-example-modal-xl'.$sn++.'" tabindex="-1"role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title m-0" id="myExtraLargeModalLabel">
                    Exhibit Acceptance '.$exhibit->id.'</h6>
                <button type="button" class="close "
                    data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i
                            class="la la-times"></i></span>
                </button>
            </div>
            <!--end modal-header-->
            <div class="modal-body">

                <div class="row">
                    <div class="form-group col-lg-9">';

                         if(file_exists("../case_assets/".$exhibit->doc_url)){

                      $output .=  '<object type="application/pdf"
                            data="'.$directory.'case_assets/'.$exhibit->doc_url.'"
                            width="100%" height="500"
                            style="height: 85vh;">No Support</object>';
                        } else $output .= 'No File Attached!';
                        
                        $output .= '</div>
                        
                    <div class="form-group col-lg-3">
                        <div class="card-body">
                            <!-- Tab panes -->
                            
                            <form method="post" enctype="multipart/form-data"  class="form-horizontal auth-form my-4 exhibitVetting">
                                <input type="hidden" name="action" value="exhibitApproval" />
                                <input type="hidden" name="exhibit" id="exhibit" value="'.$exhibit->id.'"/>
                                <div class="row">
                                    <div class="form-group col-lg-12">
                                        <h3>Remarks</h3>
                                        <div class="input-group mb-3">
                                             '.$exhibit->remarks.'

                                        </div>
                                    </div>
                                    <div class="form-group col-lg-12">
                                        <h3>Exhibit/Asset Description</h3>
                                        <div class="input-group mb-3">'.$exhibit->description.'</div>
                                    </div>
                                    '; if($exhibit !=''){
                                        $output.=' <div class="form-group col-lg-12">
                                        <h3>Exhibit/Asset Name</h3>
                                        <div class="input-group mb-3">
                                             '.$exhibit->asset_name.'

                                        </div>
                                    </div>';
                                    }
                                    if($exhibit->amount && $exhibit->currency !="" ){
                                    
                                
                                        $output.= '<div class="form-group col-lg-12">
                                        <h3>Currency</h3>
                                        <div class="input-group mb-3">'.$exhibit->currency.'</div>
                                    </div>
                                    <div class="form-group col-lg-12">
                                        <h3>Amount </h3>
                                        <div class="input-group mb-3">
                                             '.number_format($exhibit->amount).'
                                        </div>
                                    </div>';
                                    
                                        } 
                                    
                                        $output.= '

                                    <div class="form-group col-lg-12">
                                        <label
                                            for="userpassword">Decision</label>
                                        <div class="input-group mb-3">
                                            <select class="form-control"
                                                required name="decision"
                                                id="example-text-input">
                                                <option value="">Choose
                                                    Decision</option>
                                                <option value="1">
                                                    Approve</option>
                                                <option value="2">Reject
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                </div>

                                <div class="form-group mb-0 row">
                                    <div class="col-12 mt-2">
                                        <button
                                            class="btn btn-primary btn-block id="myBtn" waves-effect waves-light "
                                            type="submit">Submit</button>
                                    </div>
                                    <!--end col-->
                                </div>
                                <!--end form-group-->
                            </form>
                            </td>
                            </tr>
                            
                            <!--end form-->

                        </div>



                    </div>

                </div>
                <!--end of row-->
            </div>
            <!--end modal-body-->

            </div>
            <!--end modal-dialog-->
        </div>
        <!--end modal-->
';

  
}
   
    $output .= '</tbody>
      </table>
      </div>
    
      <br/>
      <div align="center">
      <ul class="pagination">';

        $total_links = ceil($total_record/$limit);
        $previous_link = '';
        $next_link = '';
        $page_link = '';

//echo $total_links;

if($total_links > 4)
{
  if($page < 5)
  {
    for($count = 1; $count <= 5; $count++)
    {
      $page_array[] = $count;
    }
    $page_array[] = '...';
    $page_array[] = $total_links;
  }
  else
  {
    $end_limit = $total_links - 5;
    if($page > $end_limit)
    {
      $page_array[] = 1;
      $page_array[] = '...';
      for($count = $end_limit; $count <= $total_links; $count++)
      {
        $page_array[] = $count;
      }
    }
    else
    {
      $page_array[] = 1;
      $page_array[] = '...';
      for($count = $page - 1; $count <= $page + 1; $count++)
      {
        $page_array[] = $count;
      }
      $page_array[] = '...';
      $page_array[] = $total_links;
    }
  }
}
else
{
    // 1
  for($count = 1; $count <= $total_links; $count++)
  {
    $page_array[] = $count;
  }
}

//explain
for($count = 0; $count < count($page_array); $count++)
{
    
  if($page == $page_array[$count])
  {
    //echo $page_array[$count];
    $page_link .= '
    <li class="page-item active">
      <a class="page-link" href="#">'.$page_array[$count].' <span class="sr-only">(current)</span></a>
    </li>
    ';

    $previous_id = $page_array[$count] - 1;
    if($previous_id > 0)
    {
      $previous_link = '<li class="page-item"><a class="page-link" href="javascript:void(0)" data-page_number="'.$previous_id.'">Previous</a></li>';
    }
    else
    {
      $previous_link = '
      <li class="page-item disabled">
        <a class="page-link" href="#">Previous</a>
      </li>
      ';
    }
    $next_id = $page_array[$count] + 1;
    if($next_id >= $total_links)
    {
      $next_link = '
      <li class="page-item disabled">
        <a class="page-link" href="#">Next</a>
      </li>
        ';
    }
    else
    {
      $next_link = '<li class="page-item"><a class="page-link" href="javascript:void(0)" data-page_number="'.$next_id.'">Next</a></li>';
    }
  }
  else
  {
    if($page_array[$count] == '...')
    {
      $page_link .= '
      <li class="page-item disabled">
          <a class="page-link" href="#">...</a>
      </li>
      ';
    }
    else
    {
      $page_link .= '
      <li class="page-item"><a class="page-link" href="javascript:void(0)" data-page_number="'.$page_array[$count].'">'.$page_array[$count].'</a></li>
      ';
    }
  }
}

$output .= $previous_link . $page_link . $next_link;
  '</ul>
 </div>
 ';


echo $output;
?>