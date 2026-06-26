<?php
// Verilerin saklanacağı JSON dosyası
$dataFile = 'apis.json'; // Admin paneli ile dosya adını eşitledim (apis.json)
if (!file_exists($dataFile)) {
    file_put_contents($dataFile, json_encode([]));
}
$apis = json_decode(file_get_contents($dataFile), true);

// Kartlar için Siber Mavi ve Lacivert Neon Temaları
$cardColors = [
  ['orb'=>'#00f2fe','glow'=>'rgba(0,242,254,0.35)','dim'=>'rgba(0,242,254,0.08)','border'=>'rgba(0,242,254,0.3)'],
  ['orb'=>'#38bdf8','glow'=>'rgba(56,189,248,0.4)','dim'=>'rgba(56,189,248,0.1)','border'=>'rgba(56,189,248,0.35)'],
  ['orb'=>'#6366f1','glow'=>'rgba(99,102,241,0.4)','dim'=>'rgba(99,102,241,0.1)','border'=>'rgba(99,102,241,0.35)'],
  ['orb'=>'#0ea5e9','glow'=>'rgba(14,165,233,0.4)','dim'=>'rgba(14,165,233,0.1)','border'=>'rgba(14,165,233,0.35)'],
  ['orb'=>'#22d3ee','glow'=>'rgba(34,211,238,0.35)','dim'=>'rgba(34,211,238,0.08)','border'=>'rgba(34,211,238,0.3)'],
];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Zeldy API Servis</title>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=Space+Mono:wght@400;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<script src="https://unpkg.com/lucide@latest"></script>
<style>
*{box-sizing:border-box;margin:0;padding:0}
:root{
  --bg:#030712; /* Derin Gece Siyahı/Mavisi */
  --bg2:#070f2e; /* Koyu Lacivert */
  --border:rgba(56,189,248,0.1);
  --text:#f0fdfa;
  --muted:#64748b;
  --p:#00f2fe; /* Neon Cyan */
  --p2:#2563eb; /* Canlı Mavi/Lacivert */
  --ind:#38bdf8; /* Siber Mavi */
  --warn: #f59e0b; /* Dikkat Sarı/Turuncu */
}
::-webkit-scrollbar{width:4px}
::-webkit-scrollbar-track{background:var(--bg)}
::-webkit-scrollbar-thumb{background:linear-gradient(var(--p2),var(--ind));border-radius:99px}
html{scroll-behavior:smooth}
body{font-family:'Inter',sans-serif;background:var(--bg);color:var(--text);min-height:100vh;overflow-x:hidden;position:relative}
body::after{content:'';position:fixed;inset:0;background-image:url("data:image/svg+xml,%3Csvg viewBox='0 0 512 512' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.75' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='1'/%3E%3C/svg%3E");opacity:0.018;pointer-events:none;z-index:9999}

/* Arka Plan Efektleri İçin Konumlandırma */
#hero-canvas{position:fixed;inset:0;width:100%;height:100vh;pointer-events:none;z-index:1}
.orb{position:fixed;border-radius:50%;filter:blur(100px);pointer-events:none;z-index:1}
.orb-a{width:600px;height:600px;background:radial-gradient(circle,rgba(37,99,235,0.18) 0%,transparent 70%);top:-200px;left:-200px;animation:orbA 13s ease-in-out infinite}
.orb-b{width:480px;height:480px;background:radial-gradient(circle,rgba(0,242,254,0.15) 0%,transparent 70%);bottom:-150px;right:-150px;animation:orbB 16s ease-in-out infinite}
.orb-c{width:360px;height:360px;background:radial-gradient(circle,rgba(56,189,248,0.12) 0%,transparent 70%);top:50%;left:50%;transform:translate(-50%,-50%);animation:orbC 11s ease-in-out infinite}

