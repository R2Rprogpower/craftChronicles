<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rebe Bot Setup</title>
    <style>
        :root {
            --bg: #f6f6f7;
            --text: #1f2937;
            --muted: #4b5563;
            --card: #ffffff;
            --line: #d1d5db;
            --accent: #0f766e;
            --danger: #b91c1c;
        }

        body {
            margin: 0;
            font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, sans-serif;
            color: var(--text);
            background: linear-gradient(145deg, #f8fafc, #eef2ff 55%, #f6f6f7);
        }

        .wrap {
            max-width: 980px;
            margin: 28px auto;
            padding: 0 16px 40px;
        }

        .card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 18px;
            margin-bottom: 16px;
        }

        h1, h2 { margin: 0 0 12px; }
        p { margin: 0 0 10px; color: var(--muted); }

        label { display: block; margin: 10px 0 6px; font-weight: 600; }

        input, select {
            width: 100%;
            box-sizing: border-box;
            padding: 10px 12px;
            border-radius: 8px;
            border: 1px solid var(--line);
            font-size: 14px;
            margin-bottom: 8px;
        }

        button {
            background: var(--accent);
            color: #fff;
            border: 0;
            border-radius: 8px;
            padding: 10px 14px;
            cursor: pointer;
            font-weight: 600;
        }

        button:hover { filter: brightness(0.94); }

        .grid {
            display: grid;
            gap: 14px;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        }

        .status {
            background: #ecfeff;
            color: #155e75;
            border: 1px solid #a5f3fc;
            border-radius: 8px;
            padding: 10px 12px;
            margin-bottom: 12px;
        }

        .error {
            background: #fee2e2;
            color: var(--danger);
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: 10px 12px;
            margin-bottom: 12px;
        }

        ul { margin: 8px 0 0; padding-left: 20px; }
        li { margin-bottom: 6px; }
        .muted { color: var(--muted); font-size: 13px; }
        .inline { display: flex; gap: 8px; align-items: center; margin-top: 8px; }
    </style>
