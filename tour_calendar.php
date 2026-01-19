<?php 
/* ───────── 1. VERİTABANI BAĞLANTISI ───────────────────────── */
error_reporting(E_ALL); 
ini_set('display_errors', 1); // Hataları görmek için açıyoruz

$host='localhost'; 
$user='cyntzsrb_cyn'; 
$pass='Qj!d$}Zh,-~m'; 
$db='cyntzsrb_cyn';

$conn = new mysqli($host, $user, $pass, $db);

if($conn->connect_error){ 
    die('DB Bağlantı hatası: '.$conn->connect_error); 
}

/* ÖNEMLİ DÜZELTME: Türkçe karakter sorunu ve JSON hatasını önlemek için */
$conn->set_charset("utf8mb4");

/* ───────── 1-A. VOUCHER SİLME İSTEĞİ ─────────────────────── */
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['delete_voucher'])){
    $vid = intval($_POST['delete_voucher']);
    // SQL Injection önlemek için prepare kullanmak daha iyidir ama mevcut yapıyı koruyoruz
    $conn->query("DELETE FROM city_tour_vouchers WHERE id=$vid");
    echo 'ok';
    exit;
}

/* ───────── 2. TÜM TURLARI ÇEK ─────────────────────────────── */
$sql = "
SELECT
    t.id                AS tour_id,
    t.voucher_id,
    t.tour_name,
    t.tour_date,
    t.duration,

    v.voucher_no,
    v.company_name,
    v.customer_phone,
    v.hotel_name,
    v.adult,
    v.child,
    v.infant,
    v.created_at,

    GROUP_CONCAT(c.customer_name ORDER BY c.id SEPARATOR ', ') AS customer_names
FROM   city_tour_voucher_tours        t
JOIN   city_tour_vouchers             v ON v.id = t.voucher_id
LEFT   JOIN city_tour_voucher_customers c ON c.voucher_id = v.id
GROUP  BY t.id
ORDER  BY t.tour_date ASC, t.tour_name
";

$result = $conn->query($sql);

