<!DOCTYPE html>
<html>
<head>
</head>
<body>
<?php
require_once 'C:\Users\Raam\Desktop\mywebsite\php-oauth2-example\google-api-php-client\src\Google\autoload.php';

session_start();

$client = new Google_Client();
$client->setAuthConfigFile('client_secrets.json');
$client->addScope(Google_Service_Drive::DRIVE_METADATA_READONLY);
$client->setAccessType('online');
$client->setApprovalPrompt('auto') ;
echo 
"<form action='' method='post'> 
<input type='submit' name='use_button' value='List Files' /> 
</form>";


function retrieveAllFiles($service) {
  $result = array();
  $pageToken = NULL;

  do {
    try {
      $parameters = array();
      if ($pageToken) {
        $parameters['pageToken'] = $pageToken;
      }
      $files = $service->files->listFiles($parameters);

      $result = array_merge($result, $files->getFiles());
      $pageToken = $files->getNextPageToken();
    } catch (Exception $e) {
      print "An error occurred: " . $e->getMessage();
      $pageToken = NULL;
    }
  } while ($pageToken);
  return $result;
}


if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
  $client->setAccessToken($_SESSION['access_token']);
  $drive_service = new Google_Service_Drive($client);
  $files_list = $drive_service->files->listFiles(array())->getFiles();
  $obj = json_encode($files_list);
  $data = json_decode($obj, True);
  if(isset($_POST['use_button'])){
  foreach($data as $item)
  {
	  echo $item['name'];
	  echo $item['createdTime'];
	  echo "<br>";
  }
  #echo $data[0]['name'];
  #echo json_encode($files_list);
  }
} else {
  $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/oauth2callback.php';
  header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}
#Uncomment this if you want to print json
/*
$client->setAccessToken($_SESSION['access_token']);
$drive_service = new Google_Service_Drive($client);
$x = retrieveAllFiles($drive_service);
$testobj = json_encode($x);
echo $testobj;
*/

?>
</body>
</html>