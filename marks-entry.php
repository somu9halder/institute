<?php 
session_start();
include "include/menu.php";
include "functions.php";
$franchises =  json_decode(getFranchises(), true);
$courses    =  json_decode(getCourses(), true);
?>

        <div id="page-wrapper">
		<div class="container-fluid">
					   <div class="row">
			 
            <!--    <div class="x_panel">
                   <div class="x_content"> -->
			<h3 class="page-header">Marks Entry</h3>
			<form id="myForm" method="post" action="fetch-marks.php" enctype="multipart/form-data">
			<input type="hidden" name="formid" id="formid" value="<?php echo htmlspecialchars($_SESSION['formid']);?>">
			<div class="form-group">
				<div class="col-md-3 col-sm-4col-xs-12">
					<label>Franchise</label>
					<select name="franchise" id="franchise" class="form-control">
						<option value="">Select Franchise</option>
						<?php
                            if(count($franchises['records']) > 0){
                                foreach ($franchises['records'] as $key => $franchise) {
                                    echo '<option value="'.$franchise['id'].'">'.$franchise['franchise_name'].'</option>';
                                }
                                
                            }
                        
                        ?>
					</select>
				</div>
				<div class="col-md-3 col-sm-4col-xs-12">
					<label>Course</label>
					<select name="course" id="course" class="form-control">
						<option value="">Select Course</option>
						<?php
                            if(count($courses['records']) > 0){
                                foreach ($courses['records'] as $key => $course) {
                                    echo '<option value="'.$course['id'].'">'.$course['course_name'].'</option>';
                                }
                                
                            }
                        
                        ?>
					</select>
				</div>
				<div class="col-md-3 col-sm-4 col-xs-12">
					<label>Subject</label>
					<select name="subject" id="subject" class="form-control">
						<option value="">Select Subject</option>
					</select>
				</div>
				<div class="col-md-3 col-sm-4 col-xs-12">
					<label>&nbsp;</label>
                    <button type="button" id="search" class="btn btn-info form-control">Search</button>
				</div>


				<div class="clearfix"></div>
				</div>
				
				<div class="form-group">
					<div class="col-sm-2 col-md-2 col-sm-offset-5 col-md-offset-5">
					
					</div>
				</div>
				<div class="clearfix"></div>
			</form>
				<div>&nbsp;</div>
				<div class="table-responsive">
				<table id="example" class="table table-stripped">
                    <thead>
                        <th style="text-align:left;"># </th>
                        <th style="text-align:center;">Name</th>
                        <th style="text-align:center;">Registration No </th>
                        <th style="text-align:center;">Full Marks</th>
                        <th style="text-align:center;">Obtained Marks</th>
                    </thead>
                    <tbody>

                    </tbody>
                  </table>
<!-- /panel -->		
			</div>
                  <!--</div>
                </div>-->
				
				</div>
				</div>

	
<div id="editMemberModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="editMemberModal" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">

          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 class="modal-title" id="myModalLabel">Update Password</h4>
          </div>
		  <div id="editMessage"></div>
		  <form method="post" id="updateMemberForm" class="form-horizontal" action="updateRecord.php">
			  <div class="modal-body">
			  <div class="messages"></div>
				<div id="testmodal" style="padding: 5px 20px;">
					<div class="form-group">
					  <label class="col-sm-2 control-label">Password</label>
					  <div class="col-sm-10">
						<input type="text" class="form-control" id="password" name="password" required="required"> 
						<input type="hidden" id="q_id" name="q_id">
					  </div>
					</div> 
				</div>
			  </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default antoclose" data-dismiss="modal">Close</button>
            <button type="submit" id="modalSave"  class="btn btn-primary antosubmit">Save changes</button>
          </div>
		  </form>
        </div>
      </div>
	  
    </div>
	<div class="modal fade" tabindex="-1" role="dialog" id="removeMemberModal">
		  <div class="modal-dialog" role="document">
		  <div class="modal-content">
		  <div class="modal-header">
		  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		  <h4 class="modal-title"><span class="glyphicon glyphicon-trash"></span> Warning</h4>
		  </div>
		  <div class="modal-body">
		  <font color="red">Do You Really Want To Remove This Info?</font>
		  </div>
		  <div class="modal-footer">
			  <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
			  <button type="button" class="btn btn-danger" id="removeBtn" name="removeBtn">Yes</button>
		  </div>
		  </div><!-- /.modal-content -->
		  </div><!-- /.modal-dialog -->
	</div>

        </div>
        <!-- /#page-wrapper -->


	
	    <!-- jQuery -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="vendor/metisMenu/metisMenu.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="dist/js/sb-admin-2.js"></script>
	<script src="vendor/datatables/js/jquery.dataTables.min.js"></script>
