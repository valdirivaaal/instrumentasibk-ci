<div class="row">
	<div class="col-md-12">
		<div class="panel-body bio-graph-info">
			<h1 class="font-weight-bold">User Management</h1>
		</div>
		<div class="card">
			<div class="card-header">
				User Management
			</div>
			<div class="card-body">
				<form action="<?= base_url('admin/user/edit/' . $get_user[0]['user_id']) ?>" enctype="multipart/form-data" method="POST">
					<div class="mb-3">
						<label for="" class="form-label">Nama</label>
						<input type="text" class="form-control" name="nama_lengkap" placeholder="Nama" value="<?= $get_user[0]['nama_narasumber'] ?>">
					</div>
					<div class="mb-3">
						<label for="" class="form-label">Email</label>
						<input type="text" class="form-control" name="email" placeholder="Email" value="<?= $get_user[0]['email'] ?>" disabled>
					</div>
					<div class="mb-3">
						<label for="" class="form-label">Password</label>
						<input type="text" class="form-control" name="password" placeholder="Password" value="<?= $get_user[0]['password'] ?>" disabled>
					</div>
					<div class="mb-3">
						<label for="" class="form-label">Level</label>
						<select class="form-control" name="level">
							<option value="member" <?= $get_user[0]['jenis_kelamin'] == 'member' ? 'selected' : '' ?>>Member</option>
							<option value="admin" <?= $get_user[0]['jenis_kelamin'] == 'admin' ? 'selected' : '' ?>>Admin</option>
						</select>
					</div>
					<div class="mb-3">
						<label for="" class="form-label">Jenis Kelamin</label>
						<select class="form-control" name="jenis_kelamin">
							<option value="Laki-laki" <?= $get_user[0]['jenis_kelamin'] == 'Laki-laki' ? 'selected' : '' ?>>Laki - Laki</option>
							<option value="Perempuan" <?= $get_user[0]['jenis_kelamin'] == 'Perepmuan' ? 'selected' : '' ?>>Perempuan</option>
						</select>
					</div>
					<div class="mb-3">
						<label for="" class="form-label">No Whatsapp</label>
						<input type="number" class="form-control" name="no_whatsapp" placeholder="No Whatsapp" value="<?= $get_user[0]['no_whatsapp'] ?>">
					</div>
					<div class="mb-3">
						<label for="" class="form-label">Alamat Rumah</label>
						<input type="text" class="form-control" name="alamat_rumah" placeholder="Alamat Rumah" value="<?= $get_user[0]['alamat_rumah'] ?>">
					</div>
					<div class="mb-3">
						<label for="" class="form-label">Instansi</label>
						<input type="text" class="form-control" name="instansi" placeholder="Instansi" value="<?= $get_user[0]['instansi'] ?>">
					</div>
					<div class="mb-3">
						<label for="" class="form-label">Alamat Instansi</label>
						<input type="text" class="form-control" name="alamat_instansi" placeholder="Alamat Instansi" value="<?= $get_user[0]['alamat_instansi'] ?>">
					</div>
					<div class="mb-3">
						<label for="" class="form-label">Telp Instansi</label>
						<input type="text" class="form-control" name="telp_instansi" placeholder="Telp Instansi" value="<?= $get_user[0]['telp_instansi'] ?>">
					</div>
					<div class="mb-3">
						<label for="" class="form-label">Status</label>
						<select class="form-control" name="status">
							<option value="Guru BK" <?= $get_user[0]['status'] == 'Guru BK' ? 'selected' : '' ?>>Guru BK</option>
							<option value="Konselor" <?= $get_user[0]['status'] == 'Konselor' ? 'selected' : '' ?>>Konselor</option>
						</select>
					</div>
					<div class="mb-3">
						<label for="" class="form-label">Jenjang</label>
						<select class="form-control" name="jenjang">
							<option value="SD" <?= $get_user[0]['jenjang'] == 'SD' ? 'selected' : '' ?>>SD</option>
							<option value="SMP" <?= $get_user[0]['jenjang'] == 'SMP' ? 'selected' : '' ?>>SMP</option>
							<option value="SMA" <?= $get_user[0]['jenjang'] == 'SMA' ? 'selected' : '' ?>>SMA</option>
							<option value="Konselor" <?= $get_user[0]['jenjang'] == 'Konselor' ? 'selected' : '' ?>>Konselor</option>

						</select>
					</div>


					<button type="submit" class="btn btn-primary container-fluid">Edit User</button>
				</form>
			</div>
		</div>
	</div>
</div>
