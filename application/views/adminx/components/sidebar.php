<nav class="pcoded-navbar d-print-none">
	<div class="nav-list">
		<div class="pcoded-inner-navbar main-menu">
			<div class="pcoded-navigation-label">Navigation</div>
			<ul class="pcoded-item pcoded-left-item">
				<?php
				$user_le 	= $this->session->userdata('user_level');
				$tk_c 		= $this->router->class;
				$tk_m 		= $this->router->method;
				$get_permissionsgroup_data =  $this->Dashboard_model->getroles_permissions($user_le);
				foreach ($get_permissionsgroup_data as $get_permissionsgroup) {
					$data_permissions = $this->Dashboard_model->getpermissions($get_permissionsgroup->idpermissions_group, $user_le);
					if ($data_permissions->num_rows() > 0) {
				?>
						<?php
						if ($get_permissionsgroup->permissions_groupname == 'Dashboard') {
						?>
							<li class="
            				<?php

										$dtget_method = $this->Dashboard_model->getmethod_permission($get_permissionsgroup->idpermissions_group, $tk_m, $tk_c);
										$get_method = '';
										$get_class 	= '';
										$get_group 	= '';
										if ($dtget_method != NULL) {
											$get_method = $dtget_method->code_method;
											$get_class 	= $dtget_method->code_class;
											$get_group 	= $dtget_method->idpermissions_group;
										}

										if ($tk_c == $get_class && $tk_m == $get_method && $get_permissionsgroup->idpermissions_group == $get_group) {
											echo 'active';
										} else {
											echo '';
										}
										?>
				          ">
								<a href="<?php echo base_url(); ?>adminx" class="waves-effect waves-dark">
									<span class="pcoded-micon">
										<i class="<?php echo $get_permissionsgroup->display_icon; ?>"></i>
										<b>IC</b>
									</span>
									<span class="pcoded-mtext"><?php echo $get_permissionsgroup->permissions_groupname; ?></span>
								</a>
							</li>
						<?php
						} else {
						?>
							<li class="pcoded-hasmenu 
				            	<?php
											$dtget_method = $this->Dashboard_model->getmethod_permission($get_permissionsgroup->idpermissions_group, $tk_m, $tk_c);
											$get_method = '';
											$get_class 	= '';
											$get_group 	= '';
											if ($dtget_method != NULL) {
												$get_method = $dtget_method->code_method;
												$get_class 	= $dtget_method->code_class;
												$get_group 	= $dtget_method->idpermissions_group;
											}

											if ($tk_c == $get_class && $tk_m == $get_method && $get_permissionsgroup->idpermissions_group == $get_group) {
												echo 'active pcoded-trigger';
											} else {
												echo '';
											}
											?>">
								<a href="#" class="waves-effect waves-dark">
									<span class="pcoded-micon">
										<i class="<?php echo $get_permissionsgroup->display_icon; ?>"></i>
									</span>
									<span class="pcoded-mtext">
										<?php echo $get_permissionsgroup->permissions_groupname; ?>
									</span>
								</a>
								<ul class="pcoded-submenu" data-submenu-title="<?php echo $get_permissionsgroup->permissions_groupname; ?>" 
                  <?php
                    if ($tk_c == $get_class && $tk_m == $get_method  && $get_permissionsgroup->idpermissions_group == $get_group) {
                    ?> <?php echo 'style="display: block;"';
                    } else {
                      echo 'style="display: none;"';
                    }
									?>>
									<?php
									$get_permissions_data = $this->Dashboard_model->getpermissions($get_permissionsgroup->idpermissions_group, $user_le);
									foreach ($get_permissions_data->result() as $get_permissions) {
									?>
										<li class="<?php if ($tk_m == $get_permissions->code_method && $tk_c == $get_permissions->code_class && $get_permissionsgroup->idpermissions_group == $get_permissions->idpermissions_group) { ?>  <?php echo 'active';
																																																																																																												} ?>">
											<a href="<?= base_url() . $get_permissions->code_class . '/' . $get_permissions->code_url; ?>" class="waves-effect waves-dark">
												<span class="pcoded-mtext"><?php echo $get_permissions->display_name; ?></span>
											</a>
										</li>
									<?php
									}
									?>
								</ul>
							</li>
					<?php
						}
					}
					?>
				<?php
				}
				?>
			</ul>
		</div>
	</div>
</nav>