$tours = [];
if ($result) {
    $tours = $result->fetch_all(MYSQLI_ASSOC);
} else {
    // Sorgu hatası varsa ekrana yazdır (Debugging için)
    echo "<!-- SQL Hatası: " . $conn->error . " -->";
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>CYN Şehir Turları | Takvim</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<style>
:root{--tour-upcoming:#16a34a;--tour-today:#f59e0b;--tour-past:#dc2626}
body{font-family:Poppins,Arial,Helvetica,sans-serif;background:#f7f7fc}
.calendar{overflow-x:auto}
.calendar-grid{display:grid;grid-template-columns:repeat(7,1fr);gap:.3rem}
.day-name{font-size:.75rem;font-weight:600}
.day{aspect-ratio:1/1;display:flex;align-items:center;justify-content:center;font-size:.85rem;border-radius:.4rem;cursor:pointer;transition:.15s}
.day:hover{transform:translateY(-2px);box-shadow:0 .25rem .75rem rgba(0,0,0,.07)}
.day-has-tour{border:2px solid #2563eb; font-weight: bold;}
.day-past{background:var(--tour-past)!important;color:#fff}
.day-today{background:var(--tour-today)!important;color:#fff}
.day-upcoming{background:var(--tour-upcoming)!important;color:#fff}
</style>
</head>
<body>

<div class="container py-4">
  <header class="text-center mb-4">
    <h1 class="fs-3 fw-semibold">CYN Şehir Turları – Takvim</h1>
  </header>

  <!-- Navigasyon -->
  <div class="d-flex flex-wrap justify-content-center gap-2 mb-3">
    <button class="btn btn-outline-primary btn-sm" id="prevYear"><i class="fa-solid fa-angles-left"></i> Önceki&nbsp;Yıl</button>
    <button class="btn btn-outline-primary btn-sm" id="prevMonth"><i class="fa-solid fa-angle-left"></i> Önceki&nbsp;Ay</button>
    <button class="btn btn-outline-primary btn-sm" id="nextMonth">Sonraki&nbsp;Ay <i class="fa-solid fa-angle-right"></i></button>
    <button class="btn btn-outline-primary btn-sm" id="nextYear">Sonraki&nbsp;Yıl <i class="fa-solid fa-angles-right"></i></button>
  </div>

  <h2 id="monthTitle" class="text-center fw-semibold mb-3"></h2>

  <section class="calendar card p-3 shadow-sm">
    <div id="calendar" class="calendar-grid"></div>
  </section>

  <!-- Renk Açıklamaları -->
  <div class="d-flex flex-wrap gap-3 justify-content-center mt-4">
    <span class="badge d-flex align-items-center gap-2"><span style="width:16px;height:16px;border-radius:50%;background:var(--tour-past)"></span>Geçmiş</span>
    <span class="badge d-flex align-items-center gap-2"><span style="width:16px;height:16px;border-radius:50%;background:var(--tour-today)"></span>Bugün</span>
    <span class="badge d-flex align-items-center gap-2"><span style="width:16px;height:16px;border-radius:50%;background:var(--tour-upcoming)"></span>Yaklaşan</span>
  </div>
  
  <p class="text-center text-muted small mt-2">
    Veritabanından çekilen tur sayısı: <strong><?= count($tours) ?></strong>
  </p>
</div>

<!-- Modal -->
<div class="modal fade" id="tourModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content border-0">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title"><span id="modalDate"></span> Tarihli Turlar</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <ul id="tourList" class="list-unstyled m-0"></ul>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
/* 
   DÜZELTME: JSON Encode edilirken hata oluşursa (UTF8 vb.) boş dizi döner.
   JSON_UNESCAPED_UNICODE: Türkçe karakterlerin okunabilir kalmasını sağlar.
*/
const tours = <?php echo json_encode($tours, JSON_HEX_TAG | JSON_UNESCAPED_UNICODE) ?: '[]'; ?>;

// Konsola veriyi yazdırarak kontrol edelim
console.log("Loaded Tours:", tours);

const monthNames=["Ocak","Şubat","Mart","Nisan","Mayıs","Haziran","Temmuz","Ağustos","Eylül","Ekim","Kasım","Aralık"];
const dayNames=["Paz","Pzt","Sal","Çar","Per","Cum","Cmt"];
let viewDate=new Date();
const todayISO=new Date().toISOString().slice(0,10);
const daysInMonth=(y,m)=>new Date(y,m+1,0).getDate();

/* Takvim render */
function render(){
  const y=viewDate.getFullYear(), m=viewDate.getMonth();
  $('#monthTitle').text(`${monthNames[m]} ${y}`);
  
  // getDay() Pazartesi için 1 değil, Pazar için 0 döner.
  // Takviminiz "Paz, Pzt" diye başlıyorsa (Pazar ilk gün) bu doğru.
  // Eğer Takvim "Pzt" ile başlayacaksa mantık değişmeli. Şu an Pazar başı varsayılıyor.
  const first=new Date(y,m,1).getDay(); 
  
  const total=daysInMonth(y,m); 
  const $cal=$('#calendar').empty();
  
  dayNames.forEach(n=>$cal.append(`<div class="day-name text-center text-primary-emphasis">${n}</div>`));
  
  // Boşlukları doldur
  [...Array(first)].forEach(()=>{$cal.append('<div></div>')});
  
  for(let d=1;d<=total;d++){
    const iso=`${y}-${String(m+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
    // Tarih kontrolü
    const has=tours.some(t=>t.tour_date===iso);
    
    const cell=$('<div/>').addClass('day').text(d);
    if(has){
      cell.addClass('day-has-tour').data('date',iso);
      if(iso===todayISO)cell.addClass('day-today'); 
      else if(iso<todayISO)cell.addClass('day-past'); 
      else cell.addClass('day-upcoming');
    }
    $cal.append(cell);
  }
}

/* Hücre tıkla → modal */
$('#calendar').on('click','.day-has-tour',function(){
  const date=$(this).data('date'); 
  $('#modalDate').text(date);
  
  const group={};
  // Sadece o güne ait turları filtrele
  tours.filter(t=>t.tour_date===date).forEach(t=>{
    // Voucher ID bazlı gruplama
    if(!group[t.voucher_id]) {
        group[t.voucher_id]={
            ...t,
            tour_names:[t.tour_name],
            durations:[t.duration]
        };
    } else { 
        group[t.voucher_id].tour_names.push(t.tour_name); 
        group[t.voucher_id].durations.push(t.duration);
    }
  });

  const $ul=$('#tourList').empty();
  
  if(Object.keys(group).length === 0) {
      $ul.append('<li>Tur verisi bulunamadı.</li>');
  }

  Object.values(group).forEach(t=>{
    $ul.append(`
      <li class="mb-4 p-3 border rounded shadow-sm">
        <table class="table table-sm mb-2">
          <tr><th class="w-25">Voucher No</th><td>${t.voucher_no || '-'}</td></tr>
          <tr><th>Firma</th><td>${t.company_name || '-'}</td></tr>
          <tr><th>Telefon</th><td>${t.customer_phone || '-'}</td></tr>
          <tr><th>Otel</th><td>${t.hotel_name || '-'}</td></tr>
          <tr><th>Kişi</th><td>Yetişkin ${t.adult} / Çocuk ${t.child} / Bebek ${t.infant}</td></tr>
          <tr><th>Turlar</th><td>${t.tour_names.join('<br>')}</td></tr>
          <tr><th>Süreler</th><td>${t.durations.join('<br>')}</td></tr>
          <tr><th>Yolcular</th><td>${t.customer_names ? t.customer_names : '<span class="text-muted">(yok)</span>'}</td></tr>
          <tr><th>Oluşturma</th><td>${t.created_at}</td></tr>
        </table>
        <div class="d-flex gap-2">
          <a class="btn btn-sm btn-outline-primary" href="tour_voucher.php?voucher_id=${t.voucher_id}" target="_blank">
            <i class="fa-solid fa-file-pdf"></i> PDF İndir
          </a>
          <button class="btn btn-sm btn-danger delete-voucher" data-id="${t.voucher_id}">
            <i class="fa-solid fa-trash"></i> Sil
          </button>
        </div>
      </li>
    `);
  });
  new bootstrap.Modal('#tourModal').show();
});

/* Silme butonu */
$(document).on('click','.delete-voucher',function(){
  const id=$(this).data('id');
  if(!confirm('Bu voucher ve tüm bağlı turları silmek istediğinize emin misiniz?')) return;
  $.post('',{delete_voucher:id},function(res){
      if(res.trim()==='ok'){ location.reload(); }
      else alert('Silme işlemi başarısız: ' + res);
  });
});

/* Navigasyon */
$('#prevMonth').click(()=>{viewDate.setMonth(viewDate.getMonth()-1);render();});
$('#nextMonth').click(()=>{viewDate.setMonth(viewDate.getMonth()+1);render();});
$('#prevYear').click(()=>{viewDate.setFullYear(viewDate.getFullYear()-1);render();});
$('#nextYear').click(()=>{viewDate.setFullYear(viewDate.getFullYear()+1);render();});

/* İlk çizim */
render();
</script>
</body>
</html>