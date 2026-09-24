<!doctype html>
<html lang="ru">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ $content['meta']['title'] }}</title>
<style>
:root{--bg:#090a0c;--panel:#12151a;--panel2:#191d24;--text:#f4f1e8;--muted:#9198a3;--line:#2c323c;--accent:#efc75e;--danger:#e56d6d}
*{box-sizing:border-box}body{margin:0;background:radial-gradient(circle at 80% -20%,#2b2112 0,transparent 32rem),var(--bg);color:var(--text);font:14px/1.4 Inter,system-ui,sans-serif}
header{position:sticky;top:0;z-index:40;padding:18px max(18px,calc((100vw - 1500px)/2));background:#090a0cf2;backdrop-filter:blur(16px);border-bottom:1px solid var(--line)}
.top{display:flex;align-items:flex-start;justify-content:space-between;gap:18px}.eyebrow{color:var(--accent);font-size:11px;font-weight:800;letter-spacing:.14em;text-transform:uppercase}
h1{font-size:clamp(24px,4vw,42px);line-height:1;margin:5px 0 7px}.sub{color:var(--muted);max-width:650px}.mode{border:1px solid var(--line);border-radius:99px;padding:7px 11px;white-space:nowrap;color:var(--muted)}.mode.owner{color:#a9e5b0;border-color:#35593a}
.toolbar{display:flex;gap:8px;flex-wrap:wrap;margin-top:16px}input,select,button{background:var(--panel2);color:var(--text);border:1px solid var(--line);border-radius:9px;padding:9px 11px;font:inherit}input{min-width:220px;flex:1}button{cursor:pointer;font-weight:700}button:hover{border-color:#657080}.primary{background:var(--accent);color:#17130a;border-color:var(--accent)}
.status{min-width:95px;align-self:center;color:var(--muted);font-size:12px}.status.error{color:#ff9292}main{padding:18px;max-width:1500px;margin:auto}.stats{display:flex;gap:16px;color:var(--muted);margin:0 0 12px}
.board{display:grid;gap:9px}.tier{display:grid;grid-template-columns:92px 1fr;min-height:126px;background:var(--panel);border:1px solid var(--line);border-radius:12px;overflow:hidden}.label{display:flex;flex-direction:column;align-items:center;justify-content:center;color:#111;font-size:32px;font-weight:950}.label small{font-size:10px;text-align:center;padding:3px 5px;opacity:.68}.tier[data-tier=S] .label{background:#ff7f7f}.tier[data-tier=A] .label{background:#ffbf7f}.tier[data-tier=B] .label{background:#ffdf7f}.tier[data-tier=C] .label{background:#ffff7f}.tier[data-tier=D] .label{background:#bfff7f}.tier[data-tier=F] .label{background:#7fff7f}
.drop{min-height:124px;padding:9px;display:flex;gap:9px;flex-wrap:wrap;align-content:flex-start}.drop.dragover{outline:2px dashed var(--accent);outline-offset:-6px;background:#1a1d20}.empty{color:#5f6670;margin:auto;font-size:12px}
.card{position:relative;width:150px;min-height:104px;padding:12px;background:linear-gradient(145deg,#242a33,#181c22);border:1px solid #363e49;border-radius:10px;box-shadow:0 5px 16px #0004;overflow:hidden}.editor .card{cursor:grab}.card:active{cursor:grabbing}.card.hidden{display:none}.card.has-poster{padding:0;min-height:210px}.poster{width:100%;height:210px;object-fit:cover;display:block}.poster-shade{position:absolute;inset:auto 0 0;padding:28px 10px 10px;background:linear-gradient(transparent,#08090af2)}.title{font-weight:800;line-height:1.25}.meta{font-size:11px;color:#a9b0ba;margin-top:5px}.remove{position:absolute;right:6px;top:6px;width:26px;height:26px;padding:0;border-radius:50%;background:#0b0d10dd;color:#fff;z-index:2}.unranked{margin-top:18px;border-top:1px solid var(--line);padding-top:16px}.unranked h2{font-size:16px;margin:0 0 9px}.unranked .drop{border:1px dashed #37404c;border-radius:12px;background:#0c0e12;min-height:145px}.notice{margin-top:12px;color:var(--muted);font-size:12px}
.modal{position:fixed;inset:0;z-index:100;background:#000c;display:none;align-items:center;justify-content:center;padding:16px}.modal.on{display:flex}.modalbox{width:min(500px,100%);background:var(--panel);border:1px solid var(--line);border-radius:14px;padding:18px}.modalbox h2{margin:0 0 14px}.fields{display:grid;gap:10px}.fields input{width:100%}.actions{display:flex;justify-content:flex-end;gap:8px;margin-top:14px}
@media(max-width:700px){header{position:relative}.top{display:block}.mode{display:inline-block;margin-top:11px}.tier{grid-template-columns:58px 1fr}.label{font-size:24px}.card{width:calc(50% - 5px)}.card.has-poster,.poster{min-height:0;height:190px}}
</style>
</head>
<body class="{{ $canEdit ? 'editor' : 'viewer' }}" data-can-edit="{{ $canEdit ? 'true' : 'false' }}">
<header>
  <div class="top">
    <div><div class="eyebrow">MOVIE TIER LIST</div><h1>{{ $content['meta']['title'] }}</h1><div class="sub">{{ $content['meta']['description'] }}</div></div>
    <div class="mode {{ $canEdit ? 'owner' : '' }}">{{ $canEdit ? $content['ui']['owner_mode'] : $content['ui']['viewer_mode'] }}</div>
  </div>
  <div class="toolbar">
    <input id="search" type="search" placeholder="{{ $content['ui']['search'] }}">
    <select id="genre"><option value="all">{{ $content['ui']['all_genres'] }}</option></select>
    @if($canEdit)
      <button id="add">{{ $content['ui']['add'] }}</button>
      <button id="save" class="primary">{{ $content['ui']['save'] }}</button>
    @endif
    <span id="status" class="status"></span>
  </div>
</header>
<main>
  <div class="stats"><span id="total"></span><span id="ranked"></span></div>
  <div class="board">
    @foreach($content['tiers'] as $tier)
      <section class="tier" data-tier="{{ $tier['id'] }}"><div class="label">{{ $tier['label'] }}<small>{{ $tier['caption'] }}</small></div><div class="drop" data-zone="{{ $tier['id'] }}"></div></section>
    @endforeach
  </div>
  <section class="unranked"><h2>{{ $content['ui']['unranked'] }}</h2><div class="drop" data-zone="unranked"></div></section>
  @unless($canEdit)<p class="notice">Рейтинг обновляет владелец. У посетителей нет write-доступа к API.</p>@endunless
</main>

@if($canEdit)
<div class="modal" id="modal"><form class="modalbox" id="movieForm">
  <h2>Добавить фильм</h2>
  <div class="fields"><input id="movieTitle" maxlength="200" required placeholder="Название"><input id="movieYear" type="number" min="1888" max="2100" placeholder="Год"><input id="movieGenre" maxlength="80" placeholder="Жанр"><input id="moviePoster" type="url" maxlength="2048" placeholder="URL постера (необязательно)"></div>
  <div class="actions"><button type="button" id="cancel">Отмена</button><button class="primary">Добавить</button></div>
</form></div>
@endif

<script>
const CAN_EDIT = @json($canEdit);
const UPDATE_URL = @json(route('movies-tier-list.update'));
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
const UI = @json($content['ui']);
let revision = @json($revision);
let movies = @json($movies);
let dirty = false;

function escapeHtml(value){return String(value??'').replace(/[&<>"']/g,char=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[char]))}
function slug(){return `movie-${Date.now()}-${Math.random().toString(36).slice(2,8)}`}
function card(movie){
  const el=document.createElement('article'); el.className=`card${movie.poster_url?' has-poster':''}`; el.dataset.id=movie.id; el.dataset.genre=movie.genre||''; el.draggable=CAN_EDIT;
  const info=`<div class="title">${escapeHtml(movie.title)}</div><div class="meta">${[movie.year,movie.genre].filter(Boolean).map(escapeHtml).join(' · ')}</div>`;
  el.innerHTML=movie.poster_url?`<img class="poster" src="${escapeHtml(movie.poster_url)}" alt="" loading="lazy"><div class="poster-shade">${info}</div>`:info;
  if(CAN_EDIT){
    el.insertAdjacentHTML('beforeend','<button class="remove" type="button" title="Удалить">×</button>');
    el.querySelector('.remove').addEventListener('click',()=>{movies=movies.filter(item=>item.id!==movie.id);dirty=true;render()});
    el.addEventListener('dragstart',event=>{event.dataTransfer.setData('text/plain',movie.id);requestAnimationFrame(()=>el.style.opacity='.35')});
    el.addEventListener('dragend',()=>el.style.opacity='1');
  }
  return el;
}
function ordered(){const order=['S','A','B','C','D','F','unranked'];return [...movies].sort((a,b)=>order.indexOf(a.tier)-order.indexOf(b.tier)||a.position-b.position)}
function render(){
  document.querySelectorAll('.drop').forEach(zone=>zone.innerHTML='');
  ordered().forEach(movie=>document.querySelector(`[data-zone="${movie.tier}"]`).appendChild(card(movie)));
  document.querySelectorAll('.drop').forEach(zone=>{if(!zone.children.length)zone.innerHTML=`<span class="empty">${escapeHtml(UI.empty)}</span>`});
  rebuildGenres();filter();stats();
}
function syncPositions(){document.querySelectorAll('.drop').forEach(zone=>[...zone.querySelectorAll('.card')].forEach((el,index)=>{const movie=movies.find(item=>item.id===el.dataset.id);if(movie){movie.tier=zone.dataset.zone;movie.position=index}}))}
function rebuildGenres(){const select=document.getElementById('genre');const selected=select.value;const genres=[...new Set(movies.map(movie=>movie.genre).filter(Boolean))].sort();select.innerHTML=`<option value="all">${escapeHtml(UI.all_genres)}</option>`+genres.map(genre=>`<option value="${escapeHtml(genre)}">${escapeHtml(genre)}</option>`).join('');select.value=genres.includes(selected)?selected:'all'}
function filter(){const query=document.getElementById('search').value.trim().toLowerCase();const genre=document.getElementById('genre').value;document.querySelectorAll('.card').forEach(el=>{const movie=movies.find(item=>item.id===el.dataset.id);const visible=movie&&(!query||movie.title.toLowerCase().includes(query))&&(genre==='all'||movie.genre===genre);el.classList.toggle('hidden',!visible)})}
function stats(){document.getElementById('total').textContent=`Фильмов: ${movies.length}`;document.getElementById('ranked').textContent=`Распределено: ${movies.filter(movie=>movie.tier!=='unranked').length}`}
function setStatus(message,error=false){const el=document.getElementById('status');el.textContent=message;el.classList.toggle('error',error)}
async function save(){
  if(!CAN_EDIT)return;syncPositions();setStatus(UI.saving);document.getElementById('save').disabled=true;
  try{const response=await fetch(UPDATE_URL,{method:'PUT',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':CSRF},body:JSON.stringify({revision,movies})});const data=await response.json();if(!response.ok)throw new Error(data.message||'Не удалось сохранить');revision=data.revision;dirty=false;setStatus(UI.saved)}catch(error){setStatus(error.message,true)}finally{document.getElementById('save').disabled=false}
}
document.getElementById('search').addEventListener('input',filter);document.getElementById('genre').addEventListener('change',filter);
if(CAN_EDIT){
  document.querySelectorAll('.drop').forEach(zone=>{zone.addEventListener('dragover',event=>{event.preventDefault();zone.classList.add('dragover')});zone.addEventListener('dragleave',()=>zone.classList.remove('dragover'));zone.addEventListener('drop',event=>{event.preventDefault();zone.classList.remove('dragover');const id=event.dataTransfer.getData('text/plain');const movie=movies.find(item=>item.id===id);if(movie){movie.tier=zone.dataset.zone;movie.position=zone.querySelectorAll('.card').length;dirty=true;render()}})});
  const modal=document.getElementById('modal');document.getElementById('add').addEventListener('click',()=>modal.classList.add('on'));document.getElementById('cancel').addEventListener('click',()=>modal.classList.remove('on'));document.getElementById('save').addEventListener('click',save);
  document.getElementById('movieForm').addEventListener('submit',event=>{event.preventDefault();const title=document.getElementById('movieTitle').value.trim();if(!title)return;movies.push({id:slug(),title,year:Number(document.getElementById('movieYear').value)||null,genre:document.getElementById('movieGenre').value.trim()||null,poster_url:document.getElementById('moviePoster').value.trim()||null,tier:'unranked',position:movies.filter(movie=>movie.tier==='unranked').length});event.target.reset();modal.classList.remove('on');dirty=true;render()});
  window.addEventListener('beforeunload',event=>{if(dirty){event.preventDefault();event.returnValue=''}});
}
render();
</script>
</body>
</html>
