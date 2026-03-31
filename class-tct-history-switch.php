<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * History switcher: allows changing a completion record between success and fail states.
 *
 * Used by the Goal History modal (dashboard) to toggle an existing completion row into:
 *  - a manual fail (penalty) or
 *  - a success (completion)
 */
class TCT_History_Switch {
	public function __construct() {
		add_action( 'wp_ajax_tct_switch_completion_state', array( $this, 'handle_switch_completion_state_ajax' ) );
		add_action( 'wp_ajax_tct_backdate_completion', array( $this, 'handle_backdate_completion_ajax' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'maybe_enqueue_assets' ), 99 );
	}

	public function maybe_enqueue_assets() {
		// Only on pages where the main dashboard script is enqueued.
		if ( ! function_exists( 'wp_script_is' ) ) { return; }
		if ( ! wp_script_is( 'tct-dashboard-js', 'enqueued' ) ) { return; }
		// Datepicker for backdated completion UI
		wp_enqueue_script( 'jquery-ui-datepicker' );
		$path = defined( 'TCT_PLUGIN_DIR' ) ? TCT_PLUGIN_DIR . 'history-toggle.js' : '';
		$ver = ( $path && file_exists( $path ) ) ? (string) filemtime( $path ) : null;
		wp_enqueue_script(
			'tct-history-toggle-js',
			defined( 'TCT_PLUGIN_URL' ) ? ( TCT_PLUGIN_URL . 'history-toggle.js' ) : '',
			array( 'tct-dashboard-js' ),
			$ver,
			true
		);
		$path2 = defined( 'TCT_PLUGIN_DIR' ) ? TCT_PLUGIN_DIR . 'history-backdate.js' : '';
		$ver2 = ( $path2 && file_exists( $path2 ) ) ? (string) filemtime( $path2 ) : null;
		wp_enqueue_script(
			'tct-history-backdate-js',
			defined( 'TCT_PLUGIN_URL' ) ? ( TCT_PLUGIN_URL . 'history-backdate.js' ) : '',
			array( 'jquery', 'jquery-ui-datepicker', 'tct-dashboard-js' ),
			$ver2,
			true
		);


	
			$path3 = defined( 'TCT_PLUGIN_DIR' ) ? ( TCT_PLUGIN_DIR . 'history-stats.js' ) : '';
			$ver3  = ( $path3 && file_exists( $path3 ) ) ? (string) filemtime( $path3 ) : null;

			wp_enqueue_script(
				'tct-history-stats-js',
				defined( 'TCT_PLUGIN_URL' ) ? ( TCT_PLUGIN_URL . 'history-stats.js' ) : '',
				array( 'tct-dashboard-js' ),
				$ver3,
				true
			);

}

	private function build_event_key( $source, $source_ref, $goal_id ) {
		return 'c_' . sha1( (string) $source . ':' . (string) $source_ref . ':' . (int) $goal_id );
	}

	private function is_failish( $source, $details ) {
		$src = strtolower( trim( (string) $source ) );
		$det = strtolower( trim( (string) $details ) );
		if ( 0 === strpos( $det, '[manual fail]' ) ) { return true; }
		if ( 0 === strpos( $det, '[auto miss]' ) ) { return true; }
		if ( 0 === strpos( $det, '[auto due miss]' ) ) { return true; }
		if ( 'auto_miss' === $src ) { return true; }
		if ( 'auto_due_miss' === $src ) { return true; }
		return false;
	}

	private function compute_source_label( $source, $details ) {
		$src = strtolower( trim( (string) $source ) );
		$det = trim( (string) $details );
		$det_l = strtolower( $det );
		if ( 0 === strpos( $det_l, '[manual fail]' ) ) { return 'Manual fail'; }
		if ( 0 === strpos( $det_l, '[auto miss]' ) || 'auto_miss' === $src ) { return 'Auto miss'; }
		if ( 0 === strpos( $det_l, '[auto due miss]' ) || 'auto_due_miss' === $src ) { return 'Auto due miss'; }
		if ( 'manual' === $src ) { return 'Manual'; }
		if ( 'todoist' === $src ) { return 'Todoist'; }
		if ( '' === $src ) { return '--'; }
		return ucwords( str_replace( array( '-', '_' ), ' ', $src ) );
	}

