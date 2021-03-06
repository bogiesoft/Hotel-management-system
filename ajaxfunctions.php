<?php
session_start();

error_reporting(E_ALL & ~E_NOTICE);
include_once ("queryfunctions.php");
include_once ("functions.php");

switch ($_POST["button"]){
	case "Agent":
		agents();
		break;
	case "GetRates":
		$conn=db_connect(HOST,USER,PASS,DB,PORT);
		//check room availability
		$roomid=$_POST["roomid"];
		$sql="Select rooms.roomid,rooms.roomno,rooms.status,booking.checkin_date,booking.checkout_date
		From rooms
		Left Join booking ON rooms.roomid = booking.roomid
		Where rooms.status = 'V' and rooms.roomid=$roomid";
		$results=mkr_query($sql,$conn);
		$msg[0]="";
		$msg[1]="";
		AddSuccess($results,$conn,$msg);
		if (num_rows($results)==0){
			//rooms.status - could tell if room is locked/booked/reserverd/vacant
			//get room id and find out status - to do
			echo "<h2><blink>Sorry room is either occupied or reserved</blink></h2>";
		}else{
			echo "<h1>Room ready for occupancy</h1>";
		}

		//get rate if available
		/*$booking_type=$_POST["booking_type"]; //direct booking / booking through agents
		//$agents_ac_no=$_POST["agents_ac_no"]; //agents account code
		$no_adults=$_POST["no_adults"]>1 ? 'D' : 'S'; //double or single
		$meal_plan=$_POST["meal_plan"]; //meal plan - bed only/half board/bed & breakfast/full board
		//$rate_type=$_POST["rate_type"]; //residence or non-residence
		if ($booking_type='D'){ //direct booking
			"Select rates.bookingtype,rates.occupancy,rates.ratesid,rates.rate_type,rates.currency,$meal_plan
			From rates
			Where rates.occupancy = 'S' AND
			rates.rate_type = $rate_type";
		}elseif($booking_type='A'){
			//link with agent id
			"Select agents.agentid,agents.agentname,rates.ratesid,rates.bookingtype,rates.occupancy,rates.rate_type,agents.agents_ac_no,$meal_plan
			From agents
			Inner Join rates ON agents.ratesid = rates.ratesid
			Where rates.bookingtype = 'A' AND
			rates.rate_type = $rate_type AND
			agents.agents_ac_no = $agents_ac_no";
		}*/
		break;
	case "Cash" || "Debit":
		trans_type();
		break;
	case "UpdateBill":
		updatebill();
		break;
}

switch ($_GET["submit"]){
	case "Rates":
		rates_table();
		break;
	case "RoomDetails":
		rooms_details();
		break;
	case "Roomtype":
		room_types();
		break;
	case "Bills":
		bills_details();
		break;
	case "transdetails":
		transdetails();
		break;
	case "Documents":
		Documents();
		break;
	case "Transtypes":
		Transtypes();
		break;
	case "PaymentMode":
		PaymentMode();
		break;
	default:
		echo $_GET["submit"];
}

function rates_table(){
	$conn=db_connect(HOST,USER,PASS,DB,PORT);
	$sql="Select ratesid,bookingtype,occupancy,rate_type,bo,bb,hb,fb,currency,date_started,date_stopped From rates";

	$rates=mkr_query($sql,$conn);
	$n=mysql_num_fields($rates);
	echo "<table valign=\"top\">";
	//get field names to create the column header
	echo "<tr bgcolor=\"#009999\">
		<th>Rate ID</th>
		<th>Booking Type</th>
		<th>Occupancy</th>
		<th>Rate Type</th>
		<th>Currency</th>
		<th>BO</th>
		<th>BB</th>
		<th>HB</th>
		<th>FB</th>
		<th>Starting</th>
		<th>Ending</th>
		</tr>";
	//end of field header
	//get data from selected table on the selected fields
	while ($row = fetch_object($rates)) {
		//alternate row colour
		$j++;
		if($j%2==1){
			echo "<tr id=\"row$j\" onmouseover=\"javascript:setColor('$j')\" onmouseout=\"javascript:origColor('$j')\" bgcolor=\"#CCCCCC\">";
			}else{
			echo "<tr id=\"row$j\" onmouseover=\"javascript:setColor('$j')\" onmouseout=\"javascript:origColor('$j')\" bgcolor=\"#EEEEF8\">";
		}
			//"bookingtype,occupancy,rate_type,bo,bb,hb,fb,currency,date_started,date_stopped"
			echo "<td>" . $row->ratesid . "</td>";
			echo "<td>" . ((trim($row->bookingtype)=="D")  ? "Direct" : "Agent") . "</td>";
			echo "<td>" . $row->occupancy . "</td>";
			echo "<td>" . $row->rate_type . "</td>";
			echo "<td>" . $row->currency . "</td>";
			echo "<td>" . $row->bo . "</td>";
			echo "<td>" . $row->bb . "</td>";
			echo "<td>" . $row->hb . "</td>";
			echo "<td>" . $row->fb . "</td>";
			echo "<td>" . $row->date_started . "</td>";
			echo "<td>" . $row->date_stopped . "</td>";
		echo "</tr>"; //end of - data rows
	} //end of while row
	echo "</table>";
}

