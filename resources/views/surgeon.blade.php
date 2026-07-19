<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Surgeon — текстовый симулятор</title>
    <style>
        :root { color-scheme: dark; --red:#e5484d; --green:#46a758; --paper:#d6d2c4; }
        * { box-sizing:border-box; }
        body { margin:0; min-height:100vh; background:#0a0d0f; color:var(--paper); font:16px/1.55 ui-monospace,SFMono-Regular,Consolas,monospace; }
        main { width:min(900px,100%); margin:auto; padding:32px 18px 60px; }
        header { border-bottom:1px solid #394047; padding-bottom:18px; margin-bottom:22px; }
        h1 { margin:0; color:#fff; letter-spacing:.08em; font-size:clamp(30px,6vw,52px); }
        .subtitle,.dim { color:#89939d; }
        .monitor { display:grid; grid-template-columns:repeat(4,1fr); gap:8px; margin:18px 0; }
        .vital { background:#11161a; border:1px solid #293037; padding:10px; text-align:center; }
        .vital b { display:block; color:#63e6be; font-size:20px; }
        .screen { min-height:290px; padding:22px; background:#0d1215; border:1px solid #394047; box-shadow:inset 0 0 35px #000; }
        #log { white-space:pre-wrap; color:#a7f3d0; margin-bottom:20px; }
        #scene { font-size:18px; color:#fff; }
        #choices { display:grid; gap:10px; margin-top:18px; }
        button { text-align:left; border:1px solid #4b5563; background:#171d22; color:#e5e7eb; padding:13px 15px; font:inherit; cursor:pointer; }
        button:hover,button:focus { border-color:#e5484d; background:#22171a; outline:none; }
        button.primary { text-align:center; background:#9f262b; border-color:#e5484d; font-weight:700; }
        .danger { color:#ff6369!important; }.success { color:#63e6be!important; }
        .progress { display:flex; gap:6px; margin:15px 0; }.progress i { height:5px; flex:1; background:#293037; }.progress i.done { background:#e5484d; }
        footer { margin-top:18px; font-size:12px; color:#66717b; }
        @media(max-width:600px){.monitor{grid-template-columns:repeat(2,1fr)}.screen{padding:16px}}
    </style>
</head>
<body><main>
    <header><h1>SURGEON</h1><div class="subtitle">Текстовый симулятор · 2 операции · решения имеют последствия</div></header>
    <div id="progress" class="progress"></div>
    <section id="monitor" class="monitor" hidden>
        <div class="vital">ПУЛЬС<b id="pulse">78</b></div><div class="vital">ДАВЛЕНИЕ<b id="pressure">120/80</b></div>
        <div class="vital">SpO₂<b id="oxygen">99%</b></div><div class="vital">СОСТОЯНИЕ<b id="health">100%</b></div>
    </section>
    <section class="screen"><div id="log"></div><div id="scene"></div><div id="choices"></div></section>
    <footer>Это игра, а не медицинская инструкция.</footer>
</main>
<script>
const levels=[
 {title:'УРОВЕНЬ 1 — ОСТРЫЙ АППЕНДИЦИТ',intro:'Пациент: 24 года. Боль справа внизу живота, температура 38.1°C, тошнота. УЗИ показывает воспалённый аппендикс.',steps:[
  {q:'Первое действие?',a:[['Проверить аллергию, анализы и получить согласие',1,'Подготовка завершена.'],['Сразу сделать разрез',-18,'Пропущена предоперационная проверка.'],['Отправить пациента домой',-35,'Состояние ухудшается.']]},
  {q:'Как снизить риск инфекции?',a:[['Ввести профилактический антибиотик',1,'Антибиотик введён вовремя.'],['Только вымыть руки',-14,'Этого недостаточно.'],['Ничего — операция короткая',-24,'Риск инфекции резко вырос.']]},
  {q:'Аппендикс выделен. Что дальше?',a:[['Перевязать основание и удалить аппендикс',1,'Источник воспаления удалён.'],['Удалить ближайший лимфоузел',-20,'Удалена не та структура.'],['Закрыть рану, не удаляя аппендикс',-30,'Причина болезни осталась.']]},
  {q:'Перед закрытием раны?',a:[['Проверить гемостаз и выполнить контроль инструментов',1,'Кровотечения нет, инструменты на месте.'],['Закрыть как можно быстрее',-16,'Контроль пропущен.']]}
 ]},
 {title:'УРОВЕНЬ 2 — ПРОНИКАЮЩАЯ ТРАВМА',intro:'Пациент: 37 лет. Колотая рана грудной клетки. Давление падает, дыхание слева ослаблено, пациент теряет сознание.',steps:[
  {q:'Что приоритетно?',a:[['Оценить дыхательные пути, дыхание и кровообращение',1,'Команда действует по травматологическому алгоритму.'],['Сначала подробно опросить родственников',-22,'Потеряно критическое время.'],['Сделать МРТ',-30,'Пациент нестабилен для долгой диагностики.']]},
  {q:'Слева напряжённый пневмоторакс. Решение?',a:[['Немедленно декомпрессировать грудную клетку',1,'Дыхание улучшается, SpO₂ растёт.'],['Ждать рентген',-24,'При очевидной угрозе ожидание опасно.'],['Дать только обезболивающее',-28,'Причина шока не устранена.']]},
  {q:'Из дренажа продолжается массивная кровопотеря.',a:[['Активировать массивную трансфузию и готовить операционную',1,'Кровь подана, пациент доставлен в операционную.'],['Наблюдать ещё час',-35,'Продолжается геморрагический шок.'],['Удалить дренаж',-26,'Контроль кровотечения потерян.']]},
  {q:'В операционной найден повреждённый сосуд.',a:[['Временно контролировать сосуд, затем восстановить или лигировать',1,'Кровотечение остановлено.'],['Сразу закрыть грудную клетку',-40,'Кровотечение продолжается.'],['Повысить давление без контроля сосуда',-25,'Кровопотеря усиливается.']]},
  {q:'Финальная проверка?',a:[['Гемостаз, вентиляция лёгкого, дренажи и подсчёт инструментов',1,'Операция завершена безопасно.'],['Сразу перевести пациента в палату',-20,'Пропущены обязательные проверки.']]}
 ]}
];
let level=-1,step=0,health=100,mistakes=0;
const scene=document.querySelector('#scene'),choices=document.querySelector('#choices'),log=document.querySelector('#log');
function buttons(items){choices.innerHTML='';items.forEach(([text,fn,primary=false])=>{const b=document.createElement('button');b.textContent=text;b.className=primary?'primary':'';b.onclick=fn;choices.appendChild(b)})}
function start(){level=0;step=0;health=100;mistakes=0;document.querySelector('#monitor').hidden=false;showIntro()}
function showIntro(){const l=levels[level];log.className='';log.textContent=l.title+'\n\n'+l.intro;scene.textContent='Вы готовы принять пациента?';buttons([['Начать операцию',showStep,true]]);draw()}
function showStep(){const s=levels[level].steps[step];log.textContent=`${levels[level].title}\nЭтап ${step+1}/${levels[level].steps.length}`;scene.textContent=s.q;buttons(s.a.map(a=>[a[0],()=>answer(a[1],a[2])]))}
function answer(score,text){if(score<0){health=Math.max(0,health+score);mistakes++;}else health=Math.min(100,health+3);log.className=score<0?'danger':'success';log.textContent=(score<0?'ОШИБКА: ':'ВЕРНО: ')+text;draw();if(health<=0){scene.textContent='Пациент погиб. Операция окончена.';buttons([['Начать заново',start,true]]);return}step++;if(step>=levels[level].steps.length){scene.textContent=`Операция завершена. Состояние пациента: ${health}%. Ошибок: ${mistakes}.`;buttons(level===0?[["Перейти на уровень 2",()=>{level=1;step=0;showIntro()},true]]:[["Пройти заново",start,true]])}else buttons([['Продолжить',showStep,true]])}
function draw(){document.querySelector('#health').textContent=health+'%';document.querySelector('#pulse').textContent=Math.round(78+(100-health)*.7);document.querySelector('#oxygen').textContent=Math.max(72,99-Math.round((100-health)*.2))+'%';document.querySelector('#pressure').textContent=Math.max(65,120-Math.round((100-health)*.45))+'/'+Math.max(40,80-Math.round((100-health)*.25));const total=levels.reduce((n,l)=>n+l.steps.length,0),done=(level<0?0:levels.slice(0,level).reduce((n,l)=>n+l.steps.length,0)+step);document.querySelector('#progress').innerHTML=Array.from({length:total},(_,i)=>`<i class="${i<done?'done':''}"></i>`).join('')}
scene.textContent='Две операции. Пациент реагирует на каждое решение.';buttons([['Войти в операционную',start,true]]);draw();
</script></body></html>
