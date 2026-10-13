<?php

namespace App\Http\Controllers;

use App\Models\InsuranceProduct;
use App\Services\InsuranceComparisonService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CategoryController extends Controller
{
    public function __construct(
        private readonly InsuranceComparisonService $comparison,
    ) {}

    /**
     * Halaman kategori produk (kesehatan, jiwa, risiko kerja, pendidikan).
     */
    public function show(Request $request, string $category): View
    {
        if (! in_array($category, InsuranceProduct::CATEGORIES, true)) {
            throw new NotFoundHttpException('Kategori tidak ditemukan.');
        }

        $products = $this->comparison->products($category);

        $profile = $request->user()->profileOrDefault();
        $profile = $profile->isReady() ? $profile : null;

        $suitability = $products->mapWithKeys(fn (InsuranceProduct $product) => [
            $product->id => $this->comparison->suitability($product, $profile),
        ])->all();

        return view('categories.show', [
            'category' => $category,
            'meta' => $this->meta($category),
            'products' => $products,
            'suitability' => $suitability,
        ]);
    }

    /**
     * Metadata kategori (judul, deskripsi, dan penekanan) dua bahasa.
     *
     * @return array{title: array{id: string, en: string}, description: array{id: string, en: string}, icon: string}
     */
    private function meta(string $category): array
    {
        return match ($category) {
            'health' => [
                'title' => ['id' => 'Asuransi Kesehatan', 'en' => 'Health Insurance'],
                'description' => [
                    'id' => 'Menanggung biaya rawat inap, operasi, dan pengobatan agar tabungan tidak terkuras saat sakit.',
                    'en' => 'Covers hospitalisation, surgery, and treatment costs so your savings stay intact when illness strikes.',
                ],
                'icon' => 'heart',
            ],
            'life' => [
                'title' => ['id' => 'Asuransi Jiwa', 'en' => 'Life Insurance'],
                'description' => [
                    'id' => 'Memberi santunan kepada keluarga bila pencari nafkah meninggal, menggantikan penghasilan yang hilang.',
                    'en' => 'Provides a payout to the family if the earner passes away, replacing the lost income.',
                ],
                'icon' => 'shield',
            ],
            'work' => [
                'title' => ['id' => 'Asuransi Risiko Kerja', 'en' => 'Work-Risk Insurance'],
                'description' => [
                    'id' => 'Melindungi dari kecelakaan kerja, cacat, dan kehilangan penghasilan akibat risiko pekerjaan.',
                    'en' => 'Protects against work accidents, disability, and lost income caused by occupational risk.',
                ],
                'icon' => 'briefcase',
            ],
            default => [
                'title' => ['id' => 'Asuransi Pendidikan', 'en' => 'Education Insurance'],
                'description' => [
                    'id' => 'Menyiapkan dana pendidikan anak secara terencana dan terlindungi dari risiko orang tua.',
                    'en' => 'Prepares your child\u2019s education fund in a planned way, protected from parental risk.',
                ],
                'icon' => 'graduation',
            ],
        };
    }
}