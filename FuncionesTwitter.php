<?php

function Twitter_FormatearUser($usuario){

    # @XXXX      ->  @XXXX
    # XXXX       ->  @XXXX
    # user@email.com  ->

    $retorno = "";

    # Si no es un email
    if(!filter_var($usuario, FILTER_VALIDATE_EMAIL))

        # Si contiene '@' al principio
        if(substr($usuario,0,1) == "@")
            $retorno = $usuario;
        else
            $retorno = "@$usuario";

    return $retorno;
}

function Twitter_ComprobarLogin($usuario, $contrasena){

	# Sacamos el Authenticity Token
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://twitter.com/");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.116 Safari/537.36');
	curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie.txt");
	curl_setopt($ch, CURLOPT_REFERER, "https://twitter.com/");
	$html = curl_exec($ch);
	
	# Parseamos el Token
	preg_match('/<input type="hidden" value="([a-zA-Z0-9]*)" name="authenticity_token"\/>/', $html, $match);
	$authenticity_token = $match[1];
	
	$username = urlencode($usuario);
	$password = urlencode($contrasena);
	
	# Peticion POST
	$campos = array(
			'session[username_or_email]' => $username,
			'session[password]' => $password,
			'return_to_ssl' => "true",
			'scribe_log' => "",
			'redirect_after_login' => "/",
			'authenticity_token' => $authenticity_token
			);
	
	foreach($campos as $key=>$value) { $campos_string .= $key.'='.$value.'&'; }
	rtrim($campos_string, '&');
	
	# Logueamos
	curl_setopt($ch, CURLOPT_URL, "https://twitter.com/sessions");
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $campos_string);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: application/x-www-form-urlencoded"));
	
	$respuesta = curl_exec($ch);
	curl_close($ch);
	
	# Tratamos la respuesta
	$busqueda = "/<a href=\"([^\"]*)\">/";
	preg_match($busqueda, $respuesta, $href);
	$enlace = $href[1];
	
	# El login es perfecto
	if($enlace == "https://twitter.com/") return true;
	return false;	
}

function Twitter_SacarUser($usuario, $contrasena){

	# Sacamos el Authenticity Token
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://twitter.com/");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.116 Safari/537.36');
	curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie.txt");
	curl_setopt($ch, CURLOPT_REFERER, "https://twitter.com/");
	$html = curl_exec($ch);
	
	# Parseamos el Token
	preg_match('/<input type="hidden" value="([a-zA-Z0-9]*)" name="authenticity_token"\/>/', $html, $match);
	$authenticity_token = $match[1];
	
	$username = urlencode($usuario);
	$password = urlencode($contrasena);
	
	# Peticion POST
	$campos = array(
			'session[username_or_email]' => $username,
			'session[password]' => $password,
			'return_to_ssl' => "true",
			'scribe_log' => "",
			'redirect_after_login' => "/",
			'authenticity_token' => $authenticity_token
			);
	
	foreach($campos as $key=>$value) { $campos_string .= $key.'='.$value.'&'; }
	rtrim($campos_string, '&');
	
	# Logueamos
	curl_setopt($ch, CURLOPT_URL, "https://twitter.com/sessions");
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $campos_string);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: application/x-www-form-urlencoded"));
	
	$respuesta = curl_exec($ch);
	
	# Tratamos la respuesta
	$busqueda = "/<a href=\"([^\"]*)\">/";
	preg_match($busqueda, $respuesta, $href);
	$enlace = $href[1];
	
	if($enlace == "https://twitter.com/"){

		# El login es perfecto
		# Capturamos la web principal de Twitter

		curl_setopt($ch, CURLOPT_URL, "https://twitter.com/");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.116 Safari/537.36');;
		curl_setopt($ch, CURLOPT_REFERER, "https://twitter.com/");

		$respuesta = curl_exec($ch);
		curl_close($ch);

		# Tratamos la respuesta
		$busqueda = '/<span class="screen-name hidden" dir="ltr">(.*)<\/span>/';
		preg_match($busqueda, $respuesta, $nombre);
		return $nombre[1];
	}

	curl_close($ch);
	return $user;	
}

