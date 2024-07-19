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
    $redirect_uri = 'https://evoludesign.com.br/wordpress/callback.php'; // Mude para a URL correta do seu site
    $auth_url = "https://api.instagram.com/oauth/authorize?client_id={$client_id}&redirect_uri={$redirect_uri}&scope=user_profile,user_media&response_type=code";

    echo '<div class="wrap">';
    echo '<h1>Integrar com Instagram</h1>';
    echo '<a href="' . $auth_url . '">Conectar com Instagram</a>';
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
            echo '<form method="post" action="' . admin_url('admin-post.php') . '">';
            echo '<input type="hidden" name="action" value="create_instagram_post">';
            echo '<input type="hidden" name="post_id" value="' . $post['id'] . '">';
            echo '<input type="submit" name="create_post" value="Criar Post no WordPress">';
            echo '</form>';
            echo '</div>';
        }
    } else {
        echo 'Nenhum post encontrado.';
    }
}

add_action('admin_post_create_instagram_post', 'itw_create_instagram_post');

function itw_create_instagram_post() {
    if (!current_user_can('manage_options')) {
        return;
    }

    $access_token = get_option('instagram_access_token');
    if (!$access_token) {
        wp_die('Você precisa conectar-se ao Instagram primeiro.');
    }

    if (!isset($_POST['post_id'])) {
        wp_die('ID do post não fornecido.');
    }

    $post_id = sanitize_text_field($_POST['post_id']);
    $url = "https://graph.instagram.com/{$post_id}?fields=id,caption,media_url&access_token={$access_token}";

    $response = file_get_contents($url);
    if ($response === FALSE) {
        wp_die('Erro ao obter dados do Instagram.');
    }

    $data = json_decode($response, true);
    if (!isset($data['id'])) {
        wp_die('Dados inválidos do Instagram.');
    }

    $post_content = '<img src="' . esc_url($data['media_url']) . '" alt="' . esc_attr($data['caption']) . '" style="max-width:100%;">';

    $post_data = array(
        'post_title' => wp_strip_all_tags($data['caption']),
        'post_content' => $post_content,
        'post_status' => 'publish',
        'post_author' => get_current_user_id(),
    );

    wp_insert_post($post_data);

    wp_redirect(admin_url('admin.php?page=itw-instagram-integration'));
    exit;
}
