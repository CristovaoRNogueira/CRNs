<form role="search" method="get" class="search-form-modern" action="<?php echo esc_url(home_url('/')); ?>"
    style="display:flex; width:100%; position:relative;">

    <input type="search"
        style="width:100%; padding:15px 50px 15px 20px; border:2px solid #0073aa; border-radius:30px; font-size:16px; outline:none; box-shadow: 0 4px 6px rgba(0,0,0,0.05);"
        placeholder="Pesquisar produtos e análises..." value="<?php echo get_search_query(); ?>" name="s" required />

    <button type="submit"
        style="position:absolute; right:8px; top:8px; bottom:8px; background:#0073aa; color:#fff; border:none; border-radius:50%; width:40px; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:0.2s;"
        aria-label="Pesquisar">
        <span class="dashicons dashicons-search"></span>
    </button>

</form>