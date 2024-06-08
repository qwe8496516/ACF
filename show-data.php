<?php
  
  $credentials_check = file_get_contents('http://169.254.169.254/latest/meta-data/iam/security-credentials/');
  if ($credentials_check == ''){
    exit('<span style="color:red">Unable to retrieve AWS credentials. Please assign an IAM Role to this instance.</span>');
  }

  include('get-parameters.php');

  if ($ep == '') {
   echo 'Please configure Settings to connect to database';
  }
  else {
    # Display inventory

    // Set incoming variables
    isset($_REQUEST['mode']) ? $mode=$_REQUEST['mode'] : $mode="";
    isset($_REQUEST['id']) ? $id=urldecode($_REQUEST['id']) : $id="";
    isset($_REQUEST['bookNMC']) ? $bookNMC=urldecode($_REQUEST['bookNMC']) : $bookNMC="";
    isset($_REQUEST['bookPrice']) ? $bookPrice=$_REQUEST['bookPrice'] : $bookPrice="";
    isset($_REQUEST['quantity']) ? $quantity=$_REQUEST['quantity'] : $quantity="";
  
      
    // Connect to the RDS database
    $connect = mysqli_connect($ep, $un, $pw) or die(mysqli_error($connect));
  
    mysqli_select_db($connect, $db) or die(mysqli_error($connect));
  
  if ( $mode=="add")
   {
   Print '<h2>Add BookStore</h2>
   <p>
   <form action=';
   echo $_SERVER['PHP_SELF'];
   Print '
   method=post>
   <table>
   <tr><td>BookNMC:</td><td><input type="text" name="bookNMC" /></td></tr>
   <tr><td>bookPrice:</td><td><input type="text" name="bookPrice" /></td></tr>
   <tr><td>Quantity:</td><td><input type="text" name="quantity" /></td></tr>
   <tr><td colspan="2" align="center"><input type="submit" class="blue-button"/></td></tr>
   <input type=hidden name=mode value=added>
   </table>
   </form> <p>';
   }
  
   if ( $mode=="added")
   {
   mysqli_query ($connect, "INSERT INTO BookStore (bookNMC, bookPrice, quantity) VALUES ('$bookNMC', '$bookPrice', $quantity)");
   }
  
  if ( $mode=="edit")
   {
   Print '<h2>Edit BookStore</h2>
   <p>
   <form action=';
   echo $_SERVER['PHP_SELF'];
   Print '
   method=post>
   <table>
   <tr><td>bookNMC:</td><td><input type="text" value="';
   Print $bookNMC;
   print '" name="bookNMC" /></td></tr>
   <tr><td>bookPrice:</td><td><input type="text" value="';
   Print $bookPrice;
   print '" name="bookPrice" /></td></tr>
   <tr><td>Quantity:</td><td><input type="text" value="';
   Print $quantity;
   print '" name="quantity" /></td></tr>
   <tr><td colspan="3" align="center"><input type="submit" class="blue-button" /></td></tr>
   <input type=hidden name=mode value=edited>
   <input type=hidden name=id value=';
   Print $id;
   print '>
   </table>
   </form> <p>';
   }
  
   if ( $mode=="edited")
   {
    error_log("UPDATE BookStore SET bookNMC = '$bookNMC', bookPrice = '$bookPrice', quantity = $quantity WHERE id = $id");
   mysqli_query ($connect, "UPDATE BookStore SET bookNMC = '$bookNMC', bookPrice = '$bookPrice', quantity = $quantity WHERE id = $id");
   Print "Data Updated!<p>";
   }
  
  if ( $mode=="remove")
   {
   mysqli_query ($connect, "DELETE FROM BookStore where id=$id");
   Print "Entry has been removed <p>";
   }
  
   $data = mysqli_query($connect, "SELECT * FROM BookStore ORDER BY id ASC") or die(mysqli_error($connect));
   Print "<table id='BookStore' border cellpadding=3>";
   Print "<tr><th width=10/><th width=10/> " .
     "<th>BookNMC</th> " .
     "<th>BookPrice</th> " .
     "<th>Quantity</th></tr>";
   while($info = mysqli_fetch_array( $data ))
   {
   Print "<tr><td><a href=" .$_SERVER['PHP_SELF']. "?id=" . $info['id'] ."&mode=remove><i class='fas fa-trash-alt' style='color:#d82323;'></i></a></td>";
   Print "<td><a href=" .$_SERVER['PHP_SELF']. "?id=" . $info['id'] ."&bookNMC=" . urlencode($info['bookNMC']) . "&bookPrice=" . urlencode($info['bookPrice']) . "&quantity=" . $info['quantity'] ."&email=" . "&mode=edit><i class='fas fa-edit'></i></a></td>";
   Print "<td>".$info['bookNMC'] . "</td> ";
   Print "<td>".$info['bookPrice'] . "</td> ";
   Print "<td>".$info['quantity'] . "</td> ";
   Print "<tr>";
   }
   Print "</table>";
   Print "<br/><a href=" .$_SERVER['PHP_SELF']. "?mode=add class='blue-button'><i class='fas fa-plus'></i> Add BookStore</a>";
  }
?>
