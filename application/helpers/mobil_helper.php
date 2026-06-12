<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('get_daftar_mobil')) {

  /**
   * Mengembalikan array daftar plat nomor mobil
   * @return array
   */
  function get_daftar_mobil() {
    // Format: 'value' => 'Label yang tampil'
    // Karena value dan label sama, kita buat sama isinya.
    return [
      [
        'value' => 'A 8552 ZT|7', 
        'label' => 'A 8552 ZT'
      ],
      [
        'value' => 'A 9372 ZA|6', 
        'label' => 'A 9372 ZA'
      ],
      [
        'value' => 'A 8762 YX|7', 
        'label' => 'A 8762 YX'
      ],
      // [
      //   'value' => 'A 9403 ZX', 
      //   'label' => 'A 9403 ZX'
      // ],
      // [
      //   'value' => 'A 1193 YE', 
      //   'label' => 'A 1193 YE'
      // ],
    ];
  }

  function get_mobil_value($plat_nomor)
  {
    // Definisi Array Master Data
    $list_mobil = [
      [
        'value' => 'A 8552 ZT|7',
        'label' => 'A 8552 ZT'
      ],
      [
        'value' => 'A 9372 ZA|6',
        'label' => 'A 9372 ZA'
      ],
      [
        'value' => 'A 8762 YX|7',
        'label' => 'A 8762 YX'
      ]
    ];

    foreach ($list_mobil as $item) {
      if ($item['label'] === $plat_nomor) {
        return $item['value']; 
      }
    }

    return '';
  }
}