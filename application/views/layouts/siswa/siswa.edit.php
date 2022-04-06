<div class="row">
	<div class="col-md-12">
		<div class="panel-body bio-graph-info">
			<h1 class="font-weight-bold">
				Edit Data Siswa
			</h1>
		</div>
		<div class="card">
			<div class="card-header">
				<?php
					if ($data) {
						echo $data[0]['nama'] !== '' ? $data[0]['nama'] : 'Data Siswa';

						// Set jk value
						$jk = [
							"L" => $data[0]['jk'] == "L" ? 'checked' : '',
							"P" => $data[0]['jk'] == "P" ? 'checked' : ''
						];

					}
				?>
			</div>
			<div class="card-body">
				<form action="<?php echo base_url('siswa/saveEdit'); ?>" method="post" class="form-horizontal">
					<input type="hidden" name="id" value="<?php echo $data[0]['id'] !== '' ? $data[0]['id'] : ''; ?>">
					<div class="form-group">
						<label for="" class="col-lg-12 control-label">NIS</label>
						<div class="col-md-12">
							<div class="form-group">
								<input type="text" name="nis" id="nis" value="<?php echo $data[0]['nis']; ?>" class="form-control">
							</div>
						</div>
						<label for="" class="col-lg-12 control-label">Nama</label>
						<div class="col-md-12">
							<div class="form-group">
								<input type="text" name="nama" id="nama" value="<?php echo $data[0]['nama']; ?>" class="form-control">
							</div>
						</div>
						<label for="" class="col-lg-12 control-label">Jenis Kelamin</label>
						<div class="col-md-12">
							<div class="form-group">
								<input type="radio" name="jk" id="" <?php echo $jk['L']; ?> value="L"> Laki-Laki
								<input type="radio" name="jk" id="" <?php echo $jk['P']; ?> value="P"> Perempuan
							</div>
						</div>
						<label for="" class="col-lg-12 control-label">Tanggal Lahir</label>
						<div class="col-md-12">
							<div class="form-group">
								<input type="text" placeholder="dd/mm/yyyy" data-mask="99/99/9999" class="form-control" name="tgl_lahir" value="<?php echo ($data[0]['tgl_lahir'] != '0000/00/00') ? date('d/m/Y', strtotime($data[0]['tgl_lahir'])) : ''; ?>">
              					<span class="help-inline">Format : dd/mm/yyyy</span>
							</div>
						</div>
						<label for="" class="col-lg-12 control-label">Tempat Lahir</label>
						<div class="col-md-12">
							<div class="form-group">
								<input type="text" name="tempat_lahir" id="tempat_lahir" value="<?php echo $data[0]['tempat_lahir']; ?>" class="form-control">
							</div>
						</div>
						<label for="" class="col-lg-12 control-label">Alamat</label>
						<div class="col-md-12">
							<div class="form-group">
								<textarea name="alamat" id="" cols="30" rows="10" class="form-control"><?php echo $data[0]['alamat']; ?></textarea>
							</div>
						</div>
						<div class="col-md-12">
							<div class="form-group">
								<button type="submit" class="btn btn-success">Save</button>
								<button type="button" class="btn btn-default" onclick="history.back()">Cancel</button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
