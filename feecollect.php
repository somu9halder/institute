<?php
session_start();
error_reporting(0);
include('include/menu.php');
include('include/dbconfig.php');
	$error_msg=NULL;
	$success_msg=NULL;
if(isset($_POST['formid']) && isset($_SESSION['formid']) && $_POST['formid'] == $_SESSION['formid'])
{
	extract($_POST);

	$arr = array('Admission','Installment','Fine','Prospectus','Exam Fees');
	$receiptNo= maxReceiptNo();
	for($i=0 ; $i < count($arr) ; $i++)
	{
		$amount	= $_POST['amount'][$i];
		$sql= "INSERT INTO `payment`(`receipt_no`,`date`, `course_id`, `student_id`,`payment_type`,`payment_amt`,`collect_by`)
			   VALUES ('$receiptNo','$date','$_GET[pursuingcourse]','$_GET[studentid]','$arr[$i]','$amount','$_SESSION[userid]')";
		/* echo $sql."<BR/>"; */
		$res= mysqli_query($conn,  $sql);
		
	}
	if($res)
	{
		$student	= getStudentNameById($studentid);
		/* $particulars= getCourseNameById($courseid)." COURSE FEES FROM ".$student['St_Name']."(REG NO ".$student['regno'].")" ; */
		$particulars = "ADMISSION/INSTALLMENT FEES FROM ".$student['St_Name'];
		$payby="CASH";
		addIncomeToDayBook($receiptNo,$studentid,$total ,$courseid,$particulars,$date,$payby);
		saveNoteInfo(trim($note2000),trim($note500),$receiptNo,$studentid);
		$success_msg='<a target="_blank" href="printSlip.php?id='.$receiptNo.'">Print Receipt </a>';
	}

		$_SESSION['formid']=md5(rand(0,10000000));
}
else{
		$_SESSION['formid']=md5(rand(0,10000000));
}
function addIncomeToDayBook($receiptNo,$studentID,$fees,$course,$particulars,$date,$payby)
{
	include "include/dbconfig.php" ;
	$mode=null;
	if($payby == "CASH")
	{
	$sql  = "INSERT INTO `daybook` (`receipt_No`,`student_id`, `course_id`, `user_id`, `date`, `particulars`,
			`cash`,`to`, `type`) 
		    VALUES ('$receiptNo','$studentID','$course','$_SESSION[userid]','$date','$particulars','$fees','CASH','INCOME')" ;
	}
	else{
		$mode = "BANK";
		$sql  = "INSERT INTO `daybook` (`student_id`, `course_id`, `user_id`, `date`, `particulars`,
			    `bank`, `to`, `type`) 
		        VALUES ('$studentID','$course','$_SESSION[userid]','$date','$particulars','$fees','BANK','INCOME')" ;
	}
	
	$res  = mysqli_query($conn,  $sql) ;
	return (isset($res) ? true : false);

}
function saveNoteInfo($_2000info,$_500info,$receiptNo,$studentID)
{
	include "include/dbconfig.php" ;
	if(($_2000info !="" && $_500info =="") || ($_2000info =="" && $_500info !="") || ($_2000info !="" && $_500info !="") )
	{
		$sql="INSERT INTO `note_info`(`student_id`, `recpt_no`, `2000_no`, `500_no`) 
			  VALUES('$studentID','$receiptNo','$_2000info','$_500info')" ;
		$res = mysqli_query($conn,  $sql) ;
	}
	
}
function getCourseNameById($courseid)
{
	include "include/dbconfig.php" ;
	$sql 		="SELECT * FROM `courses` WHERE `course_id`='$courseid'";
	$res 		=mysqli_query($conn,  $sql);
	$row 		=mysqli_fetch_assoc($res);
	$courseinfo =$row['course_name']."-".$row['description'] ;
	return $courseinfo ;
	
}
function getStudentNameById($studentid)
{
	include "include/dbconfig.php" ;
	$sql="SELECT * FROM `student_info` WHERE `Student_Id`='$studentid'" ;
	$res=mysqli_query($conn,  $sql);
	$row=mysqli_fetch_assoc($res);
	return $row;
	
}
function fechPreviousFeeeRecords()
{
	include('include/dbconfig.php');	
	$courseid	=trim($_GET['pursuingcourse']);
	$studentid	=trim($_GET['studentid']);
	$sql		="SELECT payment.date,payment.receipt_no ,SUM(payment.payment_amt) AS payment_amt ,student_info.*,courses.* 
				FROM `payment`
				INNER JOIN student_info 
				ON payment.student_id=student_info.Student_Id
				INNER JOIN courses
				ON payment.course_id=courses.course_id
				WHERE payment.student_id='$studentid' AND payment.course_id='$courseid' 
				GROUP BY payment.receipt_no
				";
	$res	   =mysqli_query($conn,  $sql);
	$no        =0;
	while($row=mysqli_fetch_assoc($res))
	{
		echo '<tr>
				<td style="text-align:center;">'.++$no.'</td>
				<td style="text-align:center;">'.$row['date'].'</td>
				<td style="text-align:center;">'.$row['receipt_no'].'</td>
				<td style="text-align:center;">'.$row['St_Name'].'</td>
				<td style="text-align:center;">'.$row['regno'].'</td>
				<td style="text-align:center;">'.$row['course_name'].'</td>
				<td style="text-align:center;">'.$row['payment_amt'].'</td>
				<td style="text-align:center;">'.printOption($row['receipt_no']).'</td>
			
			</tr>';
	}
}
function printOption($no)
{
	if($_SESSION['login_type'] == "Super Administrator" ||  $_SESSION['login_type'] == "Administrator")
	{
	return '<a target="_blank" href="printreceipt.php?id='.$no.'">Original Print</a> / 
			<a target="_blank" href="printSlip.php?id='.$no.'">Normal Print</a>
			';
	}
	else{
		return '
			<a target="_blank" href="printSlip.php?id='.$no.'">Print</a>
			';
	}
}
function fechdueAmount()
{
	include('include/dbconfig.php');	
	$courseid	=trim($_GET['pursuingcourse']);
	$studentid  =trim($_GET['studentid']);
	$sql		="SELECT SUM(`payment_amt`) AS totalpay FROM payment WHERE `student_id`='$studentid' AND `course_id`='$courseid' AND (`payment_type`='Installment' OR `payment_type`='Admission')";
	/* echo $sql; */
	$query		="SELECT * FROM `pursuing_course` WHERE `course_id`='$courseid' AND `student_id`='$studentid ' AND `current_status`='PURSUING'";
	$res		=mysqli_query($conn,  $sql) ;
	$ress		=mysqli_query($conn,  $query) ;
	$paymentinfo=mysqli_fetch_assoc($res);
	$feeinfo	=mysqli_fetch_assoc($ress);
	$dueamount  =$feeinfo['course_fee']-$paymentinfo['totalpay'];
	echo $dueamount;
}	
function admissionRequired()
{
	include "include/dbconfig.php";
	$query= "SELECT * FROM `pursuing_course` WHERE `student_id`='$_GET[studentid]' AND `remarks`='DIPLOMA CONVERSION'";
	$result= mysqli_query($conn,  $query);
	if(mysqli_num_rows($result) > 0)
	{
		echo "readonly";
	}
	else{
		$sql="SELECT * FROM `payment` WHERE `student_id`='$_GET[studentid]' AND `payment_type`='Admission' AND `payment_amt`!='0.00'";
		$res=mysqli_query($conn,  $sql);
		if(mysqli_num_rows($res) > 0)
		{
			echo "readonly";
		}
		else{
			echo "required";
		}
	}
	
}
function maxReceiptNo()
{
	include "include/dbconfig.php";
	$sql="SELECT MAX(`receipt_no`) AS ReceiptNo FROM payment";
	$res=mysqli_query($conn,  $sql);
	$row=mysqli_fetch_assoc($res);
	if($row['ReceiptNo'] == NULL)
	{
		return 1;
	}
	else{
		return ($row['ReceiptNo'] + 1) ;
	}
}
function fineCalculate()
{
	include "include/dbconfig.php";
	$day=date('d');
	if($day >10 && $day <= 15)
	{
		echo 10;
	}
	else if($day >15 && $day <= 20){
		echo 20;
	}
	else if($day >20 && $day <= 25){
		echo 30;
	}
	else if($day >25 && $day <= 30){
		echo 40;
	}
	else{
		echo 0;
	}
}
 ?>

