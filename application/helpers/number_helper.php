<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('format_weight')) {
    /**
     * Fungsi untuk memformat berat
     * - Menghapus titik ribuan
     * - Mengganti koma dengan titik desimal
     * - Menjamin format 4 angka di belakang koma
     *
     * @param string $weight
     * @return string
     */
    function format_weight($weight)
    {
        // Hapus titik ribuan dan ganti koma dengan titik desimal
        return number_format((float)str_replace(',', '.', str_replace('.', '', $weight)), 4, '.', '');
    }
}
