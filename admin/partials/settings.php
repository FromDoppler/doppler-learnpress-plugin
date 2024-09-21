<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

?>

<div>
    <?php $this->display_success_message() ?>

    <?php $this->display_error_message() ?>

    <div id="showSuccessResponse" class="messages-container info d-none">
    </div>

    <div id="showErrorResponse" class="messages-container blocker d-none">
    </div>

    <div class="dp-rowflex">
        <div class="col-sm-12 col-md-12 col-lg-12">
            <header class="hero-banner">
                <div class="dp-container">
                    <div class="dp-rowflex">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <h2><?php _e('List Synchronization.','doppler-for-learnpress');?></h2>
                        </div>
                        <div class="col-sm-7">
                            <p><?php
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
                    </div>
                    <span class="arrow"></span>
                </div>
            </header>
            <form id="dplr-lp-form-list" action="" method="post">
                <?php wp_nonce_field( 'map-lists' );?>
                <div class="awa-form">
                    <label><?php _e('Doppler List to send Subscribers', 'doppler-for-learnpress') ?></label>
                    <div class="dp-select m-b-6">
                        <span class="dropdown-arrow"></span>
                        <select name="dplr_learnpress_subscribers_list[buyers]" aria-invalid="false">
                            <option value="">
                                <?php _e('Select', 'doppler-for-learnpress')?>
                            </option>
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
                    </div>
                </div>
                <div class="d-flex justify-start">
                    <button id="dplr-lp-lists-btn" class="dp-button button-medium primary-green">
                        <?php _e('Sync', 'doppler-for-learnpress') ?>
                    </button>
                    <button id="dplr-lp-clear" class="dp-button button-medium primary-grey ml-1" <?php echo empty($subscribers_lists['buyers'])? 'disabled' : '' ?>>
                        <?php _e('Clear selection', 'doppler-for-learnpress') ?>
                    </button>
                </div>
            </form>
        </div>
    
        <?php
        require_once('courses-mapping.php');
        ?>
    </div>
</div>