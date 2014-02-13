<?php get_header(); ?>
<?php if ($user->isLoggedIn()): ?>
    <div class="aside-25 dashboard">
    <?php include 'sidebar_dashboard.php'; ?>
</div>
<?php endif; ?>
<div class="aside-75 dashboard">