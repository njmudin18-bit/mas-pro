<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('get_harga_bbm_pertamina')) {
  /**
   * Mengambil harga BBM dari API Pertamina berdasarkan Wilayah dan Keyword (Slug gambar)
   * * @param string $wilayah Nama wilayah persis (e.g., 'Prov. Banten')
   * @param string $keyword Keyword unik produk (e.g., 'bio-solar', 'pertamax', 'dexlite')
   * @return string|bool Harga (e.g., '6,800') atau FALSE jika gagal
   */
  function get_harga_bbm_pertamina($wilayah, $keyword) {
    // 1. Setup CURL
    $url = 'https://pertaminapatraniaga.com/api/api/v1/post/get-by-slug/page/harga-terbaru-bbm?language=en';
    
    $ch = curl_init();
    curl_setopt_array($ch, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10, // Timeout dipercepat agar tidak loading lama jika server sana down
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
    ));

    $response = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);

    if ($err) {
        log_message('error', 'BBM Helper CURL Error: ' . $err);
        return FALSE;
    }

    // 2. Decode JSON
    $json_data = json_decode($response, true);
    return json_encode(array("status" => "disini", "data" => $json_data)); exit;
    
    if (!$json_data || !isset($json_data['data']['content'])) {
        return FALSE;
    }

    // 3. Logic "Smart Search" (Sama seperti sebelumnya)
    $content_nodes = $json_data['data']['content'];
    $items_data = [];

    // Level 1: Cari ProductTable
    foreach ($content_nodes as $node) {
        if (isset($node['type']['resolvedName']) && $node['type']['resolvedName'] === 'ProductTable') {
            if (isset($node['props']['items'])) {
                $items_data = $node['props']['items'];
                break; 
            }
        }
    }

    if (empty($items_data)) return FALSE;

    // Level 2 & 3: Cari Wilayah dan Keyword Produk
    // Kita loop semua kategori (Gasoil & Gasoline) agar fungsi ini fleksibel
    foreach ($items_data as $category) {
        if (isset($category['data'])) {
            foreach ($category['data'] as $row) {
                // Cek Wilayah (Case insensitive comparison for safety)
                if (isset($row['WILAYAH']) && strcasecmp(trim($row['WILAYAH']), trim($wilayah)) === 0) {
                    
                    // Level 4: Cari harga berdasarkan keyword gambar
                    foreach ($row as $key => $value) {
                        // Cek apakah key URL gambar mengandung keyword yang dicari
                        if (strpos($key, $keyword) !== false) {
                            // Mengembalikan nilai harga saja
                            return $value; 
                        }
                    }
                }
            }
        }
    }

    return FALSE; // Tidak ketemu
  }
}