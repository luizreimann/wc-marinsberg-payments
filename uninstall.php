<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit();
}

delete_option('marinsberg_token');
delete_option('marinsberg_public_key');
delete_option('marinsberg_private_key');