@keyframes cardIn{to{opacity:1;transform:translateY(0)}}
@keyframes orbA{
  0%,100%{transform:translate(0,0) scale(1)}
  40%{transform:translate(70px,50px) scale(1.12)}
  70%{transform:translate(-35px,25px) scale(0.9)}
}
@keyframes orbB{
  0%,100%{transform:translate(0,0) scale(1)}
  35%{transform:translate(-55px,-45px) scale(1.15)}
  65%{transform:translate(28px,35px) scale(0.93)}
}
@keyframes orbC{
  0%,100%{transform:translate(-50%,-50%) scale(1);opacity:0.5}
  50%{transform:translate(-50%,-50%) scale(1.4);opacity:0.9}
}
@keyframes pulse{
  0%{opacity:0.9;transform:scale(0.4)}
  100%{opacity:0;transform:scale(3)}
}
@keyframes shimmer{
  0%{background-position:-200% 0}
  100%{background-position:200% 0}
}

/* İlgili satırları bul ve bunları yapıştır: */

/* İlgili satırları bul ve bunları yapıştır: */

.c-name{
  font-family:'Syne',sans-serif;
  font-size:19px;
  font-weight:700; /* 800 çok kalın geliyorsa 700 yap */
  color:#fff;
  line-height: 1.6; /* BURAYI 1.6 YAPINCA "g" HARFİNİN ALTI KESİLMEYECEK */
  display: block;
}


.c-url{
  background:rgba(0,0,0,0.5);
  border:1px solid rgba(255,255,255,0.03);
  border-radius:11px;
  padding:13px 16px;
  display:flex;
  align-items:center;
  gap:10px;
  position:relative;
  overflow:hidden;
  width:100%;
}

.c-url-text{
  font-family:'Space Mono',monospace;
  font-size:11px; /* Fontu biraz küçülttük */
  color:var(--cc);
  white-space:nowrap; /* Metnin aşağı kırılmasını engeller */
  overflow:hidden;    /* Taşarsa gizler */
  text-overflow:ellipsis; /* Taşan yere ... koyar */
  padding-left:6px;
  opacity:0.85;
  display:block;
  flex:1;
}


.c-url{
  background:rgba(0,0,0,0.5);
  border:1px solid rgba(255,255,255,0.03);
  border-radius:11px;
  padding:13px 16px;
  display:flex;
  align-items:center;
  gap:10px;
  position:relative;
  overflow:hidden;
  width:100%;
}

.c-url-text{
  font-family:'Space Mono',monospace;
  font-size:11px; /* Fontu biraz küçülttük */
  color:var(--cc);
  white-space:nowrap; /* Metnin aşağı kırılmasını engeller */
  overflow:hidden;    /* Taşarsa gizler */
  text-overflow:ellipsis; /* Taşan yere ... koyar */
  padding-left:6px;
  opacity:0.85;
  display:block;
  flex:1;
}



/* Dikkat Uyarı Kutusu */
.info-box {
  background: rgba(245, 158, 11, 0.05);
  border: 1px solid rgba(245, 158, 11, 0.25);
  backdrop-filter: blur(14px);
  border-radius: 16px;
  padding: 16px 20px;
  margin-bottom: 30px;
  display: flex;
  align-items: center;
  gap: 14px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.3);
}
.info-box-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--warn);
  flex-shrink: 0;
  background: rgba(245, 158, 11, 0.1);
  padding: 8px;
  border-radius: 10px;
  border: 1px solid rgba(245, 158, 11, 0.2);
}
.info-box-text {
  font-family: 'Inter', sans-serif;
  font-size: 14px;
  font-weight: 500;
  color: #fef08a;
  letter-spacing: 0.3px;
  line-height: 1.5;
}

