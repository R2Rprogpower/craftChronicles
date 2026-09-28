<!doctype html>
<html lang="{{ $locale }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#0b1020">
  <meta name="description" content="A hands-on presentation about OpenClaw for first-year students: capabilities, automation, security, and the first project.">
  <meta property="og:title" content="OpenClaw — from request to action">
  <meta property="og:description" content="10 slides on how to turn routine into AI-driven processes.">
  <title>OpenClaw — from request to action</title>
  <link rel="stylesheet" href="{{ asset('css/openclaw-presentation.css') }}">
  <link rel="stylesheet" href="{{ asset('build/css/openclaw-network-header.css') }}">
</head>
<body>
  <x-openclaw-network-header active="presentation" :locale="$locale" />
  <div class="presentation-shell" data-presentation>
    <header class="presentation-bar">
      <a class="brand" href="{{ route('root') }}" aria-label="Craft Chronicles is the main one">
        <span class="brand-mark">CC</span>
        <span>CRAFT CHRONICLES</span>
      </a>
      <div class="deck-meta" aria-live="polite">
        <span>OPENCLAW / PRACTICAL INTRODUCTION</span>
        <strong><span data-current>01</span> / 10</strong>
      </div>
      <div class="bar-actions">
        <a class="download-link" href="{{ asset('presentations/openclaw/openclaw-first-years-ua.pptx') }}" download>
          PPTX <span aria-hidden="true">↓</span>
        </a>
        <button class="icon-button" type="button" data-fullscreen aria-label="Open to full screen" title="Full screen">
          <span aria-hidden="true">⛶</span>
        </button>
      </div>
    </header>

    <main class="stage">
      <div class="deck" data-deck tabindex="0" aria-label="OpenClaw presentation, 10 slides">
        <section class="slide slide-dark slide-title is-active" data-slide aria-hidden="false">
          <div class="top-rule"></div>
          <div class="slide-content title-grid">
            <div>
              <span class="pill pill-dark">PRACTICAL INTRODUCTION</span>
              <h1>OpenClaw</h1>
              <h2>AI that not only responds, but acts</h2>
              <p class="lead">How to turn routine into automated processes, and curiosity into real experiments.</p>
              <div class="title-note">10 slides <b>•</b> 15 minutes <b>•</b> 1 idea to launch today</div>
            </div>
            <div class="signal-visual" aria-hidden="true">
              <div class="orbit orbit-large"><div class="orbit-mid"><div class="orbit-core">01</div></div></div>
              <div class="signal-card">
                <div><span>REQUEST</span><b>→</b><span>ACTION</span><b>→</b><span>RESULT</span></div>
                <strong>thought → system</strong>
              </div>
            </div>
          </div>
          <div class="slide-footer">OPENCLAW • INTRODUCTION FOR FRESHMEN <span>01</span></div>
        </section>

        <section class="slide slide-light" data-slide aria-hidden="true">
          <div class="top-rule"></div>
          <div class="slide-content">
            <header class="slide-heading">
              <span>02 • MAIN IDEA</span>
              <h2>OpenClaw is an operational layer for AI</h2>
              <p>It combines a language model, tools, memory, and communication channels into a single managed agent.</p>
            </header>
            <div class="flow-grid">
              <article class="flow-card accent-purple"><b>1</b><h3>Understands</h3><p>natural language and the context of the task</p></article>
              <i>›</i>
              <article class="flow-card accent-blue"><b>2</b><h3>Plans</h3><p>sequence of steps and checks</p></article>
              <i>›</i>
              <article class="flow-card accent-cyan"><b>3</b><h3>Works</h3><p>through files, browser, code, services</p></article>
              <i>›</i>
              <article class="flow-card accent-green"><b>4</b><h3>returns</h3><p>ready result and follow-up</p></article>
            </div>
            <div class="compare-strip">
              <div><span>Normal chat</span><strong>respond</strong></div>
              <div><span>OpenClaw</span><strong>response + action + automation</strong></div>
              <em>THE KEY DIFFERENCE</em>
            </div>
          </div>
          <div class="slide-footer">OPENCLAW • INTRODUCTION FOR FRESHMEN <span>02</span></div>
        </section>

        <section class="slide slide-dark" data-slide aria-hidden="true">
          <div class="top-rule"></div>
          <div class="slide-content">
            <header class="slide-heading">
              <span>03 • OPPORTUNITIES</span>
              <h2>What can be entrusted to the agent</h2>
              <p>Start with one process. Add tools only when you really need them.</p>
            </header>
            <div class="capability-grid">
              <article class="cap-card accent-cyan"><b>01</b><h3>Research</h3><p>collect sources, compare positions, identify gaps</p></article>
              <article class="cap-card accent-purple"><b>02</b><h3>Files and documents</h3><p>create a synopsis, table, report, presentation</p></article>
              <article class="cap-card accent-blue"><b>03</b><h3>Code and data</h3><p>explain the error, run the analysis, build the prototype</p></article>
              <article class="cap-card accent-yellow"><b>04</b><h3>Browser</h3><p>go through a multi-step web script under your control</p></article>
              <article class="cap-card accent-green"><b>05</b><h3>Communications</h3><p>prepare a response, find important, send after permission</p></article>
              <article class="cap-card accent-red"><b>06</b><h3>Automation</h3><p>remind, check changes, run regular tasks</p></article>
            </div>
          </div>
          <div class="slide-footer">OPENCLAW • INTRODUCTION FOR FRESHMEN <span>03</span></div>
        </section>

        <section class="slide slide-light" data-slide aria-hidden="true">
          <div class="top-rule"></div>
          <div class="slide-content">
            <header class="slide-heading">
              <span>04 • MECHANICS</span>
              <h2>How a request turns into a result</h2>
              <p>A person sets a goal and limits. The agent proposes a plan, uses the allowed tools, and checks the result.</p>
            </header>
            <div class="process-line">
              <article><b>01</b><h3>Target</h3><p>What should change?</p></article>
              <article><b>02</b><h3>Context</h3><p>Data, rules, deadline</p></article>
              <article><b>03</b><h3>Plan</h3><p>Steps and tools</p></article>
              <article><b>04</b><h3>Action</h3><p>Execution with permissions</p></article>
              <article class="is-final"><b>05</b><h3>Audit</h3><p>Quality, sources, risks</p></article>
            </div>
            <div class="human-loop"><span>PERSON IN CIRCLE</span><strong>confirms external actions • corrects the direction • makes the final decision</strong></div>
          </div>
          <div class="slide-footer">OPENCLAW • INTRODUCTION FOR FRESHMEN <span>04</span></div>
        </section>

        <section class="slide slide-dark" data-slide aria-hidden="true">
          <div class="top-rule"></div>
          <div class="slide-content">
            <header class="slide-heading">
              <span>05 • SCRIPT</span>
              <h2>One day of a student with OpenClaw</h2>
              <p>Not "do everything for me", but "help me understand, organize and check faster".</p>
            </header>
            <div class="timeline">
              <article class="accent-cyan"><time>08:00</time><b></b><h3>Day plan</h3><p>3 priorities + deadlines</p></article>
              <article class="accent-purple"><time>10:30</time><b></b><h3>In front of the couple</h3><p>5 theses on the topic + question</p></article>
              <article class="accent-blue"><time>14:00</time><b></b><h3>Research</h3><p>matrix of sources and contradictions</p></article>
              <article class="accent-yellow"><time>18:00</time><b></b><h3>Laboratory</h3><p>error analysis without ready-made "magic"</p></article>
              <article class="accent-green"><time>21:00</time><b></b><h3>Reflection</h3><p>what you learned + next step</p></article>
            </div>
            <div class="result-strip"><span>RESULT OF THE DAY</span><strong>less switching • more conscious work • visible progress</strong></div>
          </div>
          <div class="slide-footer">OPENCLAW • INTRODUCTION FOR FRESHMEN <span>05</span></div>
        </section>

        <section class="slide slide-light" data-slide aria-hidden="true">
          <div class="top-rule"></div>
          <div class="slide-content">
            <header class="slide-heading">
              <span>06 • AUTOMATION</span>
              <h2>5 easy ideas to get you started</h2>
              <p>Choose a repeatable action with a clear input, clear outcome, and low risk.</p>
            </header>
            <div class="idea-list">
              <article><b>01</b><h3>Deadline radar</h3><p>daily calendar check → list for 7 days</p><span>5–10 min</span></article>
              <article><b>02</b><h3>Course digest</h3><p>references and notes → short summary with questions</p><span>10–15 min</span></article>
              <article><b>03</b><h3>Matrix of sources</h3><p>topic → author, thesis, method, limitation, quote</p><span>15–25 min</span></article>
              <article><b>04</b><h3>Error log</h3><p>log or screenshot → hypothesis, test, conclusion</p><span>10–20 min</span></article>
              <article><b>05</b><h3>Weekly review</h3><p>tasks + notes → progress and next week's plan</p><span>15 min</span></article>
            </div>
            <small class="prototype-note">time for the first prototype, not "once and for all"</small>
          </div>
          <div class="slide-footer">OPENCLAW • INTRODUCTION FOR FRESHMEN <span>06</span></div>
        </section>

        <section class="slide slide-dark" data-slide aria-hidden="true">
          <div class="top-rule"></div>
          <div class="slide-content">
            <header class="slide-heading">
              <span>07 • COMMUNICATION</span>
              <h2>Strong query formula</h2>
              <p>The quality of the result increases when you describe not only the topic, but also the readiness criterion.</p>
            </header>
            <div class="prompt-formula">
              <article class="accent-purple"><h3>ROLE</h3><p>Who to be?</p></article><i>+</i>
              <article class="accent-blue"><h3>CONTEXT</h3><p>What is already known?</p></article><i>+</i>
              <article class="accent-cyan"><h3>RESULT</h3><p>What to create?</p></article><i>+</i>
              <article class="accent-yellow"><h3>LIMITS</h3><p>What not to do?</p></article><i>+</i>
              <article class="accent-green"><h3>AUDIT</h3><p>How to prove quality?</p></article>
            </div>
            <blockquote>
              <span>EXAMPLE</span>
              <p>"You are a research assistant. For a freshman, compare 5 reliable sources about the impact of generative AI on education. Create a table: thesis, method, limitations, references. Don't make up quotes. Mark the contradictions and propose 3 questions for discussion.</p>
              <small>Tip: Ask to show uncertainty—it's more helpful than a confident tone.</small>
            </blockquote>
          </div>
          <div class="slide-footer">OPENCLAW • INTRODUCTION FOR FRESHMEN <span>07</span></div>
        </section>

        <section class="slide slide-light" data-slide aria-hidden="true">
          <div class="top-rule"></div>
          <div class="slide-content">
            <header class="slide-heading">
              <span>08 • LIABILITY</span>
              <h2>AI assistant ≠ autopilot without rules</h2>
              <p>Automation amplifies both benefits and mistakes. Therefore, limits and validation are part of process design.</p>
            </header>
            <div class="safety-grid">
              <article class="accent-purple"><h3>Privacy</h3><p>Do not transmit personal data, access keys, closed materials.</p></article>
              <article class="accent-blue"><h3>Audit</h3><p>Check facts, quotes, formulas and references with primary sources.</p></article>
              <article class="accent-red"><h3>Permits</h3><p>Sending, publishing, payment and deletion - only after confirmation.</p></article>
              <article class="accent-green"><h3>Virtue</h3><p>Use AI to learn; don't disguise someone else's work as your own.</p></article>
            </div>
            <div class="pause-rule"><span>PAUSE RULE</span><strong>If the action is difficult to undo or affects others, stop and confirm it manually.</strong></div>
          </div>
          <div class="slide-footer">OPENCLAW • INTRODUCTION FOR FRESHMEN <span>08</span></div>
        </section>

        <section class="slide slide-dark" data-slide aria-hidden="true">
          <div class="top-rule"></div>
          <div class="slide-content">
            <header class="slide-heading">
              <span>09 • PRACTICE</span>
              <h2>The first project: "Radar of deadlines"</h2>
              <p>Goal: once a day to receive a short list of events and tasks for the next 7 days.</p>
            </header>
            <div class="project-steps">
              <article class="accent-purple"><b>1</b><h3>Exit</h3><p>calendar or task list</p></article>
              <article class="accent-blue"><b>2</b><h3>Rule</h3><p>only 7 days; sort by urgency</p></article>
              <article class="accent-cyan"><b>3</b><h3>Entrance</h3><p>3 blocks: today / soon / later</p></article>
              <article class="accent-green"><b>4</b><h3>CONTROL</h3><p>do not change anything without confirmation</p></article>
            </div>
            <div class="success-rule"><span>SUCCESS CRITERIA</span><strong>you haven't missed anything, the list is read in 30 seconds, mistakes are easy to correct</strong></div>
            <div class="mvp-note">MVP ≈ 30 minutes</div>
          </div>
          <div class="slide-footer">OPENCLAW • INTRODUCTION FOR FRESHMEN <span>09</span></div>
        </section>

        <section class="slide slide-light slide-finale" data-slide aria-hidden="true">
          <div class="top-rule"></div>
          <div class="slide-content finale-grid">
            <div>
              <span class="pill pill-light">10 • YOUR MOVE</span>
              <h2>7-day experiment</h2>
              <p class="lead">Pick one recurring task and turn it into a manageable process.</p>
              <div class="challenge-list">
                <article><b>1</b><strong>Watch</strong><span>where time is wasted</span></article>
                <article><b>2–3</b><strong>Describe</strong><span>input, output, limits</span></article>
                <article><b>4–5</b><strong>Collect the MVP</strong><span>and test on 3 cases</span></article>
                <article><b>6</b><strong>Check it out</strong><span>errors and security</span></article>
                <article><b>7</b><strong>show me</strong><span>result to classmates</span></article>
              </div>
            </div>
            <aside class="final-card">
              <div class="final-icon" aria-hidden="true">→</div>
              <h3>Don't expect a "perfect" case</h3>
              <p>The best way to understand agents is to create a useful little automation yourself.</p>
              <span>QUESTION → EXPERIMENT → SYSTEM</span>
            </aside>
          </div>
          <div class="slide-footer">OPENCLAW • INTRODUCTION FOR FRESHMEN <span>10</span></div>
        </section>
      </div>
    </main>

    <footer class="presentation-controls">
      <div class="progress-track" aria-hidden="true"><span data-progress></span></div>
      <button type="button" data-prev aria-label="Previous slide">←</button>
      <span class="control-hint">ARROW / SPACEBAR / SWIPE</span>
      <button type="button" data-next aria-label="Next slide">→</button>
    </footer>
  </div>
  <script src="{{ asset('js/openclaw-presentation.js') }}" defer></script>
</body>
</html>
