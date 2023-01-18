<?php 
function validacion_curl($url,$datos){
    $curl = curl_init();
    curl_setopt_array($curl, [
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => stripcslashes($datos),
    ]);
    
    $response = curl_exec($curl);
    
    $err = curl_error($curl);
    curl_close($curl);
  
    if ($err) {
      $respuesta= "cURL Error #:" . $err;
      
    } else {
      $respuesta= $response;
      //correo_traza("trama-credito",print_ttxt($respuesta));
      
    }  
    //correo_traza("trama-credito",print_ttxt($respuesta));
    return $respuesta;
}