</head>
<body>
<div class="wrap">
    <h1>Rebe Bot Setup</h1>
    <p>Step 1: connect your bot token. Step 2: connect or discover group chat IDs. This supports multiple messenger drivers in the same control panel.</p>

    @if (session('status'))
        <div class="status">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="error">
            <strong>Setup error:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid">
        <section class="card">
            <h2>1) Add Bot Token</h2>
            <form method="post" action="{{ route('bots.setup.store') }}">
                @csrf
                <label for="name">Bot name</label>
                <input id="name" name="name" placeholder="My Rebe Bot" required>

                <label for="driver">Messenger driver</label>
                <select id="driver" name="driver" required>
                    @foreach ($drivers as $driver)
                        <option value="{{ $driver }}">{{ ucfirst($driver) }}</option>
                    @endforeach
                </select>

                <label for="bot_token">Bot token</label>
                <input id="bot_token" name="bot_token" placeholder="123456:ABC-DEF..." required>

                <button type="submit">Save and Verify</button>
            </form>
        </section>

        <section class="card">
            <h2>2) Connect Group</h2>
            <form method="post" action="{{ route('bots.connect-group') }}">
                @csrf
                <label for="messenger_bot_id">Bot</label>
                <select id="messenger_bot_id" name="messenger_bot_id" required>
                    @foreach ($bots as $bot)
                        <option value="{{ $bot->id }}">#{{ $bot->id }} {{ $bot->name }} ({{ $bot->driver }})</option>
                    @endforeach
                </select>

                <label for="external_chat_id">Group chat ID</label>
                <input id="external_chat_id" name="external_chat_id" placeholder="-1001234567890" required>

                <label for="title">Group title (optional)</label>
                <input id="title" name="title" placeholder="My Study Group">

                <div class="inline">
                    <input type="checkbox" id="send_test_message" name="send_test_message" value="1" style="width:auto; margin:0;">
                    <label for="send_test_message" style="margin:0; font-weight:500;">Send test message immediately</label>
                </div>

                <div style="margin-top: 12px;">
                    <button type="submit">Link Group</button>
                </div>
            </form>

            <form method="post" action="{{ route('bots.discover-groups') }}" style="margin-top:12px;">
                @csrf
                <label for="discover_bot_id">Or auto-discover group from recent updates</label>
                <select id="discover_bot_id" name="messenger_bot_id" required>
                    @foreach ($bots as $bot)
                        <option value="{{ $bot->id }}">#{{ $bot->id }} {{ $bot->name }} ({{ $bot->driver }})</option>
                    @endforeach
                </select>
                <button type="submit">Discover Groups</button>
            </form>

            <form method="post" action="{{ route('bots.relink-group') }}" style="margin-top:12px;">
                @csrf
                <label for="relink_group_link_id">Relink existing group relation</label>
                <select id="relink_group_link_id" name="messenger_group_link_id" required>
                    @foreach ($groupLinks as $link)
                        <option value="{{ $link->id }}">#{{ $link->id }} bot#{{ $link->messenger_bot_id }} {{ $link->title ?: $link->external_chat_id }}</option>
                    @endforeach
                </select>

                <label for="relink_external_chat_id">New group chat ID</label>
                <input id="relink_external_chat_id" name="external_chat_id" placeholder="-1001234567890" required>

                <label for="relink_title">New group title (optional)</label>
                <input id="relink_title" name="title" placeholder="My New Study Group">

                <button type="submit">Relink Existing Group Row</button>
            </form>
        </section>
    </div>

    <section class="card">
        <h2>How to connect Telegram bot to a group</h2>
        <ol>
            <li>Create a bot with BotFather and copy token.</li>
            <li>Paste token in Step 1 and save.</li>
            <li>Add bot to your Telegram group.</li>
            <li>Send any message in the group (for discovery) or paste chat ID in Step 2.</li>
            <li>Use Discover Groups or Link Group. Optionally send a test message.</li>
        </ol>
        <p class="muted">Tip: for webhook mode in local dev, expose your app with a public tunnel and set Telegram webhook to /api/telegram/webhook/{botId}.</p>
    </section>

    <section class="card">
        <h2>Optional: Register Webhook URL</h2>
        <form method="post" action="{{ route('bots.register-webhook') }}">
            @csrf
            <label for="webhook_bot_id">Bot</label>
            <select id="webhook_bot_id" name="messenger_bot_id" required>
                @foreach ($bots as $bot)
                    <option value="{{ $bot->id }}">#{{ $bot->id }} {{ $bot->name }} ({{ $bot->driver }})</option>
                @endforeach
            </select>

            <label for="webhook_url">Webhook URL</label>
            <input id="webhook_url" name="webhook_url" placeholder="https://your-public-url/api/telegram/webhook/1" required>

            <button type="submit">Register Webhook</button>
        </form>
    </section>

    <section class="card">
        <h2>Registered Bots</h2>
        @if ($bots->isEmpty())
            <p>No bots configured yet.</p>
        @else
            <ul>
                @foreach ($bots as $bot)
                    <li>#{{ $bot->id }} {{ $bot->name }} | driver={{ $bot->driver }} | username={{ $bot->username ?? '-' }}</li>
                @endforeach
            </ul>
        @endif
    </section>

    <section class="card">
        <h2>Linked Groups</h2>
        @if ($groupLinks->isEmpty())
            <p>No groups linked yet.</p>
        @else
            <ul>
                @foreach ($groupLinks as $link)
                    <li>bot#{{ $link->messenger_bot_id }} ({{ $link->bot?->name ?? 'unknown' }}) -> {{ $link->external_chat_id }} {{ $link->title ? '('.$link->title.')' : '' }}</li>
                @endforeach
            </ul>
        @endif
    </section>
</div>
</body>
</html>
