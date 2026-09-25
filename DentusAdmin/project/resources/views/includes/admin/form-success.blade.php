@if ($message = Session::get('failure'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="mdi mdi-block-helper me-2"></i>
    <?= $message ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
@if ($message = Session::get('something'))
<div class="alert alert-warning alert-dismissible fade show" role="alert">
    <i class="mdi mdi-alert-outline me-2"></i>
    <?= $message ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
@if ($message = Session::get('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="mdi mdi-check-all me-2"></i>
    <?= $message ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif