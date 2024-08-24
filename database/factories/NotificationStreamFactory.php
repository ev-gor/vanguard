<?php

namespace Database\Factories;

use App\Models\NotificationStream;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationStreamFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement([
            NotificationStream::TYPE_EMAIL,
            NotificationStream::TYPE_SLACK,
            NotificationStream::TYPE_DISCORD,
            NotificationStream::TYPE_TEAMS,
            NotificationStream::TYPE_PUSHOVER,
            NotificationStream::TYPE_TELEGRAM,
        ]);

        return [
            'label' => $this->faker->word,
            'type' => $type,
            'value' => $this->getValueForType($type),
            'user_id' => User::factory(),
            'receive_successful_backup_notifications' => now(),
            'receive_failed_backup_notifications' => now(),
        ];
    }

    /**
     * Indicate that the notification stream is for email.
     */
    public function email(): static
    {
        return $this->state([
            'type' => NotificationStream::TYPE_EMAIL,
            'value' => $this->faker->safeEmail,
        ]);
    }

    /**
     * Indicate that the notification stream is for Slack.
     */
    public function slack(): static
    {
        return $this->state([
            'type' => NotificationStream::TYPE_SLACK,
            'value' => $this->generateSlackWebhook(),
        ]);
    }

    /**
     * Indicate that the notification stream is for Discord.
     */
    public function discord(): static
    {
        return $this->state([
            'type' => NotificationStream::TYPE_DISCORD,
            'value' => $this->generateDiscordWebhook(),
        ]);
    }

    /**
     * Indicate that the notification stream is for Microsoft Teams.
     */
    public function teams(): static
    {
        return $this->state([
            'type' => NotificationStream::TYPE_TEAMS,
            'value' => $this->generateTeamsWebhook(),
        ]);
    }

    /**
     * Indicate that the notification stream is for Pushover.
     */
    public function pushover(): static
    {
        return $this->state([
            'type' => NotificationStream::TYPE_PUSHOVER,
            'value' => $this->generatePushover(),
        ]);
    }

    /**
     * Indicate that the notification stream is for Telegram.
     */
    public function telegram(): static
    {
        return $this->state([
            'type' => NotificationStream::TYPE_TELEGRAM,
            'value' => $this->generateTelegram(),
        ]);
    }

    /**
     * Indicate that the notification stream will send successful notifications.
     */
    public function successEnabled(): static
    {
        return $this->state([
            'receive_successful_backup_notifications' => now(),
        ]);
    }

    /**
     * Indicate that the notification stream will not send successful notifications.
     */
    public function successDisabled(): static
    {
        return $this->state([
            'receive_successful_backup_notifications' => null,
        ]);
    }

    /**
     * Indicate that the notification stream will send failure notifications.
     */
    public function failureEnabled(): static
    {
        return $this->state([
            'receive_failed_backup_notifications' => now(),
        ]);
    }

    /**
     * Indicate that the notification stream will not send failure notifications.
     */
    public function failureDisabled(): static
    {
        return $this->state([
            'receive_failed_backup_notifications' => null,
        ]);
    }

    /**
     * Get the appropriate value for the given notification type.
     */
    private function getValueForType(string $type): string
    {
        return match ($type) {
            NotificationStream::TYPE_EMAIL => $this->faker->safeEmail,
            NotificationStream::TYPE_SLACK => $this->generateSlackWebhook(),
            NotificationStream::TYPE_DISCORD => $this->generateDiscordWebhook(),
            NotificationStream::TYPE_TEAMS => $this->generateTeamsWebhook(),
            NotificationStream::TYPE_TELEGRAM => $this->generateTelegram(),
            default => $this->faker->url,
        };
    }

    /**
     * Generate a realistic Slack webhook URL.
     */
    private function generateSlackWebhook(): string
    {
        $workspace = $this->faker->regexify('[A-Z0-9]{9}');
        $channel = $this->faker->regexify('[A-Z0-9]{9}');
        $token = $this->faker->regexify('[a-zA-Z0-9]{24}');

        return "https://hooks.slack.com/services/{$workspace}/{$channel}/{$token}";
    }

    /**
     * Generate a realistic Discord webhook URL.
     */
    private function generateDiscordWebhook(): string
    {
        $id = $this->faker->numberBetween(100000000000000000, 999999999999999999);
        $token = $this->faker->regexify('[a-zA-Z0-9_-]{68}');

        return "https://discord.com/api/webhooks/{$id}/{$token}";
    }

    /**
     * Generate a realistic Teams webhook URL.
     */
    private function generateTeamsWebhook(): string
    {
        $subdomain = $this->faker->word;
        $guid1 = $this->faker->uuid;
        $guid2 = $this->faker->uuid;
        $alphanumeric = $this->faker->regexify('[a-zA-Z0-9]{20}');
        $guid3 = $this->faker->uuid;

        return "https://{$subdomain}.webhook.office.com/webhookb2/{$guid1}@{$guid2}/IncomingWebhook/{$alphanumeric}/{$guid3}";
    }

    /**
     * Generate a realistic Pushover API token.
     */
    private function generatePushover(): string
    {
        return $this->faker->regexify('[a-zA-Z0-9]{30}');
    }

    /**
     * Generate a realistic Telegram chatID.
     */
    private function generateTelegram(): string
    {
        return $this->faker->regexify('[0-9]{10}');
    }
}
