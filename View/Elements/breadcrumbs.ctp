<?php if ($crumbs = $this->Breadcrumb->get()) { ?>
    <nav class="breadcrumb">
        <ul>
            <?php foreach ($crumbs as $i => $crumb) { ?>
                <li>
                    <a href="<?php echo $this->Html->url($crumb['url']); ?>">
                        <?php echo h($crumb['title']); ?>
                        <span class="caret">/</span>
                    </a>
                </li>
            <?php } ?>
        </ul>
    </nav>
<?php }