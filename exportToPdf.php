<?php
try {
  // include your composer dependencies
  require_once 'googleapi/vendor/autoload.php';

  $client = new Google\Client();
    /*
  $client->setApplicationName("Images Trompeuses");
  $client->setDeveloperKey("AIzaSyAg88VtvmGKzaLLcNMZvjLhsoiaAXUtz6U");
  */
  /*
  $client->setAuthConfig('assets/data/client_secret_799448019872-ll6ucvdkp0v37vtdt43d4ocq435hsgbc.apps.googleusercontent.com.json');
  $client->addScope(Google\Service\Drive::DRIVE);
  // Your redirect URI can be any registered URI, but in this example
  // we redirect back to this same page
  $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
  $client->setRedirectUri($redirect_uri);
  */
  putenv('GOOGLE_APPLICATION_CREDENTIALS=assets/data/images-trompeuses-0e0da7ebf707.json');
  $client->useApplicationDefaultCredentials();
  $client->addScope(Google\Service\Drive::DRIVE);
  $service = new Google\Service\Drive($client);

  $fileId = $_GET['id'];

  //récupère les infos du fichier
  $infos = $service->files->get($fileId);
  //ATTENTION pour avif et heic il faut imprimer en pdf à la main
  $docTypeToConvert = array("image/avif","application/vnd.google-apps.document","image/heic","image/heif");
  if(in_array($infos->mimeType, $docTypeToConvert)){
    //vérifie que le fichier est abscent
    $path = 'assets/exportpdf/'.$fileId.'.pdf';
    if(!file_exists($path)) {
      $response = $service->files->export($fileId, 'application/pdf', array(
          'alt' => 'media'));
      $pdf_data = $response->getBody()->getContents();
      // Then just save it like this
      file_put_contents( $path, $pdf_data );
    }
    echo 'http://' . $_SERVER['HTTP_HOST'] . str_replace('exportToPdf.php',$path,$_SERVER['PHP_SELF']);  
  }else{
    echo 'no';
  };

} catch (Exception $e) {
  echo 'Exception reçue : ',  $e->getMessage(), "\n";
}