function rooms_details(){
	$conn=db_connect(HOST,USER,PASS,DB,PORT);
	$sql="Select roomid,roomno,roomtypeid,roomname,noofrooms,occupancy,tv,aircondition,fun,safe,fridge,status,photo From rooms";

	$rates=mkr_query($sql,$conn);
	$n=mysql_num_fields($rates);
	echo "<table valign=\"top\">";
	//get field names to create the column header
	echo "<tr bgcolor=\"#009999\">
		<th>Room No.</th>
		<th>Room Type</th>
		<th>Name</th>
		<th>No. of Rooms</th>
		<th>Occupancy</th>
		<th>TV</th>
		<th>Air con.</th>
		<th>Fun</th>
		<th>Safe</th>
		<th>Fridge</th>
		<th>Status</th>
		<th>Photo</th>
		</tr>";
	//end of field header
	//get data from selected table on the selected fields
	while ($row = fetch_object($rates)) {
		//alternate row colour
		$j++;
		if($j%2==1){
			echo "<tr id=\"row$j\" onmouseover=\"javascript:setColor('$j')\" onmouseout=\"javascript:origColor('$j')\" bgcolor=\"#CCCCCC\">";
			}else{
			echo "<tr id=\"row$j\" onmouseover=\"javascript:setColor('$j')\" onmouseout=\"javascript:origColor('$j')\" bgcolor=\"#EEEEF8\">";
		}
			echo "<td>" . $row->roomno . "</td>";
			echo "<td>" . $row->roomtypeid . "</td>";
			echo "<td>" . $row->roomname . "</td>";
			echo "<td>" . $row->noofrooms . "</td>";
			echo "<td>" . $row->tv . "</td>";
			echo "<td>" . $row->occupancy . "</td>";
			echo "<td>" . $row->aircondition . "</td>";
			echo "<td>" . $row->fun . "</td>";
			echo "<td>" . $row->safe . "</td>";
			echo "<td>" . $row->fridge . "</td>";
			echo "<td>" . $row->status . "</td>";
			echo "<td>" . $row->photo . "</td>";
		echo "</tr>"; //end of - data rows
	} //end of while row
	echo "</table>";
}

function bills_details(){
	$conn=db_connect(HOST,USER,PASS,DB,PORT);
	$sql="SELECT bill_id,book_id,date_billed,billno,status,date_checked FROM bills";

	$bills=mkr_query($sql,$conn);
	//$n=mysql_num_fields($bills);
	echo "<table valign=\"top\">";
	//get field names to create the column header
	echo "<tr bgcolor=\"#009999\">
		<th>Action</th>
		<th>Bill ID.</th>
		<th>Booking ID.</th>
		<th>Date Billed</th>
		<th>Bill No.</th>
		<th>Status</th>
		<th>Checked In</th>
		</tr>";
	//end of field header
	//get data from selected table on the selected fields
	while ($row = fetch_object($bills)) {
		//alternate row colour
		$j++;
		if($j%2==1){
			echo "<tr id=\"row$j\" onmouseover=\"javascript:setColor('$j')\" onmouseout=\"javascript:origColor('$j')\" bgcolor=\"#CCCCCC\">";
			}else{
			echo "<tr id=\"row$j\" onmouseover=\"javascript:setColor('$j')\" onmouseout=\"javascript:origColor('$j')\" bgcolor=\"#EEEEF8\">";
		}
			echo "<td><a href=\"billings.php?search=$row->bill_id&action=search\"><img src=\"images/button_view.png\" width=\"16\" height=\"16\" border=\"0\" title=\"view bill\"/></a></td>";
			echo "<td>" . $row->bill_id . "</td>";
			echo "<td>" . $row->book_id . "</td>";
			echo "<td>" . $row->date_billed . "</td>";
			echo "<td>" . $row->billno . "</td>";
			echo "<td>" . $row->status . "</td>";
			echo "<td>" . $row->date_checked . "</td>";
		echo "</tr>"; //end of - data rows
	} //end of while row
	echo "</table>";
}

