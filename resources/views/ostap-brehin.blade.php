<!doctype html>
<html lang="uk">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#080b10">
    <title>Ostap Brehin — OpenClaw Operator</title>
    <style>
        :root {
            color-scheme: dark;
            --bg: #080b10;
            --panel: rgba(18, 23, 31, .72);
            --panel-solid: #111720;
            --line: rgba(255, 255, 255, .1);
            --text: #f4f7fb;
            --muted: #8d99aa;
            --acid: #c8ff52;
            --cyan: #53ddff;
            --orange: #ff8a4c;
            --danger: #ff6577;
            --shadow: 0 30px 80px rgba(0, 0, 0, .42);
        }

        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            margin: 0;
            min-width: 320px;
            min-height: 100vh;
            overflow-x: hidden;
            background:
                radial-gradient(circle at 14% -5%, rgba(83, 221, 255, .12), transparent 30rem),
                radial-gradient(circle at 92% 18%, rgba(200, 255, 82, .09), transparent 28rem),
                var(--bg);
            color: var(--text);
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        body::before {
            position: fixed;
            inset: 0;
            z-index: -1;
            content: "";
            opacity: .34;
            pointer-events: none;
            background-image:
                linear-gradient(rgba(255,255,255,.028) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.028) 1px, transparent 1px);
            background-size: 48px 48px;
            mask-image: linear-gradient(to bottom, #000 10%, transparent 86%);
        }

        button, a { font: inherit; }
        a { color: inherit; text-decoration: none; }

        .noise {
            position: fixed;
            inset: 0;
            z-index: 20;
            opacity: .035;
            pointer-events: none;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 180 180' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.85' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='.8'/%3E%3C/svg%3E");
        }

        .shell { width: min(1240px, calc(100% - 40px)); margin: 0 auto; }

        nav {
            position: sticky;
            top: 0;
            z-index: 10;
            border-bottom: 1px solid transparent;
            backdrop-filter: blur(18px);
            transition: border-color .25s, background .25s;
        }
        nav.scrolled { border-color: var(--line); background: rgba(8, 11, 16, .72); }
        .nav-inner { min-height: 74px; display: flex; align-items: center; justify-content: space-between; gap: 24px; }
        .brand { display: flex; align-items: center; gap: 12px; font-size: .86rem; font-weight: 800; letter-spacing: .12em; text-transform: uppercase; }
        .brand-mark { width: 34px; aspect-ratio: 1; display: grid; place-items: center; border: 1px solid var(--acid); border-radius: 10px; color: var(--acid); box-shadow: inset 0 0 18px rgba(200,255,82,.08); }
        .nav-links { display: flex; align-items: center; gap: 26px; color: var(--muted); font-size: .83rem; }
        .nav-links a:hover { color: var(--text); }
        .status { display: flex; align-items: center; gap: 8px; color: #cdd6e1; }
        .status-dot { width: 7px; aspect-ratio: 1; border-radius: 50%; background: var(--acid); box-shadow: 0 0 0 0 rgba(200,255,82,.6); animation: ping 2.2s infinite; }

        .hero { min-height: calc(100vh - 74px); display: grid; grid-template-columns: 1.08fr .92fr; align-items: center; gap: clamp(48px, 8vw, 100px); padding: 76px 0 90px; }
        .kicker { display: flex; align-items: center; gap: 10px; margin-bottom: 24px; color: var(--acid); font: 700 .72rem/1 ui-monospace, SFMono-Regular, Consolas, monospace; letter-spacing: .16em; text-transform: uppercase; }
        .kicker::before { width: 34px; height: 1px; content: ""; background: currentColor; }
        h1 { max-width: 780px; margin: 0; font-size: clamp(3.7rem, 7.4vw, 7.8rem); line-height: .87; letter-spacing: -.075em; }
        h1 span { display: block; color: transparent; -webkit-text-stroke: 1px rgba(244,247,251,.48); }
        .hero-copy { max-width: 620px; margin: 30px 0 0; color: var(--muted); font-size: clamp(1rem, 1.5vw, 1.2rem); line-height: 1.7; }
        .hero-copy strong { color: var(--text); }
        .actions { display: flex; flex-wrap: wrap; align-items: center; gap: 13px; margin-top: 34px; }
        .button { display: inline-flex; align-items: center; justify-content: center; gap: 10px; min-height: 50px; padding: 0 20px; border: 1px solid var(--line); border-radius: 13px; background: rgba(255,255,255,.035); color: var(--text); cursor: pointer; transition: .22s ease; }
        .button:hover { translate: 0 -2px; border-color: rgba(255,255,255,.24); background: rgba(255,255,255,.075); }
        .button.primary { border-color: var(--acid); background: var(--acid); color: #0b0e12; font-weight: 800; box-shadow: 0 12px 38px rgba(200,255,82,.13); }
        .button.primary:hover { box-shadow: 0 16px 46px rgba(200,255,82,.22); }
        .arrow { font-size: 1.1em; }

        .agent-window { position: relative; border: 1px solid var(--line); border-radius: 24px; background: linear-gradient(145deg, rgba(21,27,36,.94), rgba(10,14,20,.78)); box-shadow: var(--shadow); overflow: hidden; transform: perspective(1000px) rotateY(-3deg) rotateX(2deg); transition: transform .2s ease-out; }
        .window-bar { height: 52px; display: flex; align-items: center; justify-content: space-between; padding: 0 18px; border-bottom: 1px solid var(--line); color: var(--muted); font: .72rem ui-monospace, SFMono-Regular, Consolas, monospace; }
        .dots { display: flex; gap: 7px; }
        .dots i { width: 8px; aspect-ratio: 1; border-radius: 50%; background: #39414d; }
        .dots i:nth-child(1) { background: var(--danger); } .dots i:nth-child(2) { background: #ffd05c; } .dots i:nth-child(3) { background: var(--acid); }
        .terminal { min-height: 390px; padding: 26px; font: 500 .82rem/1.75 ui-monospace, SFMono-Regular, Consolas, monospace; }
        .terminal-row { display: flex; gap: 12px; opacity: 0; translate: 0 8px; animation: terminal-in .45s forwards; }
        .prompt { color: var(--acid); } .cmd { color: #e9eef5; } .dim { color: #687483; }
        .terminal-card { margin: 20px 0; padding: 18px; border: 1px solid rgba(83,221,255,.22); border-radius: 14px; background: rgba(83,221,255,.045); }
        .task-line { display: grid; grid-template-columns: 18px 1fr auto; align-items: center; gap: 10px; padding: 6px 0; color: #cfd8e4; }
        .check { width: 16px; aspect-ratio: 1; display: grid; place-items: center; border-radius: 50%; background: rgba(200,255,82,.13); color: var(--acid); font-size: .65rem; }
        .task-time { color: #647181; font-size: .7rem; }
        .cursor { display: inline-block; width: 8px; height: 1.05em; margin-left: 5px; vertical-align: -.18em; background: var(--acid); animation: blink 1s steps(1) infinite; }
        .floating-chip { position: absolute; display: flex; align-items: center; gap: 9px; padding: 11px 14px; border: 1px solid var(--line); border-radius: 999px; background: rgba(13,17,23,.9); box-shadow: 0 12px 28px rgba(0,0,0,.28); font-size: .73rem; backdrop-filter: blur(10px); }
        .chip-a { right: -24px; top: 82px; } .chip-b { left: -30px; bottom: 72px; }

        section.block { padding: 110px 0; }
        .section-label { margin: 0 0 18px; color: var(--acid); font: 700 .7rem ui-monospace, SFMono-Regular, Consolas, monospace; letter-spacing: .17em; text-transform: uppercase; }
        h2 { max-width: 850px; margin: 0; font-size: clamp(2.3rem, 5vw, 4.7rem); line-height: 1; letter-spacing: -.055em; }
        .section-intro { max-width: 600px; margin-top: 22px; color: var(--muted); line-height: 1.7; }

        .bento { display: grid; grid-template-columns: repeat(12, 1fr); gap: 16px; margin-top: 46px; }
        .card { position: relative; min-height: 250px; padding: 28px; border: 1px solid var(--line); border-radius: 20px; overflow: hidden; background: var(--panel); transition: transform .25s, border-color .25s; }
        .card:hover { transform: translateY(-5px); border-color: rgba(200,255,82,.32); }
        .card.large { grid-column: span 7; } .card.medium { grid-column: span 5; } .card.third { grid-column: span 4; }
        .card-number { color: #596575; font: .7rem ui-monospace, SFMono-Regular, Consolas, monospace; }
        .card h3 { margin: 52px 0 10px; font-size: 1.45rem; letter-spacing: -.025em; }
        .card p { max-width: 430px; margin: 0; color: var(--muted); line-height: 1.65; }
        .card-glow { position: absolute; width: 210px; aspect-ratio: 1; right: -70px; top: -80px; border-radius: 50%; background: var(--cyan); opacity: .08; filter: blur(12px); }
        .mini-flow { display: flex; align-items: center; gap: 8px; margin-top: 28px; }
        .mini-flow span { padding: 8px 10px; border: 1px solid var(--line); border-radius: 9px; color: #bcc6d2; font: .68rem ui-monospace, SFMono-Regular, Consolas, monospace; }
        .mini-flow i { width: 20px; height: 1px; background: #3b4653; }

        .workflow { margin-top: 48px; display: grid; grid-template-columns: 250px 1fr; gap: 16px; }
        .workflow-tabs { display: grid; gap: 9px; align-content: start; }
        .workflow-tab { width: 100%; padding: 17px 18px; border: 1px solid var(--line); border-radius: 13px; background: transparent; color: var(--muted); text-align: left; cursor: pointer; transition: .2s; }
        .workflow-tab.active { border-color: rgba(200,255,82,.45); background: rgba(200,255,82,.075); color: var(--text); }
        .workflow-board { min-height: 360px; padding: 30px; border: 1px solid var(--line); border-radius: 20px; background: var(--panel); }
        .workflow-view { display: none; animation: fade-up .35s ease both; }
        .workflow-view.active { display: block; }
        .workflow-top { display: flex; align-items: flex-start; justify-content: space-between; gap: 24px; }
        .workflow-top h3 { margin: 0 0 8px; font-size: 1.5rem; }
        .workflow-top p { margin: 0; color: var(--muted); }
        .badge { padding: 7px 10px; border-radius: 999px; background: rgba(200,255,82,.1); color: var(--acid); font: .68rem ui-monospace, SFMono-Regular, Consolas, monospace; white-space: nowrap; }
        .pipeline { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-top: 46px; }
        .node { position: relative; min-height: 120px; padding: 16px; border: 1px solid var(--line); border-radius: 14px; background: rgba(255,255,255,.025); }
        .node::after { position: absolute; top: 50%; left: calc(100% + 1px); width: 10px; height: 1px; content: ""; background: #45505d; }
        .node:last-child::after { display: none; }
        .node small { color: #667383; font: .66rem ui-monospace, SFMono-Regular, Consolas, monospace; }
        .node b { display: block; margin-top: 22px; font-size: .82rem; }
        .node.done { border-color: rgba(200,255,82,.28); } .node.done small { color: var(--acid); }

        .metrics { display: grid; grid-template-columns: repeat(4, 1fr); border-top: 1px solid var(--line); border-bottom: 1px solid var(--line); }
        .metric { padding: 40px 24px; border-right: 1px solid var(--line); }
        .metric:last-child { border-right: 0; }
        .metric strong { display: block; font-size: clamp(2rem, 4vw, 4rem); letter-spacing: -.06em; }
        .metric span { color: var(--muted); font-size: .8rem; }

        .cta { padding: 115px 0 130px; text-align: center; }
        .cta-box { position: relative; padding: clamp(50px, 8vw, 90px) 24px; border: 1px solid rgba(200,255,82,.24); border-radius: 28px; overflow: hidden; background: linear-gradient(135deg, rgba(200,255,82,.08), rgba(83,221,255,.055)); }
        .cta-box::after { position: absolute; width: 420px; aspect-ratio: 1; left: 50%; bottom: -360px; translate: -50% 0; border-radius: 50%; content: ""; background: var(--acid); filter: blur(70px); opacity: .18; }
        .cta h2 { margin-inline: auto; }
        .cta p { max-width: 570px; margin: 22px auto 32px; color: var(--muted); line-height: 1.7; }
        footer { display: flex; justify-content: space-between; gap: 20px; padding: 28px 0; border-top: 1px solid var(--line); color: #667181; font-size: .75rem; }

        .command-palette { position: fixed; inset: 0; z-index: 50; display: none; place-items: start center; padding-top: 14vh; background: rgba(4,6,9,.7); backdrop-filter: blur(10px); }
        .command-palette.open { display: grid; }
        .palette-box { width: min(620px, calc(100% - 30px)); border: 1px solid rgba(255,255,255,.18); border-radius: 18px; overflow: hidden; background: #111720; box-shadow: 0 40px 100px rgba(0,0,0,.6); animation: fade-up .2s ease both; }
        .palette-head { display: flex; align-items: center; gap: 12px; padding: 18px; border-bottom: 1px solid var(--line); }
        .palette-head input { width: 100%; border: 0; outline: 0; background: transparent; color: var(--text); font-size: 1rem; }
        .key { padding: 4px 7px; border: 1px solid var(--line); border-radius: 6px; color: var(--muted); font: .65rem ui-monospace, monospace; }
        .palette-items { padding: 10px; }
        .palette-item { display: flex; justify-content: space-between; padding: 13px 14px; border-radius: 10px; color: #c9d2dd; cursor: pointer; }
        .palette-item:hover, .palette-item.active { background: rgba(200,255,82,.08); color: var(--text); }
        .palette-item small { color: var(--muted); }

        @keyframes ping { 70%, 100% { box-shadow: 0 0 0 9px rgba(200,255,82,0); } }
        @keyframes blink { 50% { opacity: 0; } }
        @keyframes terminal-in { to { opacity: 1; translate: 0 0; } }
        @keyframes fade-up { from { opacity: 0; translate: 0 10px; } to { opacity: 1; translate: 0 0; } }

        @media (max-width: 900px) {
            .nav-links a:not(.status) { display: none; }
            .hero { grid-template-columns: 1fr; min-height: auto; padding-top: 70px; }
            .agent-window { transform: none; }
            .chip-a { right: 12px; } .chip-b { left: 12px; }
            .card.large, .card.medium { grid-column: span 12; }
            .card.third { grid-column: span 6; }
            .workflow { grid-template-columns: 1fr; }
            .workflow-tabs { grid-template-columns: repeat(3, 1fr); }
            .metrics { grid-template-columns: repeat(2, 1fr); }
            .metric:nth-child(2) { border-right: 0; }
            .metric:nth-child(-n+2) { border-bottom: 1px solid var(--line); }
        }

        @media (max-width: 620px) {
            .shell { width: min(100% - 24px, 1240px); }
            .nav-inner { min-height: 64px; }
            .brand { font-size: .72rem; }
            .nav-links .status { font-size: 0; }
            .hero { padding: 54px 0 72px; }
            h1 { font-size: clamp(3.2rem, 17vw, 5rem); }
            section.block { padding: 78px 0; }
            .terminal { min-height: 350px; padding: 20px 16px; font-size: .72rem; }
            .floating-chip { display: none; }
            .card.third { grid-column: span 12; }
            .workflow-tabs { grid-template-columns: 1fr; }
            .pipeline { grid-template-columns: 1fr 1fr; }
            .node::after { display: none; }
            .metrics { grid-template-columns: 1fr; }
            .metric, .metric:nth-child(2) { border-right: 0; border-bottom: 1px solid var(--line); }
            .metric:last-child { border-bottom: 0; }
            footer { flex-direction: column; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation-duration: .01ms !important; animation-iteration-count: 1 !important; scroll-behavior: auto !important; }
        }
    </style>
</head>
<body>
    <div class="noise" aria-hidden="true"></div>
    <nav id="nav">
        <div class="shell nav-inner">
            <a class="brand" href="#top"><span class="brand-mark">OB</span> Ostap Brehin</a>
            <div class="nav-links">
                <a href="#system">Система</a>
                <a href="#workflow">Workflow</a>
                <a href="#contact">Контакт</a>
                <a class="status" href="#system"><i class="status-dot"></i> Agent online</a>
            </div>
        </div>
    </nav>

    <main id="top">
        <div class="shell hero">
            <div>
                <div class="kicker">Developer × AI operator</div>
                <h1>Ostap<br><span>Brehin.</span></h1>
                <p class="hero-copy">Я будую не чат-ботів, а <strong>робочі AI-системи</strong>: агент бачить контекст, користується інструментами, виконує задачі й залишає перевірений результат.</p>
                <div class="actions">
                    <a class="button primary" href="#workflow">Подивитись workflow <span class="arrow">↘</span></a>
                    <button class="button" id="commandButton" type="button">⌘ Відкрити control center</button>
                </div>
            </div>

            <div class="agent-window" id="agentWindow" aria-label="Демонстрація роботи агента">
                <div class="window-bar"><span class="dots"><i></i><i></i><i></i></span><span>openclaw / mission-control</span><span>04:21</span></div>
                <div class="terminal">
                    <div class="terminal-row" style="animation-delay:.15s"><span class="prompt">ostap@claw ~</span><span class="cmd">run deploy-audit</span></div>
                    <div class="terminal-row dim" style="animation-delay:.45s">→ Reading repository, logs and runtime state…</div>
                    <div class="terminal-card terminal-row" style="animation-delay:.8s">
                        <div style="width:100%">
                            <div class="task-line"><span class="check">✓</span><span>Context assembled</span><span class="task-time">0.8s</span></div>
                            <div class="task-line"><span class="check">✓</span><span>Risky changes isolated</span><span class="task-time">1.4s</span></div>
                            <div class="task-line"><span class="check">✓</span><span>Tests & health checks passed</span><span class="task-time">8.2s</span></div>
                            <div class="task-line"><span class="check">✓</span><span>Handoff prepared</span><span class="task-time">0.3s</span></div>
                        </div>
                    </div>
                    <div class="terminal-row" style="animation-delay:1.2s"><span class="prompt">result</span><span class="cmd">Production-ready. Zero guesswork.<i class="cursor"></i></span></div>
                </div>
                <span class="floating-chip chip-a"><i class="status-dot"></i> tools connected</span>
                <span class="floating-chip chip-b">↗ 12 tasks automated</span>
            </div>
        </div>

        <section class="block" id="system">
            <div class="shell">
                <p class="section-label">01 / Система</p>
                <h2>Один агент.<br>Повний цикл роботи.</h2>
                <p class="section-intro">OpenClaw перетворює AI на оператора всередині вашого процесу — з доступом до потрібних інструментів, памʼяттю, правилами та контролем.</p>
                <div class="bento">
                    <article class="card large">
                        <span class="card-glow"></span><span class="card-number">CAPABILITY_01</span>
                        <h3>Від запиту до готового результату</h3>
                        <p>Агент читає код, діагностує проблему, вносить зміни, запускає перевірки та повертає не пораду, а виконану роботу.</p>
                        <div class="mini-flow"><span>context</span><i></i><span>action</span><i></i><span>verify</span></div>
                    </article>
                    <article class="card medium">
                        <span class="card-number">CAPABILITY_02</span>
                        <h3>Памʼять без хаосу</h3>
                        <p>Рішення, домовленості та контекст проєкту переживають окремі сесії й не губляться між задачами.</p>
                    </article>
                    <article class="card third"><span class="card-number">CAPABILITY_03</span><h3>Інструменти</h3><p>Terminal, Git, браузер, API, месенджери й автоматизації в одному контрольованому середовищі.</p></article>
                    <article class="card third"><span class="card-number">CAPABILITY_04</span><h3>Guardrails</h3><p>Небезпечні дії зупиняються, зміни перевіряються, а секрети не потрапляють у чат або логи.</p></article>
                    <article class="card third"><span class="card-number">CAPABILITY_05</span><h3>На звʼязку 24/7</h3><p>Запускайте задачі з Telegram, отримуйте статус і забирайте результат там, де вже працюєте.</p></article>
                </div>
            </div>
        </section>

        <section class="block" id="workflow">
            <div class="shell">
                <p class="section-label">02 / Interactive demo</p>
                <h2>Перемкни сценарій.<br>Подивись, як це працює.</h2>
                <div class="workflow">
                    <div class="workflow-tabs" role="tablist" aria-label="Сценарії агента">
                        <button class="workflow-tab active" data-view="dev" type="button">01. Розробка</button>
                        <button class="workflow-tab" data-view="ops" type="button">02. Operations</button>
                        <button class="workflow-tab" data-view="research" type="button">03. Research</button>
                    </div>
                    <div class="workflow-board">
                        <div class="workflow-view active" data-panel="dev">
                            <div class="workflow-top"><div><h3>Ship feature</h3><p>Від короткого брифу до перевіреного pull-ready коду.</p></div><span class="badge">AUTONOMOUS</span></div>
                            <div class="pipeline"><div class="node done"><small>01 / INPUT</small><b>Зрозуміти задачу</b></div><div class="node done"><small>02 / BUILD</small><b>Змінити код</b></div><div class="node done"><small>03 / CHECK</small><b>Тести + review</b></div><div class="node done"><small>04 / OUTPUT</small><b>Готовий handoff</b></div></div>
                        </div>
                        <div class="workflow-view" data-panel="ops">
                            <div class="workflow-top"><div><h3>Keep systems healthy</h3><p>Логи, деплой, cron та інциденти без ручного мікроменеджменту.</p></div><span class="badge">OBSERVABLE</span></div>
                            <div class="pipeline"><div class="node done"><small>01 / WATCH</small><b>Зібрати сигнали</b></div><div class="node done"><small>02 / TRACE</small><b>Знайти причину</b></div><div class="node done"><small>03 / FIX</small><b>Безпечна дія</b></div><div class="node done"><small>04 / REPORT</small><b>Статус і докази</b></div></div>
                        </div>
                        <div class="workflow-view" data-panel="research">
                            <div class="workflow-top"><div><h3>Turn noise into a decision</h3><p>Агент збирає джерела, порівнює варіанти й формує дієву рекомендацію.</p></div><span class="badge">EVIDENCE-FIRST</span></div>
                            <div class="pipeline"><div class="node done"><small>01 / ASK</small><b>Уточнити рамки</b></div><div class="node done"><small>02 / SEARCH</small><b>Зібрати факти</b></div><div class="node done"><small>03 / FILTER</small><b>Відсіяти шум</b></div><div class="node done"><small>04 / DECIDE</small><b>Дати маршрут</b></div></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="shell metrics" aria-label="Ключові принципи">
            <div class="metric"><strong data-count="24">0</strong><span>години доступності</span></div>
            <div class="metric"><strong data-count="1">0</strong><span>точка керування</span></div>
            <div class="metric"><strong data-count="0">0</strong><span>магії без логів</span></div>
            <div class="metric"><strong data-count="100">0</strong><span>% вашого контролю</span></div>
        </div>

        <section class="cta" id="contact">
            <div class="shell cta-box">
                <p class="section-label">03 / Next move</p>
                <h2>Не ще один AI-чат.<br>Власний digital operator.</h2>
                <p>Зберемо OpenClaw під ваш стек, процеси та правила — від першого корисного сценарію до автономної системи.</p>
                <button class="button primary" id="demoButton" type="button">Запустити демо-команду <span class="arrow">→</span></button>
            </div>
        </section>
    </main>

    <footer class="shell"><span>© {{ date('Y') }} Ostap Brehin</span><span>Built for people who ship.</span></footer>

    <div class="command-palette" id="palette" role="dialog" aria-modal="true" aria-label="Control center">
        <div class="palette-box">
            <div class="palette-head"><span>⌕</span><input id="paletteInput" autocomplete="off" placeholder="Що має зробити агент?" aria-label="Команда агенту"><span class="key">ESC</span></div>
            <div class="palette-items">
                <div class="palette-item active" data-action="workflow"><span>Показати workflow</span><small>↵</small></div>
                <div class="palette-item" data-action="system"><span>Переглянути можливості</span><small>⌘1</small></div>
                <div class="palette-item" data-action="demo"><span>Запустити demo mission</span><small>⌘2</small></div>
            </div>
        </div>
    </div>

    <script>
        const nav = document.querySelector('#nav');
        const palette = document.querySelector('#palette');
        const paletteInput = document.querySelector('#paletteInput');
        const agentWindow = document.querySelector('#agentWindow');

        addEventListener('scroll', () => nav.classList.toggle('scrolled', scrollY > 16), { passive: true });

        document.querySelectorAll('.workflow-tab').forEach(tab => tab.addEventListener('click', () => {
            document.querySelectorAll('.workflow-tab').forEach(item => item.classList.remove('active'));
            document.querySelectorAll('.workflow-view').forEach(item => item.classList.remove('active'));
            tab.classList.add('active');
            document.querySelector(`[data-panel="${tab.dataset.view}"]`).classList.add('active');
        }));

        const openPalette = () => { palette.classList.add('open'); setTimeout(() => paletteInput.focus(), 30); };
        const closePalette = () => palette.classList.remove('open');
        document.querySelector('#commandButton').addEventListener('click', openPalette);
        document.querySelector('#demoButton').addEventListener('click', openPalette);
        palette.addEventListener('click', event => { if (event.target === palette) closePalette(); });
        addEventListener('keydown', event => {
            if ((event.metaKey || event.ctrlKey) && event.key.toLowerCase() === 'k') { event.preventDefault(); openPalette(); }
            if (event.key === 'Escape') closePalette();
        });

        document.querySelectorAll('.palette-item').forEach(item => item.addEventListener('click', () => {
            closePalette();
            if (item.dataset.action === 'demo') {
                document.querySelector('.terminal').animate([{ opacity: .35 }, { opacity: 1 }], { duration: 500 });
                scrollTo({ top: 0, behavior: 'smooth' });
                return;
            }
            document.querySelector(`#${item.dataset.action}`).scrollIntoView({ behavior: 'smooth' });
        }));

        if (matchMedia('(pointer:fine)').matches && !matchMedia('(prefers-reduced-motion: reduce)').matches) {
            agentWindow.addEventListener('pointermove', event => {
                const box = agentWindow.getBoundingClientRect();
                const x = (event.clientX - box.left) / box.width - .5;
                const y = (event.clientY - box.top) / box.height - .5;
                agentWindow.style.transform = `perspective(1000px) rotateY(${x * 7 - 3}deg) rotateX(${-y * 7 + 2}deg)`;
            });
            agentWindow.addEventListener('pointerleave', () => agentWindow.style.transform = 'perspective(1000px) rotateY(-3deg) rotateX(2deg)');
        }

        const metrics = document.querySelector('.metrics');
        const observer = new IntersectionObserver(entries => {
            if (!entries[0].isIntersecting) return;
            document.querySelectorAll('[data-count]').forEach(counter => {
                const target = Number(counter.dataset.count);
                const started = performance.now();
                const tick = now => {
                    const progress = Math.min(1, (now - started) / 900);
                    counter.textContent = Math.round(target * (1 - Math.pow(1 - progress, 3)));
                    if (progress < 1) requestAnimationFrame(tick);
                };
                requestAnimationFrame(tick);
            });
            observer.disconnect();
        }, { threshold: .35 });
        observer.observe(metrics);
    </script>
</body>
</html>
