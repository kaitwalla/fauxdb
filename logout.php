<?

session_name('fauxdb');
session_start();
session_destroy();
Header('Location:/fauxdb/admin.php');

?>