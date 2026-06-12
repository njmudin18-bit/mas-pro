<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Keyence extends CI_Controller
{

  /**
   * Index Page for this controller.
   *
   * Maps to the following URL
   * 		http://example.com/index.php/welcome
   *	- or -
   * 		http://example.com/index.php/welcome/index
   *	- or -
   * Since this controller is set as the default controller in
   * config/routes.php, it's displayed at http://example.com/
   *
   * So any other public methods not prefixed with an underscore will
   * map to /index.php/welcome/<method_name>
   * @see https://codeigniter.com/user_guide/general/urls.html
   */

  public function __construct()
  {
    parent::__construct();

    $this->load->helper(array('url', 'form', 'cookie'));
    $this->load->library(array('session', 'cart'));

    $this->load->model('perusahaan_model', 'perusahaan');
    $this->load->model('machine_model', 'machine');
  }

  public function index()
  {
    $data['group_halaman']   = "PPIC";
    $data['nama_halaman']   = "Monitoring Mesin Keyence";
    $data['icon_halaman']   = "icon-airplay";
    $data['perusahaan']     = $this->perusahaan->get_details();

    $this->load->view('adminx/mesin/keyence', $data, FALSE);
  }

  public function show_data_mesin()
  {
    $response = $this->machine->get_all();
    $text     = "";
    if (count($response) > 0) {
      foreach ($response as $key => $value) {
        $nama_mesin = $value->Namamesin == "" ? "-" : $value->Namamesin;
        $nama_mold  = $value->Namamold == "" ? "-" : $value->Namamold;
        $on_off     = $value->Statusmesin == 1 ? "ON" : "OFF";
        $qty        = $value->qty;
        $no_job     = $value->Job;
        $shift      = $value->Shift;
        $bg         = "";
        $qr         = explode("/", $this->generate_qr($nama_mesin, $nama_mold, $no_job, $shift));
        switch ($value->Statusmesin) {
          case 1:
            $bg = "bg-success";
            break;

          default:
            $bg = "bg-danger";
            break;
        }

        $text .= '<tr scope="row">
                    <th class="text-center" scope="row">
                      <label class="control control--checkbox">
                        <input type="checkbox" />
                        <div class="control__indicator"></div>
                      </label>
                    </th>
                    <td>
                      <small class="d-block text-primary">' . $value->Job . '</small>
                      ' . $nama_mesin . ' - (' . $nama_mold . ')
                      <small class="d-block">' . $value->Namaoperator . '</small>
                    </td>
                    <td class="text-center">
                      <img src="' . base_url() . "qr/" . $qr[1] . '" width="100">
                    </td>
                    <td class="text-center">-</td>
                    <td class="text-center"><h5 class="font-weight-bold">' . $qty . '</h5></td>
                    <td class="text-center">-</td>
                    <td class="text-center">-</td>
                    <td class="' . $bg . ' text-white"><h5 class="font-weight-bold">' . $on_off . '</h5></td>
                  </tr>

                  <tr class="spacer">
                    <td colspan="100"></td>
                  </tr>';
      };
    } else {
    }

    echo json_encode(
      array(
        "status"    => "success",
        "message"   => "sukses menampilkan data",
        "data"      => $response,
        "html"      => $text
      )
    );
  }

  public function details()
  {
    $nama_mesin = base64_decode($this->uri->segment(3));
    $no_job     = base64_decode($this->uri->segment(4));
    $shift      = $this->uri->segment(5);
    echo $nama_mesin . "-" . $no_job . "-" . $shift;
  }

  public function show_data_mesin_OLD()
  {
    $response = $this->machine->get_all();
    //asort($response); //sort array to desc

    $text     = "";
    if (count($response) > 0) {
      foreach ($response as $key => $value) {
        $nama_mesin = $value->Namamesin_old == "" ? "-" : $value->Namamesin_old;
        $nama_mold  = $value->Namamold == "" ? "-" : $value->Namamold;
        $on_off     = $value->Statusmesin == 1 ? "ON" : "OFF";
        $qty        = $value->qty;
        $bg         = "";
        $qr         = explode("/", $this->generate_qr($nama_mesin));
        switch ($value->Statusmesin) {
          case 1:
            $bg = "bg-success";
            break;

          default:
            $bg = "bg-danger";
            break;
        }

        $text .= '<tr scope="row">
                    <th class="text-center" scope="row">
                      <label class="control control--checkbox">
                        <input type="checkbox" />
                        <div class="control__indicator"></div>
                      </label>
                    </th>
                    <td>
                      ' . $nama_mesin . '
                      <small class="d-block">' . $nama_mold . '</small>
                    </td>
                    <td class="text-center">
                      <img src="' . base_url() . "qr/" . $qr[1] . '" width="100">
                    </td>
                    <td class="text-center">-</td>
                    <td class="text-center">' . $qty . '</td>
                    <td class="text-center">-</td>
                    <td class="text-center">-</td>
                    <td class="' . $bg . ' text-white">' . $on_off . '</td>
                  </tr>

                  <tr class="spacer">
                    <td colspan="100"></td>
                  </tr>';
      };
    } else {
    }

    echo json_encode(
      array(
        "status"    => "success",
        "message"   => "sukses menampilkan data",
        "data"      => $response,
        "html"      => $text
      )
    );
  }

  public function generate_qr($nama_mesin, $nama_mold, $no_job, $shift)
  {
    if ($nama_mesin) {
      $filename = 'qr/' . $nama_mesin;
      if (!file_exists($filename)) {
        $this->load->library('ciqrcode');
        $params['data']     = base_url() . "keyence/details/" . base64_encode($nama_mesin) . "/" . base64_encode($no_job) . "/" . $shift;
        $params['level']    = 'H';
        $params['size']     = 10;
        $params['savename'] = FCPATH . "qr/" . $nama_mesin . ".png";
        return  $this->ciqrcode->generate($params);
      }
    }
  }
}
