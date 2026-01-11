<footer class="site-footer">
    <div class="footer-widgets"><div class="container"><div class="footer-grid">
        <div class="footer-col">
            <h3>Sobre</h3><p><?php bloginfo('name'); ?> é seu guia de tecnologia.</p>
        </div>
        <div class="footer-col">
            <h3>Menu</h3><?php wp_nav_menu( array( 'theme_location' => 'wp_devs_footer_menu' )); ?>
        </div>
        <div class="footer-col">
            <h3>Aviso</h3><p>Participamos de programas de afiliados e podemos receber comissões.</p>
        </div>
    </div></div></div>
    <div class="site-info"><p>&copy; <?php echo date('Y'); ?> <?php bloginfo( 'name' ); ?></p></div>
</footer>
</div><?php wp_footer(); ?>
</body></html>