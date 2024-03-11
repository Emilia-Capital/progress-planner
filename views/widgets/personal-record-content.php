<?php

$prpl_personal_record_content = \progress_planner()->get_badges()->get_badge_progress( 'personal_record_content' );

echo '<p>';
printf(
	/* translators: %s: The number of weeks. */
	esc_html__( 'Personal record: %s weeks of writing content', 'progress-planner' ),
	esc_html( $prpl_personal_record_content )
);
echo '</p>';
