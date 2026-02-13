<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<h4 class="customer-profile-group-heading">
    <?= _l('tasks'); ?>
</h4>
<style>
    /* Hide the 'Related To' filters as requested */
    #tasks_related_filter {
        display: none !important;
    }
</style>
<?php if (isset($client)) {
    init_relation_tasks_table(['data-new-rel-id' => $client->userid, 'data-new-rel-type' => 'customer']);
} ?>