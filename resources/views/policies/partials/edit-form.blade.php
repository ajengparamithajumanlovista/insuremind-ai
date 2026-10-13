<details style="margin-top:12px">
    <summary style="cursor:pointer;font-size:11.5px;font-weight:650;color:var(--blue)"
             data-id="Perbarui / hapus" data-en="Update / delete">Perbarui / hapus</summary>

    <form method="POST" action="{{ route('policies.update', $policy->id) }}" style="margin-top:12px">
        @csrf
        @method('PUT')

        <div class="grid grid-3">
            <div class="field">
                <label data-id="Perusahaan *" data-en="Provider *">Perusahaan *</label>
                <input type="text" name="provider_name" value="{{ $policy->provider_name }}" required>
            </div>
            <div class="field">
                <label data-id="Nama polis" data-en="Policy name">Nama polis</label>
                <input type="text" name="policy_name" value="{{ $policy->policy_name }}">
            </div>
            <div class="field">
                <label data-id="Nomor polis" data-en="Policy number">Nomor polis</label>
                <input type="text" name="policy_number" value="{{ $policy->policy_number }}">
            </div>
            <div class="field">
                <label data-id="Kategori *" data-en="Category *">Kategori *</label>
                <select name="category" required>
                    @foreach ($categories as $key)
                        <option value="{{ $key }}" @selected($policy->category === $key)>{{ $categoryNames[$key]['id'] ?? $key }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label data-id="Premi (Rp) *" data-en="Premium (IDR) *">Premi (Rp) *</label>
                <input type="text" name="premium_amount" inputmode="numeric"
                       value="{{ number_format($policy->premium_amount, 0, ',', '.') }}" required>
            </div>
            <div class="field">
                <label data-id="Pertanggungan (Rp)" data-en="Coverage (IDR)">Pertanggungan (Rp)</label>
                <input type="text" name="coverage_amount" inputmode="numeric"
                       value="{{ $policy->coverage_amount ? number_format($policy->coverage_amount, 0, ',', '.') : '' }}">
            </div>
            <div class="field">
                <label data-id="Periode *" data-en="Period *">Periode *</label>
                <select name="payment_period" required>
                    @foreach ($periodNames as $key => $label)
                        <option value="{{ $key }}" @selected($policy->payment_period === $key)>{{ $label['id'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label data-id="Mulai" data-en="Start">Mulai</label>
                <input type="date" name="start_date" value="{{ optional($policy->start_date)->format('Y-m-d') }}">
            </div>
            <div class="field">
                <label data-id="Jatuh tempo" data-en="Due date">Jatuh tempo</label>
                <input type="date" name="next_due_date" value="{{ optional($policy->next_due_date)->format('Y-m-d') }}">
            </div>
            <div class="field">
                <label data-id="Ingatkan (hari)" data-en="Remind (days)">Ingatkan (hari)</label>
                <input type="number" name="reminder_days" min="0" max="90" value="{{ $policy->reminder_days }}">
            </div>
            <div class="field" style="grid-column: span 2">
                <label data-id="Catatan" data-en="Notes">Catatan</label>
                <input type="text" name="notes" value="{{ $policy->notes }}">
            </div>
        </div>

        <div style="margin-top:12px">
            <button type="submit" class="btn btn-primary btn-sm"
                    data-id="Perbarui" data-en="Update">Perbarui</button>
        </div>
    </form>

    <form method="POST" action="{{ route('policies.destroy', $policy->id) }}"
          onsubmit="return confirm('Hapus polis ini? / Delete this policy?');" style="margin-top:9px">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger btn-sm"
                data-id="Hapus Polis" data-en="Delete Policy">Hapus Polis</button>
    </form>
</details>
