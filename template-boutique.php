<?php
/*
Template Name: Boutique
*/
get_header();

function display_categories($parent_id = 0)
{
    $args = array(
        'taxonomy' => 'product_cat',
        'parent' => $parent_id,
    );

    $categories = get_categories($args);

    if ($categories) {
        echo $parent_id !== 0 ? '<ul class="filters-categories__container">' : '';
        foreach ($categories as $category) {
            echo $parent_id !== 0 ? '<li class="filters-categories__item">' : '<li class="category-title">';
            echo $parent_id !== 0 ? '<label for="' . $category->slug . '"><input class="checkbox" type="checkbox" id="' . $category->slug . '" name="' . $category->name . '" value="' . $category->slug . '"/>' . $category->name . '</label>' : '<strong>' . $category->name . '</strong>';
            display_categories($category->term_id);
            echo '</li>';
        }
        echo $parent_id !== 0 ? '</ul>' : '';
    }
}
?>
<main id="boutique">
    <?php $img_top = get_field('image_de_fond'); ?>
    <section class="hero-banner" style="background: url(<?= esc_url($img_top['url']); ?>) no-repeat center;">
        <div class="container-narrow">
            <h1 class="screen-reader-text"><?php the_title(); ?></h1>
            <div class="blue-content">
                <div class="txt">
                    <?php the_field('texte_haut_de_page'); ?>
                </div>
            </div>
        </div>
    </section>
    <section id="breadcrumbs">
        <div class="container-narrow">
            <nav id="breadcrumbs-container">
                <ul>
                    <li><a href="<?= home_url("/"); ?>">Accueil</a></li>
                    <li class="separator">/</li>
                    <?php $lien_boutique = get_field('lien_boutique', 'option'); ?>
                    <li><a href="<?= esc_url($lien_boutique['url']); ?>">Boutique</a></li>
                    <li class="separator">/</li>
                    <li id="last-breadcrumb">Tous les produits</li>
                </ul>
            </nav>
        </div>
    </section>
    <article>
        <div class="container-narrow">
            <section class="filters-section">
                <button class="btn" id="show-filters">
                    Filtres
                </button>
                <div class="background-black hidden">
                    <div class="filtre-container">
                        <img src="<?= get_template_directory_uri(); ?>/public/close-blue.svg" alt="Fermer les filtres" id="close-filters">
                        <strong>Filtrer les produits</strong>
                        <ul class="filters-categories__container">
                            <li class="filters-categories__item">
                                <label for="all"><input class="checkbox" type="checkbox" name="Tous les produits" id="all" value="all" checked>Tous les produits</label>
                            </li>
                            <?php display_categories(); ?>
                        </ul>
                    </div>
                </div>
            </section>
            <section class="products">
                <?php $args = array(
                    'post_type' => 'product',
                    'posts_per_page' => -1,
                    'order' => 'DESC'
                );

                $products = new WP_Query($args);

                if ($products->have_posts()) :
                ?>
                    <ul class="products-container">
                        <?php while ($products->have_posts()) : $products->the_post();
                            $product = wc_get_product(get_the_ID());
                            $product_title = get_the_title();
                            $product_categories = get_the_terms(get_the_ID(), 'product_cat');
                            $product_image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'full');
                        ?>
                            <li class="product-container <?php
                                                            if (!empty($product_categories)) {
                                                                foreach ($product_categories as $category) {
                                                                    echo $category->slug . ' ';
                                                                }
                                                            }
                                                            ?>">
                                <a href="<?php the_permalink(); ?>" class="product-link">
                                    <?php if ($product_image) {
                                        echo '<div class="image-container">
                                            <img src="' . $product_image[0] . '" alt="' . $product_title . '" />
                                            <div class="overlay"><button class="btn">Voir la fiche produit</button></div>
                                        </div>';
                                    }
                                    echo '<h3>' . $product_title . ' - ' . get_field('marque') . '</h3>';

                                    if (!empty($product_categories)) {
                                        echo '<ul class="product-categories">';
                                        foreach ($product_categories as $category) {
                                            var_dump($category->parent);
                                            echo $category->parent !== 47 ? '<li>' . $category->name . '</li>' : '';
                                        }
                                        echo '</ul>';
                                    }

                                    ?>
                                </a>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php wp_reset_postdata();
                endif; ?>
            </section>
        </div>
    </article>
    <section class="marques">
        <div class="container-narrow">
            <h2>Nos marques</h2>
            <div class="text"><?php the_field('texte_court_marque'); ?></div>
            <?php if (have_rows('logo_marque')) : ?>
                <ul class="marques-container">
                    <?php while (have_rows('logo_marque')) : the_row();
                        $img = get_sub_field('logo');

                        echo '<li class="marque-list"><img src="' . esc_url($img['url']) . '" alt="' . esc_attr($img['alt']) . '" title="' . esc_attr($img['title']) . '" /></li>';
                    endwhile;
                    ?>
                </ul>
            <?php endif; ?>
        </div>
    </section>
</main>
<?php get_footer(); ?>