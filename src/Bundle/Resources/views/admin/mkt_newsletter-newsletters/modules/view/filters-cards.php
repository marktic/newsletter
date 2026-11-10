<h6 class="text-uppercase text-muted mb-3"><?= $escape($trans('filters.title')) ?></h6>

<?php if (empty($filters)): ?>
    <div class="card mb-3">
        <div class="card-body text-muted"><?= $escape($trans('filters.empty')) ?></div>
    </div>
<?php endif; ?>

<?php foreach ($filters as $filter): ?>
    <?php $filterType = $filter->getFilterType(); ?>
    <div class="card mb-3 mkt-newsletter-filter">
        <div class="card-body d-flex align-items-start justify-content-between">
            <div>
                <span class="badge bg-info mb-1">
                    <?= $escape($filterType ? $filterType->getLabel() : $filter->getType()) ?>
                </span>
                <div><?= $escape($filter->getDescription()) ?></div>
            </div>
            <?php if (!empty($urls['deleteFilter'])): ?>
                <form method="post" action="<?= $escape($urls['deleteFilter']) ?>" class="ms-2">
                    <input type="hidden" name="filter_id" value="<?= $escape($filter->getId()) ?>"/>
                    <button type="submit" class="btn btn-sm btn-outline-danger"
                            onclick="return confirm('<?= $escape($trans('filters.delete_confirm')) ?>');">
                        <?= $escape($trans('filters.delete')) ?>
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
<?php endforeach; ?>

<?php if (!empty($urls['addFilter']) && !empty($filterTypes)): ?>
    <div class="card mb-4">
        <div class="card-header"><h5 class="card-title mb-0"><?= $escape($trans('filters.add')) ?></h5></div>
        <div class="card-body">
            <form method="post" action="<?= $escape($urls['addFilter']) ?>">
                <div class="mb-2">
                    <label class="form-label" for="mkt-newsletter-filter-type"><?= $escape($trans('filters.type')) ?></label>
                    <select class="form-select" id="mkt-newsletter-filter-type" name="type" required>
                        <?php foreach ($filterTypes as $filterType): ?>
                            <option value="<?= $escape($filterType->getName()) ?>"><?= $escape($filterType->getLabel()) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-2">
                    <label class="form-label" for="mkt-newsletter-filter-values"><?= $escape($trans('filters.values')) ?></label>
                    <input type="text" class="form-control" id="mkt-newsletter-filter-values" name="values" required/>
                    <div class="form-text"><?= $escape($trans('filters.values_help')) ?></div>
                </div>
                <button type="submit" class="btn btn-primary btn-sm"><?= $escape($trans('filters.add')) ?></button>
            </form>
        </div>
    </div>
<?php endif; ?>
