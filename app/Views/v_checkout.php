<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<div class="row">
    <div class="col-lg-6">
        <?= form_open('buy', 'class="row g-3"') ?>

            <?= form_hidden('username', session()->get('username')) ?>

            <?= form_input([
                'type'  => 'hidden',
                'name'  => 'total_harga',
                'id'    => 'total_harga',
                'value' => '']) ?>

            <div class="col-12">
                <?= form_label('Nama', 'nama', ['class' => 'form-label']) ?>
                <?= form_input([
                    'name'     => 'nama',
                    'id'       => 'nama',
                    'class'    => 'form-control',
                    'value'    => session()->get('username'),
                    'readonly' => true]) ?>
            </div>

            <div class="col-12">
                <?= form_label('Alamat', 'alamat', ['class' => 'form-label']) ?>
                <?= form_input([
                    'name'  => 'alamat',
                    'id'    => 'alamat',
                    'class' => 'form-control']) ?>
            </div> 

            <div class="col-12"> 
                <?= form_label('Kelurahan', 'kelurahan', ['class' => 'form-label']) ?>
                <?= form_dropdown('kelurahan', [], '', ['id' => 'kelurahan', 'class' => 'form-control']) ?>
            </div>

            <div class="col-12"> 
                <?= form_label('Layanan', 'layanan', ['class' => 'form-label']) ?> 
                <?= form_dropdown('layanan', [], '', ['id' => 'layanan', 'class' => 'form-control']) ?>
            </div>

            <div class="col-12">
                <?= form_label('Ongkir', 'ongkir', ['class' => 'form-label']) ?>
                <?= form_input([
                    'name'     => 'ongkir',
                    'id'       => 'ongkir',
                    'class'    => 'form-control',
                    'readonly' => true]) ?>
            </div>
            
            <div class="col-12">
                <?= form_label('Kode Kupon', 'kupon_code', ['class' => 'form-label']) ?>
                <?= form_input([
                    'name'        => 'kupon_code',
                    'id'          => 'kupon_code',
                    'class'       => 'form-control',
                    'placeholder' => 'Masukkan kode kupon (contoh: HEMAT, SUPER)']) ?>
            </div>

            <div class="col-12">
                <?= form_submit(
                    'submit',
                    'Buat Pesanan',
                    ['class' => 'btn btn-primary']) ?>
            </div>

        <?= form_close() ?> 
    </div>
    
    <div class="col-lg-6">
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">Nama</th>
                    <th scope="col">Harga</th>
                    <th scope="col">Jumlah</th>
                    <th scope="col">Sub Total</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if (!empty($items)) :
                    foreach ($items as $index => $item) :
                ?>
                        <tr>
                            <td><?= $item['name'] ?></td>
                            <td><?= number_to_currency($item['price'], 'IDR') ?></td>
                            <td><?= $item['qty'] ?></td>
                            <td><?= number_to_currency($item['price'] * $item['qty'], 'IDR') ?></td>
                        </tr>
                <?php
                    endforeach;
                endif;
                ?>
                <tr>
                    <td colspan="2"></td>
                    <td>Subtotal</td>
                    <td><?= number_to_currency($total, 'IDR') ?></td>
                </tr>
                <tr id="row-diskon-kupon" class="text-success" style="display: none; font-weight: bold;">
                    <td colspan="2"></td>
                    <td>Diskon Kupon (<span id="diskon-persen">0</span>%)</td>
                    <td>-<span id="diskon-nominal">IDR 0,00</span></td>
                </tr>
                <tr id="row-biaya-admin" style="display: none; font-weight: bold;">
                    <td colspan="2"></td>
                    <td>Biaya Admin</td>
                    <td>+<span id="biaya-admin-nominal">IDR 0,00</span></td>
                </tr>
                <tr id="row-cashback" class="text-info" style="display: none; font-weight: bold;">
                    <td colspan="2"></td>
                    <td>Cashback</td>
                    <td><span id="cashback-nominal">IDR 0,00</span></td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td>Ongkir</td>
                    <td>+<span id="ongkir-label">IDR 0,00</span></td>
                </tr>
                <tr class="table-active fw-bold">
                    <td colspan="2"></td>
                    <td>Grand Total</td>
                    <td><span id="grand-total"><?= number_to_currency($total, 'IDR') ?></span></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script>
