<?php
namespace Bucorel\Waf\Dal;

class Uuid {
    public static function v4(): string {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000, // version 4
            mt_rand(0, 0x3fff) | 0x8000, // variant
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    public static function v5(string $namespace, string $name): string {
        // hash-based (e.g., for consistent IDs from email, etc.)
        $ns_bin = self::fromStringToBinary($namespace);
        $hash = sha1($ns_bin . $name);
        return self::formatUuid($hash, 5);
    }

    private static function fromStringToBinary(string $uuid): string {
        return pack('H*', str_replace('-', '', $uuid));
    }

    private static function formatUuid(string $hash, int $version): string {
        return sprintf('%08s-%04s-%04x-%04x-%12s',
            substr($hash, 0, 8),
            substr($hash, 8, 4),
            (hexdec(substr($hash, 12, 4)) & 0x0fff) | ($version << 12),
            (hexdec(substr($hash, 16, 4)) & 0x3fff) | 0x8000,
            substr($hash, 20, 12)
        );
    }

    public static function v7(): string {
        // Get time in milliseconds (48 bits)
        $time = (int)(microtime(true) * 1000);
        $timeBytes = pack('J', $time); // 64-bit unsigned, big endian
        $timeHex = bin2hex(substr($timeBytes, 2, 6)); // 48 bits

        // Generate 80 bits of secure random data (10 bytes)
        $rand = random_bytes(10);
        $randHex = bin2hex($rand);

        // Compose the UUID fields
        $time_low = substr($timeHex, 0, 8);
        $time_mid = substr($timeHex, 8, 4);
        $time_high_and_version = dechex((hexdec(substr($randHex, 0, 4)) & 0x0fff) | 0x7000); // version 7

        $clock_seq_and_variant = dechex((hexdec(substr($randHex, 4, 4)) & 0x3fff) | 0x8000); // variant RFC 4122
        $node = substr($randHex, 8, 12);

        return sprintf('%08s-%04s-%04s-%04s-%012s',
            $time_low,
            $time_mid,
            $time_high_and_version,
            $clock_seq_and_variant,
            $node
        );
    }
}
?>
