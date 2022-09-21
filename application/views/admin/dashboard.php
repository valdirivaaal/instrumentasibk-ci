 <div class="row">
   <div class="col-md-12">
     <div class="row state-overview">
       <div class="col-lg-3 col-sm-6">
         <section class="card">
           <div class="symbol terques">
             <i class="fa fa-users"></i>
           </div>
           <div class="value">
             <h1 class="count">
               <?= count($get_user) ?>
             </h1>
             <p>New Users</p>
           </div>
         </section>
       </div>
       <div class="col-lg-3 col-sm-6">
         <section class="card">
           <div class="symbol red">
             <i class="fa fa-shopping-cart"></i>
           </div>
           <div class="value">
             <h1 class=" count2">
               <?= count($get_transaksi) ?>
             </h1>
             <p>Sales</p>
           </div>
         </section>
       </div>
       <div class="col-lg-3 col-sm-6">
         <section class="card">
           <div class="symbol yellow">
             <i class="fa fa-user-graduate"></i>
           </div>
           <div class="value">
             <h1 class=" count3">
               <?= count($get_key_guru) ?>
             </h1>
             <p>Event Key Guru</p>
           </div>
         </section>
       </div>
       <div class="col-lg-3 col-sm-6">
         <section class="card">
           <div class="symbol blue">
             <i class="fa fa-user-tie"></i>
           </div>
           <div class="value">
             <h1 class=" count4">
               <?= count($get_key_konselor) ?>
             </h1>
             <p>Event Key Konselor</p>
           </div>
         </section>
       </div>
     </div>
   </div>
 </div>
 </div>
 <!-- <div class="card mt-3">
   <div class="card-header">
     Dashboard Instrumentasi BK
   </div>
   <div class="card-body">
     <div class="adv-table">
       <table class="display table table-bordered table-striped">
         <thead>
           <tr>
             <th>Bulan</th>
             <th>Jml</th>
             <th>Jumlah Pemasukan</th>
             <th>Biaya Maintenance</th>
             <th>Biaya Percetakan</th>
             <th>Keuntungan Bersih</th>
             <th>BK UNP</th>
             <th>ADE BK</th>
             <th>Aksi</th>
           </tr>
         </thead>
         <tbody>
           <?php
            foreach ($get_bulan as $key => $value) {
            ?>
             <tr>
               <td><?= $value ?></td>
               <td class="text-center">2</td>
               <td>Rp 2.000.000</td>
               <td>Rp 500.000</td>
               <td>Rp 50.000</td>
               <td>Rp 1.450.000</td>
               <td>Rp 870.000</td>
               <td>Rp 580.000</td>
               <td><a href="#" class="btn btn-primary btn-sm">Lihat</a></td>
             </tr>
           <?php
            }
            ?>
         </tbody>
       </table>
     </div>
   </div>
 </div> -->

 <script type="text/javascript">
   var ctx = document.getElementById('myChart').getContext('2d');
   var myChart = new Chart(ctx, {
     type: 'bar',
     data: {
       labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
       datasets: [{
         label: '# Total Penjualan',
         data: [18000000, 19000000, 3000000, 5000000, 2000000, 3000000, 20000000, 30000000, 8000000, 1000000, 4000000, 7000000],
         backgroundColor: [
           'rgba(189, 197, 129,0.2)',
           'rgba(46, 204, 113, 0.2)',
           'rgba(52, 152, 219, 0.2)',
           'rgba(155, 89, 182, 0.2)',
           'rgba(52, 73, 94, 0.2)',
           'rgba(241, 196, 15, 0.2)',
           'rgba(230, 126, 34, 0.2)',
           'rgba(231, 76, 60, 0.2)',
           'rgba(44, 58, 71, 0.2)',
           'rgba(149, 165, 166, 0.2)',
           'rgba(253, 121, 168, 0.2)',
           'rgba(247, 215, 148, 0.2)'
         ],
         borderColor: [
           'rgba(189, 197, 129,1.0)',
           'rgba(46, 204, 113, 1)',
           'rgba(52, 152, 219, 1)',
           'rgba(155, 89, 182, 1)',
           'rgba(52, 73, 94, 1)',
           'rgba(241, 196, 15, 1)',
           'rgba(230, 126, 34, 1)',
           'rgba(231, 76, 60, 1)',
           'rgba(44, 58, 71, 1)',
           'rgba(149, 165, 166, 1)',
           'rgba(253, 121, 168, 1)',
           'rgba(247, 215, 148, 1)'
         ],
         borderWidth: 1
       }]
     },
     options: {
       scales: {
         yAxes: [{
           ticks: {
             beginAtZero: true,
             callback: function(value, index, values) {
               return 'Rp ' + Intl.NumberFormat().format(value);
             }
           }
         }]
       },
       tooltips: {
         callbacks: {
           label: function(tooltipItem, data) {
             return 'Rp ' + Intl.NumberFormat().format(tooltipItem.yLabel);
           }
         }
       }
     }
   });
 </script>