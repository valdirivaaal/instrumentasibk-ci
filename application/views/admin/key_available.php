<div class="row">
	<div class="col-md-12">
		<div class="panel-body bio-graph-info row">
			<h1 class="font-weight-bold col-md-9"> Daftar Event Key <?= $status ?></h1>
			<?php if ($state == 'available') { ?>
				<a href="<?= base_url('admin/keygen'); ?>" class="btn btn-primary col-md-3 mb-3">Generate Event Key</a>
			<?php } ?>
		</div>
		<?php
		if ($this->session->flashdata('success_key')) {
		?>
			<div class="alert alert-success" role="alert">
				<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
				<strong>Sukses!</strong> Event Key berhasil dibuat.
				<ul>
					<?php foreach ($this->session->userdata('success_key') as $val) { ?>
						<li><?= $val ?></li>
					<?php } ?>
				</ul>
			</div>
		<?php
		}
		?>
		<div class="card">
			<div class="card-header">
				Event Key Guru BK
			</div>
			<div class="card-body">
				<?php
				if ($this->session->flashdata('success')) {
				?>
					<div class="alert alert-success" role="alert">
						<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
						<strong>Sukses!</strong> Data <?= $this->session->userdata('success') ?> berhasil disimpan.
					</div>
				<?php
				}
				?>
				<div class="adv-table">
					<table class="display table table-bordered table-striped dynamic-table">
						<thead>
							<tr>
								<th>No</th>
								<th>Event Key</th>
								<th>Pemilik</th>
								<th>Masa Berlaku Hingga</th>
								<?php if ($state == 'available') { ?>
									<th>Action</th>
								<?php } else { ?>
									<th>Sisa Hari</th>
									<th>Status</th>
								<?php } ?>
							</tr>
						</thead>
						<tbody>
							<?php
							$i =  1;
							foreach ($get_key_guru as $key => $value) {
								// $get_ticket = $this->Main_model->join('ticket', '*', array(array('table' => 'event_key', 'parameter' => 'event_key.id=ticket.event_key'), array('table' => 'user_info', 'parameter' => 'ticket.user_id=user_info.user_id')), array('ticket.event_key' => $value['id'], 'ticket.tgl_kadaluarsa >=' => date('Y-m-d')));
								$get_ticket = $this->db->query("SELECT * FROM `event_key` LEFT JOIN ticket ON event_key.id = ticket.event_key JOIN user_info ON ticket.user_id = user_info.user_id WHERE ticket.event_key = " . $value['id'] . "")->result_array();

								if (!empty($get_ticket)) {
									$day_remaining = ceil((strtotime($get_ticket[0]['tgl_kadaluarsa']) - time()) / (60 * 60 * 24));
								} else {
									$day_remaining = 0;
								}
							?>
								<tr class="gradeX">
									<td><?= $i++ ?></td>
									<td><?= $value['event_key'] ?></td>
									<?php if (!empty($get_ticket[0]['nama_lengkap'])) { ?>
										<td><?= $get_ticket[0]['nama_lengkap'] ?></td>
									<?php } else { ?>
										<td>-</td>
									<?php } ?>
									<?php if (!empty($get_ticket[0]['tgl_kadaluarsa'])) { ?>
										<td><?= $get_ticket[0]['tgl_kadaluarsa'] ?></td>
									<?php } else { ?>
										<td><?= $value['masa_berlaku'] ?> Hari</td>
									<?php } ?>
									<?php if ($state == 'available') { ?>
										<td>
											<a href="<?= base_url('admin/key/' . $value['event_key']) ?>" class="btn btn-info">Edit</a>
											<button onclick="deletealert(this)" data-id="<?= $value['id'] ?>" class="btn btn-danger delete-alert<?= $value['id'] ?>">Hapus</button>
										</td>
									<?php } else { ?>
										<td><?= $day_remaining ?> Hari</td>
										<td><?= $get_ticket[0]['tgl_kadaluarsa'] <= date('Y-m-d') ? 'Expired' : 'Active' ?></td>
									<?php } ?>
								</tr>
							<?php
							}
							?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="card">
			<div class="card-header">
				Event Key Konselor
			</div>
			<div class="card-body">
				<?php
				if ($this->session->flashdata('success')) {
				?>
					<div class="alert alert-success" role="alert">
						<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
						<strong>Sukses!</strong> Data <?= $this->session->userdata('success') ?> berhasil disimpan.
					</div>
				<?php
				}
				?>
				<div class="adv-table">
					<table class="display table table-bordered table-striped dynamic-table">
						<thead>
							<tr>
								<th>No</th>
								<th>Event Key</th>
								<th>Pemilik</th>
								<th>Masa Berlaku Hingga</th>
								<?php if ($state == 'available') { ?>
									<th>Action</th>
								<?php } else { ?>
									<th>Sisa Hari</th>
									<th>Status</th>
								<?php } ?>
							</tr>
						</thead>
						<tbody>
							<?php
							$i =  1;
							foreach ($get_key_konselor as $key => $value) {
								// $get_ticket = $this->Main_model->join('ticket', '*', array(array('table' => 'event_key', 'parameter' => 'event_key.id=ticket.event_key'), array('table' => 'user_info', 'parameter' => 'ticket.user_id=user_info.user_id')), array('ticket.event_key' => $value['id'], 'ticket.tgl_kadaluarsa >=' => date('Y-m-d')));

								$get_ticket = $this->db->query("SELECT * FROM `event_key` LEFT JOIN ticket ON event_key.id = ticket.event_key JOIN user_info ON ticket.user_id = user_info.user_id WHERE ticket.event_key = " . $value['id'] . "")->result_array();

								if (!empty($get_ticket)) {
									$day_remaining = ceil((strtotime($get_ticket[0]['tgl_kadaluarsa']) - time()) / (60 * 60 * 24));
								} else {
									$day_remaining = 0;
								}
							?>
								<tr class="gradeX">
									<td><?= $i++ ?></td>
									<td><?= $value['event_key'] ?></td>
									<?php if (!empty($get_ticket[0]['tgl_kadaluarsa'])) { ?>
										<td><?= $get_ticket[0]['nama_lengkap'] ?></td>
									<?php } else { ?>
										<td>-</td>
									<?php } ?>
									<?php if (!empty($get_ticket[0]['tgl_kadaluarsa'])) { ?>
										<td><?= $get_ticket[0]['tgl_kadaluarsa'] ?></td>
									<?php } else { ?>
										<td><?= $value['masa_berlaku'] ?> Hari</td>
									<?php } ?>
									<?php if ($state == 'available') { ?>
										<td>
											<a href="<?= base_url('admin/key/' . $value['event_key']) ?>" class="btn btn-info">Edit</a>
											<button onclick="deletealert(this)" data-id="<?= $value['id'] ?>" class="btn btn-danger delete-alert<?= $value['id'] ?>">Hapus</button>
										</td>
									<?php } else { ?>
										<td><?= $day_remaining ?> Hari</td>
										<td><?= $get_ticket[0]['tgl_kadaluarsa'] <= date('Y-m-d') ? 'Expired' : 'Active' ?></td>
									<?php } ?>
								</tr>
							<?php
							}
							?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="card">
			<div class="card-header">
				Event Key DCM
			</div>
			<div class="card-body">
				<?php
				if ($this->session->flashdata('success')) {
				?>
					<div class="alert alert-success" role="alert">
						<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
						<strong>Sukses!</strong> Data <?= $this->session->userdata('success') ?> berhasil disimpan.
					</div>
				<?php
				}
				?>
				<div class="adv-table">
					<table class="display table table-bordered table-striped dynamic-table">
						<thead>
							<tr>
								<th>No</th>
								<th>Event Key</th>
								<th>Pemilik</th>
								<th>Masa Berlaku Hingga</th>
								<?php if ($state == 'available') { ?>
									<th>Action</th>
								<?php } else { ?>
									<th>Sisa Hari</th>
									<th>Status</th>
								<?php } ?>


							</tr>
						</thead>
						<tbody>
							<?php
							$i =  1;
							foreach ($get_key_dcm as $key => $value) {
								// $get_ticket = $this->Main_model->join('ticket', '*', array(array('table' => 'event_key', 'parameter' => 'event_key.id=ticket.event_key'), array('table' => 'user_info', 'parameter' => 'ticket.user_id=user_info.user_id')), array('ticket.event_key' => $value['id'], 'ticket.tgl_kadaluarsa >=' => date('Y-m-d')));

								$get_ticket = $this->db->query("SELECT * FROM `event_key` LEFT JOIN ticket ON event_key.id = ticket.event_key JOIN user_info ON ticket.user_id = user_info.user_id WHERE ticket.event_key = " . $value['id'] . "")->result_array();

								if (!empty($get_ticket)) {
									$day_remaining = ceil((strtotime($get_ticket[0]['tgl_kadaluarsa']) - time()) / (60 * 60 * 24));
								} else {
									$day_remaining = 0;
								}
							?>
								<tr class="gradeX">
									<td><?= $i++ ?></td>
									<td><?= $value['event_key'] ?></td>
									<?php if (!empty($get_ticket[0]['tgl_kadaluarsa'])) { ?>
										<td><?= $get_ticket[0]['nama_lengkap'] ?></td>
									<?php } else { ?>
										<td>-</td>
									<?php } ?>
									<?php if (!empty($get_ticket[0]['tgl_kadaluarsa'])) { ?>
										<td><?= $get_ticket[0]['tgl_kadaluarsa'] ?></td>
									<?php } else { ?>
										<td><?= $value['masa_berlaku'] ?> Hari</td>
									<?php } ?>
									<?php if ($state == 'available') { ?>
										<td>
											<a href="<?= base_url('admin/key/' . $value['event_key']) ?>" class="btn btn-info">Edit</a>
											<button onclick="deletealert(this)" data-id="<?= $value['id'] ?>" class="btn btn-danger delete-alert<?= $value['id'] ?>">Hapus</button>
										</td>
									<?php } else { ?>
										<td><?= $day_remaining ?> Hari</td>
										<td><?= empty($get_ticket[0]) || $get_ticket[0]['tgl_kadaluarsa'] <= date('Y-m-d') ? 'Expired' : 'Active' ?></td>
									<?php } ?>
								</tr>
							<?php
							}
							?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	function deletealert(data) {
		var id = data.getAttribute("data-id");
		swal.fire({
			title: 'Apakah kamu yakin?',
			text: "Kamu tidak bisa mengembalikan data setelah terhapus!",
			type: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes, delete it!'
		}).then((result) => {
			if (result.value) {
				$.ajax({
					type: "POST",
					url: "<?= base_url('hapus/eventkey/') ?>" + id,
					cache: false,
					success: function(response) {
						swal.fire({
							title: 'Terhapus!',
							text: 'Tunggu beberapa detik atau klik ok.',
							type: 'success',
							timer: 3000
						}, function() {
							window.location.reload();
						});
						setTimeout(function() {
							window.location.reload();
						}, 3000);
					}
				})
			}
		})
	}
</script>
