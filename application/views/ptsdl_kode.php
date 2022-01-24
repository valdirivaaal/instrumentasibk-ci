<div class="row">
	<div class="col-md-12">
		<div class="panel-body bio-graph-info">
			<h1 class="font-weight-bold">Kode Unik AUM Umum</h1>
		</div>
		<div class="alert alert-info" role="alert">
			<strong>Kode ini akan menjadi link untuk disebarkan ke anak-anak Bapak/Ibu. Bapak/Ibu bisa menggunakan kode yang kami sediakan atau mengubah kode ini sesuai dengan keinginan Bapak/Ibu.</a>
			</div>
			<div class="card">
				<div class="card-header">
					Masukkan Kode
				</div>
				<div class="card-body">
					<?php
					if ($this->session->flashdata('error')) {
						?>
						<div class="alert alert-danger" role="alert">
							<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
							<strong>Gagal!</strong> <?= $this->session->flashdata('error') ?>.
						</div>
						<?php
					}
					?>
					<form action="<?= base_url('ptsdl/kode_save') ?>" method="post">
						<input type="hidden" value="<?= $get_instrumen[0]['id'] ?>" name="instrumen_id">
						<input type="hidden" value="<?= $this->session->userdata('id') ?>" name="user_id">
						<?php
						if (isset($jenjang)) {
							?>
							<input type="hidden" value="<?= $jenjang ?>" name="jenjang">
							<?php
						}
						?>
						<div class="form-group">
							<label  class="col-lg-12 control-label">URL</label>
							<div class="col-md-12">
								<div class="input-group mb-3">
									<div class="input-group-prepend">
										<span class="input-group-text" id="basic-addon3">https://instrumentasibk.com/ptsdl/</span>
									</div>
									<input type="text" class="form-control" name="kode_singkat" id="basic-url" aria-describedby="basic-addon3" placeholder="Masukkan kode singkat anda disini" value="<?= (isset($get_aum[0]['kode_singkat'])) ? $get_aum[0]['kode_singkat'] : random_string('alnum', 5) ?>">
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-lg-offset-2 col-lg-10">
								<button type="submit" class="btn btn-success">Simpan</button>
								<button type="button" class="btn btn-default">Batal</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>