function transdetails(){
	$conn=db_connect(HOST,USER,PASS,DB,PORT);
	$sql="Select itemid,item,description,sale,expense From details";

	$bills=mkr_query($sql,$conn);
	//$n=mysql_num_fields($bills);
	echo "<table valign=\"top\">";
	//get field names to create the column header
	echo "<tr bgcolor=\"#009999\">
		<th>Item ID.</th>
		<th>Item</th>
		<th>Description</th>
		<th>Sale</th>
		<th>Expense</th>
		</tr>";
	//end of field header
	//get data from selected table on the selected fields
	while ($row = fetch_object($bills)) {
		//alternate row colour
		$j++;
		if($j%2==1){
			echo "<tr id=\"row$j\" onmouseover=\"javascript:setColor('$j')\" onmouseout=\"javascript:origColor('$j')\" bgcolor=\"#CCCCCC\">";
			}else{
			echo "<tr id=\"row$j\" onmouseover=\"javascript:setColor('$j')\" onmouseout=\"javascript:origColor('$j')\" bgcolor=\"#EEEEF8\">";
		}
			echo "<td title=\"edit details\"><a href=\"lookup.php?search=$row->itemid\"><img src=\"images/button_edit.png\" width=\"16\" height=\"16\" border=\"0\" title=\"edit details\"/></a></td>";
			//echo "<td>" . $row->itemid . "</td>";
			echo "<td>" . $row->item . "</td>";
			echo "<td>" . $row->description . "</td>";
			echo "<td>" . $row->sale . "</td>";
			echo "<td>" . $row->expense . "</td>";
		echo "</tr>"; //end of - data rows
	} //end of while row
	echo "</table>";
}

function Documents(){
	$conn=db_connect(HOST,USER,PASS,DB,PORT);
	$sql="Select doc_id,doc_code,doc_type,remarks,accounts,cooperative,payroll From doctypes";

	$docs=mkr_query($sql,$conn);
	//$n=mysql_num_fields($bills);
	echo "<table valign=\"top\">";
	//get field names to create the column header
	echo "<tr bgcolor=\"#009999\">
		<th>Document ID.</th>
		<th>Document Code</th>
		<th>Document Type</th>
		<th>Remarks</th>
		<th>Accounts</th>
		<th>Cooperative</th>
		<th>Payroll</th>
		</tr>";
	//end of field header
	//get data from selected table on the selected fields
	while ($row = fetch_object($docs)) {
		//alternate row colour
		$j++;
		if($j%2==1){
			echo "<tr id=\"row$j\" onmouseover=\"javascript:setColor('$j')\" onmouseout=\"javascript:origColor('$j')\" bgcolor=\"#CCCCCC\">";
			}else{
			echo "<tr id=\"row$j\" onmouseover=\"javascript:setColor('$j')\" onmouseout=\"javascript:origColor('$j')\" bgcolor=\"#EEEEF8\">";
		}
			/*echo "<td>" . $row->doc_id . "</td>";*/
			//
			$i = 0;
			while ($i < mysql_num_fields($docs)) {
				$meta = mysql_fetch_field($docs, $i);
				$field=$meta->name;
				echo "<td>" . $row->$field . "</td>";
				$i++;
			}
			//
		echo "</tr>"; //end of - data rows
	} //end of while row
	echo "</table>";
	mysql_free_result($docs);
}

