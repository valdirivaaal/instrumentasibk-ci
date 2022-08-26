<div class="row">
	<div class="col-md-12">
		<div class="panel-body bio-graph-info">
			<h1 class="font-weight-bold">Pengumuman Management</h1>
		</div>
		<div class="card">
			<div class="card-header">
				Pengumuman Management
			</div>
			<div class="card-body">
				<?php
				if ($this->session->flashdata('success')) {
				?>
					<div class="alert alert-success" role="alert">
						<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
						<strong>Sukses!</strong> Data <?= $this->session->userdata('success') ?> berhasil diubah.
						<?php unset($_SESSION['success']) ?>
					</div>
				<?php
				}
				?>
				<form action="<?= base_url('admin/pengumuman_action') ?>" method="POST">
					<div class="mb-3">
						<label for="" class="form-label">Pengumuman</label>
						<textarea class="form-control" name="pesan" rows="5"><?= $get_data[0]['pesan'] ?></textarea>
					</div>
					<div class="mb-3">
						<label for="" class="form-label">Tanggal</label>
						<input type="date" class="form-control" name="tanggal" value="<?= $get_data[0]['tanggal'] ?>">
					</div>
					<div class="mb-3">
						<label for="" class="form-label">Status</label>
						<select class="form-control" name="status">
							<option value="show" <?= $get_data[0]['status'] == 'show' ? 'selected' : '' ?>>Show</option>
							<option value="hide" <?= $get_data[0]['status'] == 'hide' ? 'selected' : '' ?>>Hide</option>
						</select>
					</div>
					<button type="submit" class="btn btn-primary container-fluid">Edit User</button>
				</form>
			</div>

		</div>
	</div>
</div>