<!-- Page Content -->
<style>

</style>
<div id="page-wrapper">
	<div class="container-fluid">
	<div style="margin-top:2%;">
	<?php if($success_msg != NULL){ 
	echo '<div class="alert alert-success">
	  <strong><i class="fa fa-check-circle"></i> Success</strong> Payment Received.
		'.$success_msg.'
	</div>';
	 }
	 else if($error_msg!= NULL){
		echo '<div class="alert alert-danger">
	  <strong>Error : </strong> Process Failed 
	</div>'; 
	 }?>
	</div>
		<div class="row">
	          <h3 class="page-header">Payment Record</h3>
				<form method="post" id="createTeacherForm" action="<?php echo $_SERVER['REQUEST_URI']?>" enctype="multipart/form-data">
					<div class="table-responsive">
						<table class="table table-bordered">
							<thead>
								<th style="text-align:center;font-size:12px;">SL NO			</th>
								<th style="text-align:center;font-size:12px;">DATE			</th>
								<th style="text-align:center;font-size:12px;">RECEIPT NO			</th>
								<th style="text-align:center;font-size:12px;">STUDENT NAME 	</th>
								<th style="text-align:center;font-size:12px;">REGISTRATION NO</th>
								<th style="text-align:center;font-size:12px;">COURSE NAME 	</th>
								<th style="text-align:center;font-size:12px;">PAID AMT		</th>
								<th style="text-align:center;font-size:12px;">		</th>
							</thead>
							<tbody>
								<?php fechPreviousFeeeRecords();?>
							</tbody>
						</table>
					</div>
					<table class="table table-bordered">
						<thead>
							<th style="text-align:center;font-size:12px;width:60%">Description		</th>
							<th style="text-align:center;font-size:12px;">Amount Paid		</th>
						</thead>
						<tbody>
							<tr>
								<td style="text-align:center;"><b>Admission</b></td>
								<td style="text-align:center;"><input type="number" name="amount[]"  id="admission" class="form-control" <?php admissionRequired();?> onkeyup="subTotal()" ></td>
							</tr>
							<tr>
								<td style="text-align:center;"><b>Installment</b></td>
								<td style="text-align:center;"><input type="number" name="amount[]" id="amount" class="form-control" onkeyup="subTotal()"></td>
							</tr>
							<tr>
								<td style="text-align:center;"><b>Fine</b></td>
								<td style="text-align:center;"><input type="number" name="amount[]" id="fine" class="form-control" value="<?php fineCalculate(); ?>" onkeyup="subTotal()"></td>
							</tr>
							<tr>
								<td style="text-align:center;"><b>Prospectus</b></td>
								<td style="text-align:center;"><input type="number" name="amount[]" id="prospectus" class="form-control" onkeyup="subTotal()"></td>
							</tr>
							<tr>
								<td style="text-align:center;"><b>Exam Fees</b></td>
								<td style="text-align:center;"><input type="number" name="amount[]" id="examfees" class="form-control" onkeyup="subTotal()"></td>
							</tr>
							<tr>
								<td style="text-align:center;"><b>Total</b></td>
								<td style="text-align:center;"><input type="text" name="total" id="total" class="form-control" readonly></td>
							</tr>
						</tbody>
					</table>
				
					<div class="col-md-12 col-sm-12 col-xs-12">
						<div class="form-group">
							<label class="control-label col-sm-2 col-md-2 col-xs-12">Due</label>
							<div class="col-sm-4 col-md-4 col-xs-12">
								<input type="text" class="form-control" name="dueamount" id="dueamount" value="<?php  fechdueAmount();?>" readonly>
							</div>
							<label class="control-label col-sm-2 col-md-2 col-xs-12">Date</label>
							<div class="col-sm-4 col-md-4 col-xs-12">
									<?php if($_SESSION['login_type'] != "Administrator" && $_SESSION['login_type'] != "Super Administrator") {?>
									<input class="form-control" type="text" name="date" id="date" value="<?php echo date('Y-m-d'); ?>" readonly required>
									<?php }
									else{
									?>
									<div class="input-group date form_date col-md-16" data-date="" data-date-format="yyyy-mm-dd" data-link-field="dtp_input3" data-link-format="yyyy-mm-dd">
										<input class="form-control" type="text" name="date" id="date" value="<?php echo date('Y-m-d'); ?>" required>
										<span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
										<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
									</div>
								<?php }?>
							</div>
						<div class="clearfix"></div>
						</div>
						<div class="form-group">
								<label class="control-label col-sm-2 col-md-2 col-xs-12">Note No 2000</label>
							<div class="col-sm-4 col-md-4 col-xs-12">
								<input type="text" class="form-control" name="note2000" id="note2000" >
							</div>
							<label class="control-label col-sm-2 col-md-2 col-xs-12">Note No 500</label>
							<div class="col-sm-4 col-md-4 col-xs-12">
								<input type="text" class="form-control" name="note500" id="note500" >
							</div>
							<div class="clearfix"></div>
							
						</div>
						<div class="form-group">
							<label>&nbsp;</label>
							<div class="col-sm-5 col-sm-offset-3 col-md-offset-3 col-md-5 col-xs-12">
							<br/><button type="submit" name="submit" id="submit" class="btn btn-info btn-md btn-block">Submit</button>
							</div>
							<div class="clearfix"></div>
						</div>
						<div class="form-group">
							<div class="col-sm-5 col-sm-offset-3 col-md-offset-3 col-md-5 col-xs-12">
							 <button type="button" name="back" id="back" class="btn btn-warning btn-md btn-block">Back</button>
							</div>
							<div class="clearfix"></div>
						</div>
						<input type="hidden" class="form-control" name="studentid" id="studentid" value="<?php echo  $_GET['studentid']; ?>">
						<input type="hidden" class="form-control" name="courseid" id="courseid"   value="<?php echo  $_GET['pursuingcourse']; ?>">
					</div>
				<input type="hidden" name="formid" id="formid" value="<?php echo htmlspecialchars($_SESSION['formid']);?>">
				</form>
		  		<!-- /col-md-6 -->
			  		<!-- /col-md-4 -->
		 
		  	<!-- /col-md-12 -->
	 	<!-- /row -->
      </div>
			</div>
		</div>
	</div>
</div>
</div>
<?php include('include/footer.php'); ?>
</body>
</html>
<script type="text/javascript">
$(document).ready(function(e){
	function calculate()
	{
		var amount=parseFloat($.trim($('#amount').val())) || 0;
		var fine  =parseFloat($.trim($('#fine').val())) || 0;
		$('#total').val((amount + fine));
	}
	
	$('.form_date').datetimepicker({
        language:  'fr',
        weekStart: 1,
        todayBtn:  1,
		autoclose: 1,
		todayHighlight: 1,
		startView: 2,
		minView: 2,
		forceParse: 0
    });
	$('#back').on('click',function(e)
	{
		window.location="collectform.php";
	});
});
function subTotal()
{
	var admission =parseFloat($('#admission').val()) || 0;
	var amount    =parseFloat($('#amount').val()) || 0;
	var fine      =parseFloat($('#fine').val()) || 0;
	var prospectus=parseFloat($('#prospectus').val()) || 0;
	var examfees  =parseFloat($('#examfees').val()) || 0;
	$('#total').val(admission + amount +fine + prospectus + examfees);
}
</script>