<div class="sidebar">
    <div class="sidebar-widget">
        <div class="widget-title">
            <h3>Shop by Price</h3>
        </div>
        <div class="widget-content shop-by-price">
            <form method="GET" action="<?= site_url('products') ?>">
                <?php if(service('request')->getGet('category')): ?>
                    <input type="hidden" name="category" value="<?= esc(service('request')->getGet('category')) ?>">
                <?php endif; ?>
                
                <div class="price-filter">
                    <div class="price-filter-inner">
                        <div id="slider-range"></div>
                        <div class="price_slider_amount">
                            <input type="text" id="amount" name="price" placeholder="0 - 1000000">
                            <input type="hidden" id="min_price" name="min_price">
                            <input type="hidden" id="max_price" name="max_price">
                            <button type="submit">
                                Filter
                            </button>
                        </div>
                    </div>
                </div>  
            </form>
        </div>
    </div>
    <?php if (!empty($categories)): ?>
        <div class="sidebar-widget">
            <div class="widget-title">
                <h3>Categories</h3>
            </div>
            <div class="widget-content widget-categories">
                <ul>
                    <?php foreach ($categories as $category): ?>
                         <li>
                            <a href="<?= site_url('products?category=' . $category->slug) ?>">
                                <?= $category->name ?>
                            </a>

                            <?php if (!empty($category->children)): ?>
                                <?= view(
                                    'themes/' . $currentTheme . '/products/children_category',
                                    ['children' => $category->children]
                                ) ?>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    <?php endif;?>
</div>