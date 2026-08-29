<?php

namespace Tests\Feature;

use App\Models\Expense;
use App\Models\Income;
use App\Models\Sale;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinanceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            RoleSeeder::class,
            PermissionSeeder::class,
            RolePermissionSeeder::class,
            AdminUserSeeder::class,
        ]);

        $this->admin = User::where('email', 'admin@etnicos365.com')->firstOrFail();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/finances')->assertRedirect('/login');
    }

    public function test_user_without_permission_gets_403(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/finances')->assertForbidden();
    }

    public function test_admin_can_view_financial_summary_with_correct_totals(): void
    {
        Income::factory()->create([
            'type' => 'other',
            'amount' => 1000000,
            'income_date' => now()->toDateString(),
        ]);
        Expense::factory()->create([
            'category' => 'raw_material',
            'amount' => 400000,
            'expense_date' => now()->toDateString(),
        ]);

        $this->actingAs($this->admin)
            ->get('/finances')
            ->assertOk()
            ->assertSee('1.000.000')
            ->assertSee('400.000')
            ->assertSee('600.000');
    }

    public function test_profit_is_calculated_as_income_minus_expenses(): void
    {
        Income::factory()->create(['amount' => 2500000, 'income_date' => now()->toDateString()]);
        Income::factory()->create(['amount' => 500000, 'income_date' => now()->toDateString()]);
        Expense::factory()->create(['amount' => 800000, 'expense_date' => now()->toDateString()]);

        $this->actingAs($this->admin)
            ->get('/finances')
            ->assertOk()
            ->assertSee('3.000.000')
            ->assertSee('800.000')
            ->assertSee('2.200.000');
    }

    public function test_date_range_filters_the_summary(): void
    {
        Income::factory()->create(['amount' => 1000000, 'income_date' => now()->subMonths(2)->toDateString()]);
        Income::factory()->create(['amount' => 200000, 'income_date' => now()->toDateString()]);

        $this->actingAs($this->admin)
            ->get('/finances?date_from='.now()->startOfMonth()->toDateString().'&date_to='.now()->toDateString())
            ->assertOk()
            ->assertSee('200.000')
            ->assertDontSee('1.000.000');
    }

    public function test_store_income_creates_manual_income(): void
    {
        $this->actingAs($this->admin)
            ->post('/finances/incomes', [
                'description' => 'Venta de retazos',
                'amount' => 150000,
                'income_date' => now()->toDateString(),
            ])
            ->assertSessionHas('success');

        $income = Income::firstOrFail();

        $this->assertSame('other', $income->type);
        $this->assertSame('Venta de retazos', $income->description);
        $this->assertSame(150000.0, (float) $income->amount);
        $this->assertSame(now()->toDateString(), $income->income_date->format('Y-m-d'));
        $this->assertSame($this->admin->id, $income->user_id);
    }

    public function test_store_expense_creates_expense(): void
    {
        $this->actingAs($this->admin)
            ->post('/finances/expenses', [
                'category' => 'labor',
                'description' => 'Nómina semanal',
                'amount' => 1200000,
                'expense_date' => now()->toDateString(),
            ])
            ->assertSessionHas('success');

        $expense = Expense::firstOrFail();

        $this->assertSame('labor', $expense->category);
        $this->assertSame('Nómina semanal', $expense->description);
        $this->assertSame(1200000.0, (float) $expense->amount);
        $this->assertSame(now()->toDateString(), $expense->expense_date->format('Y-m-d'));
        $this->assertSame($this->admin->id, $expense->user_id);
    }

    public function test_validation_rejects_invalid_income(): void
    {
        $this->actingAs($this->admin)
            ->post('/finances/incomes', [
                'description' => '',
                'amount' => 0,
                'income_date' => 'not-a-date',
            ])
            ->assertSessionHasErrors(['description', 'amount', 'income_date']);
    }

    public function test_validation_rejects_invalid_expense(): void
    {
        $this->actingAs($this->admin)
            ->post('/finances/expenses', [
                'category' => 'invalid',
                'description' => '',
                'amount' => -5,
                'expense_date' => 'not-a-date',
            ])
            ->assertSessionHasErrors(['category', 'description', 'amount', 'expense_date']);
    }

    public function test_destroy_income_only_deletes_manual_incomes(): void
    {
        $manual = Income::factory()->create([
            'type' => 'other',
            'reference_type' => null,
            'reference_id' => null,
        ]);
        $saleIncome = Income::factory()->create([
            'type' => 'sale',
            'reference_type' => Sale::class,
            'reference_id' => 1,
        ]);

        $this->actingAs($this->admin)
            ->delete("/finances/incomes/{$manual->id}")
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('incomes', ['id' => $manual->id]);

        $this->actingAs($this->admin)
            ->delete("/finances/incomes/{$saleIncome->id}")
            ->assertSessionHas('error');

        $this->assertDatabaseHas('incomes', ['id' => $saleIncome->id]);
    }

    public function test_destroy_expense_deletes_expense(): void
    {
        $expense = Expense::factory()->create();

        $this->actingAs($this->admin)
            ->delete("/finances/expenses/{$expense->id}")
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('expenses', ['id' => $expense->id]);
    }
}
