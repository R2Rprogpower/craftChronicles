<!doctype html>
<html lang="ru">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Babangida Ultimate Tier List</title>
<style>
:root {
  --bg:#0b0d10; --panel:#13171c; --panel2:#1a2027; --text:#eef2f5; --muted:#87919c;
  --line:#2b333d; --accent:#f0d164;
}
*{box-sizing:border-box}
body{margin:0;background:var(--bg);color:var(--text);font:14px/1.35 Inter,Segoe UI,Arial,sans-serif}
header{position:sticky;top:0;z-index:30;background:rgba(11,13,16,.96);backdrop-filter:blur(12px);border-bottom:1px solid var(--line);padding:14px 16px}
h1{margin:0;font-size:22px;letter-spacing:.3px}
.sub{color:var(--muted);margin-top:4px}
.toolbar{display:flex;gap:8px;flex-wrap:wrap;margin-top:12px}
input,select,button{background:var(--panel2);color:var(--text);border:1px solid var(--line);border-radius:8px;padding:8px 10px}
input{min-width:230px;flex:1}
button{cursor:pointer} button:hover{border-color:#596573}
main{padding:16px;max-width:1500px;margin:auto}
.stats{display:flex;gap:14px;color:var(--muted);margin-bottom:12px;flex-wrap:wrap}
.board{display:grid;gap:8px}
.tier{display:grid;grid-template-columns:86px 1fr;min-height:92px;border:1px solid var(--line);border-radius:10px;overflow:hidden;background:var(--panel)}
.label{display:flex;flex-direction:column;align-items:center;justify-content:center;font-size:28px;font-weight:900;border-right:1px solid var(--line)}
.label small{font-size:10px;color:#0b0d10;opacity:.7;font-weight:800;text-align:center;padding:0 4px}
.tier[data-tier="S"] .label{background:#ff7f7f;color:#111}
.tier[data-tier="A"] .label{background:#ffbf7f;color:#111}
.tier[data-tier="B"] .label{background:#ffdf7f;color:#111}
.tier[data-tier="C"] .label{background:#ffff7f;color:#111}
.tier[data-tier="D"] .label{background:#bfff7f;color:#111}
.tier[data-tier="F"] .label{background:#7fff7f;color:#111}
.tier[data-tier="?"] .label{background:#8da0b4;color:#111}
.drop{padding:8px;display:flex;align-content:flex-start;align-items:flex-start;gap:7px;flex-wrap:wrap;min-height:90px}
.drop.dragover{outline:2px dashed var(--accent);outline-offset:-5px;background:#1e242b}
.card{position:relative;background:#222932;border:1px solid #343e49;border-radius:8px;padding:7px 29px 7px 9px;cursor:grab;max-width:260px;min-width:110px;box-shadow:0 1px 0 #000}
.card:active{cursor:grabbing}
.card.hidden{display:none}
.title{font-weight:650}
.tags{font-size:10px;color:#9aa6b2;margin-top:3px}
.move{position:absolute;right:5px;top:5px;width:20px;height:20px;padding:0;border-radius:5px;font-size:11px}
.unranked-wrap{margin-top:18px;border-top:1px solid var(--line);padding-top:14px}
.unranked-wrap h2{font-size:16px;margin:0 0 8px}
#unranked{min-height:150px;background:#0f1216;border:1px dashed #35404c;border-radius:10px;padding:10px;display:flex;gap:7px;flex-wrap:wrap;align-content:flex-start}
.note{color:var(--muted);font-size:12px;margin:10px 0 0}
.modal{position:fixed;inset:0;background:#000b;display:none;align-items:center;justify-content:center;z-index:100}
.modal.on{display:flex}
.modalbox{background:var(--panel);border:1px solid var(--line);border-radius:12px;padding:16px;width:min(520px,92vw)}
.modalbox select{width:100%;margin-top:10px}
@media(max-width:700px){.tier{grid-template-columns:56px 1fr}.label{font-size:22px}.card{max-width:100%;width:100%}}
</style>
</head>
<body>
<header>
  <h1>BABANGIDA — ULTIMATE TIER LIST</h1>
  <div class="sub">Battle + “Макаревич”: 62 треков/версий. Перетаскивай карточки — всё сохраняется локально.</div>
  <div class="toolbar">
    <input id="search" placeholder="Поиск трека…">
    <select id="filter">
      <option value="all">Все категории</option>
      <option value="solo">Сольные / прочее</option>
      <option value="battle">Баттлы</option>
      <option value="makarevich">Макаревич</option>
      <option value="freestyle">Фристайлы</option>
      <option value="feat">Фиты</option>
      <option value="lenina">Ленина Пакет</option>
      <option value="version">Версии / миксы</option>
      <option value="misc">Интро / скиты / интервью</option>
    </select>
    <button id="shuffle">Перемешать неоценённые</button>
    <button id="add">+ Добавить трек</button>
    <button id="export">Экспорт JSON</button>
    <button id="import">Импорт JSON</button>
    <input id="importFile" type="file" accept="application/json,.json" hidden>
    <button id="reset">Сбросить</button>
  </div>
</header>
<main>
  <div class="stats">
    <span id="shown"></span><span id="ranked"></span><span id="left"></span>
  </div>
  <div class="board" id="board">
    <div class="tier" data-tier="S"><div class="label">S<small>Шедевр</small></div><div class="drop" data-zone="S"></div></div><div class="tier" data-tier="A"><div class="label">A<small>Очень сильно</small></div><div class="drop" data-zone="A"></div></div><div class="tier" data-tier="B"><div class="label">B<small>Хорошо</small></div><div class="drop" data-zone="B"></div></div><div class="tier" data-tier="C"><div class="label">C<small>Норм</small></div><div class="drop" data-zone="C"></div></div><div class="tier" data-tier="D"><div class="label">D<small>Слабо</small></div><div class="drop" data-zone="D"></div></div><div class="tier" data-tier="F"><div class="label">F<small>Нахуй</small></div><div class="drop" data-zone="F"></div></div><div class="tier" data-tier="?"><div class="label">?<small>Не слушал</small></div><div class="drop" data-zone="?"></div></div>
  </div>
  <section class="unranked-wrap">
    <h2>НЕ РАСКИДАНО</h2>
    <div id="unranked" class="drop" data-zone="unranked"></div>
    <p class="note">Оставлены только баттловые треки и полный 19-трековый альбом «Макаревич». Альтернативные версии баттловых треков сохранены отдельно.</p>
  </section>
</main>

<div class="modal" id="modal"><div class="modalbox">
  <b id="modalTitle"></b>
  <select id="modalTier">
    <option value="unranked">Не распределено</option>
    <option value="S">S — Шедевр</option><option value="A">A</option><option value="B">B</option>
    <option value="C">C</option><option value="D">D</option><option value="F">F</option><option value="?">Не слушал</option>
  </select>
  <div class="toolbar"><button id="modalOk">Переместить</button><button id="modalClose">Отмена</button></div>
</div></div>

<script>
const initial = [{"id": 21, "title": "бойня номер шесть", "cats": ["solo", "battle"]}, {"id": 22, "title": "пропавшие страницы красной книги", "cats": ["solo", "battle"]}, {"id": 23, "title": "пример принятия мер за мир", "cats": ["solo", "battle"]}, {"id": 24, "title": "уничтожить соперника", "cats": ["solo", "battle"]}, {"id": 25, "title": "я не слышу больше рокот космодрома (версия)", "cats": ["version", "battle"]}, {"id": 26, "title": "я не слышу больше рокот космодрома", "cats": ["solo", "battle"]}, {"id": 27, "title": "возвращение легенды", "cats": ["solo", "battle"]}, {"id": 28, "title": "на улицах будущего (версия)", "cats": ["version", "battle"]}, {"id": 29, "title": "на улицах будущего", "cats": ["solo", "battle"]}, {"id": 30, "title": "закономерные случайности (часть 1)", "cats": ["solo", "battle"]}, {"id": 31, "title": "закономерные случайности (часть 2)", "cats": ["solo", "battle"]}, {"id": 32, "title": "зов природы", "cats": ["solo", "battle"]}, {"id": 33, "title": "иллюзия свободы (часть 1)", "cats": ["solo", "battle"]}, {"id": 34, "title": "иллюзия свободы (часть 2)", "cats": ["solo", "battle"]}, {"id": 35, "title": "ошибка навигатора (часть 1)", "cats": ["solo", "battle"]}, {"id": 36, "title": "ошибка навигатора (часть 2)", "cats": ["solo", "battle"]}, {"id": 37, "title": "этюд в багровых тонах (часть 1)", "cats": ["solo", "battle"]}, {"id": 38, "title": "этюд в багровых тонах (часть 2)", "cats": ["solo", "battle"]}, {"id": 39, "title": "смертельное оружие (часть 1)", "cats": ["solo", "battle"]}, {"id": 40, "title": "смертельное оружие (часть 2)", "cats": ["solo", "battle"]}, {"id": 41, "title": "приказано уничтожить (часть 1)", "cats": ["solo", "battle"]}, {"id": 42, "title": "приказано уничтожить (часть 2)", "cats": ["solo", "battle"]}, {"id": 43, "title": "жертва системы (за 3-е место)", "cats": ["battle"]}, {"id": 63, "title": "я живу в столице рэпа", "cats": ["solo", "battle"]}, {"id": 64, "title": "ходят слухи", "cats": ["battle"]}, {"id": 65, "title": "тайные желания", "cats": ["battle"]}, {"id": 66, "title": "ящик фокусника", "cats": ["battle"]}, {"id": 67, "title": "день физкультурника", "cats": ["battle"]}, {"id": 68, "title": "в стране женщин", "cats": ["battle"]}, {"id": 69, "title": "не говори ни слова", "cats": ["battle"]}, {"id": 70, "title": "нет связи (версия)", "cats": ["battle", "version"]}, {"id": 71, "title": "нет связи", "cats": ["battle"]}, {"id": 72, "title": "йети и дети", "cats": ["battle"]}, {"id": 73, "title": "кто похвалит меня лучше всех (версия)", "cats": ["battle", "version"]}, {"id": 74, "title": "кто похвалит меня лучше всех (версия2)", "cats": ["battle", "version"]}, {"id": 75, "title": "кто похвалит меня лучше всех", "cats": ["battle"]}, {"id": 77, "title": "kalikfornia love", "cats": ["solo", "makarevich"]}, {"id": 81, "title": "r.i.p. олди", "cats": ["solo", "makarevich"]}, {"id": 83, "title": "всё или ничего (tbs_round1)", "cats": ["battle"]}, {"id": 84, "title": "последствия необратимы (tbs_round2)", "cats": ["battle"]}, {"id": 85, "title": "крайние меры (tbs_round3)", "cats": ["battle"]}, {"id": 87, "title": "woodbridge на микро", "cats": ["solo", "makarevich"]}, {"id": 95, "title": "андрей вадимович", "cats": ["solo", "makarevich"]}, {"id": 97, "title": "арийская q2za на микро", "cats": ["solo", "makarevich"]}, {"id": 107, "title": "варим", "cats": ["solo", "makarevich"]}, {"id": 134, "title": "конструктор", "cats": ["solo", "makarevich"]}, {"id": 138, "title": "купол", "cats": ["solo", "makarevich"]}, {"id": 146, "title": "макаревич (фит. guf)", "cats": ["feat", "makarevich"]}, {"id": 154, "title": "не америка", "cats": ["solo", "makarevich"]}, {"id": 157, "title": "нуар (feat. double v)", "cats": ["feat", "makarevich"]}, {"id": 164, "title": "пархатое интро", "cats": ["solo", "makarevich"]}, {"id": 179, "title": "пусть говорят (фит. шахматист)", "cats": ["feat", "makarevich"]}, {"id": 194, "title": "сд (feat. шахматист)", "cats": ["feat", "makarevich"]}, {"id": 203, "title": "стал томат", "cats": ["solo", "makarevich"]}, {"id": 204, "title": "сталин фанк", "cats": ["solo", "makarevich"]}, {"id": 208, "title": "тень", "cats": ["solo", "makarevich"]}, {"id": 217, "title": "хип-хоп 2010", "cats": ["solo", "makarevich"]}, {"id": 220, "title": "хлеб (feat. ваня айван)", "cats": ["feat", "makarevich"]}, {"id": 253, "title": "Приключения на районе (Красные Пидарасы: Дядя Женя, Бабангида, Sheridan, Айван)", "cats": ["feat", "battle"]}, {"id": 254, "title": "Слышу звон, не знаю где он (Красные Пидарасы)", "cats": ["feat", "battle"]}, {"id": 255, "title": "Вместе весело шагать по просторам (командный баттл)", "cats": ["battle"]}, {"id": 256, "title": "Клуб (2-й Баттл неумеющих читать MC)", "cats": ["battle"]}];
const KEY='babangida-tierlist-v2';
let custom=[];
let state={};
let activeId=null;

function load(){
  try{
    const saved=JSON.parse(localStorage.getItem(KEY)||'{}');
    state=saved.state||{};
    custom=saved.custom||[];
  }catch(e){state={};custom=[]}
}
function save(){localStorage.setItem(KEY,JSON.stringify({state,custom}));}
function allTracks(){return initial.concat(custom)}
function makeCard(track){
  const el=document.createElement('div');
  el.className='card'; el.draggable=true; el.dataset.id=track.id; el.dataset.cats=track.cats.join(',');
  el.innerHTML=`<div class="title">${escapeHtml(track.title)}</div><div class="tags">${track.cats.join(' · ')}</div><button class="move" title="Переместить">↕</button>`;
  el.addEventListener('dragstart',e=>{e.dataTransfer.setData('text/plain',String(track.id)); setTimeout(()=>el.style.opacity=.35,0)});
  el.addEventListener('dragend',()=>el.style.opacity=1);
  el.querySelector('.move').addEventListener('click',()=>openMove(track.id,track.title));
  return el;
}
function escapeHtml(s){return s.replace(/[&<>"']/g,m=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]))}
function render(){
  document.querySelectorAll('.drop').forEach(z=>z.innerHTML='');
  allTracks().forEach(track=>{
    const zone=state[track.id]||'unranked';
    const target=document.querySelector(`[data-zone="${CSS.escape(zone)}"]`)||document.getElementById('unranked');
    target.appendChild(makeCard(track));
  });
  applyFilter(); stats();
}
document.querySelectorAll('.drop').forEach(zone=>{
  zone.addEventListener('dragover',e=>{e.preventDefault();zone.classList.add('dragover')});
  zone.addEventListener('dragleave',()=>zone.classList.remove('dragover'));
  zone.addEventListener('drop',e=>{
    e.preventDefault();zone.classList.remove('dragover');
    const id=e.dataTransfer.getData('text/plain');
    state[id]=zone.dataset.zone; save(); render();
  });
});
function applyFilter(){
  const q=document.getElementById('search').value.trim().toLowerCase();
  const f=document.getElementById('filter').value;
  let shown=0;
  document.querySelectorAll('.card').forEach(c=>{
    const title=c.querySelector('.title').textContent.toLowerCase();
    const cats=c.dataset.cats.split(',');
    const ok=(!q||title.includes(q))&&(f==='all'||cats.includes(f));
    c.classList.toggle('hidden',!ok); if(ok)shown++;
  });
  document.getElementById('shown').textContent=`Показано: ${shown} / ${allTracks().length}`;
}
function stats(){
  const total=allTracks().length;
  const ranked=allTracks().filter(t=>state[t.id]&&state[t.id]!=='unranked').length;
  document.getElementById('ranked').textContent=`Распределено: ${ranked}`;
  document.getElementById('left').textContent=`Осталось: ${total-ranked}`;
}
document.getElementById('search').addEventListener('input',()=>{applyFilter();stats()});
document.getElementById('filter').addEventListener('change',()=>{applyFilter();stats()});
document.getElementById('shuffle').addEventListener('click',()=>{
  const u=[...document.querySelectorAll('#unranked .card')];
  for(let i=u.length-1;i>0;i--){const j=Math.floor(Math.random()*(i+1));[u[i],u[j]]=[u[j],u[i]]}
  u.forEach(x=>document.getElementById('unranked').appendChild(x));
});
document.getElementById('add').addEventListener('click',()=>{
  const title=prompt('Название трека:'); if(!title||!title.trim())return;
  const max=Math.max(...allTracks().map(x=>Number(x.id)||0),0);
  custom.push({id:max+1,title:title.trim(),cats:['custom']});
  save();render();
});
document.getElementById('export').addEventListener('click',()=>{
  const result={version:1,state,custom,tracks:allTracks().map(t=>({id:t.id,title:t.title,tier:state[t.id]||'unranked',categories:t.cats}))};
  const blob=new Blob([JSON.stringify(result,null,2)],{type:'application/json'});
  const a=document.createElement('a');a.href=URL.createObjectURL(blob);a.download='babangida-tierlist.json';a.click();URL.revokeObjectURL(a.href);
});
document.getElementById('import').addEventListener('click',()=>document.getElementById('importFile').click());
document.getElementById('importFile').addEventListener('change',async event=>{
  const file=event.target.files[0]; if(!file)return;
  try{
    const data=JSON.parse(await file.text());
    if(!data||typeof data!=='object'||Array.isArray(data)||typeof data.state!=='object'||!Array.isArray(data.custom))throw new Error('bad format');
    state=data.state; custom=data.custom; save(); render();
  }catch(error){alert('Не удалось импортировать JSON. Выбери файл, экспортированный с этой страницы.');}
  event.target.value='';
});
document.getElementById('reset').addEventListener('click',()=>{
  if(confirm('Сбросить весь прогресс и добавленные вручную треки?')){localStorage.removeItem(KEY);state={};custom=[];render()}
});
function openMove(id,title){
  activeId=id;document.getElementById('modalTitle').textContent=title;
  document.getElementById('modalTier').value=state[id]||'unranked';
  document.getElementById('modal').classList.add('on');
}
document.getElementById('modalOk').addEventListener('click',()=>{
  state[activeId]=document.getElementById('modalTier').value;save();render();document.getElementById('modal').classList.remove('on');
});
document.getElementById('modalClose').addEventListener('click',()=>document.getElementById('modal').classList.remove('on'));
load();render();
</script>
</body>
</html>