<script src="vendor/datatables-plugins/dataTables.bootstrap.min.js"></script>
<script src="vendor/datatables-responsive/dataTables.responsive.js"></script>
	<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js">
</script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap.min.js">
</script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js">
</script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.bootstrap.min.js">
</script>
<script type="text/javascript" language="javascript" src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js">
</script>
<script type="text/javascript" language="javascript" src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js">
</script>
<script type="text/javascript" language="javascript" src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js">
</script>
<script type="text/javascript" language="javascript" src="//cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js">
</script>
<script type="text/javascript" language="javascript" src="//cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js">
</script>
<script type="text/javascript" language="javascript" src="//cdn.datatables.net/buttons/1.5.1/js/buttons.colVis.min.js">
</script>
<!--MultiSelect -->
<script type="text/javascript" src="docs/js/prettify.js"></script>
<script type="text/javascript" src="dist/js/bootstrap-multiselect.js"></script>
<!-- CK Editor -->
<script src="ckeditor/ckeditor.js"></script>

    <!-- Page-Level Demo Scripts - Notifications - Use for reference -->
<script type="text/javascript">
$(document).ready(function(){

$('#course').on('change',function(e){
		var form	  = $('#myForm');
        $.ajax({
            url : "fetch-subjects.php",
            type : 'POST',
            data : form.serialize(),
            dataType : 'json',
            success:function(response) {
                let option ='<option value="">Select Subject</option>';
                if(response.success){
                    for(i=0; i< response.records.length ; i++){
                        option += '<option value="'+response.records[i].id+'">'+response.records[i].subject+'</option>';
                    }
                    $('#subject').html(option);
                }
            }
        });
		
    return false;
		
	});
});

function editMember(id = null) 
{
	$('.messages').html("");
	$('#updateMemberForm')[0].reset();
	/* alert(id); */
    if(id) 
	{

        $.ajax({
            url: 'getSelectedRecord.php',
            type: 'post',
            data: {member_id : id},
            dataType: 'json',
            success:function(response) {
				/* alert(response.id); */
                $("#password").val(response.password);
                $("#q_id").val(response.id);
            } 
        }); 
		
 
    } else {
        alert("Error : Refresh the page again");
    }
}
function removeMember(id=null)
{
	if(id)
	{
		$('#removeBtn').unbind('click').bind('click',function()
		{

			 $.ajax({
                url: 'removeRecord.php',
                type: 'post',
                data: {member_id : id},
                dataType: 'json',
                success:function(response) {
                    if(response.success == true) {                      
                        $(".removeMessages").html('<div class="alert alert-success alert-dismissible" role="alert">'+
                             '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+
                             '<strong> <span class="glyphicon glyphicon-ok-sign"></span> </strong>'+response.messages+
                            '</div>');
 
                        // refresh the table
                        reload($('#examid').val(),$('#course').val(),$('#year').val(),$('#month').val());
 
                        // close the modal
                        $("#removeMemberModal").modal('hide');
 
                    } else {
                        $(".removeMessages").html('<div class="alert alert-warning alert-dismissible" role="alert">'+
                             '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+
                             '<strong> <span class="glyphicon glyphicon-exclamation-sign"></span> </strong>'+response.messages+
                            '</div>');
                    }
                }
            }); 
			
		});
	}
	
}




    </script>

</body>

</html>
	
	
