<?php

include_once("HumanNames.php");

$hm = new HumanNames();
$humans = $hm->generateHumans(3000);
$content = "age;sex;first_name;last_name;email;login;password\n";
foreach($humans as $human) {
    $content .= implode(";",array_values($human))."\n";
}
file_put_contents("example.csv", $content);
echo "data saved to example.csv";
