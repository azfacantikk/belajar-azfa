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
                <tr>
                    <td colspan="2"></td>
                    <td>Total</td>
                    <td><span id="total"><?= number_to_currency($total, 'IDR') ?></span></td>
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

    function hitungTotal() {
        let subtotalNum = parseInt(subtotal) || 0;
        let ongkirNum = parseInt(ongkir) || 0;
        let total = subtotalNum + ongkirNum;

        $("#ongkir").val(ongkirNum);
        $("#total").text(`IDR ${total.toLocaleString('id-ID')},00`);
        $("#total_harga").val(total);
    }

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
                            // Render teks panjang bawaan modul dosen ke dropdown
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