<!doctype html>
<html lang="{{ $locale }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#07110f">
    <meta name="description" content="OpenClaw as a Research Assistant: Indexing Large Libraries, Meaning Searches, Answers with Citations, and Research Reviews.">
    <title>OpenClaw Research - Research Assistant for Large Libraries</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Source+Serif+4:opsz,wght@8..60,600;8..60,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('build/css/openclaw-research.css') }}">
    <link rel="stylesheet" href="{{ asset('build/css/openclaw-network-header.css') }}">
</head>
<body>
    <x-openclaw-network-header active="research" :locale="$locale" />

    <main>
        <section class="research-hero page-shell">
            <div class="hero-copy">
                <p class="eyebrow"><span></span> OpenClaw Research</p>
                <h1>The entire library -<br><em>in one exploratory dialogue.</em></h1>
                <p class="hero-lead">Research Assistant indexes books, articles, archives, and manuscripts, finds connections between sources, and returns verifiable answers with exact citations.</p>
                <div class="hero-actions">
                    <a class="button button-primary" href="#pilot">Discuss the pilot <span>↗</span></a>
                    <a class="button button-secondary" href="#architecture">How does this work <span>↓</span></a>
                </div>
                <ul class="hero-notes">
                    <li>Works with your private collection</li>
                    <li>Each thesis is linked to a source</li>
                    <li>The researcher controls the findings</li>
                </ul>
            </div>

            <div class="library-console" aria-label="Example of a research assistant's response">
                <div class="console-top"><span>RESEARCH SESSION / 024</span><i></i><i></i><i></i></div>
                <div class="query"><span>REQUEST</span><p>How did the concept of “authorship” change in works from the 1920s to the 1970s?</p></div>
                <div class="search-state">
                    <div><strong>12 480</strong><span>documents verified</span></div>
                    <div><strong>38</strong><span>relevant fragments</span></div>
                </div>
                <div class="answer">
                    <span>SYNTHESIS</span>
                    <p>Three shifts can be traced in the corpus: from the individual creator to the function of the text, then to the distributed production of knowledge...</p>
                    <ol>
                        <li><b>[12]</b> Benjamin, 1936 · p. 27</li>
                        <li><b>[19]</b> Barth, 1967 · p. 4</li>
                        <li><b>[31]</b> Foucault, 1969 · p. 14</li>
                    </ol>
                </div>
                <div class="console-footer"><span>✓ links verified</span><span>export: DOCX · CSV · BIB</span></div>
            </div>
        </section>

        <section class="signal-strip">
            <div class="page-shell signal-grid">
                <article><strong>Millions of pages</strong><span>a single index instead of dozens of separate archives</span></article>
                <article><strong>Answers with evidence</strong><span>page, snippet and metadata for each statement</span></article>
                <article><strong>Your access rules</strong><span>collections, roles, query logs and local deployment</span></article>
            </div>
        </section>

        <section class="research-section page-shell" id="architecture">
            <header class="section-heading">
                <p class="eyebrow">More than just chat with PDF</p>
                <h2>A research system that knows the origin of every fact</h2>
                <p>OpenClaw combines the collection preparation pipeline, hybrid search, and analysis tools into one reproducible process.</p>
            </header>
            <div class="pipeline">
                <article><span>01</span><h3>Collection reception</h3><p>PDF, EPUB, scans, catalogs, notes and databases go into managed storage.</p><small>Files API Cloud NAS</small></article>
                <article><span>02</span><h3>Recognition</h3><p>OCR, cleanup, structure markup, language detection and saving page coordinates.</p><small>Text · tables · footnotes · illustrations</small></article>
                <article><span>03</span><h3>Scientific index</h3><p>Full-text and semantic searches are complemented by authors, dates, topics, and connections.</p><small>Keywords + meaning + metadata</small></article>
                <article><span>04</span><h3>Research Agent</h3><p>Plans the search, compares sources, notes inconsistencies, and compiles a response with citations.</p><small>Human control at each output</small></article>
            </div>
        </section>

        <section class="research-section workflow-section">
            <div class="page-shell two-column">
                <header class="section-heading sticky-heading">
                    <p class="eyebrow">Working scenarios</p>
                    <h2>From question to verifiable result</h2>
                    <p>The assistant does not replace the researcher. It reduces the mechanical work and leaves the interpretation to the person.</p>
                </header>
                <div class="workflow-list">
                    <article><b>01</b><div><h3>Literature review</h3><p>Collects positions on the topic, groups schools, shows discrepancies and gaps in the corpus.</p></div><span>source matrix</span></article>
                    <article><b>02</b><div><h3>Finding Hidden Connections</h3><p>Finds where different authors describe the same idea in different terms, languages, or decades.</p></div><span>connection graph</span></article>
                    <article><b>03</b><div><h3>Hypothesis testing</h3><p>Searches for supporting and disconfirming fragments, recording search criteria and limitations.</p></div><span>evidence table</span></article>
                    <article><b>04</b><div><h3>Preparation of publication</h3><p>Creates notes, chronology, citation cards and bibliography in the required format.</p></div><span>DOCX · CSV · BibTeX</span></article>
                </div>
            </div>
        </section>

        <section class="research-section page-shell">
            <header class="section-heading compact-heading">
                <p class="eyebrow">For different collections</p>
                <h2>One platform - different research environments</h2>
            </header>
            <div class="use-case-grid">
                <article><span>01</span><h3>University Library</h3><p>Unified search for books, dissertations, articles and internal archives of departments.</p></article>
                <article><span>02</span><h3>Historical archive</h3><p>OCR of manuscripts and scans, search for variants of names, events and geographical references.</p></article>
                <article><span>03</span><h3>R&D team</h3><p>Monitoring of new publications, comparison of methods and map of evidence for the product hypothesis.</p></article>
                <article><span>04</span><h3>Corporate knowledge</h3><p>Technical guides, reports and studies are made available based on role.</p></article>
            </div>
        </section>

        <section class="research-section integrity-section">
            <div class="page-shell integrity-layout">
                <div>
                    <p class="eyebrow">Scientific integrity</p>
                    <h2>The system shows evidence - and honestly tells you when there is not enough evidence.</h2>
                </div>
                <div class="integrity-grid">
                    <article><i>✓</i><h3>Quote to page</h3><p>The original fragment opens next to the answer.</p></article>
                    <article><i>✓</i><h3>Housing boundaries</h3><p>The response shows which collections and periods were checked.</p></article>
                    <article><i>✓</i><h3>Separation of fact and conclusion</h3><p>The system flags direct evidence and interpretations.</p></article>
                    <article><i>✓</i><h3>Copyright</h3><p>Access and release of full texts follows your collection's licenses.</p></article>
                </div>
            </div>
        </section>

        <section class="research-section page-shell" id="pilot">
            <div class="pilot-card">
                <div>
                    <p class="eyebrow">Pilot project</p>
                    <h2>Start with one collection and one research question.</h2>
                    <p>We will identify sources, collect a test index, set up quality criteria and show the answers using real materials from your team.</p>
                </div>
                <div class="pilot-steps">
                    <p><span>01</span> Audit of collection and access rights</p>
                    <p><span>02</span> Indexing a representative sample</p>
                    <p><span>03</span> Set of test questions and evaluation of quotations</p>
                    <p><span>04</span> Scaling and Operation Plan</p>
                    <a class="button button-primary" href="{{ route('openclaw-short', ['lang' => $locale]) }}#request">Discuss the collection <span>↗</span></a>
                </div>
            </div>
        </section>
    </main>

    <footer class="research-footer page-shell">
        <strong>OPENCLAW RESEARCH</strong>
        <span>A scientific search that can be verified.</span>
        <span>© {{ date('Y') }} Craft Chronicles</span>
    </footer>
</body>
</html>
