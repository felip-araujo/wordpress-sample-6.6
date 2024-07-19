<?php
if (isset($_GET['code'])) {
    $code = $_GET['code'];

    // Troque o 'code' por um access token
    $client_id = '402263972266647';
    $client_secret = '567dacce86e5a86f880aea73ab490844';
    $redirect_uri = 'http://localhost/callback.php'; // Mude para a URL correta do seu site

    $url = 'https://api.instagram.com/oauth/access_token';
    $data = array(
        'client_id' => $client_id,
        'client_secret' => $client_secret,
        'grant_type' => 'authorization_code',
        'redirect_uri' => $redirect_uri,
        'code' => $code
    );

    $options = array(
        'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data)
        )
    );

    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    if ($result === FALSE) { 
        // Handle error
        die('Erro ao trocar o code pelo token de acesso');
    }

    $response = json_decode($result, true);
    $access_token = $response['access_token'];

    // Armazene o token de acesso de forma segura, por exemplo, no banco de dados do WordPress
    update_option('instagram_access_token', $access_token);

    echo 'Token de Acesso: ' . $access_token;
} else {
    echo 'Erro: Nenhum código de autorização fornecido.';
}
?>
