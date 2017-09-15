<?php

class Hash{
	public static function create($password) {
		$options = [
    		'cost' => 11,
    		'salt' => mcrypt_create_iv(22, MCRYPT_DEV_URANDOM),
		];
		return password_hash($password, PASSWORD_BCRYPT, $options);
	}

	public static function check($password, $hash) {
		return password_verify($password, $hash);
	}
}