	private function get_goal_pause_state_at( $goal_row, $at, DateTimeZone $tz = null ) {
		$out = array(
			'enabled' => false,
			'is_paused' => false,
		);

		if ( ! is_array( $goal_row ) ) {
			return $out;
		}

		if ( ! class_exists( 'TCT_Interval' )
			|| ! is_callable( array( 'TCT_Interval', 'is_goal_eligible_for_availability_cycle' ) )
			|| ! is_callable( array( 'TCT_Interval', 'normalize_availability_cycle_from_row' ) )
			|| ! is_callable( array( 'TCT_Interval', 'availability_cycle_state_at_datetime' ) ) ) {
			return $out;
		}

		if ( ! TCT_Interval::is_goal_eligible_for_availability_cycle( $goal_row ) ) {
			return $out;
		}

		$cfg = TCT_Interval::normalize_availability_cycle_from_row( $goal_row );
		if ( ! is_array( $cfg ) || empty( $cfg['enabled'] ) ) {
			return $out;
		}

		$tz_use = ( $tz instanceof DateTimeZone ) ? $tz : TCT_Utils::wp_timezone();

		try {
			if ( $at instanceof DateTimeInterface ) {
				$at_tz = $at->getTimezone();
				if ( ! ( $at_tz instanceof DateTimeZone ) ) {
					$at_tz = $tz_use;
				}
				$dt = new DateTimeImmutable( $at->format( 'Y-m-d H:i:s' ), $at_tz );
			} else {
				$dt = new DateTimeImmutable( (string) $at, new DateTimeZone( 'UTC' ) );
			}
			$dt = $dt->setTimezone( $tz_use );
		} catch ( Exception $e ) {
			return $out;
		}

		$state = TCT_Interval::availability_cycle_state_at_datetime( $cfg, $dt, $tz_use );
		if ( ! is_array( $state ) ) {
			return $out;
		}

		$out['enabled'] = ! empty( $state['enabled'] );
		$out['is_paused'] = ! empty( $state['is_paused'] );
		return $out;
	}

	private function upsert_ledger_row( $ledger_table, $user_id, $event_key, $goal_row, $completion_row, $points, $details ) {
		global $wpdb;
		$user_id = (int) $user_id;
		$now_utc = current_time( 'mysql', true );
		$occurred_at = isset( $completion_row['completed_at'] ) && $completion_row['completed_at'] ? (string) $completion_row['completed_at'] : $now_utc;
		$existing_id = (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT id FROM {$ledger_table} WHERE user_id = %d AND event_key = %s",
				$user_id,
				(string) $event_key
			)
		);
		$data = array(
			'user_id' => $user_id,
			'event_key' => (string) $event_key,
			'event_type' => 'completion',
			'points' => (int) $points,
			'occurred_at' => $occurred_at,
			'goal_id' => (int) $goal_row['id'],
			'goal_name' => (string) $goal_row['goal_name'],
			'label_name' => (string) $goal_row['label_name'],
			'todoist_completed_id' => isset( $completion_row['todoist_completed_id'] ) ? (string) $completion_row['todoist_completed_id'] : '',
			'todoist_task_id' => isset( $completion_row['todoist_task_id'] ) ? (string) $completion_row['todoist_task_id'] : '',
			'interval_unit' => '',
			'interval_mode' => '',
			'interval_target' => 0,
			'bonus_points' => 0,
			'window_start' => null,
			'window_end' => null,
			'met' => 0,
			'details' => (string) $details,
			'updated_at' => $now_utc,
		);
		$formats = array(
			'%d', '%s', '%s', '%d', '%s',
			'%d', '%s', '%s', '%s', '%s',
			'%s', '%s', '%d', '%d',
			'%s', '%s', '%d', '%s',
			'%s',
		);

