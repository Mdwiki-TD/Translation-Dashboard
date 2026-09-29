<?php

/**
 * @var string   $category    Category name (e.g. RTT)
 * @var int      $totalPages  Number of pages in the category
 * @var array    $rows        Language rows (num, code, name, autonym, exists, missing)
 * @var callable $e           HTML escape helper
 * @var callable $number      Number format helper
 */
?>
<div align="center">
    <h4>Top languages by missing Articles</h4>
    <h5>Number of pages in Category:<?= $e($category) ?> : <?= (int)$totalPages ?></h5>
</div>

<div class="card">
    <div class="card-body" style="padding:5px 0px 5px 5px;">
        <table class="table table-striped compact soro table-mobile-responsive table_100 table_text_left">
            <thead>
                <tr>
                    <th class="spannowrap">#</th>
                    <th class="spannowrap">Language Name</th>
                    <th class="spannowrap">Code</th>
                    <th class="spannowrap">Autonym</th>
                    <th>Exists Articles</th>
                    <th>Missing Articles</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $row): ?>
                    <?php $missingFormatted = $number($row['missing']); ?>
                    <tr>
                        <th data-content="#"><?= $row['num'] ?></th>
                        <td data-content="Language name">
                            <a target="_blank" href="https://<?= $e($row['code']) ?>.wikipedia.org">
                                <?= $e($row['name']) ?>
                            </a>
                        </td>
                        <td data-content="Language code"><?= $e($row['code']) ?></td>
                        <td data-content="Autonym"><?= $e($row['autonym']) ?></td>
                        <td data-content="Exists Articles"><?= $row['exists'] ?></td>
                        <td data-content="Missing Articles" data-search="<?= $e($missingFormatted) ?>">
                            <a href="index.php?<?= http_build_query([
                                                    'cat'   => $category,
                                                    'depth' => 1,
                                                    'doit'  => 'Do it',
                                                    'code'  => $row['code'],
                                                    'type'  => 'lead',
                                                ]) ?>">
                                <?= $e($missingFormatted) ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
