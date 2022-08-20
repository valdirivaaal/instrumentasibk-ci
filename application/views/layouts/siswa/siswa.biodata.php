<div class="row mt-5">
	<div class="col-md-12">
		<div class="panel-body bio-graph-info">

		</div>
		<div class="card">
			<div class="card-header text-center">
				<h1 class="font-weight-bold">Biodata Siswa</h1>
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
					} elseif ($this->session->flashdata('error')) {
						?>
							<div class="alert alert-danger" role="alert">
								<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
								<strong>Gagal!</strong> Data <?= $this->session->userdata('error') ?> gagal disimpan.
							</div>
						<?php
					}
				?>
				<form action="<?php echo base_url('siswa/saveBiodata'); ?>" class="form-horizontal" method="post" id="biodataForm">
					<div class="form-group">
						<label for="" class="col-lg-12 control-label">Kelas</label>
						<div class="col-lg-12 control-label">
							<select name="id_kelas" id="kelas" class="form-control" placeholder="Kelas" required>
								<option value="">Pilih Kelas</option>
								<?php 
									if ($kelas) {
										foreach($kelas as $row) {
											echo "<option value='" .$row['id']. "'>" .$row['kelas']. "</option>";
										}
									}
								?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-lg-12 control-label">NIS</label>
						<div class="col-lg-12 control-label">
							<select name="nis" id="nis" class="form-control">
								<option value="">Pilih Nis</option>
							</select>
						</div>
					</div>
					<div class="biodata d-none">
						<input type="hidden" name="id">
						<div class="form-group">
							<label for="nama" class="col-lg-12 control-label">Nama</label>
							<div class="col-lg-12 control-label">
								<input type="text" name="nama" id="" class="form-control" required>
							</div>
						</div>
						<div class="form-group">
							<label for="jk" class="col-lg-12 control-label">Jenis Kelamin</label>
							<div class="col-lg-12 control-label">
								<input type="radio" name="jk" id="laki" value="L"> Laki - Laki
								<input type="radio" name="jk" id="perempuan" value="P"> Perempuan
							</div>
						</div>
						<div class="form-group">
							<label for="tgl_lahir" class="col-lg-12 control-label">Tanggal Lahir</label>
							<div class="col-lg-12 control-label">
								<input type="text" placeholder="dd/mm/yyyy" data-mask="99/99/9999" class="form-control" name="tgl_lahir" required>
              					<span class="help-inline">Format : dd/mm/yyyy --- Contoh : 17-08-2000</span>
							</div>
						</div>
						<div class="form-group">
							<label for="tempat_lahir" class="col-lg-12 control-label">Tempat Lahir</label>
							<div class="col-lg-12 control-label">
								<input type="text" name="tempat_lahir" id="" class="form-control" required>
							</div>
						</div>
						<div class="form-group">
							<label for="email" class="col-lg-12 control-label">Email</label>
							<div class="col-lg-12 control-label">
								<input type="text" name="email" id="" class="form-control" required>
							</div>
						</div>
						<div class="form-group">
							<label for="no_telepon" class="col-lg-12 control-label">No. Telepon</label>
							<div class="col-lg-12 control-label">
								<input type="text" name="no_telepon" id="" class="form-control" required>
							</div>
						</div>
						<div class="form-group">
							<label for="alamat" class="col-lg-12 control-label">Alamat</label>
							<div class="col-lg-12 control-label">
								<textarea name="alamat" id="" cols="30" rows="10" class="form-control" required></textarea>
							</div>
						</div>
						<div class="col-md-12">
							<div class="form-group">
								<button type="submit" class="btn btn-success">Save</button>
								<!-- <button type="button" class="btn btn-default" onclick="history.back()">Cancel</button> -->
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<script>
	$(document).ready(function() {
		$("#kelas").on("change", function(){
			$.ajax({
				type: "post",
				url: "<?php echo base_url('siswa/getNis'); ?>",
				data: {id: $(this).val()},
				success: function(res) {
					if (res.success) {
						$.each(res.nis, function(i, v) {
							$("#nis").append(
								$('<option></option>').val(v.nis).html(v.nis)
							)
						})
					}
				},
				error: function() {

				}
			})
		})

		$("#nis").on("change", function(){
			$.ajax({
				type: 'post',
				url: "<?php echo base_url('siswa/getSiswa'); ?>",
				data: {nis: $(this).val()},
				success: function(res) {
					if (res.success) {
						$(".biodata").removeClass('d-none')

						// Retrieve all student's information to each elements
						$('input[name="id"]').val(res.siswa.id)
						$('input[name="nama"]').val(res.siswa.nama)
						$('input[name="tgl_lahir"]').val(dateFormat(res.siswa.tgl_lahir, 'dd-MM-yyyy'))
						$('input[name="tempat_lahir"]').val(res.siswa.tempat_lahir)
						$('input[name="email"]').val(res.siswa.email)
						$('input[name="no_telepon"]').val(res.siswa.no_telepon)
						$('textarea[name="alamat"]').val(res.siswa.alamat)

						if (res.siswa.jk == 'P') {
							$('#perempuan').prop('checked', true)
						} else {
							$('#laki').prop('checked', true)
						}
					}
				},
				error: function() {

				}
			})
		})

		function dateFormat(inputDate, format) {
			//parse the input date
			const date = new Date(inputDate);

			//extract the parts of the date
			const day = date.getDate();
			const month = date.getMonth() + 1;
			const year = date.getFullYear();    

			//replace the month
			format = format.replace("MM", month.toString().padStart(2,"0"));        

			//replace the year
			if (format.indexOf("yyyy") > -1) {
				format = format.replace("yyyy", year.toString());
			} else if (format.indexOf("yy") > -1) {
				format = format.replace("yy", year.toString().substr(2,2));
			}

			//replace the day
			format = format.replace("dd", day.toString().padStart(2,"0"));

			return format;
		}
	})
</script>
