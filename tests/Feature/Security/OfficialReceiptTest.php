<?php

namespace Tests\Feature\Security;

use App\Models\Enrollment;
use App\Models\PaymentTransaction;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * One Official Receipt everywhere: the Cashier's slip lives in public/js/official-receipt.js
 * and every page that prints receipts loads it (no page keeps its own copy); the Receipt PDF
 * uses the same OR No. (the payment's reference number).
 */
class OfficialReceiptTest extends TestCase
{
    use RefreshDatabase;

    public function test_receipt_pdf_uses_the_reference_number_as_or_no(): void
    {
        $finance = User::factory()->finance()->create();
        $e = Enrollment::factory()->fullyPaid()->create();
        $tx = PaymentTransaction::create([
            'enrollment_id' => $e->id, 'user_id' => $e->user_id, 'payment_type' => 'walkin', 'payment_method' => 'cash',
            'amount' => 1278.32, 'reference_number' => 'CASH-TEST1234', 'status' => 'completed', 'description' => 'Monthly Installment',
        ]);

        $res = $this->actingAs($finance, 'finance')->get("/finance/payments/{$tx->id}/receipt-pdf")->assertOk();
        $this->assertStringContainsString('CASH-TEST1234.pdf', $res->headers->get('content-disposition'));

        $pending = PaymentTransaction::create(array_merge($tx->only(['enrollment_id', 'user_id', 'payment_type', 'payment_method', 'amount']), ['reference_number' => 'X-2', 'status' => 'pending']));
        $this->actingAs($finance, 'finance')->get("/finance/payments/{$pending->id}/receipt-pdf")->assertNotFound();
    }

    public function test_every_receipt_page_loads_the_shared_receipt_and_has_no_copy_of_its_own(): void
    {
        Setting::set('current_school_year', '2026-2027');
        $pages = [
            ['web', User::factory()->admin()->create(), '/admin/dashboard'],
            ['cashier', User::factory()->cashier()->create(), '/cashier/dashboard'],
            ['finance', User::factory()->finance()->create(), '/finance/payments'],
            ['web', Enrollment::factory()->create()->user, '/student/portal'],
        ];
        foreach ($pages as [$guard, $user, $url]) {
            $html = $this->actingAs($user, $guard)->get($url)->assertOk()->getContent();
            $this->assertStringContainsString('/js/official-receipt.js', $html, $url);
            $this->assertStringNotContainsString('function printOfficialReceipt', $html, "$url still defines its own receipt");
        }
        $this->assertFileExists(public_path('js/official-receipt.js'));
    }
}