function Transtypes(){
	$conn=db_connect(HOST,USER,PASS,DB,PORT);
	$sql="Select trans_id,trans_code,trans_type,remarks,accounts,cooperative,payroll From transtype";
	$trans=mkr_query($sql,$conn);
	//$n=mysql_num_fields($bills);
	echo "<table valign=\"top\">";
	//get field names to create the column header
	echo "<tr bgcolor=\"#009999\">
		<th>Trans. Type ID.</th>
		<th>Trans. Type Code</th>
		<th>Transaction Type</th>
		<th>Remarks</th>
		<th>Accounts</th>
		<th>Cooperative</th>
		<th>Payroll</th>
		</tr>";
	//end of field header
	//get data from selected table on the selected fields
	while ($row = fetch_object($trans)) {
		//alternate row colour
		$j++;
		if($j%2==1){
			echo "<tr id=\"row$j\" onmouseover=\"javascript:setColor('$j')\" onmouseout=\"javascript:origColor('$j')\" bgcolor=\"#CCCCCC\">";
			}else{
			echo "<tr id=\"row$j\" onmouseover=\"javascript:setColor('$j')\" onmouseout=\"javascript:origColor('$j')\" bgcolor=\"#EEEEF8\">";
		}
			/*echo "<td>" . $row->doc_id . "</td>";*/
			//
			$i = 0;
			while ($i < mysql_num_fields($trans)) {
				$meta = mysql_fetch_field($trans, $i);
				$field=$meta->name;
				echo "<td>" . $row->$field . "</td>";
				$i++;
			}
			//
		echo "</tr>"; //end of - data rows
	} //end of while row
	echo "</table>";
	mysql_free_result($trans);
}


function PaymentMode(){
	$conn=db_connect(HOST,USER,PASS,DB,PORT);
	$sql="Select paymentid,payment_option From payment_mode";
	$paymode=mkr_query($sql,$conn);
	//$n=mysql_num_fields($bills);
	echo "<table valign=\"top\">";
	//get field names to create the column header
	echo "<tr bgcolor=\"#009999\">
		<th>Payment ID.</th>
		<th>Payment Mode</th>
		</tr>";
	//end of field header
	//get data from selected table on the selected fields
	//consider having this as a function - is being called in several places
	while ($row = fetch_object($paymode)) {
		//alternate row colour
		$j++;
		if($j%2==1){
			echo "<tr id=\"row$j\" onmouseover=\"javascript:setColor('$j')\" onmouseout=\"javascript:origColor('$j')\" bgcolor=\"#CCCCCC\">";
			}else{
			echo "<tr id=\"row$j\" onmouseover=\"javascript:setColor('$j')\" onmouseout=\"javascript:origColor('$j')\" bgcolor=\"#EEEEF8\">";
		}
			$i = 0;
			while ($i < mysql_num_fields($paymode)) {
				$meta = mysql_fetch_field($paymode, $i);
				$field=$meta->name;
				echo "<td>" . $row->$field . "</td>";
				$i++;
			}
		echo "</tr>"; //end of - data rows
	} //end of while row
	echo "</table>";
	mysql_free_result($paymode);
}

function room_types(){
	$conn=db_connect(HOST,USER,PASS,DB,PORT);
	$sql="SELECT * FROM roomtype";
	$roomtype=mkr_query($sql,$conn);
	//$n=mysql_num_fields($bills);
	echo "<table valign=\"top\">";
	//get field names to create the column header
	echo "<tr bgcolor=\"#009999\">
		<th>Room Type ID.</th>
		<th>Room Type</th>
		<th>Description</th>
		</tr>";
	//end of field header
	//get data from selected table on the selected fields
	//consider having this as a function - is being called in several places - todo
	while ($row = fetch_object($roomtype)) {
		//alternate row colour
		$j++;
		if($j%2==1){
			echo "<tr id=\"row$j\" onmouseover=\"javascript:setColor('$j')\" onmouseout=\"javascript:origColor('$j')\" bgcolor=\"#CCCCCC\">";
			}else{
			echo "<tr id=\"row$j\" onmouseover=\"javascript:setColor('$j')\" onmouseout=\"javascript:origColor('$j')\" bgcolor=\"#EEEEF8\">";
		}
			$i = 0;
			while ($i < mysql_num_fields($roomtype)) {
				$meta = mysql_fetch_field($roomtype, $i);
				$field=$meta->name;
				echo "<td>" . $row->$field . "</td>";
				$i++;
			}
		echo "</tr>"; //end of - data rows
	} //end of while row
	echo "</table>";
	mysql_free_result($roomtype);
}

function agents(){
	echo "<td>Agent Name</td><td><select name=\"agent\" id=\"agent\">
        <option value=\"\">Select Agent</option>";
        echo populate_select('agents','agents_ac_no','agentname',$reservations->agents_ac_no);
      echo "</select></td>";
}

