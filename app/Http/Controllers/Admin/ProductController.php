<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\InsuranceProduct;
use App\Models\ProductHistory;
use App\Services\AuditLogger;
use App\Support\AuditCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Katalog Produk (khusus admin).
 *
 * Setiap perubahan penting (premi, manfaat, status aktif) ditulis ke
 * `product_histories` sekaligus ke Audit Log sehingga riwayat katalog dapat
 * ditelusuri dari dua sudut: halaman produk dan halaman audit.
 */
class ProductController extends Controller
{
    public function __construct(
        private readonly AuditLogger $audit,
    ) {}

    /**
     * Daftar produk dengan pencarian dan filter kategori/status.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $category = (string) $request->query('category', '');
        $visibility = (string) $request->query('visibility', '');

        $products = InsuranceProduct::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('provider', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
            })
            ->when(in_array($category, InsuranceProduct::CATEGORIES, true), fn ($query) => $query->where('category', $category))
            ->when($visibility === 'active', fn ($query) => $query->where('is_active', true))
            ->when($visibility === 'inactive', fn ($query) => $query->where('is_active', false))
            ->orderBy('provider')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.products.index', [
            'products' => $products,
            'filters' => ['q' => $search, 'category' => $category, 'visibility' => $visibility],
            'categories' => InsuranceProduct::CATEGORIES,
            'summary' => [
                'total' => InsuranceProduct::query()->count(),
                'active' => InsuranceProduct::query()->where('is_active', true)->count(),
                'inactive' => InsuranceProduct::query()->where('is_active', false)->count(),
                'sample' => InsuranceProduct::query()->where('is_sample', true)->count(),
            ],
        ]);
    }

    /**
     * Formulir penambahan produk baru.
     */
    public function create(): View
    {
        return view('admin.products.create', [
            'categories' => InsuranceProduct::CATEGORIES,
        ]);
    }

    /**
     * Simpan produk baru ke katalog.
     *
     * Slug dibuat otomatis dari penyedia + nama produk lalu dijaga tetap unik
     * (menambahkan akhiran angka bila bentrok). Seluruh kolom katalog ikut
     * tersimpan, dan penambahan dicatat ke Audit Log dengan riwayat "created".
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $product = InsuranceProduct::create([
            'slug' => $this->uniqueSlug($data['name'], $data['provider']),
            // Kolom JSON wajib diisi; produk baru dimulai tanpa daftar dokumen.
            'claim_documents' => [],
            ...$this->payload($data),
        ]);

        $this->writeHistory(
            $product,
            'created',
            'name',
            null,
            $data['name'],
            $data['reason'] ?? 'Produk baru ditambahkan ke katalog.',
            $request
        );

        $this->audit->log([
            'category' => AuditCategory::INSURANCE_PRODUCT,
            'action' => 'product.created',
            'description' => "Menambahkan produk {$product->name} ke katalog.",
            'subject' => $product,
            'before' => null,
            'after' => $this->flatten($product->only([
                'provider', 'name', 'category', 'monthly_premium',
                'annual_premium', 'coverage_limit', 'is_active', 'is_sample',
            ])),
            'reason' => $data['reason'] ?? null,
        ]);

        return redirect()
            ->route('admin.products.show', $product)
            ->with('status', 'Produk baru ditambahkan ke katalog.|New product added to catalog.');
    }

    /**
     * Detail produk beserta riwayat perubahannya.
     */
    public function show(InsuranceProduct $product): View
    {
        return view('admin.products.show', [
            'product' => $product,
            'histories' => $product->histories()->with('user')->take(20)->get(),
            'auditLogs' => AuditLog::query()
                ->where('subject_type', InsuranceProduct::class)
                ->where('subject_id', $product->getKey())
                ->latest('id')
                ->take(15)
                ->get(),
        ]);
    }

    /**
     * Ubah cepat ketersediaan produk (tanpa membuka halaman detail).
     */
    public function toggle(Request $request, InsuranceProduct $product): RedirectResponse
    {
        $target = ! $product->is_active;
        $before = $product->is_active;

        $product->update(['is_active' => $target]);

        $description = $target
            ? 'Mengaktifkan produk pada katalog.'
            : 'Menonaktifkan produk pada katalog.';

        $this->writeHistory($product, 'updated', 'is_active', $before, $target, $description, $request);

        $this->audit->log([
            'category' => AuditCategory::INSURANCE_PRODUCT,
            'action' => 'product.is_active_changed',
            'description' => $description.' ('.$product->name.')',
            'subject' => $product,
            'before' => ['is_active' => $before],
            'after' => ['is_active' => $target],
            'reason' => $description,
        ]);

        return back()->with('status', 'Status produk diperbarui.|Product status updated.');
    }

