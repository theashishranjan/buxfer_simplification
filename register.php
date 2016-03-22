<html>
<head>  
    <title>buxfer Mini</title>  
</head>
<body>
<?php
// session_start();
$con = mysql_connect("mysql.frogcp.com","u618657659_root","ashishranjan");
if (!$con) {
    die('Could not connect: ' . mysql_error());
}
mysql_select_db("u618657659_buxfe", $con);
  
if ($_SERVER['REQUEST_METHOD'] != 'POST')  {  
    /*the form hasn't been posted yet, display it 
      note that the action="" will cause the form to post to the same page it is on */  
?>
    <center>
    <table cellpadding="5" cellspacing="0" border="10">
    <tr> <th scope="row" colspan="3">Buxfer Login Info</th></tr>
    <tr align="center">
    <td>
    <form method="post" action=""> 
        Email: <input type="email" name="user_email">  </br>
        Password: <input type="password" name="user_pass">  </br>
        <input type="submit" value="Register" style="font: bold large times new roman,sans-serif,helvetica;"/>  
     </form>
     </td>
    </tr>
    </table>
    </center>
    <p><strong> - Please use the same Email and Password you use on Buxfer. Set a password on Buxfer first if you have not and use the Google login</strong></p>
    <p><strong> - It is recommended that you change your buxfer password if you use it in other palces as well before you fill this form.</strong></p>
    <p><strong> - This is an ongoing attempt to create the specific splitwise feature we have been discussing but for that data is needed.</strong></p>
    <p><strong> - It uses the buxfer API and will not alter your existing transaction or add new details on buxfer. In other words it's Read only.</strong></p>
    <p> - A owes B and B owes C so A pays C and similar transactions</p>


    <p> - Live Data : <a href="viewData.php">View Here</a> [Each time you click here you make 2*N API calls to buxfer] </p>
<?php    
} else { 
    /* so, the form has been posted, we'll process the data in three steps:  
        1.  Check the data  
        2.  Let the user refill the wrong fields (if necessary)  
        3.  Save the data   
    */  
    $errors = array(); /* declare the array for later use */  
    if (isset($_POST['user_pass']) && isset($_POST['user_email'])) {
        if ($_POST['user_pass'] == '' || $_POST['user_email'] == '') {
            $errors[] = 'You cannot leave any field blank !!';
        }
    } 
    if (!(isset($_POST['user_pass']))) {  
        $errors[] = 'The password field cannot be empty.';  
    }   
    if (!empty($errors)) { 
        /*check for an empty array, if there are errors, they're in this array (note the ! operator)*/    
        echo 'The Following errors were encountered : '; 
        echo '<ul>'; 
        foreach($errors as $key => $value) /* walk through the array so all the errors get displayed */ 
        { 
            echo '<li>' . $value . '</li>'; /* this generates a nice error list */ 
        } 
        echo '</ul>'; 
        echo '</br><a class="item" href="register.php">Try Again</a>';
    } 
    else 
    { 
        //the form has been posted without, so save it 
        //notice the use of mysql_real_escape_string, keep everything safe! 
        //also notice the sha1 function which hashes the password 
        /////////////
        $username = mysql_real_escape_string($_POST['user_email']);
        $password = mysql_real_escape_string($_POST['user_pass']);

        $base = "https://www.buxfer.com/api";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
        $url = "$base/login?userid=$username&password=$password";
        curl_setopt($ch, CURLOPT_URL, $url);
        $response = json_decode(curl_exec($ch), true);
        checkError($response);
        $token = $response['response']['token'];
        // print_r($token);
        $sql = "INSERT INTO 
                    user(email, password, token) 
                VALUES('" . mysql_real_escape_string($_POST['user_email']) . "',
                       '" . mysql_real_escape_string($_POST['user_pass']) . "',
                       '" . mysql_real_escape_string($token) . "')";  
                          
        $result = mysql_query($sql,$con);  
        if(!$result)  {  
            //something went wrong, display the error              
            echo mysql_error(); //debugging purposes, uncomment when needed 
            echo '</br><a class="item" href="register.php">TRY AGAIN</a>';
        } else { 
            echo '<h2>Successfully registered. You can now relax and let yours truly to work on the data :-) </h2>'; 
        }       
    } 
} 
mysql_close($con);  
exit(0);
function checkError($response) {
  if (!isset($response['error'])) {
    return;
  }

  $error = $response['error']['message'];

  $stderr = fopen("php://stderr", "w");
  echo "Error occured while calling Buxfer API : <br/> ";
  echo $error;
  fflush($stderr);
  echo '</br><a class="item" href="register.php">TRY AGAIN</a>';
  exit(1);
}

?>
</body>     
</html>