function trans_type(){
	//get items for cashsale or get items for expenses
	switch($_POST["button"]){
	case "Cash":
		echo "<table>
		<tr>
		<td width=\"27%\">Date</td>
		<td width=\"22%\">Details</td>
		<td width=\"26%\">CR Amount</td>
		<td width=\"25%\">Rec No. </td>
		<td align=\"center\">Post</td>
	  </tr>
		<tr><td><input type=\"text\" name=\"doc_date\" id=\"doc_date\" size=\"15\"/><a href=\"javascript:showCal('Calendar7')\"> <img src=\"images/ew_calendar.gif\" width=\"16\" height=\"15\" border=\"0\"/></a></td>
		<td><select name=\"details\">
			<option value=\"\">Select Item</option>";
			populate_select("details","itemid","item",0);
		  echo "</select></td>
		<td><input type=\"text\" name=\"cr\" size=\"20\"/></td>
		<td><input type=\"text\" name=\"doc_no\" size=\"15\" /></td>
		<td align=\"center\"><input type=\"submit\" name=\"Submit\" value=\"Update\"/></td>
		</tr></table>";
		break;
	case "Debit":
		echo "<table>
		<tr>
		<td width=\"27%\">Date</td>
		<td width=\"22%\">Details</td>
		<td width=\"26%\">DR Amount</td>
		<td width=\"25%\">Chit No. </td>
		<td align=\"center\">Post</td>
	  </tr>
		<tr><td><input type=\"text\" name=\"doc_date\" id=\"doc_date\" size=\"15\"/><a href=\"javascript:showCal('Calendar7')\"> <img src=\"images/ew_calendar.gif\" width=\"16\" height=\"15\" border=\"0\"/></a></td>
		<td><select name=\"details\">
			<option value=\"\">Select Item</option>";
			populate_select("details","itemid","item",0);
		  echo "</select></td>
		<td><input type=\"text\" name=\"dr\" size=\"20\"/></td>
		<td><input type=\"text\" name=\"doc_no\" size=\"15\" /></td>
		<td align=\"center\"><input type=\"submit\" name=\"Submit\" value=\"Update\" /></td>
		</tr></table>";
		break;
	}
}

function updatebill(){
	$billno=!empty($_POST['search']) ? $_POST['search'] : 0;
	//$billno=!empty($_POST['billid']) ? $_POST['billid'] : 1;
	$sql="Select transactions.doc_date,details.item,transactions.dr,transactions.cr,transactions.doc_no,transactions.doc_type,details.itemid
		From transactions
		Inner Join details ON transactions.details = details.itemid
		Where transactions.billno = '$search'";
	$results=mkr_query($sql,$conn);

	echo "<table width=\"100%\"  border=\"0\" cellpadding=\"1\">
	  <tr bgcolor=\"#FF9900\">
		<th></th>
		<th>Date</th>
		<th>Details</th>
		<th>DR</th>
		<th>CR</th>
		<th>Balance</th>
		<th>Doc. No. </th>
		<th>Doc. Type</th>
	  </tr>";
	//get data from selected table on the selected fields
	while ($trans = fetch_object($results)) {
		$balance=$balance-$trans->cr+$trans->dr;
		//alternate row colour
		$j++;
		if($j%2==1){
			echo "<tr id=\"row$j\" onmouseover=\"javascript:setColor('$j')\" onmouseout=\"javascript:origColor('$j')\" bgcolor=\"#CCCCCC\">";
			}else{
			echo "<tr id=\"row$j\" onmouseover=\"javascript:setColor('$j')\" onmouseout=\"javascript:origColor('$j')\" bgcolor=\"#EEEEF8\">";
		}
			echo "<td><a href=\"billings.php?search=$guest->guestid&action=search\"><img src=\"images/button_signout.png\" width=\"16\" height=\"16\" border=\"0\" title=\"bill guest\"/></a></td>";
			echo "<td>" . $trans->doc_date . "</td>";
			echo "<td>" . $trans->item . "</td>";
			echo "<td>" . $trans->dr . "</td>";
			echo "<td>" . $trans->cr . "</td>";
			echo "<td>" . $balance . "</td>";
			echo "<td>" . $trans->doc_no . "</td>";
			echo "<td>" . $trans->doc_type . "</td>"; //calucate running balance
		echo "</tr>"; //end of - data rows
	} //end of while row
	echo "<tr><td colspan=\"3\" align=\"center\"><b>TOTAL</b></td><td><b>DR Total</b></td><td><b>CR Total</b></td><td><b>Total Bal.</b></td><tr>";
	echo "</table>";
}
?>
