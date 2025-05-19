<?php declare(strict_types=1);

use Defuse\Crypto\Exception\BadFormatException;
use Defuse\Crypto\Exception\EnvironmentIsBrokenException;
use Defuse\Crypto\Key;

/**
 * Creates a UUID v4 as per RFC 4122
 *
 * The UUID contains 128 bits of data (where 122 are random), i.e. 36 characters
 *
 * @return string the UUID
 *
 * @author copied from https://github.com/delight-im/PHP-Auth/blob/master/src/Auth.php
 */
function createUuid(): string{
	$data = \openssl_random_pseudo_bytes(16);

	// set the version to 0100
	$data[6] = \chr(\ord($data[6]) & 0x0f | 0x40);
	// set bits 6-7 to 10
	$data[8] = \chr(\ord($data[8]) & 0x3f | 0x80);

	return \vsprintf('%s%s-%s-%s-%s-%s%s%s', \str_split(\bin2hex($data), 4));
}

/**
 * @throws BadFormatException
 * @throws EnvironmentIsBrokenException
 */
function loadEncryptionKeyFromConfig(): Key{
	return Key::loadFromAsciiSafeString($_ENV['CRYPTO_KEY']);
}

function joinPath(...$segments): string{
	return join(DIRECTORY_SEPARATOR, $segments);
}
