<?php

if (!defined('ABSPATH')) exit;

if (!class_exists('FP_EncryptionHandler')) {
    class FP_EncryptionHandler
    {
        const ENCRYPTION_METHOD = 'AES-256-CBC';

        private $encryptionKey;

        public function __construct()
        {
            $this->initializeEncryptionKey();
        }

        private function initializeEncryptionKey()
        {
            $e_key_settings = get_option('fp_theme_encryption_settings', []);
            $e_key = isset($e_key_settings['link_encryption_key']) ? $e_key_settings['link_encryption_key'] : false;
            if ($e_key !== false) {
                $this->encryptionKey = $e_key;
            } else {
                $e_key = $this->generateEncryptionKey();
                $e_key_settings['link_encryption_key'] = $e_key;
                update_option('fp_theme_encryption_settings', $e_key_settings);
                $this->encryptionKey = $e_key;
            }
        }

        public function encryptData($data)
        {
            $data = serialize($data);
            $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(self::ENCRYPTION_METHOD));
            $encrypted = openssl_encrypt($data, self::ENCRYPTION_METHOD, $this->encryptionKey, 0, $iv);
            return base64_encode($encrypted . '::' . $iv);
        }

        public function decryptData($data)
        {
            list($encrypted_data, $iv) = explode('::', base64_decode($data), 2);
            $decrypted = openssl_decrypt($encrypted_data, self::ENCRYPTION_METHOD, $this->encryptionKey, 0, $iv);
            return unserialize($decrypted);
        }

        private function generateEncryptionKey()
        {
            $key = openssl_random_pseudo_bytes(32); // AES-256 key length is 32 bytes (256 bits)
            return base64_encode($key);
        }

        public function getEncryptionKey()
        {
            return $this->encryptionKey;
        }

        public function verifyHash($data, $hash) {
            $v_hash = hash('sha256', $data . $this->encryptionKey) === $hash;
            fp_log('Hash verification: ' . ($v_hash ? 'Success' : 'Failed'));
            return $v_hash;
        }
        
        public function createHash($data) {
            $c_hash = hash('sha256', $data . $this->encryptionKey);
            fp_log('Hash created: ' . $c_hash);
            return $c_hash;
        }
    }
}
