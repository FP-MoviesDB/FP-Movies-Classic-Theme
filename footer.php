<?php
/*
* -------------------------------------------------------------------------------------
* @author: FP Movies Classic Theme
* @author URI: https://fpmoviesdb.xyz/
* @copyright: (c) | All rights reserved
* -------------------------------------------------------------------------------------
*
* @since 1.0.0
*
*/

if (!defined('ABSPATH')) exit;

$b_footer_settings = get_option('fp_theme_all_settings')['basic_settings'] ?? [];
$t_footer_text = $b_footer_settings['footer_text'] ?? '';
if (!empty($t_footer_text)) {
    $t_footer_text = base64_decode($t_footer_text);
}
?>


<footer class="footer-wrapper">
    <div class="footer-container p-5">
        <?php
        if (!empty($t_footer_text)) {
            echo $t_footer_text;
        } else {
            echo "<p class='text-center'>&copy; " . date('Y') . " " . get_bloginfo('name') . ". All rights reserved.</p>";
        }
        ?>
    </div>
</footer>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?php wp_footer(); ?>
</div>
</body>

</html>