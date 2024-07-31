<?php


use Marktic\Newsletter\Utility\NewsletterModels;

$params = $params ?? $_GET;
?>

<form action="<?= NewsletterModels::subscriptions()->compileURL('export', $params) ?>"
      method="post" class="right">
    <input type="hidden" name="action" value="export"/>
    <input type="hidden" name="type" value="excel"/>
    <button type="submit" class="btn btn-success btn-xs">
        <img src="<?= $this->Url()->image("ico/xls.gif"); ?>" alt=""/>
        Export XLS
    </button>
</form>
