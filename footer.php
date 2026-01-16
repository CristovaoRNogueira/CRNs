<footer class="site-footer">
    <div class="container">
        
        <div class="footer-widgets">
            
            <div class="footer-col brand-col">
                <div class="footer-logo">
                    <?php if( has_custom_logo() ) { the_custom_logo(); } else { ?>
                        <span class="text-logo-footer"><?php bloginfo( 'name' ); ?><span>.tech</span></span>
                    <?php } ?>
                </div>
                <p class="footer-desc">
                    Seu guia definitivo para comprar tecnologia. Análises imparciais, comparativos técnicos e as melhores ofertas do dia.
                </p>
                <div class="footer-social">
                    <a href="https://www.instagram.com/crn.sistemas/" aria-label="Instagram"><span class="dashicons dashicons-instagram"></span></a>
                    <a href="https://chat.whatsapp.com/L1gN8KvrxVH63AY591Rrbs" aria-label="Grupo WhatsApp"><span class="dashicons dashicons-whatsapp"></span></a>
                    <a href="#" aria-label="YouTube"><span class="dashicons dashicons-video-alt3"></span></a>                    
                </div>
            </div>

            <div class="footer-col">
                <h3>Categorias</h3>
                <ul class="footer-links">
                    <?php 
                    $terms = get_terms(['taxonomy' => 'tipo_produto', 'hide_empty' => true, 'number' => 6]);
                    foreach($terms as $term): ?>
                        <li><a href="<?php echo get_term_link($term); ?>"><?php echo $term->name; ?></a></li>
                    <?php endforeach; ?>
                    <li><a href="<?php echo site_url('/ofertas'); ?>" style="color:var(--primary)">Ver Todas as Ofertas</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h3>Institucional</h3>
                <?php wp_nav_menu( array( 
                    'theme_location' => 'wp_devs_footer_menu',
                    'container' => false,
                    'menu_class' => 'footer-links'
                )); ?>
            </div>

            <div class="footer-col legal-col">
                <h3>Transparência</h3>
                <div class="affiliate-notice">
                    <p><strong>Nota de Transparência:</strong> O <?php bloginfo('name'); ?> é um participante de programas de afiliados (como Amazon, Magalu, Mercado Livre).</p>
                    <p>Quando você compra através de links em nosso site, podemos ganhar uma comissão de afiliado sem nenhum custo extra para você. Isso ajuda a manter nossas análises independentes.</p>
                </div>
            </div>

        </div>

        <div class="site-info">
            <div class="copyright">
                &copy; <?php echo date('Y'); ?> <strong><?php bloginfo( 'name' ); ?></strong>. Todos os direitos reservados.
            </div>
            <div class="developer">
                Feito por: <a href="https://crnsistemas.com.br" style="color:#e25555">CRN Sistemas </a>
            </div>
        </div>

    </div>
</footer>

</div><?php wp_footer(); ?>
</body>
</html>