<div class="row mb-4">

<div class="col-md-3">
<div class="card shadow">
<div class="card-body">

<h6>Total Penjualan</h6>
<h3>0</h3>

</div>
</div>
</div>

<div class="col-md-3">
<div class="card shadow">
<div class="card-body">

<h6>Pesanan Baru</h6>
<h3>0</h3>

</div>
</div>
</div>

<div class="col-md-3">
<div class="card shadow">
<div class="card-body">

<h6>Produk Tersedia</h6>
<h3>0</h3>

</div>
</div>
</div>

<div class="col-md-3">
<div class="card shadow">
<div class="card-body">

<h6>Pelanggan</h6>
<h3>0</h3>

</div>
</div>
</div>

</div>


<div class="card shadow">

<div class="card-body">

<h5>Grafik Penjualan</h5>

<canvas id="grafikPenjualan"></canvas>

</div>

</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

var ctx = document.getElementById('grafikPenjualan');

new Chart(ctx,{

type:'line',

data:{

labels:['Jan','Feb','Mar','Apr','Mei','Jun'],

datasets:[

{
label:'Pendapatan',
data:[0,0,0,0,0,0],
borderColor:'blue',
fill:false
},

{
label:'Pesanan',
data:[0,0,0,0,0,0],
borderColor:'orange',
fill:false
}

]

}

});

</script>