    /**
     * Simpan perubahan produk (halaman detail).
     *
     * Deskripsi dan manfaat menerima teks dua bahasa (id/en). Field angka
     * opsional dikosongkan menjadi null bila tidak diisi.
     */
    public function update(Request $request, InsuranceProduct $product): RedirectResponse
    {
        $data = $this->validated($request);

        $payload = $this->payload($data);

        $before = $this->flatten($product->only(array_keys($payload)));
        $product->update($payload);
        $after = $this->flatten($product->only(array_keys($payload)));

        $diff = $this->audit->diff($before, $after);

        foreach ($diff['after'] as $field => $newValue) {
            $this->writeHistory(
                $product,
                'updated',
                $field,
                $diff['before'][$field] ?? null,
                $newValue,
                $data['reason'] ?? null,
                $request
            );
        }

        if ($diff['after'] !== []) {
            $this->audit->log([
                'category' => AuditCategory::INSURANCE_PRODUCT,
                'action' => 'product.updated',
                'description' => "Memperbarui produk {$product->name}.",
                'subject' => $product,
                'before' => $diff['before'],
                'after' => $diff['after'],
                'reason' => $data['reason'] ?? null,
            ]);
        }

        return back()->with('status', 'Perubahan produk tersimpan.|Product changes saved.');
    }

    /**
     * Aturan validasi bersama untuk pembuatan dan pembaruan produk.
     *
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'provider' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', Rule::in(InsuranceProduct::CATEGORIES)],
            'monthly_premium' => ['required', 'numeric', 'min:0'],
            'annual_premium' => ['nullable', 'numeric', 'min:0'],
            'coverage_limit' => ['required', 'numeric', 'min:0'],
            'description_id' => ['nullable', 'string', 'max:2000'],
            'description_en' => ['nullable', 'string', 'max:2000'],
            'benefits' => ['nullable', 'string', 'max:4000'],
            'exclusions' => ['nullable', 'string', 'max:4000'],
            'source_url' => ['nullable', 'url', 'max:500'],
            'reason' => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
            'is_sample' => ['nullable', 'boolean'],
        ]);
    }

    /**
     * Bentuk data siap-simpan dari hasil validasi.
     *
     * Dipakai bersama oleh `store()` dan `update()` agar kedua alur selalu
     * menulis kolom yang sama. Field angka & daftar yang kosong diberi nilai
     * netral (0 / daftar kosong) karena kolomnya NOT NULL pada skema.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function payload(array $data): array
    {
        return [
            'provider' => $data['provider'],
            'name' => $data['name'],
            'category' => $data['category'],
            'monthly_premium' => (float) $data['monthly_premium'],
            'annual_premium' => filled($data['annual_premium'] ?? null) ? (float) $data['annual_premium'] : 0.0,
            'coverage_limit' => (float) $data['coverage_limit'],
            'description' => array_filter([
                'id' => $data['description_id'] ?? null,
                'en' => $data['description_en'] ?? null,
            ], fn ($value) => filled($value)),
            'benefits' => $this->lines($data['benefits'] ?? null) ?? [],
            'exclusions' => $this->lines($data['exclusions'] ?? null) ?? [],
            'source_url' => $data['source_url'] ?? null,
            'is_active' => (bool) ($data['is_active'] ?? false),
            'is_sample' => (bool) ($data['is_sample'] ?? false),
        ];
    }

    /**
     * Slug unik dari penyedia + nama produk.
     *
     * Bila slug sudah dipakai, akhiran angka ditambahkan (`-2`, `-3`, ...)
     * sampai tersedia sehingga penambahan produk tidak pernah gagal karena
     * bentrok kolom unik.
     */
    private function uniqueSlug(string $name, string $provider): string
    {
        $base = Str::slug($provider.' '.$name);
        $base = $base !== '' ? $base : 'produk';
        $slug = $base;
        $suffix = 2;

        while (InsuranceProduct::query()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }

    /**
     * Catat satu perubahan field ke riwayat produk.
     */
    private function writeHistory(
        InsuranceProduct $product,
        string $action,
        string $field,
        mixed $old,
        mixed $new,
        ?string $reason,
        Request $request
    ): void {
        ProductHistory::create([
            'insurance_product_id' => $product->getKey(),
            'user_id' => $request->user()?->getKey(),
            'action' => $action,
            'field' => $field,
            'old_value' => $this->scalar($old),
            'new_value' => $this->scalar($new),
            'reason' => $reason,
        ]);
    }

    /**
     * Ubah input multiline menjadi daftar baris bersih.
     *
     * @return list<string>|null
     */
    private function lines(?string $value): ?array
    {
        if (! filled($value)) {
            return null;
        }

        $lines = array_map(
            fn ($line) => trim((string) Str::of($line)->ltrim("-•\t ")),
            preg_split('/\r?\n/', (string) $value)
        );

        return array_values(array_filter($lines, fn ($line) => $line !== ''));
    }

    /**
     * Ratakan nilai array (mis. deskripsi dua bahasa) menjadi string agar
     * perbandingan before/after tidak menghasilkan false positive.
     *
     * @param  array<string, mixed>  $values
     * @return array<string, mixed>
     */
    private function flatten(array $values): array
    {
        return array_map(fn ($value) => is_array($value) ? json_encode($value) : $value, $values);
    }

    private function scalar(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        return is_array($value) ? json_encode($value) : (string) $value;
    }
}
