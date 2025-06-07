<?= $this->extend('backend/layout/auth-layout') ?>

<?= $this->section('content') ?>

<!-- Authentication card start -->
<?php $validation = \Config\Services::validation() ?>
<form class="md-float-material form-material" action="<?= route_to('admin.login.handler') ?>" method="post">
    <?= csrf_field() ?>
    <div class="text-center">
        <!-- <img src="/backend/assets/images/logo.png" alt="logo.png"> -->
    </div>
    <div class="auth-box card">
        <div class="card-block">
            <div class="row m-b-20">
                <div class="col-md-12">
                    <h3 class="text-center">Login Admin</h3>
                </div>
            </div>

            <?php if (!empty(session()->getFlashdata('success'))) : ?>
            <div class="alert alert-success">
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php endif; ?>

            <?php if (!empty(session()->getFlashdata('fail'))) : ?>
            <div class="alert alert-danger">
                <?= session()->getFlashdata('fail') ?>

                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php endif; ?>

            <div class="form-group form-primary mb-4">
                <input type="text" name="login_id" class="form-control" value="<?= set_value('login_id') ?>">
                <span class="form-bar"></span>
                <label class="float-label">Username OR Email</label>
            </div>

            <?php if ($validation->getError('login_id')) : ?>
            <div class="d-block text-danger" style="margin-top:-25px; margin-bottom:25px">
                <?= $validation->getError('login_id') ?>
            </div>
            <?php endif; ?>

            <div class="form-group form-primary mb-4">
                <input type="password" name="password" class="form-control" value="<?= set_value('password') ?>">
                <span class="form-bar"></span>
                <label class="float-label">Password</label>
            </div>

            <?php if ($validation->getError('password')) : ?>
            <div class="d-block text-danger" style="margin-top:-25px; margin-bottom:25px">
                <?= $validation->getError('password') ?>
            </div>
            <?php endif; ?>

            <div class="row m-t-25 text-left">
                <div class="col-12">
                    <div class="checkbox-fade fade-in-primary d-">
                        <label>
                            <input type="checkbox" value="">
                            <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span>
                            <span class="text-inverse">Remember me</span>
                        </label>
                    </div>
                    <div class="forgot-phone text-right f-right">
                        <a href="#" class="text-right f-w-600"> Forgot Password?</a>
                    </div>
                </div>
            </div>
            <div class="row m-t-30">
                <div class="col-md-12">
                    <button type="submit"
                        class="btn btn-primary btn-md btn-block waves-effect waves-light text-center m-b-20">Sign
                        in</button>
                </div>
            </div>

        </div>
    </div>
</form>
<!-- end of form -->

<?= $this->endSection('content') ?>