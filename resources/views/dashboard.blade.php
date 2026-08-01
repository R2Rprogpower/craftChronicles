<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard</title>
    <style>
        :root {
            --bg: #f8fafc;
            --card: #ffffff;
            --line: #dbe3ec;
            --text: #0f172a;
            --muted: #475569;
            --accent: #1d4ed8;
        }

        body {
            margin: 0;
            font-family: ui-sans-serif, system-ui, sans-serif;
            background: var(--bg);
            color: var(--text);
        }

        .wrap {
            max-width: 1200px;
            margin: 24px auto;
            padding: 0 16px 24px;
        }

        .header {
            margin-bottom: 14px;
        }

        .header h1 {
            margin: 0;
            font-size: 28px;
        }

        .header p {
            margin: 6px 0 0;
            color: var(--muted);
        }

        .card {
            border: 1px solid var(--line);
            background: var(--card);
            border-radius: 12px;
            padding: 14px;
            margin-bottom: 12px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 10px;
        }

        .label {
            color: var(--muted);
            font-size: 12px;
            margin-bottom: 2px;
        }

        .value {
            font-weight: 600;
            word-break: break-word;
        }

        .actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 10px;
        }

        .btn {
            display: inline-block;
            background: var(--accent);
            color: #fff;
            text-decoration: none;
            border-radius: 8px;
            padding: 8px 12px;
            font-weight: 600;
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        th, td {
            border: 1px solid var(--line);
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background: #f1f5f9;
        }

        .mono {
            font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
        }

        .small {
            color: var(--muted);
            font-size: 12px;
            margin-top: 8px;
        }
    </style>
</head>
<body>
    <div class="wrap">
        <section class="header">
            <h1>Dashboard</h1>
            <p>Signed-in user context and full route map.</p>
        </section>

        <section class="card">
            <h2>Logged In User</h2>
            <div class="grid">
                <div>
                    <div class="label">ID</div>
                    <div class="value">{{ $user?->id ?? '-' }}</div>
                </div>
                <div>
                    <div class="label">Name</div>
                    <div class="value">{{ $user?->name ?? '-' }}</div>
                </div>
                <div>
                    <div class="label">Email</div>
                    <div class="value">{{ $user?->email ?? '-' }}</div>
                </div>
            </div>
            <div class="actions">
                <a class="btn" href="{{ route('bots.setup') }}">Bot Setup</a>
                <a class="btn" href="{{ route('religions.admin') }}">Religions Admin</a>
            </div>
        </section>

        <section class="card">
            <h2>Available Routes</h2>
            @if (empty($routes))
                <p>No routes available.</p>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>Methods</th>
                            <th>URI</th>
                            <th>Name</th>
                            <th>Action</th>
                            <th>Middleware</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($routes as $route)
                            <tr>
                                <td class="mono">{{ $route['methods'] }}</td>
                                <td class="mono">{{ $route['uri'] }}</td>
                                <td class="mono">{{ $route['name'] }}</td>
                                <td class="mono">{{ $route['action'] }}</td>
                                <td class="mono">{{ $route['middleware'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="small">Total routes: {{ count($routes) }}</div>
            @endif
        </section>
    </div>
</body>
</html>