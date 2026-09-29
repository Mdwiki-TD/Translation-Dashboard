<?php

/**
 * @var array    $params  Request parameters (site, heads_limit, title_limit, items_with_no_links)
 * @var array    $view    Prepared view data (heads, qids, counts, notitle, ...)
 * @var callable $e       HTML escape helper
 * @var callable $slug    URL-safe wiki title helper
 */

// Form fields: name => input type
$fields = [
    'site'        => 'text',
    'heads_limit' => 'number',
    'title_limit' => 'number',
];
?>

<!-- Filter form -->
<div style="box-sizing:border-box;">
    <form class="form-inline" action="sitelinks.php" method="get">
        <div class="row">
            <?php foreach ($fields as $name => $type): ?>
                <div class="col-md-3">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><?= $e($name) ?></span>
                        </div>
                        <input class="form-control w-50"
                            type="<?= $type ?>"
                            id="<?= $e($name) ?>"
                            name="<?= $e($name) ?>"
                            value="<?= $e((string)$params[$name]) ?>">
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="col-md-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="switch2"
                        name="items_with_no_links" role="switch" value="1"
                        <?= $params['items_with_no_links'] ? 'checked' : '' ?>>
                    <label class="check-label" for="switch2">&nbsp;Items with no links</label>
                </div>
            </div>
        </div>
        <input type="submit" value="Submit" class="btn btn-outline-primary">
    </form>
</div>

<!-- Summary -->
<div style="box-sizing:border-box;">
    <h3>
        Heads: <?= (int)$view['len_heads_all'] ?>,
        Qids: <?= (int)$view['len_qids_all'] ?>
        <?= $e($view['with_site_note']) ?>
    </h3>
</div>

<!-- Results table -->
<div style="box-sizing:border-box;">
    <table class="table table-striped compact sortable" id="table-1">
        <thead>
            <tr>
                <th>#</th>
                <th>qid</th>
                <th>links</th>
                <th>mdtitle</th>
                <?php foreach ($view['heads'] as $head): ?>
                    <th><?= $e($head) ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php $i = 0; ?>
            <?php foreach ($view['qids'] as $qid => $tab): ?>
                <?php $i++;
                $mdtitle = $tab['mdtitle'] ?? ''; ?>
                <tr>
                    <td><?= $i ?></td>
                    <td><a href="https://wikidata.org/wiki/<?= $e((string)$qid) ?>"><?= $e((string)$qid) ?></a></td>
                    <td><?= count($tab['sitelinks']) ?></td>
                    <td><a href="https://mdwiki.org/wiki/<?= $slug($mdtitle) ?>"><?= $e($mdtitle) ?></a></td>

                    <?php foreach ($view['heads'] as $head): ?>
                        <?php $title = $tab['sitelinks'][$head] ?? ''; ?>
                        <td>
                            <?php if ($title !== ''): ?>
                                <a href="https://<?= $e($head) ?>.wikipedia.org/wiki/<?= $slug($title) ?>">
                                    <?= $view['notitle'] ? 'O' : $e($title) ?>
                                </a>
                            <?php endif; ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
