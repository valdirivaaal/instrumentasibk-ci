<!DOCTYPE html>
<html>

<head>
	<title>Export data Narasumber</title>
</head>

<body>
	<style type="text/css">
		body {
			font-family: sans-serif;
		}

		table {
			margin: 20px auto;
			border-collapse: collapse;
		}

		table th,
		table td {
			border: 1px solid #3c3c3c;
			padding: 3px 8px;

		}

		a {
			background: blue;
			color: #fff;
			padding: 8px 10px;
			text-decoration: none;
			border-radius: 2px;
		}
	</style>

	<?php
	header("Content-type: application/vnd-ms-excel");
	if (!empty($_GET['tahun_ajaran']) && empty($_GET['sekolah'])) {
		header("Content-Disposition: attachment; filename=Data Narasumber Tahun Ajaran " . $_GET['tahun_ajaran'] . ".xls");
	} else if (!empty($_GET['tahun_ajaran']) && !empty($_GET['sekolah'])) {
		header("Content-Disposition: attachment; filename=Data Narasumber Tahun Ajaran " . $_GET['tahun_ajaran'] . " Sekolah " . $_GET['sekolah'] . ".xls");
	} else {
		header("Content-Disposition: attachment; filename=Data Narasumber Instrumentasi BK.xls");
	}
	?>

	<center>
		<h1>Data Narasumber Instrumentasi BK</h1>
	</center>

	<table border="1">
		<thead style="font-weight: bold;">
			<tr>
				<th>No</th>
				<th>Nama</th>
				<th>JK</th>
				<th>Tanggal Lahir</th>
				<th>Email</th>
				<th>Nomor Whatsapp</th>
				<th>Instansi</th>
				<th>Kelas</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$i = 1;
			foreach ($datas as $val) {
				if ($val['jk'] == 'Laki-laki' || $val['jk'] == 'L') {
					$jeniskelamin = 'L';
				} else {
					$jeniskelamin = 'P';
				}
				$get_kelas = $this->db->query("SELECT DISTINCT * FROM kelas JOIN user_info ON kelas.user_id = user_info.user_id WHERE kelas.id = " . $val['id_kelas'])->result_array();

				if (!empty($get_kelas)) {
					$sekolah = $get_kelas[0]['instansi'];
					$kelas = $get_kelas[0]['kelas'];
				} else {
					$sekolah = 'No Data';
					$kelas = 'No Data';
				} ?>
				<tr>
					<td><?= $i ?></td>
					<td><?= $val['nama'] ?></td>
					<td><?= $jeniskelamin ?></td>
					<td><?= $val['tgl_lahir'] ?></td>
					<td><?= $val['email'] ?></td>
					<td><?= $val['no_telepon'] ?></td>
					<td><?= $sekolah ?></td>
					<td><?= $kelas ?></td>
				</tr>
			<?php
				$i++;
			} ?>
		</tbody>

	</table>
</body>

</html>