.cards{display:grid;gap:16px}
.api-card{
  background:rgba(7,15,46,0.55);border:1px solid rgba(56,189,248,0.08);
  backdrop-filter:blur(14px);
  border-radius:20px;padding:28px 30px;
  position:relative;overflow:hidden;
  opacity:0;transform:translateY(32px);
  transition:border-color 0.4s,box-shadow 0.45s,transform 0.45s;
  will-change:transform;
}
.api-card.in{animation:cardIn 0.6s cubic-bezier(0.22,1,0.36,1) forwards}
.api-card::before{
  content:'';position:absolute;top:0;left:0;right:0;height:1px;
  background:linear-gradient(90deg,transparent,var(--cc),transparent);
  opacity:0;transition:opacity 0.4s;
}
.glow-blob{
  position:absolute;top:-80px;right:-80px;
  width:220px;height:220px;border-radius:50%;
  background:var(--cg);filter:blur(55px);
  opacity:0;transition:opacity 0.5s;pointer-events:none;
}
.api-card:hover{
  border-color:var(--cb);
  box-shadow:0 0 0 1px var(--cb),0 24px 60px rgba(0,0,0,0.6),inset 0 0 40px rgba(0,0,0,0.4);
  transform:translateY(-8px) rotateX(1.5deg);
}
<!-- URL GÖSTERİMİ -->
<div class="c-url">
  <div class="c-url-text"><?= htmlspecialchars(html_entity_decode($a['endpoint'], ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8') ?></div>
</div>

<!-- KOPYALA BUTONU -->
<button class="btn btn-copy" onclick="doCopy('<?= addslashes(html_entity_decode($a['endpoint'], ENT_QUOTES, 'UTF-8')) ?>')">
  <svg viewBox="0 0 24 24"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
  <span>KOPYALA</span>
</button>

.api-card:hover .pulse-ring{animation:pulse 1.4s ease-out infinite}
.card-body{display:flex;flex-direction:column;gap:20px}
.card-row{display:flex;align-items:center;gap:16px}
.c-icon{
  width:48px;height:48px;flex-shrink:0;border-radius:14px;
  background:var(--cd);border:1px solid var(--cb);
  display:flex;align-items:center;justify-content:center;transition:all 0.35s;
  color: var(--cc);
}
.c-name{
  font-family:'Syne',sans-serif;
  font-size:18px; 
  font-weight:500; /* 800 yerine 500 yaptık */
  color:#fff;
}

.c-url-text{
  font-family:'Space Mono',monospace;
  font-size:12px;
  color:var(--cc);
  word-break:break-all; /* Yazının birbirine girmesini engeller */
  padding-left:6px;
  opacity:0.85;
  flex:1;
  line-height:1.5; /* Satır aralığını açtık */
}

.c-url::before{content:'';position:absolute;left:0;top:0;bottom:0;width:3px;background:var(--cc)}
.c-url-text{
  font-family:'Space Mono',monospace;font-size:12px;
  color:var(--cc);word-break:break-all;padding-left:6px;opacity:0.85;flex:1;
}
.c-actions{display:flex;gap:10px;flex-wrap:wrap}
.btn{
  display:inline-flex;align-items:center;gap:7px;
  padding:11px 22px;border-radius:11px;border:none;cursor:pointer;
  font-family:'Space Mono',monospace;font-size:12px;font-weight:700;
  letter-spacing:0.5px;text-decoration:none;transition:all 0.25s;
  position:relative;overflow:hidden;
}
.btn-copy{background:transparent;color:var(--muted);border:1px solid rgba(255,255,255,0.07)}
.btn-copy:hover{color:#fff;border-color:rgba(56,189,248,0.4);background:rgba(56,189,248,0.07);box-shadow:0 0 20px rgba(56,189,248,0.15)}
.btn-go{
  background:linear-gradient(135deg,var(--p2),var(--ind));
  color:#fff;font-weight:800;box-shadow:0 4px 20px rgba(37,99,235,0.3);
}
.btn-go::before{
  content:'';position:absolute;inset:0;
  background:linear-gradient(90deg,transparent,rgba(255,255,255,0.15),transparent);
  background-size:200%;animation:shimmer 2.5s ease-in-out infinite;
}
.btn-go:hover{transform:translateY(-2px);box-shadow:0 10px 35px rgba(37,99,235,0.5);filter:brightness(1.1)}
.btn svg{width:13px;height:13px;fill:none;stroke:currentColor;stroke-width:2.5;stroke-linecap:round;stroke-linejoin:round;position:relative;z-index:1}
.btn span{position:relative;z-index:1}
.empty{padding:80px 0;text-align:center;font-family:'Space Mono',monospace;font-size:13px;color:var(--muted);letter-spacing:1px}

.code-wrap{
  margin-top:64px;border-radius:20px;overflow:hidden;
  border:1px solid rgba(56,189,248,0.12);background:rgba(7,15,46,0.55);backdrop-filter:blur(14px);position:relative;
}
.code-wrap::before{
  content:'';position:absolute;top:0;left:0;right:0;height:1px;
  background:linear-gradient(90deg,transparent,var(--p),var(--ind),transparent);
}
.code-top{
  padding:14px 24px;background:rgba(0,0,0,0.5);
  border-bottom:1px solid rgba(56,189,248,0.08);
  display:flex;align-items:center;justify-content:space-between;
}
.code-label{
  display:flex;align-items:center;gap:10px;
  font-family:'Space Mono',monospace;font-size:11px;letter-spacing:2px;
  color:rgba(56,189,248,0.6);
}
.code-label svg{width:14px;height:14px;fill:none;stroke:var(--p);stroke-width:2;stroke-linecap:round;stroke-linejoin:round}
.dots{display:flex;gap:7px}
.dots span{width:12px;height:12px;border-radius:50%;display:block}
.code-body{
  padding:28px 30px;font-family:'Space Mono',monospace;font-size:13px;line-height:1.9;
  color:#f0fdfa;overflow-x:auto;background:rgba(0,0,0,0.3);
}
.k{color:#00f2fe;font-weight:700}.f{color:#38bdf8}.s{color:#34d399}.n{color:#38bdf8}.c2{color:#0ea5e9}
.toast{
  position:fixed;bottom:32px;left:50%;
  transform:translateX(-50%) translateY(100px);z-index:9000;
  display:flex;align-items:center;gap:10px;
  padding:13px 28px;border-radius:14px;
  background:rgba(3,7,18,0.96);border:1px solid rgba(56,189,248,0.4);
  font-family:'Space Mono',monospace;font-size:12px;letter-spacing:1px;color:#00f2fe;
  box-shadow:0 0 50px rgba(0,242,254,0.15),0 20px 50px rgba(0,0,0,0.7);
  backdrop-filter:blur(20px);
  transition:transform 0.5s cubic-bezier(0.22,1,0.36,1),opacity 0.5s;opacity:0;
}
.toast.on{transform:translateX(-50%) translateY(0);opacity:1}
.toast svg{width:14px;height:14px;fill:none;stroke:#00f2fe;stroke-width:2.5;stroke-linecap:round;stroke-linejoin:round}
@media(max-width:600px){
  main{padding:40px 16px 100px}
  .api-card{padding:20px 18px}
  .btn{padding:10px 16px;font-size:11px}
  .sec-head{flex-direction:column;align-items:flex-start}
}
</style>
</head>
<body>

<canvas id="hero-canvas"></canvas>
<div class="orb orb-a"></div>
<div class="orb orb-b"></div>
<div class="orb orb-c"></div>

<div id="toast" class="toast"><svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>KOPYALANDI</div>

<main id="main">
  <div class="sec-head">
    <div class="sec-head-left">
      <h2 class="sec-title">Zeldy Api Servis</h2>
      <div class="sec-count"><?= count($apis) ?> Api</div>
    </div>
  </div>

  <div class="info-box">
    <div class="info-box-icon">
      <i data-lucide="alert-triangle" style="width:20px; height:20px;"></i>
    </div>
    <div class="info-box-text">
      Bu Apiler Örnek Sorgular Yapmaktadır.
    </div>
  </div>

  <div class="cards" id="cards">
    <?php if (empty($apis)): ?>
      <div class="empty">henüz api eklenmedi</div>
    <?php else: ?>
      <?php foreach ($apis as $i => $a):
        $c = $cardColors[$i % count($cardColors)];
        $iconName = !empty($a['logo']) ? htmlspecialchars($a['logo']) : 'terminal';
        // Çift filtreleme sorununu çözmek için endpoint verisini decode edip temizliyoruz
        $cleanEndpoint = html_entity_decode($a['endpoint'], ENT_QUOTES, 'UTF-8');
      ?>
        <div class="api-card" data-i="<?= $i ?>" style="--cc:<?= $c['orb'] ?>;--cg:<?= $c['glow'] ?>;--cd:<?= $c['dim'] ?>;--cb:<?= $c['border'] ?>;">
          <div class="glow-blob"></div>
          <div class="pulse-ring"></div>
          <div class="card-body">
            <div class="card-row">
              <div class="c-icon">
                <i data-lucide="<?= $iconName ?>"></i>
              </div>
              <div>
                <div class="c-name"><?= htmlspecialchars($a['name'], ENT_QUOTES, 'UTF-8') ?></div>
              </div>
            </div>
            <div class="c-url">
              <div class="c-url-text"><?= htmlspecialchars($cleanEndpoint, ENT_QUOTES, 'UTF-8') ?></div>
            </div>
            <div class="c-actions">
              <button class="btn btn-copy" onclick="doCopy('<?= addslashes($cleanEndpoint) ?>')">
                <svg viewBox="0 0 24 24"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
                <span>KOPYALA</span>
              </button>
              <a href="<?= htmlspecialchars($cleanEndpoint, ENT_QUOTES, 'UTF-8') ?>" target="_blank" class="btn btn-go">
                <svg viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                <span>GİT</span>
              </a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

</main>

<script>
(function(){
  const cvs = document.getElementById('hero-canvas');
  const ctx = cvs.getContext('2d');
  let W, H, pts = [];
  const mouse = {x:-999, y:-999};
  function resize(){W=cvs.width=window.innerWidth;H=cvs.height=window.innerHeight;}
  resize();
  window.addEventListener('resize', resize);
  window.addEventListener('mousemove', e=>{mouse.x=e.clientX;mouse.y=e.clientY});
  
  const COLS = ['#00f2fe','#38bdf8','#6366f1','#0ea5e9','#2563eb','#22d3ee'];
  function h2r(hex,a){const r=parseInt(hex.slice(1,3),16),g=parseInt(hex.slice(3,5),16),b=parseInt(hex.slice(5,7),16);return `rgba(${r},${g},${b},${a})`}
  class Pt{
    constructor(){
      this.x=Math.random()*window.innerWidth;this.y=Math.random()*window.innerHeight;
      this.vx=(Math.random()-0.5)*0.45;this.vy=(Math.random()-0.5)*0.45;
      this.r=1+Math.random()*2.5;
      this.col=COLS[Math.floor(Math.random()*COLS.length)];
      this.alpha=0.3+Math.random()*0.55;this.phase=Math.random()*Math.PI*2;
    }
    update(t){
      this.x+=this.vx+Math.sin(t*0.0008+this.phase)*0.35;
      this.y+=this.vy+Math.cos(t*0.0007+this.phase)*0.35;
      if(this.x<-80)this.x=W+80;if(this.x>W+80)this.x=-80;
      if(this.y<-80)this.y=H+80;if(this.y>H+80)this.y=-80;
    }
    draw(){
      ctx.beginPath();ctx.arc(this.x,this.y,this.r,0,Math.PI*2);
      ctx.fillStyle=h2r(this.col,this.alpha);
      ctx.shadowColor=this.col;ctx.shadowBlur=this.r*7;
      ctx.fill();ctx.shadowBlur=0;
    }
  }
  pts=Array.from({length:80},()=>new Pt());
  let t=0;
  function loop(){
    ctx.clearRect(0,0,W,H);
    for(let i=0;i<pts.length;i++){
      for(let j=i+1;j<pts.length;j++){
        const dx=pts[i].x-pts[j].x,dy=pts[i].y-pts[j].y,d=Math.sqrt(dx*dx+dy*dy);
        if(d<160){ctx.beginPath();ctx.moveTo(pts[i].x,pts[i].y);ctx.lineTo(pts[j].x,pts[j].y);ctx.strokeStyle=`rgba(56,189,248,${(1-d/160)*0.18})`;ctx.lineWidth=0.7;ctx.stroke()}
      }
      const mdx=pts[i].x-mouse.x,mdy=pts[i].y-mouse.y,md=Math.sqrt(mdx*mdx+mdy*mdy);
      if(md<200){ctx.beginPath();ctx.moveTo(pts[i].x,pts[i].y);ctx.lineTo(mouse.x,mouse.y);ctx.strokeStyle=`rgba(0,242,254,${(1-md/200)*0.35})`;ctx.lineWidth=0.9;ctx.stroke()}
    }
    pts.forEach(p=>{p.update(t);p.draw()});
    t++;requestAnimationFrame(loop);
  }
  loop();
  function scheduleGlitch(){
    setTimeout(()=>{
      const end=Date.now()+80+Math.random()*120;
      function gf(){
        ctx.save();
        for(let s=0;s<3+Math.floor(Math.random()*4);s++){ctx.drawImage(cvs,(Math.random()-0.5)*18,0,W,H,0,0,W,H)}
        ctx.restore();
        if(Date.now()<end)requestAnimationFrame(gf);
      }
      gf();scheduleGlitch();
    },5000+Math.random()*9000);
  }
  scheduleGlitch();
})();

const obs=new IntersectionObserver(entries=>{
  entries.forEach(e=>{
    if(e.isIntersecting){
      const i=parseInt(e.target.dataset.i||0);
      setTimeout(()=>e.target.classList.add('in'), i*90);
      obs.unobserve(e.target);
    }
  });
},{threshold:0.05});
document.querySelectorAll('.api-card').forEach(c=>obs.observe(c));

document.querySelectorAll('.api-card').forEach(card=>{
  card.addEventListener('mousemove',e=>{
    const r=card.getBoundingClientRect();
    const x=(e.clientX-r.left)/r.width-0.5,y=(e.clientY-r.top)/r.height-0.5;
    card.style.transform=`translateY(-8px) rotateX(${-y*9}deg) rotateY(${x*9}deg)`;
  });
  card.addEventListener('mouseleave',()=>{
    card.style.transform='';
    card.style.transition='border-color 0.4s,box-shadow 0.45s,transform 0.65s cubic-bezier(0.22,1,0.36,1)';
  });
  card.addEventListener('mouseenter',()=>{
    card.style.transition='border-color 0.4s,box-shadow 0.45s,transform 0.1s';
  });
});

function doCopy(t){
  const ok=()=>showToast();
  if(navigator.clipboard&&window.isSecureContext){navigator.clipboard.writeText(t).then(ok);}
  else{const ta=document.createElement('textarea');ta.value=t;ta.style.position='fixed';ta.style.left='-9999px';document.body.appendChild(ta);ta.focus();ta.select();try{document.execCommand('copy');ok()}catch(e){}document.body.removeChild(ta)}
}
function showToast(){
  const el=document.getElementById('toast');
  el.classList.add('on');setTimeout(()=>el.classList.remove('on'),2400);
}
</script>
<script>
// Sayfa yüklendiğinde Lucide ikonlarını render et
lucide.createIcons();
</script>
</body>
</html>