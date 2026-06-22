<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

<section class="content-header">
    <div class="container-fluid">
        <h1>New Recipe</h1>
    </div>
</section>

<section class="content">
<div class="container-fluid">

<div class="card">
    <div class="card-body">

        <form id="recipeForm">

            <div class="form-group">
                <label>Pilih Produk</label>

                <select id="productSelect" class="form-control"></select>
            </div>

            <button type="submit" class="btn btn-success">
                Lanjut Kelola BOM
            </button>

        </form>

    </div>
</div>

</div>
</section>

<?= $this->endSection() ?>

<?= $this->section('script') ?>

<script>

$('#productSelect').select2({
    ajax:{
        url:'<?= site_url('admin/recipes/getProductAjax') ?>',
        dataType:'json',
        delay:250,
        data:function(params){
            return {
                q:params.term
            };
        },
        processResults:function(data){
            return {
                results:data
            };
        }
    },
    minimumInputLength:1
});

$('#recipeForm').submit(function(e){

    e.preventDefault();

    let productId = $('#productSelect').val();

    if(!productId){
        alert('Pilih produk terlebih dahulu');
        return;
    }

    window.location.href =
        '<?= site_url('admin/recipes/detail') ?>/' + productId;

});

</script>

<?= $this->endSection() ?>