<html>
    <head>
        <title><?php echo e($site_name); ?> - <?php echo $__env->yieldContent('title'); ?></title>
        <?php echo Assoto\Stack::display('head'); ?>

    </head>
    <body>
        <?php $__env->startSection('sidebar'); ?>
            This is the master sidebar.
        <?php echo $__env->yieldSection(); ?>

        <div class="container">
            <?php echo $__env->yieldContent('content'); ?>
        </div>
        <?php echo Assoto\Stack::display('footer'); ?>

    </body>
</html><?php /**PATH C:\xampp\htdocs\business_suite\Public\Themes\Default\Views/layouts/app.blade.php ENDPATH**/ ?>