$(document).ready(function() {
    let ongkir = 0;
    let subtotal = <?= $total ?>;
    hitungTotal();

    function formatRupiah(val) {
        return `IDR ${val.toLocaleString('id-ID')},00`;
    }

    function hitungTotal() {
        let s = parseInt(subtotal) || 0;
        let o = parseInt(ongkir) || 0;

        // --- Biaya Admin (0.5% jika <= 20jt, 0.75% jika > 20jt) ---
        let biayaAdmin = s <= 20000000 ? s * 0.005 : s * 0.0075;
        biayaAdmin = Math.round(biayaAdmin);

        // --- Kupon Promo ---
        let kuponCode = ($("#kupon_code").val() || "").toUpperCase().trim();
        let persenDiskon = 0;
        if (kuponCode === 'HEMAT') persenDiskon = 15;
        else if (kuponCode === 'SUPER') persenDiskon = 20;
        let diskonKupon = Math.round((persenDiskon / 100) * s);

        // --- Cashback (2% jika > 10jt) ---
        let cashback = s > 10000000 ? Math.round(s * 0.02) : 0;

        // --- Tampilkan Biaya Admin ---
        $("#biaya-admin-nominal").text(formatRupiah(biayaAdmin));
        if (biayaAdmin > 0) $("#row-biaya-admin").show(); else $("#row-biaya-admin").hide();

        // --- Tampilkan Diskon Kupon ---
        if (persenDiskon > 0) {
            $("#diskon-persen").text(persenDiskon);
            $("#diskon-nominal").text(formatRupiah(diskonKupon));
            $("#row-diskon-kupon").show();
        } else {
            $("#row-diskon-kupon").hide();
        }

        // --- Tampilkan Cashback ---
        if (cashback > 0) {
            $("#cashback-nominal").text(formatRupiah(cashback));
            $("#row-cashback").show();
        } else {
            $("#row-cashback").hide();
        }

        // --- Ongkir ---
        $("#ongkir").val(o);
        $("#ongkir-label").text(formatRupiah(o));

        // --- Grand Total = Subtotal - Diskon + Biaya Admin + Ongkir ---
        let grandTotal = s - diskonKupon + biayaAdmin + o;
        $("#grand-total").text(formatRupiah(grandTotal));
        $("#total_harga").val(grandTotal);
    }

    // Event: ketika kupon diketik
    $("#kupon_code").on('input', function() {
        hitungTotal();
    });

    // 1. Inisialisasi Select2 Kelurahan 
    $('#kelurahan').select2({
        placeholder: 'Cari daerah tujuan',
        minimumInputLength: 3,
        ajax: {
            url: '<?= site_url('ajax/destinations') ?>',
            dataType: 'json',
            delay: 300,
            data: function(params) {
                return { q: params.term };
            },
            processResults: function(data) {
                return data;
            },
            cache: true
        }
    });

    // 2. Event On Change Kelurahan -> Ambil Data Ongkir Layanan 
    $("#kelurahan").on('change', function () {
        let id_kelurahan = $(this).val();

        $("#layanan").empty().append('<option value="0">-- Pilih Layanan --</option>');
        ongkir = 0;
        hitungTotal(); 

        if (id_kelurahan) {
            $.ajax({
                url: '<?= site_url('ajax/services') ?>', 
                type: 'GET',
                data: { destination: id_kelurahan },
                dataType: 'json',
                success: function(data) {
                    console.log("Data sukses diterima:", data);

                    let listLayanan = data.results ?? [];

                    if (listLayanan && listLayanan.length > 0) {
                        $.each(listLayanan, function(index, item) {
                            $("#layanan").append(
                                `<option value="${item.cost}">
                                    ${item.name}
                                 </option>`
                            );
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", error);
                }
            });
        }
    });

    // 3. Ketika Layanan Dipilih -> Ambil Nilai dari Value
    $("#layanan").on('change', function() {
        let hargaTerpilih = $(this).val();
        
        ongkir = parseInt(hargaTerpilih) || 0;
        hitungTotal();
    });
});
</script>
<?= $this->endSection() ?>