<ul>
    <?php foreach ($dates as $d) { ?>
        <li><?=$d->format('Y-m-d H:i:s')?></li>
    <?php } ?>
</ul>