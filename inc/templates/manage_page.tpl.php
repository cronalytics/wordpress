<h1>Cronalytics - Cron management</h1>
<div>
    <?php if ( ! empty( $action_response['type'] ) ) : ?>
        <div class="alert alert-<?php esc_html_e( $action_response['type'] ); ?>">
            <?php esc_html_e( $action_response['message'] ); ?>
        </div>
    <?php endif; ?>
    <div>
        <?php
            $now = new DateTime();
            $current_time = $now->format( 'Y-m-d H:i:s e' );
        ?>
        Current time: <?php esc_html_e( $current_time ); ?>
    </div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Hook</th>
                <th>Schedule</th>
                <th>Next Activation</th>
                <th>Interval</th>
                <th>Args</th>
                <th>Manage</th>
                <th>cronalytics debug</th>
            </tr>
        </thead>
        <tbody>
        <?php
            foreach( $crons as $cron ) {
                $next_activation = new DateTime();
                $next_activation->setTimestamp($cron['next_activation']);
                $next_activation = $next_activation->format('Y-m-d H:i:s e');
                ?>
                <tr>
                    <td><?php esc_html_e( $cron['hook'] ); ?></td>
                    <td><?php esc_html_e( $cron['schedule'] ); ?></td>
                    <td><?php esc_html_e( $next_activation ); ?></td>
                    <td><?php esc_html_e( $cron['interval'] ); ?></td>
                    <td><?php esc_html_e( json_encode( $cron['args'] ) ); ?></td>
                    <td>
                        <?php if (!isset($cron['cronalytics'])) { ?>
                            <a href="tools.php?page=cronalytics_admin_manage_page&action=ca_add&hook=<?php esc_attr_e($cron['hook']) ?>">Add</a>
                        <?php } else { ?>
                            <a href="https://dashboard.cronalytics.io/cron/<?php esc_attr_e($cron['cronalytics']['public_hash']); ?>" target="_blank">View</a>
                            | <a href="tools.php?page=cronalytics_admin_manage_page&action=ca_remove&hook=<?php esc_attr_e( $cron['hook'] ); ?>">Remove from cronalytics.io</a>
                        <?php } ?>
<!--                        | <a href="#">Run now</a>-->
                    </td>
<!--                    <td>--><?php //print_r($cron); ?><!--</td>-->
                    <td><?php esc_html_e( json_encode( $cron['cronalytics'] ) ); ?></td>
                </tr>
        <?php } ?>    
        </tbody>
    </table>
    
</div>