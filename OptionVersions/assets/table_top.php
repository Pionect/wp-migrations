<form id="optionversions-filter" method="get">
    <input type="hidden" name="page" value="optionversions"/>
    <input type="hidden" name="orderby" value="updated_at"/>
    <input type="hidden" name="order" value="<?php echo $order; ?>"/>
    <div class="alignleft actions">
        <?php if ($types): ?>
            <label for="group-filter" class="screen-reader-text">Choose type & group</label>
            <select name="group-filter" id="group-filter" class="ewc-filter-cat">
                <option value="">Select a type</option>
                <?php
                foreach ($types as $type => $groups): ?>
                    <optgroup label="<?php echo $type; ?>">
                        <?php foreach ($groups as $group):
                            $selected = '';
                            if (array_key_exists('group-filter', $_REQUEST) && $_REQUEST['group-filter'] == $type) {
                                $selected = ' selected = "selected"';
                            }
                            ?>
                            <option value="<?php echo $group; ?>" <?php echo $selected; ?>><?php echo $group; ?></option>
                        <?php endforeach; ?>
                    </optgroup>
                    <?php
                endforeach;
                ?>
            </select>
        <?php endif; ?>
        <input type="submit" name="filter_action" id="post-query-submit" class="button" value="Filter">
    </div>
</form>