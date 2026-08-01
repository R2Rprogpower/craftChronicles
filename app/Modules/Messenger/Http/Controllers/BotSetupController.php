<?php

declare(strict_types=1);

namespace App\Modules\Messenger\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Messenger\Models\MessengerBot;
use App\Modules\Messenger\Models\MessengerGroupLink;
use App\Modules\Messenger\Services\BotOnboardingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class BotSetupController extends Controller
{
    public function __construct(
        private readonly BotOnboardingService $onboardingService
    ) {}

    public function index(): View
    {
        return view('bot-setup', [
            'drivers' => config('messengers.drivers', []),
            'bots' => MessengerBot::query()->orderByDesc('id')->get(),
            'groupLinks' => MessengerGroupLink::query()->with('bot')->orderByDesc('id')->limit(50)->get(),
        ]);
    }

    public function storeBot(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'driver' => ['required', 'string', 'in:'.implode(',', config('messengers.drivers', []))],
            'bot_token' => ['required', 'string', 'min:20'],
        ]);

        try {
            $bot = $this->onboardingService->upsertBot(
                (string) $data['name'],
                (string) $data['driver'],
                (string) $data['bot_token']
            );

            return redirect()
                ->route('bots.setup')
                ->with('status', "Bot '{$bot->name}' connected successfully.");
        } catch (Throwable $exception) {
            return redirect()
                ->route('bots.setup')
                ->withErrors(['bot_setup' => $exception->getMessage()]);
        }
    }

    public function connectGroup(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'messenger_bot_id' => ['required', 'integer', 'exists:messenger_bots,id'],
            'external_chat_id' => ['required', 'string', 'max:64'],
            'title' => ['nullable', 'string', 'max:255'],
            'send_test_message' => ['nullable', 'boolean'],
        ]);

        $bot = MessengerBot::query()->findOrFail((int) $data['messenger_bot_id']);

        try {
            $this->onboardingService->linkGroup($bot, (string) $data['external_chat_id'], $data['title'] ?? null);

            if ((bool) ($data['send_test_message'] ?? false)) {
                $this->onboardingService->sendGroupTestMessage($bot, (string) $data['external_chat_id']);
            }

            return redirect()
                ->route('bots.setup')
                ->with('status', 'Group linked successfully.');
        } catch (Throwable $exception) {
            return redirect()
                ->route('bots.setup')
                ->withErrors(['group_connect' => $exception->getMessage()]);
        }
    }

    public function relinkGroup(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'messenger_group_link_id' => ['required', 'integer', 'exists:messenger_group_links,id'],
            'external_chat_id' => ['required', 'string', 'max:64'],
            'title' => ['nullable', 'string', 'max:255'],
        ]);

        $groupLink = MessengerGroupLink::query()->with('bot')->findOrFail((int) $data['messenger_group_link_id']);

        try {
            $this->onboardingService->relinkGroup(
                $groupLink,
                (string) $data['external_chat_id'],
                $data['title'] ?? null
            );

            return redirect()
                ->route('bots.setup')
                ->with('status', 'Group relation relinked successfully.');
        } catch (Throwable $exception) {
            return redirect()
                ->route('bots.setup')
                ->withErrors(['group_relink' => $exception->getMessage()]);
        }
    }

    public function discoverGroups(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'messenger_bot_id' => ['required', 'integer', 'exists:messenger_bots,id'],
        ]);

        $bot = MessengerBot::query()->findOrFail((int) $data['messenger_bot_id']);

        try {
            $groups = $this->onboardingService->discoverGroupsFromUpdates($bot);

            if ($groups === []) {
                return redirect()
                    ->route('bots.setup')
                    ->with('status', 'No group updates found yet. Add bot to group and send a message, then try again.');
            }

            return redirect()
                ->route('bots.setup')
                ->with('status', 'Discovered and linked '.count($groups).' group(s).');
        } catch (Throwable $exception) {
            return redirect()
                ->route('bots.setup')
                ->withErrors(['group_discovery' => $exception->getMessage()]);
        }
    }

    public function registerWebhook(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'messenger_bot_id' => ['required', 'integer', 'exists:messenger_bots,id'],
            'webhook_url' => ['required', 'url', 'max:2048'],
        ]);

        $bot = MessengerBot::query()->findOrFail((int) $data['messenger_bot_id']);

        try {
            $this->onboardingService->registerWebhook($bot, (string) $data['webhook_url']);

            return redirect()
                ->route('bots.setup')
                ->with('status', 'Webhook registered successfully.');
        } catch (Throwable $exception) {
            return redirect()
                ->route('bots.setup')
                ->withErrors(['webhook_register' => $exception->getMessage()]);
        }
    }
}
