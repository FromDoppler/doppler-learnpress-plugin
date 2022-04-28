<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

?>

<div class="dplr-tab-content dplr-learnpress-tab">

    <?php $this->display_success_message() ?>

    <?php $this->display_error_message() ?>

    <div id="showSuccessResponse" class="messages-container info d-none">
    </div>

    <div id="showErrorResponse" class="messages-container blocker d-none">
    </div>

    <div class="d-flex flex-row">

        <div class="col-68">
            <p class="size-medium mt10" id="dplr-settings-text">
                <?php
                if(!empty($subscribers_lists['buyers'])){
                    _e('As they enroll in a course, your Subscribers will be automatically sent to the selected Doppler List.', 'doppler-for-learnpress');
                }else{
                    if(empty($lists)){
                        _e('You currently have no Doppler Lists created. Create a List in Doppler by entering a List name and pressing Create List.','doppler-for-learnpress');

                    }else{
                        _e('Select the Doppler List where you want to import Subscribers of your courses. When synchronized, those customers already registered and future customers will be sent automatically.', 'doppler-for-learnpress');
                    }
                }
                ?>
            </p>
        </div>
        <div class="flex-grow-1">
            <form id="dplr-form-list-new" class="text-right" action="" method="post">

                <input type="text" value="" class="d-inline-block"  maxlength="100" placeholder="<?php _e('Write the List name', 'doppler-for-learnpress')?>"/>

                <button id="dplrlp-save-list" class="dp-button dp-button--inline button-medium primary-green" disabled="disabled">
                    <?php _e('Create List', 'doppler-form') ?>
                </button>

            </form>
        </div>
        
    </div>

    <form id="dplr-lp-form-list" action="" method="post">

        <?php wp_nonce_field( 'map-lists' );?>         
        <p>
            <label><?php _e('Doppler List to send Subscribers', 'doppler-for-learnpress') ?></label>
            <select name="dplr_learnpress_subscribers_list[buyers]" class="dplr-lp-lists">
            <option value=""><?php _e('Select', 'doppler-for-learnpress')?></option>
            <?php 
            if(!empty($lists)){
                foreach($lists as $k=>$v){
                    ?>
                    <option value="<?php echo esc_attr($k)?>" 
                        <?php if(!empty($subscribers_lists['buyers']) && $subscribers_lists['buyers']==$k) echo 'selected' ?>
                        data-subscriptors="<?php echo esc_attr($v['subscribersCount'])?>">
                        <?php echo esc_html($v['name'])?>
                    </option>
                    <?php
                }
            }   
            ?>
            </select>
        </p>

        <p class="d-flex justify-end">

            <button id="dplr-lp-clear" class="dp-button button-medium primary-grey" <?php echo empty($subscribers_lists['buyers'])? 'disabled' : '' ?>>
                <?php _e('Clear selection', 'doppler-for-learnpress') ?>
            </button>
        
            <button id="dplr-lp-lists-btn" class="dp-button button-medium primary-green ml-1">
                <?php _e('Sync', 'doppler-for-learnpress') ?>
            </button>

        </p>

    </form>

    <?php
    require_once('courses-mapping.php');
    ?>

</div>