<?php
$con = mysql_connect("mysql.frogcp.com","u618657659_root","ashishranjan");
if (!$con) {
    die('Could not connect: ' . mysql_error());
}
mysql_select_db("u618657659_buxfe", $con);
  
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
