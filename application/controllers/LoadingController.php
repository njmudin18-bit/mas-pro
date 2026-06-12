<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class LoadingController extends CI_Controller {

    public function __construct() {
      parent::__construct();

      $this->load->library('pusherlibrary');
      $this->BJGMAS01 = $this->load->database("bjsmas01_db", true);
      $this->load->model('perusahaan_model', 'perusahaan');
    }

    public function index() {
      $data['group_halaman'] 	= "Warehouse";
			$data['nama_halaman'] 	= "Loading Barang";
			$data['icon_halaman'] 	= "icon-airplay";
      $data['perusahaan'] 		= $this->perusahaan->get_details();
      $this->load->view('testing', $data, FALSE);
    }

    // Endpoint for sending messages
    public function loading_data() {
      $BarcodeID  = $this->input->post('BarcodeID');
      $Sql        = "SELECT
                      a.no_po AS PONumber,a.no_do AS DONumber, a.part_id AS PartID, b1.PartName,
                      FORMAT(CAST(a.qty_order AS DECIMAL(18, 0)), 'N0') AS QtyOrder, 
                      a.nama_customer AS CustomerName, c.ShipmentID,
                      d.Alamat, a.create_date AS CreateDate
                    FROM 
                      tbl_scanbarcode_approval a 
                      LEFT JOIN Ms_Part b1 ON b1.PartID = a.part_id
                      LEFT JOIN Trans_SJHD202411 c ON c.NoBukti = a.no_do
                      LEFT JOIN Ms_CustomerAlamatKirim d ON d.CustomerIDAlamat = c.ShipmentID
                      LEFT JOIN (
                        SELECT barcodeid FROM tbl_printqrcodedo
                        UNION ALL
                        SELECT barcodeid FROM tbl_printqrcodedoulang
                      ) b2 ON b2.barcodeid = a.barcode_id
                    WHERE 
                      CAST(a.create_date AS DATE) = CAST(GETDATE() AS DATE)
                      AND a.barcode_id = '$BarcodeID'
                    ORDER BY 
                      a.create_date DESC";
      $Query      = $this->BJGMAS01->query($Sql);
      $Res        = $Query->row();
      // Prepare the data for Pusher
      if ($BarcodeID) {
        $Data = [
          'type'    => 'message',
          'user'    => 'User1',
          'message' => $BarcodeID,
          'data'    => $Res
        ];
        $this->pusherlibrary->trigger('my-channel', 'my-event', $Data);
      }

      echo json_encode(['status' => 'success', 'data' => $Data]);
    }

    public function sendMessageOLD() {
        $message = "Kuy lah";//$this->input->post('message');
        $file = $_FILES['file'];

        // Prepare the data for Pusher
        if ($message) {
            $data = [
                'type' => 'message',
                'user' => 'User1',  // You can dynamically set this to the logged-in user
                'message' => $message
            ];
            $this->pusherlibrary->trigger('my-channel', 'my-event', $data);
        }

        // Handle file upload
        if ($file) {
            // Perform the necessary file upload (you can use CodeIgniter's upload library here)
            $file_name = $file['name']; // Example, process the file upload

            $data = [
                'type' => 'file',
                'user' => 'User1',  // User
                'file_name' => $file_name
            ];
            $this->pusherlibrary->trigger('my-channel', 'my-event', $data);
        }

        echo json_encode(['status' => 'success']);
    }
}
