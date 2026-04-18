<?php

namespace Tests\Unit\Models;

use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function test_status_badge_classes_use_tailwind_colors(): void
    {
        $order = new Order();

        foreach (Order::STATUS_COLORS as $status => $color) {
            $order->status = $status;
            $badgeClass = $order->getStatusBadgeClass();

            // Should not contain hardcoded hex colors
            $this->assertStringNotContainsString('#', $badgeClass);

            // Should use Tailwind bg/text pattern
            $this->assertStringContainsString('bg-', $badgeClass);
            $this->assertStringContainsString('text-', $badgeClass);
        }
    }

    #[Test]
    public function test_user_and_admin_status_colors_match(): void
    {
        // Verify STATUS_COLORS uses standard Tailwind palette
        $expectedColors = [
            Order::STATUS_PENDING => 'yellow',
            Order::STATUS_MENUNGGU_VERIFIKASI => 'orange',
            Order::STATUS_PROCESSING => 'blue',
            Order::STATUS_COMPLETED => 'green',
            Order::STATUS_CANCELLED => 'red',
        ];

        foreach ($expectedColors as $status => $expectedColor) {
            $this->assertEquals(
                $expectedColor,
                Order::STATUS_COLORS[$status],
                "Status {$status} should use {$expectedColor} color"
            );
        }
    }

    #[Test]
    public function test_get_status_label_returns_correct_label(): void
    {
        $order = new Order();

        foreach (Order::STATUS_LABELS as $status => $expectedLabel) {
            $order->status = $status;
            $this->assertEquals($expectedLabel, $order->getStatusLabel());
        }
    }

    #[Test]
    public function test_all_statuses_have_defined_colors(): void
    {
        // Ensure every active status has a corresponding color
        $allStatuses = [
            Order::STATUS_PENDING,
            Order::STATUS_MENUNGGU_VERIFIKASI,
            Order::STATUS_PROCESSING,
            Order::STATUS_COMPLETED,
            Order::STATUS_CANCELLED,
        ];

        foreach ($allStatuses as $status) {
            $this->assertArrayHasKey(
                $status,
                Order::STATUS_COLORS,
                "Status '{$status}' should have a defined color in STATUS_COLORS"
            );
        }
    }

    #[Test]
    public function test_active_status_labels_only_use_five_supported_statuses(): void
    {
        $this->assertSame([
            Order::STATUS_PENDING,
            Order::STATUS_MENUNGGU_VERIFIKASI,
            Order::STATUS_PROCESSING,
            Order::STATUS_COMPLETED,
            Order::STATUS_CANCELLED,
        ], array_keys(Order::STATUS_LABELS));

        $this->assertSame('Belum Bayar', Order::STATUS_LABELS[Order::STATUS_PENDING]);
    }

    #[Test]
    public function test_active_status_colors_only_use_five_supported_statuses(): void
    {
        $this->assertSame([
            Order::STATUS_PENDING,
            Order::STATUS_MENUNGGU_VERIFIKASI,
            Order::STATUS_PROCESSING,
            Order::STATUS_COMPLETED,
            Order::STATUS_CANCELLED,
        ], array_keys(Order::STATUS_COLORS));
    }

    #[Test]
    public function test_get_status_color_returns_expected_values(): void
    {
        $order = new Order();

        foreach (Order::STATUS_COLORS as $status => $expectedColor) {
            $order->status = $status;
            $this->assertEquals($expectedColor, $order->getStatusColor());
        }
    }

    #[Test]
    public function test_status_badge_class_returns_consistent_format(): void
    {
        $order = new Order();

        foreach (Order::STATUS_COLORS as $status => $color) {
            $order->status = $status;
            $badgeClass = $order->getStatusBadgeClass();

            // Verify format: bg-{color}-100 text-{color}-800
            $this->assertStringContainsString("bg-{$color}-100", $badgeClass);
            $this->assertStringContainsString("text-{$color}-800", $badgeClass);
        }
    }
}
