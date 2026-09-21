# AASKAR donation page

Минимальный автономный статический сайт для маршрута `/askar_kz_1`.

Внутри нет Laravel, PHP, Node.js, базы данных, Redis и blue-green deploy.
Контейнер содержит только Caddy и один статический `index.html`.

## Локальный запуск

```bash
cd standalone/askar-kz-1
docker compose -f compose.yml up -d --build
curl -I http://127.0.0.1:18091/
```

Открыть в браузере: <http://localhost:18091/>.

Остановка:

```bash
docker compose -f compose.yml down
```

## Развёртывание на VPS

### Вариант 1: папка внутри клона Craft Chronicles

```bash
cd /opt/craftChronicles
git pull --ff-only
cd standalone/askar-kz-1
docker compose -f compose.yml up -d --build
curl -I http://127.0.0.1:18091/
```

### Вариант 2: полностью отдельная папка

Скопировать содержимое `standalone/askar-kz-1` на VPS в
`/opt/askar-kz-1`, затем:

```bash
cd /opt/askar-kz-1
docker compose -f compose.yml up -d --build
curl -I http://127.0.0.1:18091/
```

Порт публикуется только на `127.0.0.1`, поэтому контейнер недоступен напрямую
из интернета. Внешний HTTPS завершает системный Caddy.

Если `18091` занят, запустить с другим loopback-портом:

```bash
ASKAR_KZ_1_PORT=18092 docker compose -f compose.yml up -d --build
```

Тот же порт нужно указать в системном Caddyfile.

## Системный Caddy

Добавить блоки **внутрь существующего блока домена Craft Chronicles**, перед
общим `handle`/`reverse_proxy` приложения:

```caddyfile
@askarKzDonateExact path /askar_kz_1
handle @askarKzDonateExact {
    rewrite * /
    reverse_proxy 127.0.0.1:18091
}

handle_path /askar_kz_1/* {
    reverse_proxy 127.0.0.1:18091
}
```

Пример полного упрощённого блока:

```caddyfile
craft.example.com {
    @askarKzDonateExact path /askar_kz_1
    handle @askarKzDonateExact {
        rewrite * /
        reverse_proxy 127.0.0.1:18091
    }

    handle_path /askar_kz_1/* {
        reverse_proxy 127.0.0.1:18091
    }

    # Существующее приложение Craft Chronicles остаётся fallback-маршрутом.
    handle {
        reverse_proxy 127.0.0.1:8081
    }
}
```

Проверить и применить конфигурацию:

```bash
sudo caddy validate --config /etc/caddy/Caddyfile
sudo systemctl reload caddy
curl -I https://craft.example.com/askar_kz_1
```

`craft.example.com` и порт основного приложения `8081` в примере нужно
заменить фактическими значениями существующей конфигурации.

## Обновление страницы

После изменения `index.html`:

```bash
cd /opt/craftChronicles/standalone/askar-kz-1
git pull --ff-only
docker compose -f compose.yml up -d --build
```

Перезагрузка системного Caddy для обновления HTML не требуется.

## Диагностика

```bash
docker compose -f compose.yml ps
docker compose -f compose.yml logs --tail=100 web
curl -I http://127.0.0.1:18091/
```

Ожидаемый ответ контейнера: `HTTP/1.1 200 OK`.