		if ( $existing_id > 0 ) {
			$update_data = $data;
			unset( $update_data['user_id'] );
			unset( $update_data['event_key'] );
			return ( false !== $wpdb->update( $ledger_table, $update_data, array( 'id' => $existing_id ), array_slice( $formats, 2 ), array( '%d' ) ) );
		}

		$data['created_at'] = $now_utc;
		$formats[] = '%s';
		return ( false !== $wpdb->insert( $ledger_table, $data, $formats ) );
	}

	public function handle_switch_completion_state_ajax() {
		if ( ! TCT_Utils::enforce_ajax_nonce( 'tct_undo_completion', 'nonce', 403 ) ) { return; }
		$user_id = function_exists( 'get_current_user_id' ) ? (int) get_current_user_id() : 0;
		if ( $user_id <= 0 ) {
			TCT_Utils::send_json_error( array( 'code' => 'not_logged_in', 'message' => 'You must be logged in.' ), 401 );
			return;
		}
		$completion_id = isset( $_POST['completion_id'] ) ? (int) $_POST['completion_id'] : 0;
		$goal_id = isset( $_POST['goal_id'] ) ? (int) $_POST['goal_id'] : 0;
		$to_state = isset( $_POST['to_state'] ) ? sanitize_key( wp_unslash( $_POST['to_state'] ) ) : '';
		if ( $completion_id <= 0 ) {
			TCT_Utils::send_json_error( array( 'code' => 'bad_request', 'message' => 'Invalid completion.' ), 400 );
			return;
		}
		if ( 'fail' !== $to_state && 'success' !== $to_state ) {
			TCT_Utils::send_json_error( array( 'code' => 'bad_request', 'message' => 'Invalid state.' ), 400 );
			return;
		}

		global $wpdb;
		$completions_table = TCT_DB::table_completions();
		$goals_table = TCT_DB::table_goals();
		$ledger_table = TCT_DB::table_ledger();

		$completion_row = $wpdb->get_row(
			$wpdb->prepare( "SELECT * FROM {$completions_table} WHERE id = %d AND user_id = %d", $completion_id, $user_id ),
			ARRAY_A
		);
		if ( ! $completion_row ) {
			TCT_Utils::send_json_error( array( 'code' => 'not_found', 'message' => 'Completion not found.' ), 404 );
			return;
		}
		if ( $goal_id > 0 && (int) $completion_row['goal_id'] !== $goal_id ) {
			TCT_Utils::send_json_error( array( 'code' => 'bad_request', 'message' => 'Goal mismatch.' ), 400 );
			return;
		}
		$goal_id = (int) $completion_row['goal_id'];
		$goal_row = $wpdb->get_row(
			$wpdb->prepare( "SELECT id, user_id, goal_name, label_name, points_per_completion, target, points_enabled_at, goal_type, period_unit, period_span, availability_cycle_json FROM {$goals_table} WHERE id = %d AND user_id = %d", $goal_id, $user_id ),
			ARRAY_A
		);
		if ( ! $goal_row ) {
			TCT_Utils::send_json_error( array( 'code' => 'not_found', 'message' => 'Goal not found.' ), 404 );
			return;
		}

		$source = isset( $completion_row['source'] ) ? (string) $completion_row['source'] : '';
		$source_ref = isset( $completion_row['source_ref'] ) ? (string) $completion_row['source_ref'] : '';
		$old_event_key = $this->build_event_key( $source, $source_ref, $goal_id );
		$event_key = $old_event_key;

		// If converting an auto-miss record into a success, re-classify it as manual so
		// the auto-miss cron will not overwrite the ledger state later.
		if ( 'success' === $to_state ) {
			$src_l = strtolower( trim( $source ) );
			if ( 'auto_miss' === $src_l || 'auto_due_miss' === $src_l ) {
				$updated = $wpdb->update(
					$completions_table,
					array( 'source' => 'manual' ),
					array( 'id' => $completion_id, 'user_id' => $user_id ),
					array( '%s' ),
					array( '%d', '%d' )
				);
				if ( false === $updated ) {
					TCT_Utils::send_json_error( array( 'code' => 'db_error', 'message' => 'Could not update completion source.' ), 500 );
					return;
				}
				$source = 'manual';
				$event_key = $this->build_event_key( $source, $source_ref, $goal_id );
				$completion_row['source'] = 'manual';
				// Remove the old auto-miss ledger row so it does not orphan points.
				$wpdb->delete( $ledger_table, array( 'user_id' => $user_id, 'event_key' => $old_event_key ), array( '%d', '%s' ) );
			}
		}

		if ( 'fail' === $to_state ) {
			$pause_state = $this->get_goal_pause_state_at( $goal_row, isset( $completion_row['completed_at'] ) ? (string) $completion_row['completed_at'] : '', TCT_Utils::wp_timezone() );
			if ( ! empty( $pause_state['is_paused'] ) ) {
				TCT_Utils::send_json_error( array( 'code' => 'tct_goal_paused', 'message' => 'This goal was paused on that date — manual fail is unavailable.' ), 400 );
				return;
			}
		}

		$ppc = isset( $goal_row['points_per_completion'] ) ? (int) $goal_row['points_per_completion'] : 0;
		$target = isset( $goal_row['target'] ) ? (int) $goal_row['target'] : 0;
		$occurred_at = isset( $completion_row['completed_at'] ) ? (string) $completion_row['completed_at'] : '';
		$points_enabled_at = isset( $goal_row['points_enabled_at'] ) ? (string) $goal_row['points_enabled_at'] : '';

		$new_points = 0;
		$new_details = '';

		if ( 'fail' === $to_state ) {
			$new_points = TCT_Utils::compute_penalty_points( $ppc, $target, 0 );
			$new_details = '[manual fail]';
		} else {
			// Success: award normal completion points when eligible.
			$new_points = 0;
			if ( $ppc > 0 && '' !== $occurred_at && '' !== $points_enabled_at ) {
				$occur_ts = strtotime( $occurred_at . ' UTC' );
				$enabled_ts = strtotime( $points_enabled_at . ' UTC' );
				if ( false !== $occur_ts && false !== $enabled_ts && $occur_ts >= $enabled_ts ) {
					$new_points = $ppc;
				}
			}
			$note = isset( $completion_row['note'] ) ? trim( (string) $completion_row['note'] ) : '';
			$task = isset( $completion_row['task_content'] ) ? trim( (string) $completion_row['task_content'] ) : '';
			$new_details = ( '' !== $note ) ? $note : ( ( '' !== $task ) ? $task : '' );
		}

		$ok = $this->upsert_ledger_row( $ledger_table, $user_id, $event_key, $goal_row, $completion_row, $new_points, $new_details );
		if ( ! $ok ) {
			TCT_Utils::send_json_error( array( 'code' => 'db_error', 'message' => 'Could not update ledger.' ), 500 );
			return;
		}

		$out = array(
			'completionId' => $completion_id,
			'goalId' => $goal_id,
			'state' => (string) $to_state,
			'points' => (int) $new_points,
			'sourceLabel' => $this->compute_source_label( $source, $new_details ),
			'isFail' => $this->is_failish( $source, $new_details ),
		);
		TCT_Utils::send_json_success( $out );
	}


	public function handle_backdate_completion_ajax() {
		// Reuse the same nonce as other history mutations.
		if ( ! TCT_Utils::enforce_ajax_nonce( 'tct_undo_completion', 'nonce', 403 ) ) { return; }
		$user_id = function_exists( 'get_current_user_id' ) ? (int) get_current_user_id() : 0;
		if ( $user_id <= 0 ) {
			TCT_Utils::send_json_error( array( 'code' => 'not_logged_in', 'message' => 'You must be logged in.' ), 401 );
			return;
		}

		$goal_id = isset( $_POST['goal_id'] ) ? (int) $_POST['goal_id'] : 0;
		$date = isset( $_POST['date'] ) ? sanitize_text_field( wp_unslash( $_POST['date'] ) ) : '';
		if ( $goal_id <= 0 ) {
			TCT_Utils::send_json_error( array( 'code' => 'bad_request', 'message' => 'Invalid goal.' ), 400 );
			return;
		}
		if ( '' === $date || ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $date ) ) {
			TCT_Utils::send_json_error( array( 'code' => 'bad_request', 'message' => 'Invalid date.' ), 400 );
			return;
		}

		global $wpdb;
		$goals_table = TCT_DB::table_goals();
		$completions_table = TCT_DB::table_completions();

		$goal_row = $wpdb->get_row(
			$wpdb->prepare( "SELECT id, goal_type, target, period_unit, period_span, availability_cycle_json FROM {$goals_table} WHERE id = %d AND user_id = %d", $goal_id, $user_id ),
			ARRAY_A
		);
		if ( ! is_array( $goal_row ) || empty( $goal_row['id'] ) ) {
			TCT_Utils::send_json_error( array( 'code' => 'not_found', 'message' => 'Goal not found.' ), 404 );
			return;
		}

		$tz = TCT_Utils::wp_timezone();
		try {
			$start_local = new DateTimeImmutable( $date . ' 00:00:00', $tz );
		} catch ( Exception $e ) {
			TCT_Utils::send_json_error( array( 'code' => 'bad_request', 'message' => 'Invalid date.' ), 400 );
			return;
		}
		$end_local = $start_local->add( new DateInterval( 'P1D' ) );

		$start_utc = TCT_Utils::dt_to_mysql_utc( $start_local );
		$end_utc = TCT_Utils::dt_to_mysql_utc( $end_local );

		$existing = (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM {$completions_table} WHERE user_id = %d AND goal_id = %d AND completed_at >= %s AND completed_at < %s",
				$user_id,
				$goal_id,
				$start_utc,
				$end_utc
			)
		);
		if ( $existing > 0 ) {
			TCT_Utils::send_json_error( array( 'code' => 'already_exists', 'message' => 'A completion already exists for that date.' ), 409 );
			return;
		}

		// Store the completion at noon local-time to avoid DST edge cases.
		try {
			$completed_local = new DateTimeImmutable( $date . ' 12:00:00', $tz );
		} catch ( Exception $e ) {
			$completed_local = $start_local->setTime( 12, 0, 0 );
		}
		$completed_utc = TCT_Utils::dt_to_mysql_utc( $completed_local );

		$pause_state = $this->get_goal_pause_state_at( $goal_row, $completed_local, $tz );
		$paused_manual = ! empty( $pause_state['is_paused'] );

		$new_id = TCT_DB::insert_manual_completion( $user_id, $goal_id, $completed_utc );
		if ( ! $new_id ) {
			TCT_Utils::send_json_error( array( 'code' => 'db_error', 'message' => 'Could not add completion.' ), 500 );
			return;
		}

		// Reconcile ledger from this day forward so points and interval-derived entries remain consistent.
		if ( class_exists( 'TCT_Ledger' ) && is_callable( array( 'TCT_Ledger', 'reconcile_user' ) ) ) {
			$until_utc = current_time( 'mysql', true );
			try {
				TCT_Ledger::reconcile_user( $user_id, $start_utc, $until_utc );
			} catch ( Exception $e ) {
				// Ledger reconciliation is best-effort. The completion is still stored.
			}
		}

		TCT_Utils::send_json_success(
			array(
				'completionId' => (int) $new_id,
				'goalId' => (int) $goal_id,
				'date' => (string) $date,
				'message' => $paused_manual ? 'Completion added. This paused-day manual log will count when the goal resumes.' : 'Completion added.',
			)
		);
	}
}
