

<?php $__env->startSection('title', $pageTitle); ?>

<?php $__env->startSection('sidebar'); ?>
    <?php echo \Illuminate\View\Factory::parentPlaceholder('sidebar'); ?>
    <p>This is appended to the master sidebar.</p>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php echo $content; ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\business_suite\Public\Themes\Default\Views/index.blade.php ENDPATH**/ ?>