<div class="row">
	<div class="col-md-12">
		<div class="panel-body bio-graph-info">
			<h1 class="font-weight-bold">Detail Sosiometri</h1>
		</div>
		<nav>
			<div class="nav nav-tabs" id="nav-tab" role="tablist">
				<a class="nav-item nav-link active" id="nav-respon-tab" data-toggle="tab" href="#nav-respon" role="tab" aria-controls="nav-respon" aria-selected="true">Respon</a>
				<a class="nav-item nav-link" id="nav-tabulasi-arah-tab" data-toggle="tab" href="#nav-tabulasi-arah" role="tab" aria-controls="nav-tabulasi-arah" aria-selected="false">Tabulasi Arah Pilihan</a>
				<a class="nav-item nav-link" id="nav-index-pilihan-tab" data-toggle="tab" href="#nav-index-pilihan" role="tab" aria-controls="nav-index-pilihan" aria-selected="false">Index Pilihan</a>
				<a class="nav-item nav-link" id="nav-sociogram-tab" data-toggle="tab" href="#nav-sociogram" role="tab" aria-controls="nav-sociogram" aria-selected="false">Sosiogram</a>
			</div>
		</nav>
		<div class="tab-content" id="nav-tabContent">
			<div class="tab-pane fade show active" id="nav-respon" role="tabpanel" aria-labelledby="nav-respon-tab">
				<div class="card">
					<div class="card-body">
						<h4><?php echo $data['kelas_detail']['kelas'] ?? ''; ?></h4>
						<p>Jumlah Siswa : <?php echo $data['responded']; ?>/<?php echo $data['kelas_total']; ?></p>
						<table class="table">
							<thead class="thead-dark">
								<tr>
									<th class="text-center" scope="col">NIS</th>
									<th class="text-center" scope="col">TANGGAL PENGISIAN</th>
									<th class="text-center" scope="col">NAMA SISWA</th>
									<th class="text-center" scope="col">JENIS KELAMIN</th>
									<th class="text-center" scope="col">PILIHAN 1</th>
								</tr>
							</thead>
							<tbody>
								<?php if ($data['details']) { ?>
									<?php foreach ($data['details'] as $row) { ?>
										<tr>
											<td class="text-center"><?php echo $row['nis']; ?></td>
											<td class="text-center"><?php echo $row['created_at']; ?></td>
											<td class="text-center"><?php echo $row['nama']; ?></td>
											<td class="text-center"><?php echo $row['jk'] == 'P' ? 'Perempuan' : 'Laki - Laki'; ?></td>
											<td class="text-center">
												<?php echo $row['pilihan'][0]['nis']; ?>
											</td>
										</tr>
									<?php } ?>
								<?php } else { ?>
									<tr>
										<th class="text-center" colspan="5"><i>Belum ada siswa yang mengisi respon.</i></th>
									</tr>
								<?php } ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="tab-pane fade" id="nav-tabulasi-arah" role="tabpanel" aria-labelledby="nav-tabulasi-arah-tab">
				<div class="card scrollable">
					<div class="card-body">
						<h4><?php echo $data['kelas_detail']['kelas'] ?? ''; ?></h4>
						<div class="row">
							<label for="" class="col-sm-2 font-weight-bold">Jumlah Data : <?php echo $data['tabulasi']['studentTotal']; ?></label>
							<label for="" class="col-sm-3 font-weight-bold">Keterangan </label>
						</div>
						<div class="row">
							<label for="" class="col-sm-2 font-weight-bold">Laki-Laki : <?php echo $data['tabulasi']['boys']; ?></label>
							<div class="col-sm-3"><strong>1</strong> : dipilih sebagai pilihan ke-n</div>
						</div>
						<div class="row">
							<label for="" class="col-sm-2 font-weight-bold">Perempuan : <?php echo $data['tabulasi']['girls']; ?></label>
							<div class="col-sm-3"><strong>x</strong> : ditolak</div>
						</div>
						<div class="row">
							<table class="table">
								<thead class="thead-dark">
									<tr>
										<th class="align-middle text-center" rowspan="2">NIS</th>
										<th class="align-middle text-center" rowspan="2">NAMA</th>
										<th class="text-center" colspan="<?php echo $data['tabulasi']['dataTotal']; ?>">Pemilih/Penolak</th>
										<th class="align-middle text-center" rowspan="2">Bobot Pemilih</th>
										<th class="align-middle text-center" rowspan="2">Bobot Penolak</th>
									</tr>
									<tr>
										<?php if ($data['tabulasi']['data']) { ?>
											<?php foreach ($data['tabulasi']['data'] as $row) { ?>
												<th class="text-center"><?php echo $row['nis']; ?></th>
											<?php } ?>
										<?php } ?>
									</tr>
								</thead>
								<tbody>
									<?php if ($data['tabulasi']['data']) { ?>
										<?php foreach ($data['tabulasi']['data'] as $row) { ?>
											<tr>
												<td class="text-center"><?php echo $row['nis']; ?></td>
												<td><?php echo $row['nama']; ?></td>
												<?php
												foreach ($data['tabulasi']['data'] as $indexOptions) {
													if ($row['pilihan']) {
														if (in_array($indexOptions['id'], $row['pilihan'])) {
															echo "<td class=\"text-center\">1</td>";
														} else {
															if ($row['pilihan_negatif'] !== '') {
																if ($indexOptions['id'] == $row['pilihan_negatif']) {
																	echo "<td class=\"text-center\">X</td>";
																} else {
																	echo "<td>&nbsp;</td>";
																}
															} else {
																echo "<td>&nbsp;</td>";
															}
														}
													} else {
														echo "<td>&nbsp;</td>";
													}
												}
												?>
												<td class="text-center"><?php echo $row['score_pemilih']; ?></td>
												<td class="text-center"><?php echo $row['score_penolak']; ?></td>
											</tr>
										<?php } ?>
									<?php } ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			<div class="tab-pane fade" id="nav-index-pilihan" role="tabpanel" aria-labelledby="nav-index-pilihan-tab">
				<div class="card">
					<div class="card-body">
						<h4><?php echo $data['kelas_detail']['kelas'] ?? ''; ?></h4>
						<div class="row">
							<table class="table">
								<thead class="thead-dark">
									<tr>
										<th>NIS</th>
										<th>NAMA</th>
										<th>NILAI</th>
									</tr>
								</thead>
								<tbody>
									<?php if ($data['tabulasi']['data']) { ?>
										<?php foreach ($data['tabulasi']['data'] as $row) { ?>
											<tr>
												<td><?php echo $row['nis']; ?></td>
												<td><?php echo $row['nama']; ?></td>
												<td><?php echo $row['score_pemilih'] . ' / ' . $data['tabulasi']['studentTotal'] . ' = ' . ($row['score_pemilih'] / $data['tabulasi']['studentTotal']); ?></td>
											</tr>
										<?php } ?>
									<?php } ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			<div class="tab-pane fade" id="nav-sociogram" role="tabpanel" aria-labelledby="nav-sociogram-tab">
				<div class="card">
					<div class="card-body">
						<div id="sociogram"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	// $(document).ready(function() {

	// })
	(function(H) {
		Highcharts.seriesType('sociogram', 'line', {
			dataLabels: {
				enabled: true,
				allowOverlap: true,
				align: "center",
				// y: 2,
				x: 5,
				// rotation: -45,
				style: {
					fontSize: "12px"
				},
				visibility: 'inherit',
				format: '{point.name}',
			}
		}, {
			// Use the drawGraph function to draw relational paths between the nodes
			drawGraph: function() {
				var series = this,
					chart = this.chart,
					relations = this.relations = this.relations || {};

				this.points.forEach(function(point) {
					console.log('Points', point)
					point.connections.forEach(function(connId) {

						var key = point.id + '-' + connId,
							connPoint = chart.get(connId);

						if (connPoint) {

							if (!relations[key]) {
								relations[key] = chart.renderer.path()
									.add(series.group);
							}

							relations[key].attr({
								d: [
									'M', point.plotX, point.plotY,
									'L', connPoint.plotX, connPoint.plotY
								],
								zIndex: 10,
								stroke: connId == point.rejection ? '#000' : series.color,
								'stroke-width': H.pick(series.options.lineWidth, 2),
								'marker-end': "url(#arrow-end)"
								//'marker-start': "url(#arrow-start)"
							});
						}

					});
				});
			}
		});

		H.wrap(H.Chart.prototype, 'getContainer', function(proceed) {
			proceed.apply(this);

			var chart = this,
				renderer = chart.renderer,
				defOptions = chart.options.defs || [],
				i = defOptions.length,
				def,
				marker;

			while (i--) {
				def = defOptions[i];
				marker = renderer.createElement('marker').attr({
					id: def.id,
					viewBox: "0 -5 10 20",
					refX: 16,
					refY: 6,
					markerWidth: 6,
					markerHeight: 6,
					orient: 'auto',
					fill: 'inherit'
				}).add(renderer.defs);
				renderer.createElement('path').attr({
					d: def.path,
					fill: 'black'
				}).add(marker);
			}
		});

		H.wrap(H.Series.prototype, 'drawGraph', function(proceed) {
			proceed.apply(this);

		});
	}(Highcharts));

	let seriesChart = []

	function getSociogramSeries() {
		let idKelas = "<?php echo $data['kelas_detail']['id'] ?>";
		let title = "<?php echo $data['kelas_detail']['kelas'] ?? ''; ?>"
		$.ajax({
			type: 'GET',
			url: "<?php echo base_url(); ?>sosiometri/getSociogramData/" + idKelas,
			success: function(res) {
				console.log('Ajax res', res)

				if (res) {
					if (res.success) {
						let temp = []
						res.data.forEach((item, index) => {
							let y = res.occurrences[item.id] !== undefined ? res.occurrences[item.id] + 1 : 1
							temp.push({
								id: item.id,
								name: item.id,
								// name: item.nis,
								connections: item.connections,
								rejection: item.pilihan_negatif ? item.pilihan_negatif : false,
								// y: item.pilihan.length ? (item.pilihan.length) : 0,
								y: y,
								color: item.jk == 'L' ? 'blue' : 'red'
							})
						})

						seriesChart = temp;

						console.log('Series', seriesChart)
						Highcharts.chart('sociogram', {

							chart: {
								height: '100%',
								polar: true,
								// width: 900,
							},

							legend: {
								enabled: true
							},

							// navigation: {
							// 	buttonOptions: {
							// 		verticalAlign: 'left',
							// 		y: -20,
							// 		x: 100
							// 	}
							// },

							defs: [{
								id: 'arrow-start',
								path: 'M 0 0 L 10 5 L 0 10 z',
								fill: 'gray'
							}, {
								id: 'arrow-end',
								path: 'M 0 0 L 10 5 L 0 10 z', //M 0 0 L 10 5 L 0 10 z
								fill: 'gray'
							}],

							title: {
								// text: 'Highcharts Sociogram Study'
								text: title + ' Sociogram'
							},

							xAxis: {
								visible: false,
							},

							yAxis: {
								labels: {
									enabled: false,
								},
								reversed: true,
								plotBands: [{
									from: 0,
									to: Infinity,
									color: 'rgba(0, 255, 96, 0.1)'
								}, {
									from: 1,
									to: Infinity,
									color: 'rgba(0, 255, 96, 0.1)'
								}, {
									from: 2,
									to: Infinity,
									color: 'rgba(0, 255, 96, 0.1)'
								}, {
									from: 3,
									to: Infinity,
									color: 'rgba(0, 255, 96, 0.1)'
								}, {
									from: 4,
									to: Infinity,
									color: 'rgba(0, 255, 96, 0.1)'
								}],
								gridLineColor: 'white'
							},

							series: [{
								marker: {
									enabled: true
								},
								data: seriesChart,
								// data: [{
								// 		id: 'MarcusID',
								// 		name: 'Marcus',
								// 		connections: [
								// 			'TomID',
								// 			'AnnID',
								// 			'JulieID',
								// 			'MartinID',
								// 			'JohnID'
								// 		],
								// 		rejection: 'JohnID',
								// 		y: 4,
								// 		color: 'red'
								// 	},
								// 	{
								// 		id: 'TomID',
								// 		name: 'Tom',
								// 		connections: [
								// 			'AnnID',
								// 			'JulieID'
								// 		],
								// 		rejection: false,
								// 		y: 2,
								// 		color: 'blue'
								// 	},
								// 	{
								// 		id: 'AnnID',
								// 		name: 'Ann',
								// 		connections: [
								// 			'TomID',
								// 			'MartinID'
								// 		],
								// 		rejection: false,
								// 		y: 2,
								// 		color: 'red'
								// 	},
								// 	{
								// 		id: 'JulieID',
								// 		name: 'Julie',
								// 		connections: [
								// 			'TomID',
								// 			'AnnID',
								// 			'MartinID'
								// 		],
								// 		rejection: false,
								// 		y: 3,
								// 		color: 'red'
								// 	},
								// 	{
								// 		id: 'MartinID',
								// 		name: 'Martin',
								// 		connections: [
								// 			'MarcusID'
								// 		],
								// 		rejection: false,
								// 		y: 1,
								// 		color: 'red'
								// 	},
								// 	{
								// 		id: 'JohnID',
								// 		name: 'John',
								// 		connections: [
								// 			'NoraID',
								// 			'MonicaID'
								// 		],
								// 		rejection: false,
								// 		color: 'red',
								// 		y: 2
								// 	}, {
								// 		id: 'MonicaID',
								// 		name: 'Monica',
								// 		connections: [
								// 			'NoraID'
								// 		],
								// 		rejection: false,
								// 		y: 1,
								// 		color: 'red'
								// 	}, {
								// 		id: 'NoraID',
								// 		name: 'Nora',
								// 		connections: [
								// 			'MonicaID'
								// 		],
								// 		rejection: false,
								// 		y: 1,
								// 		color: 'red'
								// 	}
								// ],
								type: 'sociogram',
								name: 'Positive relations',
								marker: {
									// lineWidth: 3
								}
							}, {
								color: '#000',
								type: 'sociogram',
								name: 'Negative relations'
							}]

						});
					}
				}
			}
		})

	}

	getSociogramSeries()
</script>
<style>
	.scrollable {
		overflow-y: hidden !important;
		overflow-x: auto !important;
	}
</style>
