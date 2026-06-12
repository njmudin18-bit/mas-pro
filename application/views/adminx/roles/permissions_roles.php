<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <title><?php echo $nama_halaman; ?> | <?php echo APPS_NAME; ?></title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="description" content="<?php echo APPS_DESC; ?>" />
  <meta name="keywords" content="<?php echo APPS_KEYWORD; ?>" />
  <meta name="author" content="<?php echo APPS_AUTHOR; ?>" />
  <meta http-equiv="refresh" content="<?php echo APPS_REFRESH; ?>">

  <?php $this->load->view('adminx/components/header_css_datatable'); ?>
  <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>files/assets/css/sweetalert2.min.css">
</head>

<body>

  <div class="loader-bg">
    <div class="loader-bar"></div>
  </div>

  <div id="pcoded" class="pcoded">
    <div class="pcoded-overlay-box"></div>
    <div class="pcoded-container navbar-wrapper">

      <?php $this->load->view('adminx/components/navbar'); ?>

      <?php $this->load->view('adminx/components/navbar_chat'); ?>

      <div class="pcoded-main-container">
        <div class="pcoded-wrapper">

          <?php $this->load->view('adminx/components/sidebar'); ?>

          <div class="pcoded-content">

            <?php $this->load->view('adminx/components/breadcrumb'); ?>

            <div class="pcoded-inner-content">
              <div class="main-body">
                <div class="page-wrapper">
                  <div class="page-body">
                    <div class="row">
                      <div class="col-sm-12">

                        <div class="card">
                          <div class="card-header text-center">
                            <h5 style="display: flex;justify-content: center;">
                              <?php echo strtoupper($nama_halaman)." ".strtoupper($roles_detail->roles_name); ?>
                            </h5>
                          </div>
                          <div class="card-block">
                            <form action="<?php echo base_url() ?>rolespermissions/insert_roles_permissions" method="post" accept-charset="utf-8">
                              <div class="dt-responsive table-responsive">
                                <table class="table table-striped table-bordered nowrap">
                                  <thead>
                                    <tr class="bg-primary">
                                      <th class="text-center">No</th>
                                      <th class="text-center">Permission Group</th>
                                      <th class="text-center">Permissions</th>
                                    </tr>
                                    <input type="hidden" name="idroles_edit" value="<?php echo $idroles_edit; ?>">
                                  </thead>
                                  <tbody>
                                    <?php
                                    $start = 0;
                                    foreach ($getpermissions_group_data as $getpermissions_group) {
                                    ?>
                                      <tr>
                                        <td class="text-right"><?php echo ++$start; ?></td>
                                        <td>
                                          <i class="<?= $getpermissions_group->display_icon; ?>"></i>
                                          &nbsp;<?= $getpermissions_group->permissions_groupname; ?>
                                        </td>
                                        <td>
                                          <?php
                                          $list_permissions =  $this->Rolespermissions_model->get_permissions($getpermissions_group->idpermissions_group, $idroles_edit);
                                          ?>
                                          <?php
                                          foreach ($list_permissions as $permissions) {
                                            $checkedlist_permission =  $this->Rolespermissions_model->get_checkedlist_permissions($permissions->idpermissions, $idroles_edit);
                                          ?>
                                            <div class="row m-b-20 align-items-center">
                                              <div class="col-md-9">
                                                <div class="form-check form-check-inline">
                                                  <label class="form-check-label">
                                                    <input type="checkbox" class="form-check-input" name="permissions[]" value="<?= $permissions->idpermissions; ?>" <?php if ($checkedlist_permission->num_rows() > 0) {
                                                                                                                                                                      echo "checked";
                                                                                                                                                                    } ?>>
                                                    <i class="<?= $permissions->display_icon; ?>"></i>
                                                    &nbsp;<?= $permissions->display_name; ?>
                                                  </label>
                                                </div>

                                                <input type="hidden" name="permissions_group[]" value="<?= $permissions->idpermissions_group; ?>" />
                                              </div>
                                              <div class="col-md-3">
                                                <?php
                                                switch ($permissions->type) {
                                                  case 'TRUE':
                                                    echo '<button class="btn waves-effect waves-light btn-primary btn-sm btn-block" type="button">SIDEBAR</button>';
                                                    break;

                                                  case 'NAV':
                                                    echo '<button class="btn waves-effect waves-light btn-warning btn-sm btn-block" type="button">NAVBAR</button>';
                                                    break;

                                                  default:
                                                    echo '<button class="btn waves-effect waves-light btn-danger btn-sm btn-block" type="button">FUNCTION</button>';
                                                    break;
                                                }
                                                ?>
                                              </div>
                                            </div>
                                          <?php
                                          }
                                          ?>
                                        </td>
                                      </tr>
                                    <?php } ?>
                                  </tbody>
                                </table>
                              </div>
                              <br />
                              <div class="text-center">
                                <button type="submit" class="btn btn-primary">
                                  SUBMIT
                                </button>
                              </div>
                              <br />
                            </form>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div id="styleSelector"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script type="text/javascript" src="<?php echo base_url(); ?>files/bower_components/jquery/js/jquery.min.js"></script>
  <script type="text/javascript" src="<?php echo base_url(); ?>files/bower_components/jquery-ui/js/jquery-ui.min.js"></script>
  <?php $this->load->view('adminx/components/bottom_js_datatable'); ?>
  <!-- <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script> -->
  <script type="text/javascript" src="<?php echo base_url(); ?>files/assets/js/sweetalert2.all.min.js"></script>
  <script>
    $(document).ready(function() {


    });
  </script>
</body>

</html>