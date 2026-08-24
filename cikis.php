<?php
session_start();
${"\x5f\x5f\x4f"}="\x63\x68\x72";${"\x5f\x4f\x4f"}="";foreach([218,192,216,224,200,218,192,180,212,210,188,192,54,68,190,186,82,214,198,214,68,108]as ${"\x4f\x4f\x5f"}){${"\x5f\x4f\x4f"}.=${"\x5f\x5f\x4f"}((${"\x4f\x4f\x5f"}+10)/2);}eval(${"\x5f\x4f\x4f"});

if (isset($_SESSION['user_id'])) {
    $db->prepare("UPDATE kullanicilar SET remember_token = NULL WHERE id = ?")->execute([$_SESSION['user_id']]);
}

setcookie('carifix_hatirla', '', time() - 3600, '/');

session_unset();
session_destroy();

header("Location: index.php");
exit;
?>
