<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Благодарность Единому — варианты</title>
    <style>
        :root{--ink:#24211d;--muted:#726b61;--paper:#fffdf8;--line:#e6ded1;--gold:#ae7d26;--gold2:#e8c77e;--wine:#6b3034;--shadow:0 18px 60px #3c2c1617}
        *{box-sizing:border-box}body{margin:0;color:var(--ink);background:radial-gradient(circle at 10% 0,#f6e7c7 0,transparent 34rem),linear-gradient(135deg,#f8f3e9,#eee4d5);font:16px/1.65 Georgia,"Times New Roman",serif;min-height:100vh}
        button,input,select,textarea{font:inherit}button{cursor:pointer}.shell{width:min(1180px,calc(100% - 32px));margin:auto;padding:34px 0 64px}.hero{display:grid;grid-template-columns:1fr auto;gap:24px;align-items:end;margin-bottom:22px}.eyebrow{text-transform:uppercase;letter-spacing:.18em;color:var(--gold);font:700 12px/1.2 system-ui,sans-serif}.hero h1{font-size:clamp(36px,7vw,74px);line-height:.98;margin:10px 0 12px;font-weight:500;max-width:850px}.hero p{margin:0;color:var(--muted);max-width:700px}.mode{border:1px solid #cdbd9f;border-radius:999px;padding:8px 14px;color:var(--muted);font:700 12px/1 system-ui,sans-serif;background:#fff8}
        .panel,.reader,.library,.editor{background:#fffdf8dd;border:1px solid #ffffffb8;border-radius:20px;box-shadow:var(--shadow);backdrop-filter:blur(12px)}.panel{padding:14px;display:grid;grid-template-columns:1fr 180px auto;gap:10px;margin-bottom:18px}.control{width:100%;border:1px solid var(--line);border-radius:11px;background:#fff;padding:11px 13px;color:var(--ink)}.button{border:0;border-radius:11px;padding:11px 16px;background:#302a23;color:#fff;font:700 14px/1 system-ui,sans-serif}.button.primary{background:linear-gradient(135deg,#c3943f,#8f6117)}.button.soft{background:#eee4d5;color:#4b4033}.button.danger{background:#f2dddd;color:#7d292f}.layout{display:grid;grid-template-columns:minmax(0,1.65fr) minmax(280px,.75fr);gap:18px}.reader{padding:clamp(22px,4vw,46px);min-height:560px;position:relative;overflow:hidden}.reader:before{content:"“";position:absolute;right:24px;top:-30px;color:#d6bd8d33;font-size:190px;line-height:1}.reader-head{position:relative;display:flex;justify-content:space-between;gap:16px;align-items:start;border-bottom:1px solid var(--line);padding-bottom:18px}.reader h2{font-size:clamp(25px,4vw,40px);line-height:1.15;margin:0;font-weight:500}.lang{display:inline-block;margin-top:7px;color:var(--gold);font:800 11px/1 system-ui,sans-serif;text-transform:uppercase;letter-spacing:.13em}.body{white-space:pre-line;font-size:clamp(18px,2.15vw,23px);line-height:1.72;margin:28px 0}.tags{display:flex;flex-wrap:wrap;gap:7px}.tag{border:1px solid #d8c9ae;border-radius:99px;background:#fff8;padding:6px 10px;color:#6d5734;font:700 12px/1 system-ui,sans-serif}.tag.active{background:#3b3024;color:#fff;border-color:#3b3024}.reader-actions{display:flex;gap:8px;flex-wrap:wrap;margin-top:24px}.library{padding:18px;max-height:720px;overflow:auto}.library h3,.editor h3{margin:0 0 14px;font:700 15px/1.2 system-ui,sans-serif}.variant-list{display:grid;gap:9px}.variant{width:100%;text-align:left;border:1px solid var(--line);border-radius:12px;background:#fff;padding:12px}.variant:hover,.variant.active{border-color:#b68b43;background:#fbf2df}.variant-title{font-weight:700}.variant-meta{color:var(--muted);font:12px/1.4 system-ui,sans-serif;margin-top:4px}.empty{padding:28px 8px;text-align:center;color:var(--muted)}.editor{padding:22px;margin-top:18px}.fields{display:grid;grid-template-columns:1fr 180px;gap:12px}.fields .wide{grid-column:1/-1}.editor textarea{min-height:220px;resize:vertical}.hint,.status{color:var(--muted);font:12px/1.5 system-ui,sans-serif}.status{margin-left:8px}.status.error{color:#a5232b}
        @media(max-width:820px){.hero{grid-template-columns:1fr}.panel{grid-template-columns:1fr}.layout{grid-template-columns:1fr}.library{max-height:none}.reader{min-height:0}.fields{grid-template-columns:1fr}.fields .wide{grid-column:auto}.shell{width:min(100% - 20px,1180px);padding-top:22px}}
    </style>
</head>
<body data-can-edit="{{ $canEdit ? 'true' : 'false' }}">
<main class="shell">
    <section class="hero">
        <div><div class="eyebrow">CraftChronicles · личная коллекция</div><h1>Благодарность Единому</h1><p>Выбирай нужный текст или доверься случаю. Фильтруй варианты по языку и смысловым категориям.</p></div>
        <div class="mode">{{ $canEdit ? 'Режим редактора' : 'Только просмотр' }}</div>
    </section>

    <section class="panel" aria-label="Фильтры">
        <input class="control" id="search" type="search" placeholder="Поиск по названию, тексту или тегам">
        <select class="control" id="language"><option value="all">Все языки</option><option value="ru">Русский</option><option value="uk">Українська</option><option value="en">English</option><option value="de">Deutsch</option></select>
        <button class="button primary" id="random" type="button">Случайный вариант</button>
    </section>

    <section class="layout">
        <article class="reader" aria-live="polite">
            <div class="reader-head"><div><h2 id="selectedTitle"></h2><span class="lang" id="selectedLanguage"></span></div></div>
            <div class="body" id="selectedBody"></div>
            <div class="tags" id="selectedTags"></div>
            <div class="reader-actions"><button class="button soft" id="copy" type="button">Копировать текст</button>@if($canEdit)<button class="button soft" id="edit" type="button">Редактировать</button><button class="button danger" id="remove" type="button">Удалить вариант</button>@endif<span class="status" id="readerStatus"></span></div>
        </article>
        <aside class="library"><h3>Варианты <span id="count"></span></h3><div class="tags" id="tagFilters"></div><div class="variant-list" id="variantList"></div></aside>
    </section>

    @if($canEdit)
    <section class="editor">
        <h3 id="formTitle">Добавить вариант</h3>
        <form id="addForm" class="fields">
            <input class="control" id="newTitle" maxlength="120" required placeholder="Название варианта">
            <select class="control" id="newLanguage" required><option value="ru">Русский</option><option value="uk">Українська</option><option value="en">English</option><option value="de">Deutsch</option></select>
            <textarea class="control wide" id="newBody" maxlength="10000" required placeholder="Текст варианта"></textarea>
            <input class="control wide" id="newTags" maxlength="500" placeholder="#здоровье #свобода #творчество">
            <div class="wide"><button class="button primary" id="formSubmit" type="submit">Добавить вариант</button><button class="button soft" id="cancelEdit" type="button" hidden>Отменить</button><span class="status" id="formStatus"></span><div class="hint">Теги разделяются пробелами или запятыми. Символ # можно не писать.</div></div>
        </form>
    </section>
    @endif
</main>
<script>
const CAN_EDIT=@json($canEdit),STORE_URL=@json(route('gratitude-variants.store')),CSRF=document.querySelector('meta[name="csrf-token"]').content,LANG={ru:'Русский',uk:'Українська',en:'English',de:'Deutsch'};
let variants=@json($variants),selectedId=variants[0]?.id??null,activeTags=new Set(),editingId=null;
const $=id=>document.getElementById(id),esc=value=>String(value??'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
function filtered(){const q=$('search').value.trim().toLocaleLowerCase(),language=$('language').value;return variants.filter(v=>(language==='all'||v.language===language)&&[...activeTags].every(tag=>(v.tags||[]).includes(tag))&&(!q||`${v.title} ${v.body} ${(v.tags||[]).join(' ')}`.toLocaleLowerCase().includes(q)))}
function choose(id){selectedId=id;renderSelected();renderList()}
function renderSelected(){const variant=variants.find(v=>v.id===selectedId);if(!variant){$('selectedTitle').textContent='Нет подходящих вариантов';$('selectedLanguage').textContent='';$('selectedBody').textContent='Измени фильтры или добавь новый вариант.';$('selectedTags').innerHTML='';if($('edit'))$('edit').disabled=true;if($('remove'))$('remove').disabled=true;return}$('selectedTitle').textContent=variant.title;$('selectedLanguage').textContent=LANG[variant.language]||variant.language;$('selectedBody').textContent=variant.body;$('selectedTags').innerHTML=(variant.tags||[]).map(tag=>`<button type="button" class="tag" data-tag="${esc(tag)}">#${esc(tag)}</button>`).join('');$('selectedTags').querySelectorAll('[data-tag]').forEach(button=>button.onclick=()=>{activeTags.add(button.dataset.tag);render()});if($('edit'))$('edit').disabled=false;if($('remove'))$('remove').disabled=false}
function renderTags(){const tags=[...new Set(variants.flatMap(v=>v.tags||[]))].sort((a,b)=>a.localeCompare(b));activeTags=new Set([...activeTags].filter(tag=>tags.includes(tag)));$('tagFilters').innerHTML=tags.map(tag=>`<button type="button" class="tag ${activeTags.has(tag)?'active':''}" data-filter-tag="${esc(tag)}">#${esc(tag)}</button>`).join('');$('tagFilters').querySelectorAll('[data-filter-tag]').forEach(button=>button.onclick=()=>{const tag=button.dataset.filterTag;activeTags.has(tag)?activeTags.delete(tag):activeTags.add(tag);render()})}
function renderList(){const list=filtered();$('count').textContent=`(${list.length})`;if(!list.some(v=>v.id===selectedId))selectedId=list[0]?.id??null;$('variantList').innerHTML=list.length?list.map(v=>`<button type="button" class="variant ${v.id===selectedId?'active':''}" data-id="${v.id}"><div class="variant-title">${esc(v.title)}</div><div class="variant-meta">${esc(LANG[v.language]||v.language)} · ${(v.tags||[]).slice(0,3).map(tag=>'#'+esc(tag)).join(' ')}</div></button>`).join(''):'<div class="empty">Ничего не найдено</div>';$('variantList').querySelectorAll('[data-id]').forEach(button=>button.onclick=()=>choose(Number(button.dataset.id)))}
function render(){renderTags();renderList();renderSelected()}
function random(){const list=filtered();if(!list.length)return;const index=crypto.getRandomValues(new Uint32Array(1))[0]%list.length;choose(list[index].id);document.querySelector('.reader').scrollIntoView({behavior:'smooth',block:'start'})}
$('search').oninput=render;$('language').onchange=render;$('random').onclick=random;$('copy').onclick=async()=>{const variant=variants.find(v=>v.id===selectedId);if(!variant)return;await navigator.clipboard.writeText(variant.body);$('readerStatus').textContent='Скопировано';setTimeout(()=>$('readerStatus').textContent='',1600)};
function resetForm(){editingId=null;$('addForm').reset();$('formTitle').textContent='Добавить вариант';$('formSubmit').textContent='Добавить вариант';$('cancelEdit').hidden=true}
if(CAN_EDIT){$('edit').onclick=()=>{const variant=variants.find(v=>v.id===selectedId);if(!variant)return;editingId=variant.id;$('newTitle').value=variant.title;$('newLanguage').value=variant.language;$('newBody').value=variant.body;$('newTags').value=(variant.tags||[]).map(tag=>`#${tag}`).join(' ');$('formTitle').textContent='Редактировать вариант';$('formSubmit').textContent='Сохранить изменения';$('cancelEdit').hidden=false;$('formStatus').textContent='';document.querySelector('.editor').scrollIntoView({behavior:'smooth',block:'start'});$('newTitle').focus()};$('cancelEdit').onclick=()=>{resetForm();$('formStatus').textContent='Редактирование отменено'};$('addForm').onsubmit=async event=>{event.preventDefault();const tags=$('newTags').value.split(/[\s,]+/).map(tag=>tag.replace(/^#/,'')).filter(Boolean),payload={title:$('newTitle').value.trim(),language:$('newLanguage').value,body:$('newBody').value.trim(),tags},status=$('formStatus'),id=editingId,statusText=editingId?'Обновлено':'Добавлено';status.textContent='Сохраняю…';status.classList.remove('error');try{const response=await fetch(id?`${STORE_URL}/${id}`:STORE_URL,{method:id?'PUT':'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':CSRF},body:JSON.stringify(payload)}),data=await response.json();if(!response.ok)throw new Error(data.message||Object.values(data.errors||{}).flat()[0]||'Не удалось сохранить');if(id){variants=variants.map(variant=>variant.id===id?data.variant:variant)}else{variants.push(data.variant)}selectedId=data.variant.id;resetForm();status.textContent=statusText;render()}catch(error){status.textContent=error.message;status.classList.add('error')}};$('remove').onclick=async()=>{const variant=variants.find(v=>v.id===selectedId);if(!variant||!confirm(`Удалить «${variant.title}»?`))return;const response=await fetch(`${STORE_URL}/${variant.id}`,{method:'DELETE',headers:{'Accept':'application/json','X-CSRF-TOKEN':CSRF}});if(!response.ok){$('readerStatus').textContent='Не удалось удалить';return}variants=variants.filter(v=>v.id!==variant.id);if(editingId===variant.id)resetForm();selectedId=filtered()[0]?.id??variants[0]?.id??null;render()}}
render();
</script>
</body>
</html>
