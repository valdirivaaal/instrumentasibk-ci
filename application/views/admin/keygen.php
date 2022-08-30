<div class="row">
	<div class="col-md-12">
		<div class="panel-body bio-graph-info">
			<h1 class="font-weight-bold">Generate new Event Key</h1>
		</div>
		<div class="card">
			<div class="card-header">
				Generate Event Key
			</div>
			<div class="card-body">
				<?php
				if ($this->session->flashdata('error')) {
				?>
					<div class="alert alert-danger" role="alert">
						<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
						<strong>Gagal!</strong> <?= $this->session->userdata('error') ?>
					</div>
				<?php
				}
				?>
				<form action="<?= base_url('admin/keygenProses') ?>" method="POST">
					<div class="mb-3">
						<label for="" class="form-label">Jumlah</label>
						<input type="number" class="form-control" name="jumlah" placeholder="100" required>
					</div>
					<div class="mb-3">
						<label for="" class="form-label">Panjang Event Key</label>
						<input type="number" class="form-control" name="panjang" placeholder="Default = 6">
					</div>
					<div class="mb-3">
						<label for="" class="form-label">Masa Berlaku (dalam hari)</label>
						<input type="number" class="form-control" name="masa_berlaku" placeholder="365">
					</div>
					<div class="mb-3">
						<label for="" class="form-label">Tipe</label>
						<select class="form-control" name="tipe">
							<option value="1">Guru BK</option>
							<option value="2">Konselor</option>
							<option value="3">DCM</option>
						</select>
					</div>
					<div class="mb-3">
						<label for="" class="form-label">Key Type</label>
						<select class="form-control" name="key_type">
							<option value="single">Single Key</option>
							<option value="multi">Multi Key</option>
						</select>
					</div>
					<button type="submit" class="btn btn-primary container-fluid">Generate Key</button>
				</form>
			</div>
		</div>
	</div>
</div>
