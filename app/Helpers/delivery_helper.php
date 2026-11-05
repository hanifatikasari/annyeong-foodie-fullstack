<?php

/**
 * Helper pengiriman Batterbit.
 *
 * Isi: validasi koordinat, validasi/normalisasi link Google Maps,
 * pembuatan URL navigasi Google Maps, pencarian area pengiriman aktif,
 * dan pemeriksaan kelengkapan profil pengiriman.
 *
 * Pemakaian: helper('delivery');
 */

if (! function_exists('delivery_valid_coordinates')) {
    /**
     * @param mixed $lat
     * @param mixed $lng
     */
    function delivery_valid_coordinates($lat, $lng): bool
    {
        if (! is_numeric($lat) || ! is_numeric($lng)) {
            return false;
        }

        $lat = (float) $lat;
        $lng = (float) $lng;

        if ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
            return false;
        }

        // (0,0) = "Null Island": hampir pasti nilai default/error, bukan alamat nyata.
        return ! ($lat == 0.0 && $lng == 0.0);
    }
}

if (! function_exists('delivery_normalize_gmaps_url')) {
    /**
     * Mengembalikan URL Google Maps yang valid, atau null bila tidak valid.
     * Hanya https + host Google Maps yang diterima. Skema berbahaya
     * (javascript:, data:, http:, dll), userinfo, dan host lain ditolak.
     */
    function delivery_normalize_gmaps_url(?string $url): ?string
    {
        $url = trim((string) $url);

        if ($url === '' || strlen($url) > 500 || preg_match('/[\x00-\x20\x7F]/', $url)) {
            return null;
        }

        $parts = parse_url($url);

        if ($parts === false || empty($parts['scheme']) || empty($parts['host'])) {
            return null;
        }

        if (strtolower($parts['scheme']) !== 'https') {
            return null;
        }

        if (isset($parts['user']) || isset($parts['pass'])) {
            return null;
        }

        if (isset($parts['port']) && (int) $parts['port'] !== 443) {
            return null;
        }

        $host = strtolower($parts['host']);
        $path = $parts['path'] ?? '/';

        if ($host === 'maps.app.goo.gl' || $host === 'maps.google.com') {
            return $url;
        }

        if ($host === 'goo.gl' && strncmp($path, '/maps', 5) === 0) {
            return $url;
        }

        if (preg_match('/^(www\.)?google\.(com|co\.id)$/', $host) && strncmp($path, '/maps', 5) === 0) {
            return $url;
        }

        return null;
    }
}

if (! function_exists('delivery_build_maps_url')) {
    /**
     * URL navigasi Google Maps. Prioritas: koordinat -> link manual yang valid.
     * Tidak memerlukan API key.
     *
     * @param mixed $lat
     * @param mixed $lng
     */
    function delivery_build_maps_url($lat, $lng, ?string $gmapsLink = null): ?string
    {
        if (delivery_valid_coordinates($lat, $lng)) {
            return 'https://www.google.com/maps/dir/?api=1&destination='
                . number_format((float) $lat, 7, '.', '') . ','
                . number_format((float) $lng, 7, '.', '');
        }

        return delivery_normalize_gmaps_url($gmapsLink);
    }
}

if (! function_exists('delivery_order_maps_url')) {
    /** URL Google Maps dari snapshot di baris t_penjualan. */
    function delivery_order_maps_url(?object $order): ?string
    {
        if ($order === null) {
            return null;
        }

        return delivery_build_maps_url(
            $order->shipping_latitude ?? null,
            $order->shipping_longitude ?? null,
            isset($order->shipping_gmaps_link) ? (string) $order->shipping_gmaps_link : null
        );
    }
}

if (! function_exists('delivery_find_active_area')) {
    /** Area pengiriman yang ada, aktif, dan tidak terhapus (soft delete). */
    function delivery_find_active_area(int $areaId): ?object
    {
        if ($areaId <= 0) {
            return null;
        }

        $row = \Config\Database::connect()
            ->table('m_shipping_area')
            ->where('id', $areaId)
            ->where('is_active', 1)
            ->where('deleted_at', null)
            ->get(1)
            ->getRow();

        return $row ?: null;
    }
}

if (! function_exists('delivery_profile_missing')) {
    /**
     * Daftar data pengiriman yang belum lengkap pada profil pelanggan.
     * Array kosong = profil lengkap. Dipakai di dashboard dan checkout,
     * berlaku sama untuk akun daftar biasa maupun Google OAuth.
     *
     * @return list<string>
     */
    function delivery_profile_missing(?object $user): array
    {
        if ($user === null) {
            return ['Data akun'];
        }

        $missing = [];

        if (trim((string) ($user->first_name ?? '')) === '') {
            $missing[] = 'Nama depan';
        }
        if (trim((string) ($user->phone ?? '')) === '') {
            $missing[] = 'Nomor HP';
        }
        if (trim((string) ($user->address ?? '')) === '') {
            $missing[] = 'Alamat lengkap';
        }
        if (trim((string) ($user->province ?? '')) === '') {
            $missing[] = 'Provinsi';
        }
        if (trim((string) ($user->city ?? '')) === '') {
            $missing[] = 'Kota/Kabupaten';
        }
        if (trim((string) ($user->postal_code ?? '')) === '') {
            $missing[] = 'Kode pos';
        }
        if (delivery_find_active_area((int) ($user->shipping_area_id ?? 0)) === null) {
            $missing[] = 'Area pengiriman';
        }

        return $missing;
    }
}