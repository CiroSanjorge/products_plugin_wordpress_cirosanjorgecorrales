<form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST">
    <label for="wc_data">Woocommerce Client: </label>
    <div id="wc_data">
        <input type="text" name="url" placeholder="url" id="url" value="http://localhost/wordpress/" required>
        <input type="text" name="ck" placeholder="ck" id="ck" value="ck_aa43c6f0d3bb95ff3d29eba7d4b82dcfa25db30e" required>
        <input type="text" name="cs" placeholder="cs" id="cs" value="cs_ed39072c2c6afc79eaa78b9b3b6d9c6c6fea76e6" required>
    </div>
    <label for="db_data">Database Connection: </label>
    <div id="db_data">
        <input type="text" name="user" placeholder="user" id="user" value="root" required>
        <input type="text" name="pass" placeholder="pass" id="pass" value="">
        <input type="text" name="server" placeholder="server" id="server" value="localhost" required>
        <input type="text" name="db" placeholder="db" id="db" value="tienda_1" required>
        <input type="text" name="table" placeholder="table" id="table" value="woocommerce_products" required>
    </div>
    <input type="hidden" name="action" value="data_form">
    <input type="submit" value="Enviar">
</form>