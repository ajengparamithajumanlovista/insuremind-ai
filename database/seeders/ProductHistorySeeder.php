<?php

namespace Database\Seeders;

use App\Models\InsuranceProduct;
use App\Models\ProductHistory;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Riwayat perubahan katalog produk contoh.
 *
 * Mengisi tab "Riwayat" pada detail produk dengan contoh perubahan premi,
 * manfaat, dan status aktif sehingga jejak perubahan katalog dapat dilihat.
 */
class ProductHistorySeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->where('email', 'admin@insuremind.ai')->first();

        foreach ($this->histories() as $slug => $entries) {
            $product = InsuranceProduct::query()->where('slug', $slug)->first();

            if (! $product) {
                continue;
            }

            foreach ($entries as $entry) {
                ProductHistory::updateOrCreate(
                    [
                        'insurance_product_id' => $product->getKey(),
                        'action' => $entry['action'],
                        'field' => $entry['field'],
                        'new_value' => $entry['new_value'],
                    ],
                    [
                        'user_id' => $admin?->getKey(),
                        'old_value' => $entry['old_value'] ?? null,
                        'reason' => $entry['reason'] ?? null,
                    ],
                );
            }
        }
    }

    /**
     * @return array<string, array<int, array<string, mixed>>>
     */
    private function histories(): array
    {
        return [
            'sehatku-rawat-inap-plus' => [
                [
                    'action' => 'updated',
                    'field' => 'monthly_premium',
                    'old_value' => '350000',
                    'new_value' => '385000',
                    'reason' => 'Penyesuaian premi tahunan sesuai kebijakan penyedia.',
                ],
                [
                    'action' => 'updated',
                    'field' => 'coverage_limit',
                    'old_value' => '400000000',
                    'new_value' => '500000000',
                    'reason' => 'Peningkatan batas pertanggungan tahunan.',
                ],
            ],
            'amanah-medika-keluarga' => [
                [
                    'action' => 'updated',
                    'field' => 'is_active',
                    'old_value' => '0',
                    'new_value' => '1',
                    'reason' => 'Produk diaktifkan kembali setelah verifikasi data.',
                ],
            ],
        ];
    }
}
