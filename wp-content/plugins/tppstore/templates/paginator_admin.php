<?php if ($this->max_pages > 1): ?>
    <nav class="pagination">
        <strong>Page: <?php echo $this->page ?> of <?php echo $this->max_pages ?></strong>
        <?php if ($this->max_pages > 1): ?>
            <?php for ($x = 1; $x <= $this->max_pages; $x++): ?>
                <?php if ($x == $this->page): ?>
                    <span><?php echo $x; ?></span>
                <?php else: ?>
                    <a href="<?php echo $this->base . $this->query_string; ?>&paged=<?php echo $x; ?>"><?php echo $x; ?></a>
                <?php endif; ?>
            <?php endfor; ?>
        <?php endif; ?>
    </nav>
<?php endif; ?>