<?php
$courses = $this->get_courses();
$courses_map = get_option('dplr_learnpress_courses_map');
/*
$actions = array(   '1'=> __('Student subscribes to course', 'doppler-for-learnpress'),
                    '2'=>  __('Student finishes course', 'doppler-for-learnpress'));*/
?>

<div class="col-sm-12 col-md-12 col-lg-12 panel dp-box-shadow">
    <form id="course-mapping-form">
        <label><?php _e('Courses Mapping','doppler-for-learnpress') ?></label>
        
        <div class="awa-form">
            <div class="dp-rowflex">
                <div class="col-sm-6 col-md-4 col-lg-4">
                    <div class="dp-select">
                        <span class="dropdown-arrow"></span>
                        <select id="map-course" name="map courses" aria-invalid="false">
                            <option value="">
                                <?php _e('Select course','doppler-for-learnpress')?>
                            </option>
                            <?php
                                if(!empty($courses)){
                                    foreach($courses as $course):
                                    ?>
                                        <option value="<?php echo $course->ID?>">
                                            <?php echo $course->post_title?>
                                        </option>
                                    <?php
                                    endforeach;
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-4">
                    <div class="dp-select">
                        <span class="dropdown-arrow"></span>
                        <select id="map-list" name="map lists" aria-invalid="false">
                            <option value="">
                                <?php _e('Select List','doppler-for-learnpress')?>
                            </option>
                            <?php
                                if(!empty($lists)){
                                    foreach($lists as $k=>$v):
                                    ?>
                                        <option value="<?php echo esc_attr($k)?>">
                                            <?php echo esc_html($v['name'])?>
                                        </option>
                                    <?php
                                    endforeach;
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-sm-6 col-md-2 col-md-4">
                    <button class="dp-button dp-button--inline button-medium primary-green" disabled><?php _e('Associate List', 'doppler-for-learnpress')?></button>
                </div>
            </div>
        </div>
    </form>
    <div id="courses-mapping-messages"></div>

    <table id="associated-lists-tbl"
        class="dp-c-table m-t-18 m-b-12 <?php if(empty($courses_map)) echo 'd-none'?>"
        aria-label="courses table"
        summary="courses table">
            <thead>
            <tr>
                <th aria-label="Course" scope="col">
                <span><?php _e('Course', 'doppler-for-learnpress')?></span>
                </th>
                <th aria-label="List name" scope="col">
                <span><?php _e('Associated List', 'doppler-for-learnpress')?></span>
                </th>
                <th aria-label="Actions" scope="col" style="width: 25px;">
                <span><?php _e('Actions', 'doppler-form')?></span>
                </th>
            </tr>
            </thead>
            <tbody>
                <?php if(!empty($courses_map)):
                    foreach($courses_map as $key=>$value):
                        $list_id = $value['list_id'];
                        $course_post = get_post($value['course_id']);
                        ?>
                            <tr>
                                <td><?php echo $course_post->post_title ?></td>
                                <td><?php echo isset($lists[$list_id])?$lists[$list_id]['name']:__('Warning: list is missing', 'doppler-for-learnpress')?></td>
                                <td>
                                <div class="dp-icons-group">
                                    <a data-assoc="<?php echo $value['course_id']?>-1"
                                        class="dplr-remove">
                                        <div class="dp-tooltip-container">
                                        <span class="ms-icon icon-delete"></span>
                                        <div class="dp-tooltip-top">
                                            <span><?php _e('Delete', 'doppler-form')?></span>
                                        </div>
                                        </div>
                                    </a>
                                </div>
                            </tr>
                        <?php
                    endforeach;
                endif; ?>
            </tbody>
    </table>
</div>

<div id="dplr-lp-dialog-confirm" title="<?php _e('Are you sure?', 'doppler-for-learnpress'); ?>">
    <p><span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span> <?php _e('If you proceed, the Course will no longer send subscriptors to the List.', 'doppler-for-learnpress')?></p>
</div>