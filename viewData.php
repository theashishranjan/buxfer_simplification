<?php
$con = mysql_connect("mysql.frogcp.com","u618657659_root","ashishranjan");
if (!$con) {
    die('Could not connect: ' . mysql_error());
}
mysql_select_db("u618657659_buxfe", $con);
$sql = "SELECT
            id, 
            email,
            password
        FROM 
            user";  

              
$result = mysql_query($sql,$con); 
// $email = array();
// $pass = array();

if (!$result)  {  
    //something went wrong, display the error                           
    echo mysql_error();die; //debugging purposes, uncomment when needed 
    //echo '</br><a class="item" href="index.php">TRY AGAIN</a>';
} else {
    while ($row = mysql_fetch_array($result)) {
        // print_r($row);
        $id[] = $row['id'];
        $email[] = $row['email'];
        $pass[] = $row['password'];

    }
}
// var_dump($email, $pass);
#############
$registered_users = array();
$all_users = array();
$base = "https://www.buxfer.com/api";

$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");  
foreach ($email as $key => $username) {
    $registered_users[] = $username;
    $password = $pass[$key];
    $userid = $id[$key]; 
    $url = "$base/login?userid=$username&password=$password";
    curl_setopt($ch, CURLOPT_URL, $url);
    $response = json_decode(curl_exec($ch), true);
    checkError($response);
    $token = $response['response']['token'];
  
    $url = "$base/contacts?token=$token";
    curl_setopt($ch, CURLOPT_URL, $url);
    $contacts = json_decode(curl_exec($ch), true);
    checkError($contacts);
    $contacts = $contacts ['response'];

    $url = "$base/loans?token=$token";
    curl_setopt($ch, CURLOPT_URL, $url);
    $response = json_decode(curl_exec($ch), true);
    checkError($response);
    $response = $response ['response'];
    
    foreach ($response ['loans'] as $loans) { 
        // $transactions[] = array(
        //         'owner' => $username,
        //         'entity' => $loans['key-loan']['entity'],
        //         'type' => $loans['key-loan']['type'],
        //         'balance' => $loans['key-loan']['balance'],
        //         'description' => $loans['key-loan']['description'],
        // );
        $confusion[$username][$loans['key-loan']['entity']] = $loans['key-loan']['balance'];
        $all_users[] = $loans['key-loan']['entity'];
    }
}

$all_users = array_values(array_unique($all_users));
sort($all_users);
sort($registered_users);

// echo "<table border='2' style='width:100%'>
//   <tr>
//     <th></th>";
// foreach ($all_users as $all_user_key => $all_user) {
//     echo "<td tyle='word-break:break-all;'>$all_user</td>";
// }   
// echo "</tr>";
// foreach ($registered_users as $registered_user_key => $registered_user) {
//     echo "<tr>";
//     echo "<th>".$registered_user."</th>";
//     foreach ($all_users as $key => $value) {
//         if(isset($confusion[$registered_user][$value])) {
//             echo "<td>".$confusion[$registered_user][$value]."</td>";
//         } else {
//             echo "<td>0</td>";
//         }
//     }
//     echo "</tr>";
// }

// echo "</table>";

// echo "<pre>";
// print_r($confusion);
// echo "</pre>";
$sum = array();
foreach ($confusion as $key => $value) {
    $sum[$key] = array_sum($value);
}
echo json_encode(array('confusion' => $confusion, 'sum' => $sum));

// echo "<pre>";
// print_r($all_users);
// echo "</pre>";

// usort($transaction, "cmp");
function cmp($a, $b)
{
    return strcmp($a["entity"], $b["entity"]);
}

exit(0);

function checkError($response) {
  if (!isset($response['error'])) {
    return;
  }

  $error = $response['error']['message'];

  $stderr = fopen("php://stderr", "w");
  fprintf($stderr, "An error occured: %s\n", $error);
  fflush($stderr);
  exit(1);
}
?>