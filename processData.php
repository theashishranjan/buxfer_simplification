<?php

$aliases = array(
    'Akarsh Kumar' => 'akarsh.iitb@gmail.com',
    'Anish Sankhe' => 'anishsankhe@gmail.com',
    'Ankit Malhan' => 'ankitmalhan.iitd@gmail.com',
    'Ashish Ranjan' => 'ashishranjaniitd@gmail.com',
    'Gaurav Jethliya' => 'gauravjaithliya@gmail.com',
    'Harshal Jain' => 'harshaljain950@gmail.com',
    'harshal' => 'harshaljain950@gmail.com',
    'harshaljain950' => 'harshaljain950@gmail.com',
    'harshaljain950@gmail' => 'harshaljain950@gmail.com',
    'Nikita Navral' => 'nikitanavral1404@gmail.com',
    'nikita' => 'nikitanavral1404@gmail.com',
    'Nimish Mehta' => 'mehta144nimish@gmail.com',
    'nimish' => 'mehta144nimish@gmail.com',
    'Nivedan Rathi' => 'rathinivedan@gmail.com',
    'nivedan' => 'rathinivedan@gmail.com',
    'Piyush Singh' => 'piyush.singh1611@gmail.com',
    'piyush' => 'piyush.singh1611@gmail.com',
    'piyushsingh1611@gmail.com' => 'piyush.singh1611@gmail.com' ,
    'Pragya' => 'pragyamaheshwari90@gmail.com',
    'pragyamaheshwari@gmail.com' => 'pragyamaheshwari90@gmail.com',
    'Rahul' => 'rahul13kol@gmail.com',
    'Rajesh Kamineni' => 'rajeshraokamineni@gmail.com',
    'Robin Singh' => 'robinsinghiitd@gmail.com',
    'Siddharth Bidwan' => 'sidbid009@gmail.com',
    'jai.chaudhary.iitd@gmail..com' => 'jai.chaudhary.iitd@gmail.com',
    'shukla.shashwat@gmail.com' => 'shukla.shashwat21@gmail.com',
);

           
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
            user
        WHERE email not like 'nikitanavral1404@gmail.com'";  

$result = mysql_query($sql,$con); 

if (!$result)  {  
    //something went wrong, display the error                           
    echo mysql_error();die;
} else {
    while ($row = mysql_fetch_array($result)) {
        $id[] = $row['id'];
        $email[] = $row['email'];
        $pass[] = $row['password'];

    }
}
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
    $your_contacts[$username] = $contacts ['response']['contacts'];

    $url = "$base/loans?token=$token";
    curl_setopt($ch, CURLOPT_URL, $url);
    $response = json_decode(curl_exec($ch), true);
    checkError($response);
    $response = $response ['response'];

    $count_alias = array();
    foreach ($response ['loans'] as $loans) {
        if (array_key_exists($loans['key-loan']['entity'], $aliases)) {
            $borrower = $aliases[$loans['key-loan']['entity']];
            if (isset($count_alias[$borrower])){
                $count_alias[$borrower] += 1;
            } else {
                $count_alias[$borrower] = 1;
            }
            
        } else {
            $borrower = $loans['key-loan']['entity'];
            $count_alias[$borrower] = 1;
        }

        if (isset($confusion[$username][$borrower])) {
            $confusion[$username][$borrower] += $loans['key-loan']['balance'];
        } else {
            $confusion[$username][$borrower] = $loans['key-loan']['balance'];
        }

        if ($count_alias[$borrower] > 1) {
            if (isset($confusion[$borrower][$username])) {
                $confusion[$borrower][$username] += (-1 * $loans['key-loan']['balance']);
            } else {
                $confusion[$borrower][$username] = -1 * $loans['key-loan']['balance'];
            }
        }
        $all_users[] = $borrower;
    }
}

$all_users = array_values(array_unique($all_users));
// sort($all_users);
// sort($registered_users);
// foreach ($all_users as $value) {
//     echo $value."</br>";
// }

// making zero balance group 

$final = array();
$left_out = array();
foreach ($confusion as $user => $confusion_subset) {
    foreach ($confusion_subset as $key => $value) {
        if (strcmp($user, $key) != 0) {
            if (in_array($key, $registered_users) && in_array($user, $registered_users)) {
                $final[$user][$key] = $value;
            } else {
                $left_out[$user][$key] = $value;
            }
        }
    }
}


// echo json_encode($final);die;
foreach ($final as $key => $value) {
    $user_balance = array_sum($value);
    if ($user_balance >= 0) {
        $positive[$key] = round((double) $user_balance, 2);
    } else {
        $negative[$key] = round((double) $user_balance, 2);
    }
}


asort($positive);
asort($negative);

if (array_sum($positive) != array_sum($negative)) {
    echo json_encode(array('status' => false));
    die;
}
// echo json_encode(array('final' => $final,'reg_users'=>$registered_users,'p'=>$positive, 'n'=>$negative, 'p_sum'=>array_sum($positive), 'n_sum'=>array_sum($negative)));die;

// die;
// 159.65
$p_keys = array_keys($positive);
$n_keys = array_keys($negative);



$p_count = 0;
$n_count = 0;

$original_count_p = count($p_keys);
$original_count_n = count($n_keys);

$total = 0;
$min_transfer = array();
while ($total < (count($registered_users))) {
    $positive = array_filter($positive);
    $negative = array_filter($negative);
    $p_keys = array_keys($positive);
    $n_keys = array_keys($negative);

    if (empty($positive) && empty($negative)) {
        break;
    }

    if (((count($n_keys) - $n_count) === 0) || (count($n_keys) != $original_count_n)) {
        $n_count = 0;
        $original_count_n = count($n_keys);
    }
    if (((count($p_keys) - $p_count) === 0) || (count($p_keys) != $original_count_p)) {
        $p_count = 0;
        $original_count_p = count($p_keys);
    }
    
    // Person with negative balance is A.
    $A = $n_keys[$n_count];
    $B = $p_keys[$p_count];
    
    if ($n_count < count($n_keys)) {
        $n_count += 1;
    }
    if ($p_count < count($p_keys)) {
         $p_count += 1;
    }
    
    if (isset($negative[$A]) && isset($positive[$B])) {
        $S = $negative[$A];
        $T = $positive[$B];
        $M = min((-1*$S), $T);

        $min_transfer[$A][$B] = $M;
         // echo $A." -> ".$B." :".$M." ,,,,,    ";
        
        if (abs($negative[$A]+$M) < 0.01 ) {
            $negative[$A] = 0;
        } else {
            $negative[$A] += $M;
        }
        if (abs($positive[$B]-$M) < 0.01 ) {
            $positive[$B] = 0;
        } else {
            $positive[$B] -= $M;
        }
         // echo $negative[$A]." and ".$positive[$B]."</br>";
    }    
    $total += 1;
}

echo json_encode(array('optimized' => $min_transfer, 'left' => $left_out, 'status' => true));

// echo json_encode($final);

// echo json_encode($positive);
// echo json_encode($negative);


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