function Twitter_SacarEmail($usuario, $contrasena){
    
    # Comprobamos si no es un e-mail
    if(filter_var($usuario, FILTER_VALIDATE_EMAIL)) return $usuario;

	# Sacamos el Authenticity Token
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://twitter.com/");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.116 Safari/537.36');
	curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie.txt");
	curl_setopt($ch, CURLOPT_REFERER, "https://twitter.com/");
	$html = curl_exec($ch);
	
	# Parseamos el Token
	preg_match('/<input type="hidden" value="([a-zA-Z0-9]*)" name="authenticity_token"\/>/', $html, $match);
	$authenticity_token = $match[1];
	
	$username = urlencode($usuario);
	$password = urlencode($contrasena);
	
	# Peticion POST
	$campos = array(
			'session[username_or_email]' => $username,
			'session[password]' => $password,
			'return_to_ssl' => "true",
			'scribe_log' => "",
			'redirect_after_login' => "/",
			'authenticity_token' => $authenticity_token
			);
	
	foreach($campos as $key=>$value) { $campos_string .= $key.'='.$value.'&'; }
	rtrim($campos_string, '&');
	
	# Logueamos
	curl_setopt($ch, CURLOPT_URL, "https://twitter.com/sessions");
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $campos_string);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: application/x-www-form-urlencoded"));
	
	$respuesta = curl_exec($ch);
	
	# Tratamos la respuesta
	$busqueda = "/<a href=\"([^\"]*)\">/";
	preg_match($busqueda, $respuesta, $href);
	$enlace = $href[1];
	
	if($enlace == "https://twitter.com/"){

		# El login es perfecto
		# Capturamos la web 'https://twitter.com/settings/account'

		curl_setopt($ch, CURLOPT_URL, "https://twitter.com/settings/account");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.116 Safari/537.36');;
		curl_setopt($ch, CURLOPT_REFERER, "https://twitter.com/");

		$respuesta = curl_exec($ch);
		curl_close($ch);

		# Tratamos la respuesta
		//echo htmlentities($respuesta);
		$busqueda = '/name="user\[email\]" type="text" value="([^"]*)">/';
		preg_match($busqueda, $respuesta, $email);
		return $email[1];
	}

	curl_close($ch);
	return $usuario;	
}

function Twitter_TwittearMensaje($usuario, $contrasena, $texto){

	# Sacamos el Authenticity Token
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://twitter.com/");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.116 Safari/537.36');
	curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie.txt");
	curl_setopt($ch, CURLOPT_REFERER, "https://twitter.com/");
	$html = curl_exec($ch);
	
	# Parseamos el Token
	preg_match('/<input type="hidden" value="([a-zA-Z0-9]*)" name="authenticity_token"\/>/', $html, $match);
	$authenticity_token = $match[1];
	
	$username = urlencode($usuario);
	$password = urlencode($contrasena);
	
	# Peticion POST
	$campos = array(
			'session[username_or_email]' => $username,
			'session[password]' => $password,
			'status' => $texto,
			'authenticity_token' => $authenticity_token
			);
	
	foreach($campos as $key=>$value) { $campos_string .= $key.'='.$value.'&'; }
	rtrim($campos_string, '&');
	
	# Logueamos
	curl_setopt($ch, CURLOPT_URL, "https://twitter.com/intent/tweet/update");
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $campos_string);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: application/x-www-form-urlencoded"));
	
	$respuesta = curl_exec($ch);
	curl_close($ch);
	
	# Tratamos la respuesta
	$busqueda = "/<a href=\"([^\"]*)\">/";
	preg_match($busqueda, $respuesta, $href);
	$enlace = $href[1];
	
	# El login es perfecto
	if(strpos($enlace,'https://twitter.com/intent/tweet/complete') !== false) return true;
	return false;	
}

?>