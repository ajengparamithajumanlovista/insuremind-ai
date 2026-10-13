<?php

namespace Database\Seeders;

use App\Models\InsuranceProduct;
use Illuminate\Database\Seeder;

/**
 * Data contoh produk asuransi untuk keperluan demo.
 *
 * Semua baris ditandai `is_sample = true` dan `data_source` yang jelas agar
 * pengguna tidak salah mengira ini penawaran resmi dari perusahaan asuransi.
 */
class InsuranceProductSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->products() as $product) {
            InsuranceProduct::updateOrCreate(
                ['slug' => $product['slug']],
                $product,
            );
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function products(): array
    {
        return array_merge(
            $this->healthProducts(),
            $this->lifeProducts(),
            $this->workProducts(),
            $this->educationProducts(),
        );
    }

    /**
     * @param  array<int, array{0: string, 1: string}>  $pairs
     * @return array<int, array{id: string, en: string}>
     */
    private function bi(array $pairs): array
    {
        return array_map(fn (array $pair) => ['id' => $pair[0], 'en' => $pair[1]], $pairs);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function healthProducts(): array
    {
        return [
            [
                'slug' => 'sehatku-rawat-inap-plus',
                'provider' => 'SehatKu',
                'name' => 'Rawatinap Plus',
                'category' => 'health',
                'monthly_premium' => 385_000,
                'annual_premium' => 4_235_000,
                'coverage_limit' => 500_000_000,
                'benefits' => $this->bi([
                    ['Rawat inap hingga Rp 500 juta per tahun', 'Inpatient care up to IDR 500 million per year'],
                    ['Kamar standar 1 tempat tidur', 'Standard single-bed room'],
                    ['Operasi & tindakan medis sesuai indikasi', 'Surgery and medical procedures as indicated'],
                    ['Rawat jalan kanker dan cuci darah', 'Outpatient cancer treatment and dialysis'],
                ]),
                'exclusions' => $this->bi([
                    ['Penyakit bawaan sejak lahir', 'Congenital conditions'],
                    ['Perawatan kosmetik tanpa indikasi medis', 'Cosmetic treatment without medical indication'],
                    ['Perang dan kerusuhan sipil', 'War and civil unrest'],
                ]),
                'claim_documents' => $this->bi([
                    ['Kartu peserta dan identitas', 'Member card and ID'],
                    ['Surat rujukan dokter', 'Doctor referral letter'],
                    ['Kwitansi asli dan rincian biaya', 'Original receipts and itemised bill'],
                ]),
                'waiting_period' => '30',
                'coverage_period' => '1 tahun / 1 year',
                'claim_process' => 'Ajukan klaim maksimal 30 hari setelah perawatan melalui aplikasi atau kantor cabang. / Submit within 30 days of treatment via app or branch.',
                'is_sample' => true,
                'data_source' => 'Data contoh InsureMind AI untuk demo',
                'notes' => 'Cocok untuk pekerja kantoran tanpa asuransi kesehatan dari kantor. / Suited to office workers without employer health cover.',
            ],
            [
                'slug' => 'amanah-medika-keluarga',
                'provider' => 'Amanah Medika',
                'name' => 'Medika Keluarga',
                'category' => 'health',
                'monthly_premium' => 610_000,
                'annual_premium' => 6_710_000,
                'coverage_limit' => 1_000_000_000,
                'benefits' => $this->bi([
                    ['Rawat inap hingga Rp 1 miliar per tahun', 'Inpatient care up to IDR 1 billion per year'],
                    ['Kamar VIP dengan penjaminan cepat', 'VIP room with fast admission guarantee'],
                    ['Manfaat rawat jalan bulanan', 'Monthly outpatient benefit'],
                    ['Tanggungan 2 anak', 'Covers up to 2 children'],
                ]),
                'exclusions' => $this->bi([
                    ['Kondisi pra-eksisting selama 12 bulan pertama', 'Pre-existing conditions for the first 12 months'],
                    ['Terapi eksperimental', 'Experimental therapy'],
                ]),
                'claim_documents' => $this->bi([
                    ['Kartu peserta dan identitas', 'Member card and ID'],
                    ['Diagnosis dokter spesialis', 'Specialist diagnosis'],
                    ['Rincian tagihan rumah sakit', 'Hospital billing details'],
                ]),
                'waiting_period' => '60',
                'coverage_period' => '1 tahun / 1 year',
                'claim_process' => 'Gunakan fasilitas cashless di rumah sakit rekanan; klaim reimbursement maksimal 30 hari. / Use cashless at partner hospitals; reimbursement claims within 30 days.',
                'is_sample' => true,
                'data_source' => 'Data contoh InsureMind AI untuk demo',
                'notes' => 'Premi lebih tinggi karena mencakup anggota keluarga. / Higher premium because it covers family members.',
            ],
            [
                'slug' => 'nusantara-care-essentials',
                'provider' => 'Nusantara Care',
                'name' => 'Care Essentials',
                'category' => 'health',
                'monthly_premium' => 215_000,
                'annual_premium' => 2_365_000,
                'coverage_limit' => 150_000_000,
                'benefits' => $this->bi([
                    ['Rawat inap hingga Rp 150 juta per tahun', 'Inpatient care up to IDR 150 million per year'],
                    ['Kamar standar kelas 3', 'Standard 3rd-class room'],
                    ['Manfaat melahirkan dasar', 'Basic maternity benefit'],
                ]),
                'exclusions' => $this->bi([
                    ['Perawatan gigi dan mata', 'Dental and optical care'],
                    ['Penyakit kritis lanjutan', 'Advanced critical illnesses'],
                ]),
                'claim_documents' => $this->bi([
                    ['Kartu peserta', 'Member card'],
                    ['Kwitansi dan resep dokter', 'Receipts and doctor prescriptions'],
                ]),
                'waiting_period' => '90',
                'coverage_period' => '1 tahun / 1 year',
                'claim_process' => 'Klaim reimbursement maksimal 30 hari setelah pulang rawat inap. / Reimbursement claims within 30 days after discharge.',
                'is_sample' => true,
                'data_source' => 'Data contoh InsureMind AI untuk demo',
                'notes' => 'Pilihan hemat untuk proteksi dasar. / An economical choice for basic protection.',
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function lifeProducts(): array
    {
        return [
            [
                'slug' => 'warisan-jiwa-sejahtera',
                'provider' => 'Warisan Jiwa',
                'name' => 'Sejahtera Term Life',
                'category' => 'life',
                'monthly_premium' => 310_000,
                'annual_premium' => 3_410_000,
                'coverage_limit' => 1_000_000_000,
                'benefits' => $this->bi([
                    ['Santunan meninggal Rp 1 miliar', 'Death benefit of IDR 1 billion'],
                    ['Manfaat tambahan kecelakaan 100%', '100% accidental death rider'],
                    ['Premi tetap selama masa polis', 'Level premium for the policy term'],
                ]),
                'exclusions' => $this->bi([
                    ['Bunuh diri pada 2 tahun pertama', 'Suicide within the first 2 years'],
                    ['Meninggal akibat tindak kriminal', 'Death due to criminal activity'],
                ]),
                'claim_documents' => $this->bi([
                    ['Akta kematian', 'Death certificate'],
                    ['Surat keterangan sebab kematian', 'Certificate of cause of death'],
                    ['Kartu peserta dan identitas ahli waris', 'Member card and beneficiary ID'],
                ]),
                'waiting_period' => '0',
                'coverage_period' => 'Hingga usia 70 tahun / Up to age 70',
                'claim_process' => 'Ahli waris mengajukan klaim maksimal 60 hari setelah kejadian. / Beneficiaries file the claim within 60 days of the event.',
                'is_sample' => true,
                'data_source' => 'Data contoh InsureMind AI untuk demo',
                'notes' => 'Fokus pada penggantian penghasilan keluarga. / Focused on replacing family income.',
            ],
            [
                'slug' => 'pelindung-keluarga-legacy',
                'provider' => 'Pelindung Keluarga',
                'name' => 'Legacy Whole Life',
                'category' => 'life',
                'monthly_premium' => 725_000,
                'annual_premium' => 7_975_000,
                'coverage_limit' => 1_500_000_000,
                'benefits' => $this->bi([
                    ['Santunan meninggal Rp 1,5 miliar', 'Death benefit of IDR 1.5 billion'],
                    ['Nilai tunai yang tumbuh', 'Growing cash value'],
                    ['Manfaat penyakit kritis Rp 250 juta', 'Critical illness benefit of IDR 250 million'],
                    ['Bebas premi jika cacat total', 'Premium waiver on total disability'],
                ]),
                'exclusions' => $this->bi([
                    ['Penyakit kritis pra-eksisting', 'Pre-existing critical illness'],
                    ['Olahraga berisiko tinggi tanpa pemberitahuan', 'High-risk sports without disclosure'],
                ]),
                'claim_documents' => $this->bi([
                    ['Akta kematian', 'Death certificate'],
                    ['Laporan medis', 'Medical report'],
                    ['Identitas ahli waris', 'Beneficiary identification'],
                ]),
                'waiting_period' => '90',
                'coverage_period' => 'Seumur hidup / Lifetime',
                'claim_process' => 'Klaim diajukan oleh ahli waris dengan pendampingan agen. / Claims filed by beneficiaries with agent assistance.',
                'is_sample' => true,
                'data_source' => 'Data contoh InsureMind AI untuk demo',
                'notes' => 'Premi lebih mahal karena ada nilai tunai dan manfaat kritis. / Costlier due to cash value and critical illness benefit.',
            ],
            [
                'slug' => 'mitra-hidup-aman-term',
                'provider' => 'Mitra Hidup',
                'name' => 'Aman Term 20',
                'category' => 'life',
                'monthly_premium' => 165_000,
                'annual_premium' => 1_815_000,
                'coverage_limit' => 750_000_000,
                'benefits' => $this->bi([
                    ['Santunan meninggal Rp 750 juta', 'Death benefit of IDR 750 million'],
                    ['Masa polis 20 tahun', '20-year policy term'],
                    ['Tanpa pemeriksaan medis untuk usia muda', 'No medical exam for younger applicants'],
                ]),
                'exclusions' => $this->bi([
                    ['Penyakit yang sudah ada saat mendaftar', 'Conditions existing at application'],
                    ['Meninggal karena bencana nuklir', 'Death from nuclear disaster'],
                ]),
                'claim_documents' => $this->bi([
                    ['Akta kematian', 'Death certificate'],
                    ['Kartu peserta', 'Member card'],
                ]),
                'waiting_period' => '0',
                'coverage_period' => '20 tahun / 20 years',
                'claim_process' => 'Klaim maksimal 60 hari setelah kejadian dengan dokumen lengkap. / Claim within 60 days with complete documents.',
                'is_sample' => true,
                'data_source' => 'Data contoh InsureMind AI untuk demo',
                'notes' => 'Premi rendah untuk perlindungan jiwa murni. / Low premium for pure life protection.',
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function workProducts(): array
    {
        return [
            [
                'slug' => 'kerja-aman-accident-cover',
                'provider' => 'Kerja Aman',
                'name' => 'Accident Cover',
                'category' => 'work',
                'monthly_premium' => 125_000,
                'annual_premium' => 1_375_000,
                'coverage_limit' => 300_000_000,
                'benefits' => $this->bi([
                    ['Santunan kecelakaan kerja Rp 300 juta', 'Work-accident benefit of IDR 300 million'],
                    ['Biaya pengobatan hingga Rp 25 juta', 'Medical costs up to IDR 25 million'],
                    ['Tunjangan harian rawat inap', 'Daily inpatient allowance'],
                ]),
                'exclusions' => $this->bi([
                    ['Kecelakaan di luar pekerjaan yang dijamin', 'Accidents outside covered employment'],
                    ['Kecelakaan akibat alkohol', 'Accidents caused by alcohol'],
                ]),
                'claim_documents' => $this->bi([
                    ['Laporan kecelakaan kerja', 'Work accident report'],
                    ['Surat keterangan dokter', 'Doctor certificate'],
                    ['Bukti hubungan kerja', 'Proof of employment'],
                ]),
                'waiting_period' => '14',
                'coverage_period' => '1 tahun / 1 year',
                'claim_process' => 'Laporkan kejadian maksimal 7 hari kerja, klaim maksimal 30 hari. / Report within 7 business days, claim within 30 days.',
                'is_sample' => true,
                'data_source' => 'Data contoh InsureMind AI untuk demo',
                'notes' => 'Relevan untuk pekerja lapangan dan teknis. / Relevant for field and technical workers.',
            ],
            [
                'slug' => 'proteksi-karier-disability',
                'provider' => 'Proteksi Karier',
                'name' => 'Disability Income Guard',
                'category' => 'work',
                'monthly_premium' => 260_000,
                'annual_premium' => 2_860_000,
                'coverage_limit' => 480_000_000,
                'benefits' => $this->bi([
                    ['Penggantian penghasilan bulanan hingga 60 bulan', 'Monthly income replacement for up to 60 months'],
                    ['Santunan cacat total tetap', 'Total permanent disability benefit'],
                    ['Rehabilitasi kerja', 'Occupational rehabilitation'],
                ]),
                'exclusions' => $this->bi([
                    ['Cacat akibat penyakit degeneratif', 'Disability from degenerative disease'],
                    ['Cedera saat aktivitas ilegal', 'Injury during illegal activity'],
                ]),
                'claim_documents' => $this->bi([
                    ['Laporan medis spesialis', 'Specialist medical report'],
                    ['Surat keterangan cacat', 'Disability certificate'],
                    ['Riwayat penghasilan', 'Income history'],
                ]),
                'waiting_period' => '30',
                'coverage_period' => '5 tahun / 5 years',
                'claim_process' => 'Klaim setelah masa tunggu 30 hari dengan bukti ketidakmampuan bekerja. / Claim after a 30-day wait with proof of inability to work.',
                'is_sample' => true,
                'data_source' => 'Data contoh InsureMind AI untuk demo',
                'notes' => 'Melindungi arus kas saat tidak bisa bekerja. / Protects cash flow when you cannot work.',
            ],
            [
                'slug' => 'sigap-kerja-kecelakaan-24jam',
                'provider' => 'Sigap Kerja',
                'name' => 'Kecelakaan 24 Jam',
                'category' => 'work',
                'monthly_premium' => 185_000,
                'annual_premium' => 2_035_000,
                'coverage_limit' => 200_000_000,
                'benefits' => $this->bi([
                    ['Perlindungan kecelakaan 24 jam', '24-hour accident protection'],
                    ['Santunan harian rawat inap Rp 300 ribu', 'Daily inpatient allowance of IDR 300k'],
                    ['Santunan meninggal akibat kecelakaan Rp 200 juta', 'Accidental death benefit of IDR 200 million'],
                ]),
                'exclusions' => $this->bi([
                    ['Penyakit bukan akibat kecelakaan', 'Non-accidental illness'],
                    ['Kecelakaan saat balapan', 'Accidents during racing'],
                ]),
                'claim_documents' => $this->bi([
                    ['Laporan polisi bila ada', 'Police report if applicable'],
                    ['Kwitansi pengobatan', 'Medical receipts'],
                ]),
                'waiting_period' => '7',
                'coverage_period' => '1 tahun / 1 year',
                'claim_process' => 'Klaim maksimal 30 hari setelah kejadian. / Claim within 30 days of the incident.',
                'is_sample' => true,
                'data_source' => 'Data contoh InsureMind AI untuk demo',
                'notes' => 'Menjangkau aktivitas non-kerja (olahraga, perjalanan). / Covers non-work activities (sports, travel).',
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function educationProducts(): array
    {
        return [
            [
                'slug' => 'cendekia-pendidikan-aman',
                'provider' => 'Cendekia',
                'name' => 'Pendidikan Aman',
                'category' => 'education',
                'monthly_premium' => 425_000,
                'annual_premium' => 4_675_000,
                'coverage_limit' => 250_000_000,
                'benefits' => $this->bi([
                    ['Dana pendidikan terjadwal', 'Scheduled education fund'],
                    ['Bebas premi bila orang tua meninggal', 'Premium waiver if the parent passes away'],
                    ['Nilai tunai setelah tahun ke-3', 'Cash value after year 3'],
                ]),
                'exclusions' => $this->bi([
                    ['Berhenti menabung sebelum 12 bulan', 'Stopping contributions before 12 months'],
                    ['Penggunaan dana di luar pendidikan', 'Using funds outside education'],
                ]),
                'claim_documents' => $this->bi([
                    ['Akta kematian (untuk bebas premi)', 'Death certificate (for premium waiver)'],
                    ['Identitas anak dan orang tua', 'Child and parent identification'],
                ]),
                'waiting_period' => '365',
                'coverage_period' => 'Hingga anak usia 25 tahun / Until the child turns 25',
                'claim_process' => 'Pencairan sesuai jadwal; klaim bebas premi maksimal 60 hari. / Payouts follow the schedule; waiver claims within 60 days.',
                'is_sample' => true,
                'data_source' => 'Data contoh InsureMind AI untuk demo',
                'notes' => 'Cocok untuk anak usia di bawah 10 tahun. / Suited to children under 10.',
            ],
            [
                'slug' => 'masa-depan-pendidikan-plus',
                'provider' => 'Masa Depan',
                'name' => 'Pendidikan Plus',
                'category' => 'education',
                'monthly_premium' => 690_000,
                'annual_premium' => 7_590_000,
                'coverage_limit' => 400_000_000,
                'benefits' => $this->bi([
                    ['Dana pendidikan hingga Rp 400 juta', 'Education fund up to IDR 400 million'],
                    ['Manfaat kesehatan dasar untuk anak', 'Basic health benefit for the child'],
                    ['Bebas premi bila orang tua cacat total', 'Premium waiver on total parental disability'],
                ]),
                'exclusions' => $this->bi([
                    ['Biaya kursus non-formal', 'Non-formal course fees'],
                    ['Berhenti menabung sebelum 24 bulan', 'Stopping contributions before 24 months'],
                ]),
                'claim_documents' => $this->bi([
                    ['Bukti pembayaran premi', 'Premium payment proof'],
                    ['Dokumen pendukung ketidakmampuan (bila ada)', 'Supporting disability documents (if any)'],
                ]),
                'waiting_period' => '365',
                'coverage_period' => 'Hingga anak usia 25 tahun / Until the child turns 25',
                'claim_process' => 'Pencairan tahap pendidikan dengan pemberitahuan 30 hari sebelumnya. / Education-stage payouts with 30 days notice.',
                'is_sample' => true,
                'data_source' => 'Data contoh InsureMind AI untuk demo',
                'notes' => 'Kombinasi tabungan pendidikan dan proteksi. / A blend of education savings and protection.',
            ],
            [
                'slug' => 'cerdas-jiwa-pendidikan-dasar',
                'provider' => 'Cerdas Jiwa',
                'name' => 'Pendidikan Dasar',
                'category' => 'education',
                'monthly_premium' => 245_000,
                'annual_premium' => 2_695_000,
                'coverage_limit' => 120_000_000,
                'benefits' => $this->bi([
                    ['Dana pendidikan Rp 120 juta', 'Education fund of IDR 120 million'],
                    ['Santunan tambahan jika orang tua meninggal', 'Additional benefit if the parent passes away'],
                ]),
                'exclusions' => $this->bi([
                    ['Biaya pendidikan di luar negeri', 'Overseas education costs'],
                    ['Berhenti menabung sebelum 12 bulan', 'Stopping contributions before 12 months'],
                ]),
                'claim_documents' => $this->bi([
                    ['Identitas penanggung dan anak', 'Insured parent and child identification'],
                    ['Bukti pembayaran premi', 'Premium payment proof'],
                ]),
                'waiting_period' => '365',
                'coverage_period' => 'Hingga anak usia 18 tahun / Until the child turns 18',
                'claim_process' => 'Pencairan pada jenjang SD hingga SMA sesuai jadwal polis. / Payouts at primary to high-school stages per policy schedule.',
                'is_sample' => true,
                'data_source' => 'Data contoh InsureMind AI untuk demo',
                'notes' => 'Pilihan premi paling ringan untuk dana pendidikan. / The lightest-premium education option.',
            ],
        ];
    }
}
