<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="_buttons">
                            <a href="#" class="btn btn-info pull-left display-block"
                                onclick="add_section(); return false;">
                                <i class="fa fa-plus"></i>
                                <?php echo _l('new_section'); ?>
                            </a>
                            <button class="btn btn-success pull-right" onclick="save_menu_order();">
                                <?php echo _l('save'); ?>
                            </button>
                            <div class="clearfix"></div>
                        </div>
                        <hr class="hr-panel-heading" />

                        <div class="clearfix"></div>

                        <div class="row">
                            <div class="col-md-8 col-md-offset-2">
                                <div class="dd" id="menu_builder">
                                    <ol class="dd-list">
                                        <!-- This will be populated by JS to be strictly Sortable, 
                                             but for now let's try a simpler UL/LI sortable with jQuery UI which is included in Perfex -->
                                    </ol>
                                    <ul id="menu-sortable" class="list-group">
                                        <!-- Render saved items and new available items -->
                                        <?php
                                        // Combine saved config and current items to build the list
                                        // This logic is a bit complex for PHP view, better to do in JS or prep in controller.
                                        // For simplicity, we will output all available items in a hidden JS var, 
                                        // and the saved config in another, and rebuild the DOM in JS.
                                        ?>
                                    </ul>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<template id="section-template">
    <li class="list-group-item menu-element" data-type="section">
        <div class="drag-handle"><i class="fa fa-bars"></i></div>
        <div class="content-area">
            <span class="text-uppercase text-bold section-title"></span>
            <input type="text" class="form-control section-input hidden" value="" />
        </div>
        <div class="actions">
            <a href="#" onclick="edit_section(this); return false;" class="text-muted"><i class="fa fa-pencil"></i></a>
            <a href="#" onclick="remove_element(this); return false;" class="text-danger"><i
                    class="fa fa-trash"></i></a>
        </div>
    </li>
</template>

<template id="item-template">
    <li class="list-group-item menu-element" data-type="item">
        <div class="drag-handle"><i class="fa fa-bars"></i></div>
        <div class="content-area">
            <i class="icon-preview"></i> <span class="item-name"></span>
        </div>
    </li>
</template>

<style>
    #menu-sortable {
        list-style-type: none;
        margin: 0;
        padding: 0;
    }

    .menu-element {
        margin: 5px 0;
        padding: 10px;
        background: #fdfdfd;
        border: 1px solid #e3e3e3;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .menu-element.ui-sortable-helper {
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        background: #fff;
    }

    .drag-handle {
        cursor: move;
        margin-right: 15px;
        color: #999;
    }

    .content-area {
        flex-grow: 1;
        display: flex;
        align-items: center;
    }

    .icon-preview {
        margin-right: 10px;
    }

    .section-title {
        color: #777;
        letter-spacing: 1px;
    }

    .hidden {
        display: none;
    }

    .actions {
        margin-left: 15px;
    }
</style>

<?php init_tail(); ?>
<script>
    var availableItems = <?php echo json_encode($items); ?>;
    var savedConfig = <?php echo json_encode($saved_config); ?>;

    $(function () {
        renderMenu();

        $("#menu-sortable").sortable({
            handle: '.drag-handle',
            placeholder: "ui-state-highlight",
            start: function (e, ui) {
                ui.placeholder.height(ui.item.height());
            }
        });

    });

    function renderMenu() {
        var $container = $('#menu-sortable');
        $container.empty();

        // Index available items by slug for easy lookup
        var itemsMap = {};
        $.each(availableItems, function (i, item) {
            itemsMap[item.slug] = item;
        });

        // 1. Render Saved Config
        if (savedConfig && savedConfig.length > 0) {
            $.each(savedConfig, function (i, node) {
                if (node.type === 'section') {
                    addSectionToDom(node.name, node.id || generateId());
                } else if (node.type === 'item') {
                    if (itemsMap[node.id]) {
                        addItemToDom(itemsMap[node.id]);
                        delete itemsMap[node.id]; // Remove from available so we don't duplicate
                    }
                }
            });
        }

        // 2. Render remaining available items (orphan items)
        $.each(itemsMap, function (slug, item) {
            addItemToDom(item);
        });
    }

    function addSectionToDom(name, id) {
        var tpl = document.getElementById('section-template');
        var $el = $(tpl.content.cloneNode(true)).find('li');
        $el.data('id', id);
        $el.find('.section-title').text(name);
        $el.find('input').val(name);
        $('#menu-sortable').append($el);
    }

    function addItemToDom(item) {
        var tpl = document.getElementById('item-template');
        var $el = $(tpl.content.cloneNode(true)).find('li');
        $el.data('id', item.slug);

        // Handle Icon
        var iconClass = item.icon;
        if (!iconClass && item.icon_image) {
            // Handle image icons if necessary, but Perfex mostly uses classes
            // For now just safe fallback
            iconClass = 'fa fa-circle-o';
        }
        $el.find('.icon-preview').addClass(iconClass);
        $el.find('.item-name').text(item.name); // item.name might be HTML or translated text. We trust it for now.

        $('#menu-sortable').append($el);
    }

    function add_section() {
        var name = prompt("Enter Section Name:");
        if (name) {
            addSectionToDom(name, generateId());
        }
    }

    function edit_section(btn) {
        var $li = $(btn).closest('li');
        var currentName = $li.find('.section-title').text();
        var newName = prompt("Edit Section Name:", currentName);
        if (newName) {
            $li.find('.section-title').text(newName);
            $li.find('input').val(newName);
        }
    }

    function remove_element(btn) {
        if (confirm('Are you sure you want to remove this section?')) {
            $(btn).closest('li').remove();
        }
    }

    function generateId() {
        return 'section-' + Math.random().toString(36).substr(2, 9);
    }

    function save_menu_order() {
        var order = [];
        $('#menu-sortable li').each(function () {
            var $el = $(this);
            var type = $el.data('type');
            var id = $el.data('id');

            if (type === 'section') {
                var name = $el.find('input').val();
                order.push({
                    type: 'section',
                    id: id,
                    name: name
                });
            } else {
                order.push({
                    type: 'item',
                    id: id
                });
            }
        });

        $.post(admin_url + 'menus_sections/save', {
            data: order
        }).done(function (resp) {
            resp = JSON.parse(resp);
            if (resp.success) {
                alert_float('success', 'Menu order saved successfully');
                // Reload to reflect changes in the real sidebar
                setTimeout(function () { window.location.reload(); }, 1000);
            }
        });
    }
</script>