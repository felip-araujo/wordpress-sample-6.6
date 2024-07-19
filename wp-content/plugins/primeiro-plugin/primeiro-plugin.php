<?php
/*
Plugin Name: Primeiro Plugin de Integração com Instagram
Description: Um plugin básico para integrar posts do Instagram no WordPress.
Version: 1.0
Author: Seu Nome
*/

// Adicione um menu no WordPress
function itw_add_menu() {
    add_menu_page(
        'InstaPost',
        'InstaPost',
        'manage_options',
        'itw-instagram-integration',
        'itw_instagram_integration_page'
    );
}

add_action('admin_menu', 'itw_add_menu');

function itw_instagram_integration_page() {
    $client_id = '402263972266647';
    $redirect_uri = 'https://localhost/site-teste/wordpress/callback.php'; // Mude para a URL correta do seu site
    $auth_url = "https://api.instagram.com/oauth/authorize?client_id={$client_id}&redirect_uri={$redirect_uri}&scope=user_profile,user_media&response_type=code";

    echo '<div class="wrap">';
    echo '<h1>Integrar com Instagram</h1>';
    echo '<a href="' . $auth_url . '">Conectar com Instagram </a>';
    itw_display_instagram_posts();
    
    echo '</div>';
}

function itw_display_instagram_posts() {
    $access_token = get_option('instagram_access_token');
    if (!$access_token) {
        echo 'Você precisa conectar-se ao Instagram primeiro.';
        return;
    }

    $url = "https://graph.instagram.com/me/media?fields=id,caption,media_url&access_token={$access_token}";

    $response = file_get_contents($url);
    if ($response === FALSE) {
        echo 'Erro ao obter dados do Instagram.';
        return;
    }

    $data = json_decode($response, true);

    if (isset($data['data'])) {
        echo '<h2>Posts do Instagram</h2>';
        foreach ($data['data'] as $post) {
            echo '<div>';
            echo '<img src="' . $post['media_url'] . '" alt="' . htmlspecialchars($post['caption']) . '" style="max-width:100%;">';
            echo '<p>' . htmlspecialchars($post['caption']) . '</p>';
            echo '</div>';
        }
    } else {
        echo 'Nenhum post encontrado.';
    }
}
